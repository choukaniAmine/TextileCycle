<?php

namespace App\Services\Ml;

use App\Models\Demande;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * IA n°1 : client du service d'estimation LOCAL (Python/scikit-learn).
 * Ne dépend d'aucune API externe et ne connaît rien de Grok.
 */
class RepairTimeEstimator
{
    public function featuresFromDemande(Demande $d): array
    {
        return [
            'categorie_vetement' => $d->categorie_vetement,
            'type_intervention' => $d->type_intervention,
            'type_demande' => $d->type->value,
            'niveau_difficulte' => $d->niveau_difficulte ?? 2,
            'description_len' => mb_strlen((string) $d->description),
        ];
    }

    /** Appelle le service ; ne lève jamais d'exception (le formulaire doit rester utilisable). */
    public function estimate(array $features): array
    {
        $cfg = config('ai.ml');

        try {
            $response = Http::baseUrl($cfg['url'])->acceptJson()->asJson()
                ->connectTimeout($cfg['connect_timeout'])->timeout($cfg['timeout'])
                ->post('/predict', $features);
        } catch (ConnectionException) {
            return $this->indisponible('service_hors_ligne', 'Le service d’estimation local n’est pas démarré.');
        }

        if ($response->failed()) {
            return $this->indisponible('erreur_service', 'Le service d’estimation a renvoyé une erreur.');
        }

        return $this->normalise((array) $response->json());
    }

    /** Calcule puis enregistre l'estimation d'une demande (si les informations nécessaires sont là). */
    public function estimateAndStore(Demande $demande): void
    {
        if (! $demande->categorie_vetement || ! $demande->type_intervention) {
            $demande->update(['duree_estimee_min' => null, 'estimation' => null]);

            return;
        }

        $result = $this->estimate($this->featuresFromDemande($demande));
        $demande->update([
            'duree_estimee_min' => $result['disponible'] ? $result['minutes'] : null,
            'estimation' => $result + ['calcule_le' => now()->toIso8601String()],
        ]);
    }

    /** Valide la forme de la réponse : on ne fait jamais confiance aveuglément à un service. */
    private function normalise(array $r): array
    {
        if (! ($r['disponible'] ?? false)) {
            return $this->indisponible($r['raison'] ?? 'donnees_insuffisantes', (string) ($r['message'] ?? 'Estimation indisponible.'), $r['n_echantillons'] ?? null);
        }

        $minutes = (int) ($r['minutes'] ?? 0);
        if ($minutes < 5 || $minutes > 10080) {
            return $this->indisponible('reponse_invalide', 'Réponse du service d’estimation incohérente.');
        }

        return [
            'disponible' => true,
            'mode' => in_array($r['mode'] ?? null, ['modele', 'mediane'], true) ? $r['mode'] : 'mediane',
            'minutes' => $minutes,
            'min' => min($minutes, (int) ($r['min'] ?? $minutes)),
            'max' => max($minutes, (int) ($r['max'] ?? $minutes)),
            'confiance' => in_array($r['confiance'] ?? null, ['faible', 'moyenne', 'elevee'], true) ? $r['confiance'] : 'faible',
            'n_echantillons' => (int) ($r['n_echantillons'] ?? 0),
            'mae_minutes' => isset($r['mae_minutes']) ? (float) $r['mae_minutes'] : null,
            'donnees_demo' => (bool) ($r['donnees_demo'] ?? false),
            'message' => (string) ($r['message'] ?? ''),
        ];
    }

    private function indisponible(string $raison, string $message, ?int $n = null): array
    {
        return ['disponible' => false, 'raison' => $raison, 'message' => $message, 'n_echantillons' => $n];
    }
}
