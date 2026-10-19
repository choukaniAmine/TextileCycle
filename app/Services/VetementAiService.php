<?php

namespace App\Services;

use App\Enums\EtatVetement;
use App\Enums\TypeVetement;
use App\Exceptions\AiException;
use App\Models\Categorie;
use App\Models\Vetement;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VetementAiService
{
    /** Fonctionnalités 1 + 2 : analyse de la photo. */
    public function analyserPhoto(UploadedFile $photo): array
    {
        $categories = Categorie::orderBy('nom')->get(['id', 'nom']);
        if ($categories->isEmpty()) {
            throw new AiException("Aucune catégorie n'existe encore. Demandez à l'administrateur d'en créer.");
        }
        $noms = $categories->pluck('nom')->implode(', ');

        $prompt = <<<PROMPT
Tu es l'assistant d'une plateforme française de réemploi textile (dons, réparation, transformation).
Analyse la photo et réponds UNIQUEMENT avec un objet JSON contenant exactement ces clés :
- "est_vetement" : booléen, false si la photo ne montre pas un vêtement ou un accessoire textile
- "nom" : titre court (ex : "Jean bleu droit")
- "description" : 2 à 3 phrases en français, factuelles, sans inventer ce qui n'est pas visible
- "categorie" : une valeur EXACTE parmi : {$noms}
- "couleur" : couleur principale
- "etat" : "neuf", "tres_bon", "bon" ou "use"
- "defauts" : liste de défauts visibles (déchirure, tache, bouton ou fermeture manquants...), liste vide si aucun
- "type_conseille" : "don", "reparation" ou "transformation"
- "raison" : une phrase expliquant le type conseillé

Règles pour "type_conseille" :
- "don" : bon état, aucun défaut notable
- "reparation" : défaut réparable (fermeture, bouton, couture, petite déchirure) et vêtement sinon en bon état
- "transformation" : très usé ou taché de façon irrécupérable, mais tissu réutilisable
PROMPT;

        $data = $this->generer([
            ['text' => $prompt],
            ['inline_data' => [
                'mime_type' => $photo->getMimeType() ?: 'image/jpeg',
                'data' => base64_encode(file_get_contents($photo->getRealPath())),
            ]],
        ]);

        if (! ($data['est_vetement'] ?? false)) {
            throw new AiException("La photo ne semble pas montrer un vêtement. Essayez avec une autre photo.");
        }

        $categorie = $categories->first(
            fn ($c) => mb_strtolower($c->nom) === mb_strtolower(trim((string) ($data['categorie'] ?? '')))
        );

        return [
            'nom' => Str::limit(trim((string) ($data['nom'] ?? '')), 255, ''),
            'description' => trim((string) ($data['description'] ?? '')),
            'categorie_id' => $categorie?->id,
            'etat' => EtatVetement::tryFrom((string) ($data['etat'] ?? ''))?->value,
            'type' => TypeVetement::tryFrom((string) ($data['type_conseille'] ?? ''))?->value,
            'couleur' => trim((string) ($data['couleur'] ?? '')),
            'defauts' => array_values(array_filter(array_map('strval', (array) ($data['defauts'] ?? [])))),
            'raison' => trim((string) ($data['raison'] ?? '')),
        ];
    }

    /** Fonctionnalité 3 : idées de seconde vie (mises en cache pour économiser le quota). */
    public function idees(Vetement $vetement): array
    {
        $vetement->loadMissing('categorie');
        $cle = "ia.idees.{$vetement->id}.{$vetement->updated_at->timestamp}";

        return Cache::remember($cle, now()->addDays(7), function () use ($vetement) {
            $prompt = <<<PROMPT
Tu es un expert en upcycling textile. Propose exactement 3 idées concrètes et réalisables
pour donner une seconde vie à ce vêtement :
- Nom : {$vetement->nom}
- Catégorie : {$vetement->categorie->nom}
- Description : {$vetement->description}
- État : {$vetement->etat->label()}
- Taille : {$vetement->taille}

Réponds UNIQUEMENT en JSON : {"idees":[{"titre":"...","description":"2 phrases maximum","difficulte":"facile|moyen|difficile","duree":"ex : 1 heure","materiel":["...","..."]}]}
Écris en français.
PROMPT;

            $data = $this->generer([['text' => $prompt]], 0.8);

            $idees = collect($data['idees'] ?? [])->take(3)->map(fn ($i) => [
                'titre' => Str::limit((string) ($i['titre'] ?? 'Idée'), 120, ''),
                'description' => (string) ($i['description'] ?? ''),
                'difficulte' => in_array($i['difficulte'] ?? '', ['facile', 'moyen', 'difficile'], true) ? $i['difficulte'] : 'moyen',
                'duree' => (string) ($i['duree'] ?? ''),
                'materiel' => array_values(array_map('strval', (array) ($i['materiel'] ?? []))),
            ])->values()->all();

            if (empty($idees)) {
                throw new AiException("L'IA n'a proposé aucune idée. Réessayez.");
            }

            return $idees;
        });
    }

    /** Fonctionnalité 5 : réécriture de la description. */
    public function ameliorerDescription(array $v): string
    {
        $categorie = $v['categorie_id'] ? optional(Categorie::find($v['categorie_id']))->nom : null;

        $prompt = <<<PROMPT
Réécris la description d'une annonce de vêtement pour une plateforme de dons et de réemploi.
Style : clair, chaleureux, honnête, 2 à 4 phrases, 400 caractères maximum, en français.
N'invente AUCUNE caractéristique absente des informations ci-dessous.

Nom : {$v['nom']}
Catégorie : {$categorie}
Taille : {$v['taille']}
État : {$v['etat']}
Description actuelle : {$v['description']}

Réponds UNIQUEMENT en JSON : {"description":"..."}
PROMPT;

        $data = $this->generer([['text' => $prompt]], 0.6);
        $texte = trim((string) ($data['description'] ?? ''));

        if ($texte === '') {
            throw new AiException("L'IA n'a pas pu rédiger de description. Réessayez.");
        }

        return Str::limit($texte, 600, '');
    }

    /** Appel HTTP à Gemini, réponse JSON décodée. */
    private function generer(array $parts, float $temperature = 0.4): array
    {
        $cle = config('services.gemini.key');
        if (! $cle) {
            throw new AiException("L'assistant IA n'est pas configuré (GEMINI_API_KEY manquante dans .env).");
        }
        $modele = config('services.gemini.model');

        try {
            $reponse = Http::withHeaders(['x-goog-api-key' => $cle])
                ->acceptJson()
                ->timeout(45)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$modele}:generateContent", [
                    'contents' => [['role' => 'user', 'parts' => $parts]],
                    'generationConfig' => [
                        'temperature' => $temperature,
                        'responseMimeType' => 'application/json',
                    ],
                ]);
        } catch (ConnectionException $e) {
            Log::warning('Gemini injoignable : '.$e->getMessage());
            throw new AiException("Le service IA ne répond pas. Réessayez ou remplissez le formulaire manuellement.");
        }

        if ($reponse->failed()) {
            Log::warning('Erreur Gemini', ['status' => $reponse->status(), 'body' => $reponse->body()]);

            throw new AiException(match ($reponse->status()) {
                429 => "Quota IA atteint. Réessayez dans une minute.",
                400, 403 => "Requête refusée par l'IA (clé invalide ou région non prise en charge). Détails dans storage/logs/laravel.log.",
                404 => "Modèle IA introuvable. Vérifiez GEMINI_MODEL dans .env.",
                default => "Le service IA est momentanément indisponible.",
            });
        }

        $texte = collect(data_get($reponse->json(), 'candidates.0.content.parts', []))
            ->pluck('text')->filter()->implode('');
        $texte = trim(preg_replace('/^```(?:json)?|```$/m', '', $texte));

        $json = json_decode($texte, true);
        if (! is_array($json)) {
            throw new AiException("Réponse de l'IA illisible. Réessayez.");
        }

        return $json;
    }
}