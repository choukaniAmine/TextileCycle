<?php

namespace App\Services;

use App\Models\Atelier;
use App\Models\Service;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiDamageAnalysisService
{
    /**
     * Analyse une image de vêtement endommagé via l'IA (Google Gemini Vision)
     * et recommande les réparations et ateliers adaptés.
     */
    public function analyze(UploadedFile|string $image): array
    {
        // 1. Préparer l'image en base64
        $imageData = $this->prepareImageBase64($image);

        // 2. Tenter l'appel à l'API Gemini
        $apiKey = config('services.gemini.api_key');
        $model = config('services.gemini.model', 'gemini-1.5-flash');

        $aiResult = null;

        if (!empty($apiKey)) {
            $aiResult = $this->callGeminiVision($apiKey, $model, $imageData);
        }

        // 3. Fallback intelligent si l'API n'a pas répondu ou en cas de problème réseau
        if (!$aiResult) {
            $aiResult = $this->getMockAnalysis($image);
        }

        // 4. Associer les résultats IA avec la BDD TextileCycle (Services & Ateliers réels)
        $aiResult['matching_services'] = $this->findMatchingServices($aiResult['suggested_service_keywords'] ?? []);
        $aiResult['matching_ateliers'] = $this->findMatchingAteliers($aiResult['matching_services']);

        return $aiResult;
    }

    /**
     * Convertit le fichier ou chemin en données base64 et mime type.
     */
    protected function prepareImageBase64(UploadedFile|string $image): array
    {
        if ($image instanceof UploadedFile) {
            $mimeType = $image->getMimeType() ?: 'image/jpeg';
            $data = base64_encode(file_get_contents($image->getRealPath()));
        } else {
            $mimeType = mime_content_type($image) ?: 'image/jpeg';
            $data = base64_encode(file_get_contents($image));
        }

        return [
            'mime_type' => $mimeType,
            'data' => $data,
        ];
    }

    /**
     * Appel à l'API Google Gemini Vision.
     */
    protected function callGeminiVision(string $apiKey, string $model, array $imageData): ?array
    {
        $prompt = <<<PROMPT
Tu es un expert mondial en confection textile, retouche, réparation et économie circulaire de vêtements.
Analyse précisément la photo fournie représentant un vêtement ou accessoire textile abîmé.

Renvoie UNIQUEMENT un objet JSON valide (sans balises markdown ```json, aucun texte superflu) avec exactement cette structure :
{
  "garment_type": "Type de vêtement identifié (ex: Jean en denim, Veste en laine, Chemise coton, Robe, Manteau...)",
  "material_detected": "Matière probable détectée (ex: Coton épais, Lin, Denim, Laine mérinos, Soie...)",
  "damage_type": "Nom précis du dommage (ex: Déchirure sur couture, Fermeture éclair cassée, Trou d'usure, Ourlet décousu, Tache tenace...)",
  "damage_description": "Description concise en 2-3 phrases de l'état du vêtement et du problème visible.",
  "severity": "Faible | Moyenne | Élevée",
  "repairability_score": 8, // Note entière de 1 à 10 sur la facilité et pertinence de réparation
  "repair_advice": "Conseil professionnel précis pour prolonger la durée de vie de cette pièce textile.",
  "suggested_repair": "Action concrète recommandée (ex: Remplacement de zip, Pose de patch / empiècement, Reprise de couture, Ajustement...)",
  "suggested_service_keywords": ["fermeture", "zip", "couture", "retouche", "patch", "ourlet"], // 3 à 5 mots-clés simples pour matcher des services en base de données
  "eco_impact_benefit": "Environ 6.5 kg de CO₂ et 2 500 L d'eau préservés en réparant plutôt qu'en rachetant neuf."
}
PROMPT;

        try {
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            $response = Http::timeout(25)->post($endpoint, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                            [
                                'inline_data' => [
                                    'mime_type' => $imageData['mime_type'],
                                    'data' => $imageData['data'],
                                ]
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'topP' => 0.8,
                    'responseMimeType' => 'application/json',
                ]
            ]);

            if ($response->successful()) {
                $body = $response->json();
                $text = $body['candidates'][0]['content']['parts'][0]['text'] ?? null;
                if ($text) {
                    $cleaned = trim($text);
                    $cleaned = preg_replace('/^```(?:json)?/i', '', $cleaned);
                    $cleaned = preg_replace('/```$/', '', $cleaned);
                    $decoded = json_decode(trim($cleaned), true);
                    if (is_array($decoded) && isset($decoded['garment_type'])) {
                        $decoded['source'] = 'gemini_ai';
                        return $decoded;
                    }
                }
            } else {
                Log::warning('Gemini API response failed', [
                    'status' => $response->status(),
                    'error' => $response->body()
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Gemini API Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Analyse de démonstration de secours (robuste pour tests & démo hors ligne).
     */
    protected function getMockAnalysis(UploadedFile|string $image): array
    {
        return [
            'source' => 'demo_smart_analysis',
            'garment_type' => 'Pantalon Jean / Denim',
            'material_detected' => 'Denim de coton renforcé',
            'damage_type' => 'Accroc / Déchirure au niveau de l\'entrejambe ou genou',
            'damage_description' => 'Accroc d\'usure typique sur tissu denim suite à des frottements répétés. Les fibres sont détendues mais la structure globale du pantalon reste saine.',
            'severity' => 'Moyenne',
            'repairability_score' => 9,
            'repair_advice' => 'Une pose de renfort thermocollant intérieur doublée d\'une piqûre en points serrés (darning) redonnera à ce jean plusieurs années de vie sans altérer son confort.',
            'suggested_repair' => 'Reprise d\'accroc ou pose d\'empiècement invisible',
            'suggested_service_keywords' => ['retouche', 'couture', 'patch', 'ourlet', 'ajustement'],
            'eco_impact_benefit' => 'Environ 8.2 kg de CO₂ et 3 200 L d\'eau économisés en prolongeant la vie de ce jean.',
        ];
    }

    /**
     * Recherche les prestations en base de données qui correspondent aux mots-clés détectés par l'IA.
     */
    protected function findMatchingServices(array $keywords): array
    {
        if (empty($keywords)) {
            $keywords = ['reparation', 'retouche', 'couture'];
        }

        $query = Service::query()->where('disponible', true)->with('atelier');

        $query->where(function ($q) use ($keywords) {
            foreach ($keywords as $kw) {
                $kw = trim($kw);
                if (strlen($kw) >= 3) {
                    $q->orWhere('nom', 'LIKE', "%{$kw}%")
                      ->orWhere('description', 'LIKE', "%{$kw}%")
                      ->orWhere('type_service', 'LIKE', "%{$kw}%");
                }
            }
        });

        $services = $query->limit(6)->get();

        if ($services->isEmpty()) {
            // Fallback : renvoyer les 4 premiers services disponibles
            $services = Service::query()->where('disponible', true)->with('atelier')->limit(4)->get();
        }

        return $services->map(function ($s) {
            return [
                'id' => $s->id,
                'nom' => $s->nom,
                'type_service' => $s->type_service,
                'tarif_estime' => (float) $s->tarif_estime,
                'duree_estimee' => $s->duree_estimee,
                'atelier_id' => $s->atelier_id,
                'atelier_nom' => $s->atelier->nom ?? 'Atelier partenaire',
                'atelier_ville' => $s->atelier->ville ?? 'Tunisie',
                'atelier_url' => route('ateliers.show', $s->atelier_id),
            ];
        })->toArray();
    }

    /**
     * Extrait les ateliers uniques proposant les prestations matchées.
     */
    protected function findMatchingAteliers(array $services): array
    {
        $atelierIds = collect($services)->pluck('atelier_id')->unique()->values();

        if ($atelierIds->isEmpty()) {
            return Atelier::query()->withCount('services')->limit(3)->get()->map(fn ($a) => [
                'id' => $a->id,
                'nom' => $a->nom,
                'ville' => $a->ville,
                'adresse' => $a->adresse,
                'url' => route('ateliers.show', $a->id),
            ])->toArray();
        }

        return Atelier::query()
            ->whereIn('id', $atelierIds)
            ->withCount('services')
            ->get()
            ->map(function ($a) {
                return [
                    'id' => $a->id,
                    'nom' => $a->nom,
                    'ville' => $a->ville,
                    'adresse' => $a->adresse,
                    'telephone' => $a->telephone,
                    'services_count' => $a->services_count,
                    'url' => route('ateliers.show', $a->id),
                ];
            })->toArray();
    }
}
