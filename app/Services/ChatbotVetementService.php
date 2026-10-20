<?php

namespace App\Services;

use App\Enums\EtatVetement;
use App\Enums\StatutVetement;
use App\Enums\TypeVetement;
use App\Models\Categorie;
use App\Models\Vetement;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class ChatbotVetementService
{
    private const MAX_TOURS = 3;       // allers-retours IA <-> base au maximum
    private const MAX_RESULTATS = 6;

    /**
     * @param  array<int, array{role: string, text: string}>  $historique
     * @return array{reponse: string, vetements: array, filtres: array}
     */
    public function repondre(string $message, array $historique = []): array
    {
        set_time_limit(120);

        $contents = [];
        foreach (array_slice($historique, -8) as $h) {
            $contents[] = [
                'role' => $h['role'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $h['text']]],
            ];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $message]]];

        $trouves = collect();
        $filtres = [];

        for ($tour = 0; $tour < self::MAX_TOURS; $tour++) {
            $data = $this->appeler($contents);
            $parts = data_get($data, 'candidates.0.content.parts', []);

            $appels = collect($parts)->filter(fn ($p) => isset($p['functionCall']));

            // Pas d'appel de fonction : c'est la réponse finale
            if ($appels->isEmpty()) {
                $texte = trim(collect($parts)->where('thought', '!==', true)->pluck('text')->filter()->implode(''));

                return [
                    'reponse' => $texte !== '' ? $texte : "Je n'ai pas bien compris, pouvez-vous reformuler ?",
                    'vetements' => $trouves->unique('id')->values()->all(),
                    'filtres' => $filtres,
                ];
            }

            // On renvoie à l'IA ses propres parts telles quelles (signatures de réflexion comprises)
            $contents[] = ['role' => 'model', 'parts' => $this->normaliser($parts)];

            $reponses = [];
            foreach ($appels as $p) {
                $nom = $p['functionCall']['name'];
                $args = $p['functionCall']['args'] ?? [];

                Log::info('Chatbot : appel de fonction', ['fonction' => $nom, 'args' => $args]);

                $resultat = $this->executer($nom, $args);
                $trouves = $trouves->concat($resultat['cartes']);
                if ($nom === 'rechercher_vetements') {
                    $filtres = $resultat['filtres'];
                }

                $reponses[] = ['functionResponse' => [
                    'name' => $nom,
                    'response' => ['resultat' => $resultat['pour_ia']],
                ]];
            }
            $contents[] = ['role' => 'user', 'parts' => $reponses];
        }

        return [
            'reponse' => "Je n'ai pas réussi à finaliser la recherche. Essayez de reformuler votre demande.",
            'vetements' => $trouves->unique('id')->values()->all(),
            'filtres' => $filtres,
        ];
    }

    // ------------------------------------------------------------------
    // Outils exposés à l'IA
    // ------------------------------------------------------------------

    private function executer(string $nom, array $args): array
    {
        return match ($nom) {
            'rechercher_vetements' => $this->rechercher($args),
            default => ['pour_ia' => ['erreur' => "Fonction inconnue : {$nom}"], 'cartes' => [], 'filtres' => []],
        };
    }

    private function rechercher(array $a): array
    {
        $filtres = [];
        $q = Vetement::with('categorie');

        // Seulement les vêtements disponibles, si ton enum contient ce cas (voir note en bas)
        if ($dispo = StatutVetement::tryFrom('disponible')) {
            $q->where('statut', $dispo->value);
        }

        if (! empty($a['categorie'])) {
            $mot = rtrim(Str::lower(trim($a['categorie'])), 'sx');
            $q->whereHas('categorie', fn ($c) => $c->where('nom', 'like', "%{$mot}%"));
            $filtres['catégorie'] = $a['categorie'];
        }
        if (! empty($a['taille'])) {
            $q->whereRaw('UPPER(taille) = ?', [Str::upper(trim($a['taille']))]);
            $filtres['taille'] = Str::upper(trim($a['taille']));
        }
        if (! empty($a['etat']) && ($etat = EtatVetement::tryFrom($a['etat']))) {
            $q->where('etat', $etat->value);
            $filtres['état'] = $etat->label();
        }
        if (! empty($a['type']) && ($type = TypeVetement::tryFrom($a['type']))) {
            $q->where('type', $type->value);
            $filtres['type'] = $type->label();
        }
        if (! empty($a['mot_cle'])) {
            $mot = trim($a['mot_cle']);
            $q->where(fn ($w) => $w->where('nom', 'like', "%{$mot}%")->orWhere('description', 'like', "%{$mot}%"));
            $filtres['mot-clé'] = $mot;
        }

        $total = (clone $q)->count();
        $liste = $q->latest()->limit(self::MAX_RESULTATS)->get();

        return [
            'filtres' => $filtres,
            'pour_ia' => [
                'total_trouves' => $total,
                'affiches' => $liste->count(),
                'vetements' => $liste->map(fn ($v) => [
                    'nom' => $v->nom,
                    'categorie' => $v->categorie?->nom,
                    'taille' => $v->taille,
                    'etat' => $v->etat->label(),
                    'type' => $v->type->label(),
                ])->all(),
            ],
            'cartes' => $liste->map(fn ($v) => [
                'id' => $v->id,
                'nom' => $v->nom,
                'categorie' => $v->categorie?->nom,
                'taille' => $v->taille,
                'etat' => $v->etat->label(),
                'type' => $v->type->label(),
                'image' => $v->imageUrl(),
                'url' => route('vetements.show', $v),
            ])->all(),
        ];
    }

    // ------------------------------------------------------------------
    // Définition de l'outil et consignes
    // ------------------------------------------------------------------

    private function declarations(): array
    {
        return [[
            'functionDeclarations' => [[
                'name' => 'rechercher_vetements',
                'description' => "Recherche des vêtements disponibles sur la plateforme TextileCycle. "
                    ."Tous les paramètres sont facultatifs : ne renseigne que ce que l'utilisateur a demandé.",
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'categorie' => ['type' => 'string', 'description' => 'Nom de catégorie, parmi la liste fournie.'],
                        'taille' => ['type' => 'string', 'description' => 'Taille : XS, S, M, L, XL, XXL, ou pointure (ex. 42).'],
                        'etat' => [
                            'type' => 'string',
                            'enum' => array_map(fn ($c) => $c->value, EtatVetement::cases()),
                            'description' => 'État du vêtement.',
                        ],
                        'type' => [
                            'type' => 'string',
                            'enum' => array_map(fn ($c) => $c->value, TypeVetement::cases()),
                            'description' => "Usage prévu du vêtement (don, réparation, transformation...).",
                        ],
                        'mot_cle' => ['type' => 'string', 'description' => 'Mot-clé libre (couleur, matière, style) cherché dans le nom et la description.'],
                    ],
                ],
            ]],
        ]];
    }

    private function consignes(): string
    {
        $categories = Categorie::orderBy('nom')->pluck('nom')->implode(', ');
        $etats = collect(EtatVetement::cases())->map(fn ($c) => "{$c->value} = {$c->label()}")->implode(' ; ');
        $types = collect(TypeVetement::cases())->map(fn ($c) => "{$c->value} = {$c->label()}")->implode(' ; ');

        return <<<TXT
Tu es l'assistant de TextileCycle, une plateforme tunisienne de seconde vie des vêtements (don, réparation, transformation).
Réponds toujours en français, de façon courte, chaleureuse et concrète. N'utilise pas de Markdown (pas d'astérisques, pas de titres).

RECHERCHE : dès que l'utilisateur cherche des vêtements, appelle la fonction rechercher_vetements. N'invente jamais un vêtement.
- Catégories existantes : {$categories}
- États (valeur = libellé) : {$etats}
- Types (valeur = libellé) : {$types}
- Associe les mots de l'utilisateur à ces valeurs (ex. « en bon état » -> l'état correspondant, « à donner » -> le type don).
- Si aucun résultat, relance UNE recherche en retirant le filtre le moins important, puis explique ce que tu as assoupli.
- Après la recherche, résume en une ou deux phrases ; les fiches s'affichent automatiquement sous ta réponse, ne les recopie pas.

CONSEILS : si l'utilisateur décrit un problème sur un vêtement (déchirure, tache, bouton, fermeture...), donne 2 à 4 conseils pratiques,
puis recommande l'option TextileCycle adaptée : réparation par un atelier, transformation, ou don à une association.

HORS SUJET : si la question n'a aucun rapport avec les vêtements ou la plateforme, dis poliment que tu ne peux aider que sur ce sujet.
TXT;
    }

    // ------------------------------------------------------------------
    // Appel HTTP avec modèle de secours
    // ------------------------------------------------------------------

    private function appeler(array $contents): array
    {
        $cle = config('services.gemini.key');
        if (! $cle) {
            throw new RuntimeException("L'assistant IA n'est pas configuré (GEMINI_API_KEY manquante).");
        }

        $modeles = array_values(array_unique(array_filter([
            config('services.gemini.model'),
            config('services.gemini.fallback_model'),
        ])));

        $reponse = null;
        foreach ($modeles as $modele) {
            try {
                $reponse = Http::withHeaders(['x-goog-api-key' => $cle])
                    ->acceptJson()
                    ->timeout(25)
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$modele}:generateContent", [
                        'systemInstruction' => ['parts' => [['text' => $this->consignes()]]],
                        'contents' => $contents,
                        'tools' => $this->declarations(),
                        'generationConfig' => ['temperature' => 0.3],
                    ]);
            } catch (ConnectionException $e) {
                Log::warning("Chatbot : Gemini injoignable ({$modele}) : ".$e->getMessage());
                $reponse = null;
                continue;
            }

            if (in_array($reponse->status(), [404, 429, 500, 503], true)) {
                Log::warning("Chatbot : {$modele} indisponible ({$reponse->status()}), modèle suivant.");
                continue;
            }
            break;
        }

        if ($reponse === null) {
            throw new RuntimeException("Le service IA ne répond pas. Vérifiez votre connexion et réessayez.");
        }
        if ($reponse->failed()) {
            Log::warning('Chatbot : erreur Gemini', ['status' => $reponse->status(), 'body' => $reponse->body()]);
            throw new RuntimeException(match ($reponse->status()) {
                429 => "Quota IA atteint. Réessayez dans une minute.",
                500, 503 => "Les serveurs de l'IA sont très sollicités. Réessayez dans un instant.",
                default => "L'assistant est momentanément indisponible (code {$reponse->status()}).",
            });
        }

        return $reponse->json();
    }

    /** Un objet JSON vide décodé devient [] en PHP : on le remet en objet avant de le renvoyer à l'API. */
    private function normaliser(array $parts): array
    {
        return array_map(function ($p) {
            if (isset($p['functionCall']) && empty($p['functionCall']['args'])) {
                $p['functionCall']['args'] = new \stdClass();
            }
            return $p;
        }, $parts);
    }
}