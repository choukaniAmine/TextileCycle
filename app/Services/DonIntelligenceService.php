<?php

namespace App\Services;

use App\Enums\DonStatus;
use App\Models\Don;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DonIntelligenceService
{
    protected ?array $model = null;
    protected array $associationStats = [];
    protected array $donorStats = [];

    public function acceptedValues(): array
    {
        return $this->valuesMatching(config('donation_ai.accepted_keywords', []));
    }

    public function refusedValues(): array
    {
        return $this->valuesMatching(config('donation_ai.refused_keywords', []));
    }

    public function pendingValues(): array
    {
        return $this->valuesMatching([], exclude: array_merge(
            config('donation_ai.accepted_keywords', []),
            config('donation_ai.refused_keywords', [])
        ));
    }

    protected function valuesMatching(array $keywords, array $exclude = []): array
    {
        return collect(DonStatus::cases())
            ->filter(function (DonStatus $c) use ($keywords, $exclude) {
                $hasKeyword = empty($keywords) || collect($keywords)
                    ->contains(fn ($kw) => Str::contains($c->name, $kw, true));
                $isExcluded = ! empty($exclude) && collect($exclude)
                    ->contains(fn ($kw) => Str::contains($c->name, $kw, true));
                return $hasKeyword && ! $isExcluded;
            })
            ->map(fn (DonStatus $c) => $c->value)
            ->values()
            ->all();
    }

    protected function statusValue(Don $don)
    {
        return $don->status instanceof DonStatus ? $don->status->value : $don->status;
    }

    public function train(): array
    {
        $acceptedValues = $this->acceptedValues();
        $refusedValues  = $this->refusedValues();

        $dons = Don::query()
            ->whereIn('status', array_merge($acceptedValues, $refusedValues))
            ->get(['id', 'association_id', 'user_id', 'status', 'quantity',
                   'description', 'notes', 'donated_at', 'created_at']);

        $this->buildHistoricalStats($dons, $acceptedValues);

        $accepted = $dons->filter(fn ($d) => in_array($this->statusValue($d), $acceptedValues, true));
        $refused  = $dons->filter(fn ($d) => in_array($this->statusValue($d), $refusedValues, true));

        $total      = $accepted->count() + $refused->count();
        $minSamples = (int) config('donation_ai.min_training_samples', 10);

        if ($total < $minSamples || $accepted->isEmpty() || $refused->isEmpty()) {
            return $this->model = [
                'ready'   => false,
                'reason'  => 'insufficient_data',
                'samples' => $total,
            ];
        }

        return $this->model = [
            'ready'   => true,
            'samples' => $total,
            'prior'   => [
                'accepted' => $accepted->count() / $total,
                'refused'  => $refused->count() / $total,
            ],
            'counts'  => [
                'accepted' => $this->countFeatures($accepted),
                'refused'  => $this->countFeatures($refused),
            ],
            'totals'  => [
                'accepted' => $accepted->count(),
                'refused'  => $refused->count(),
            ],
        ];
    }

    protected function buildHistoricalStats(Collection $dons, array $acceptedValues): void
    {
        $this->associationStats = [];
        $this->donorStats = [];

        foreach ($dons as $d) {
            $field = in_array($this->statusValue($d), $acceptedValues, true) ? 'accepted' : 'refused';
            if ($d->association_id) {
                $this->associationStats[$d->association_id] ??= ['accepted' => 0, 'refused' => 0];
                $this->associationStats[$d->association_id][$field]++;
            }
            if ($d->user_id) {
                $this->donorStats[$d->user_id] ??= ['accepted' => 0, 'refused' => 0];
                $this->donorStats[$d->user_id][$field]++;
            }
        }
    }

    protected function countFeatures(Collection $dons): array
    {
        $counts = [];
        foreach ($dons as $don) {
            foreach ($this->features($don) as $feature => $value) {
                $key = "{$feature}={$value}";
                $counts[$key] = ($counts[$key] ?? 0) + 1;
            }
        }
        return $counts;
    }

    protected function features(Don $don): array
    {
        return [
            'quantity_bucket'    => $this->quantityBucket((int) $don->quantity),
            'description_length' => $this->lengthBucket(strlen((string) ($don->description ?? ''))),
            'day_of_week'        => $don->donated_at?->dayOfWeekIso ?? 0,
            'has_notes'          => empty($don->notes) ? 0 : 1,
            'association_rate'   => $this->rateBucket($this->associationStats[$don->association_id] ?? null),
            'donor_rate'         => $this->rateBucket($this->donorStats[$don->user_id] ?? null),
        ];
    }

    protected function quantityBucket(int $q): string
    {
        return match (true) {
            $q <= 0  => 'zero',
            $q < 10  => 'small',
            $q < 50  => 'medium',
            $q < 200 => 'large',
            default  => 'xlarge',
        };
    }

    protected function lengthBucket(int $len): string
    {
        return match (true) {
            $len === 0 => 'none',
            $len < 50  => 'short',
            $len < 200 => 'medium',
            default    => 'long',
        };
    }

    protected function rateBucket(?array $stats): string
    {
        if (! $stats || ($stats['accepted'] + $stats['refused']) === 0) {
            return 'unknown';
        }
        $rate = $stats['accepted'] / ($stats['accepted'] + $stats['refused']);
        return match (true) {
            $rate < 0.4 => 'low',
            $rate < 0.7 => 'medium',
            default     => 'high',
        };
    }

    public function predictProbability(Don $don): float
    {
        $model = $this->ensureModel();
        if (! $model['ready']) {
            return 0.5;
        }

        $features    = $this->features($don);
        $numFeatures = count($features);

        $logA = log($model['prior']['accepted']);
        $logR = log($model['prior']['refused']);

        foreach ($features as $feature => $value) {
            $key    = "{$feature}={$value}";
            $aCount = $model['counts']['accepted'][$key] ?? 0;
            $rCount = $model['counts']['refused'][$key]  ?? 0;
            $logA  += log(($aCount + 1) / ($model['totals']['accepted'] + $numFeatures));
            $logR  += log(($rCount + 1) / ($model['totals']['refused']  + $numFeatures));
        }

        $max = max($logA, $logR);
        $a   = exp($logA - $max);
        $r   = exp($logR - $max);

        return $a / ($a + $r);
    }

    public function priorityScore(Don $don, float $acceptanceProbability): float
    {
        $w = config('donation_ai.priority_weights');

        $daysWaiting = $don->created_at ? (float) $don->created_at->diffInDays(now()) : 0.0;
        $staleness   = min(1.0, $daysWaiting / 30.0);

        $qty            = max(1, (int) $don->quantity);
        $quantityFactor = min(1.0, log($qty + 1) / log(1000));

        $base = $w['acceptance'] * $acceptanceProbability
              + $w['staleness']  * $staleness
              + $w['quantity']   * $quantityFactor;

        $boost = $this->keywordBoost($don);

        return round(min(100.0, max(0.0, ($base + $boost) * 100.0)), 2);
    }

    protected function keywordBoost(Don $don): float
    {
        $text = Str::lower(trim(($don->description ?? '') . ' ' . ($don->notes ?? '')));
        if ($text === '') return 0.0;

        $boost = 0.0;
        foreach (config('donation_ai.keyword_boost', []) as $category) {
            $matched = collect($category['keywords'] ?? [])
                ->contains(fn ($kw) => Str::contains($text, Str::lower($kw)));
            if ($matched) $boost += (float) ($category['weight'] ?? 0);
        }
        return $boost;
    }

    public function scoreAllPending(int $chunk = 200): int
    {
        $this->ensureModel();
        $count = 0;
        Don::query()
            ->whereIn('status', $this->pendingValues())
            ->chunkById($chunk, function ($dons) use (&$count) {
                foreach ($dons as $don) {
                    $this->applyScores($don);
                    $count++;
                }
            });
        return $count;
    }

    protected function applyScores(Don $don): void
    {
        $prob  = $this->predictProbability($don);
        $score = $this->priorityScore($don, $prob);
        $don->forceFill([
            'acceptance_probability' => round($prob, 4),
            'priority_score'         => $score,
            'scored_at'              => now(),
        ])->saveQuietly();
    }

    protected function ensureModel(): array
    {
        return $this->model ??= $this->train();
    }
    public function recommendAssociations(string $description, int $quantity): array
    {
        // On s'assure que le modèle est entraîné
        $this->ensureModel();
        
        // On récupère toutes les associations actives
        $associations = \App\Models\Association::active()->get();
        $recommendations = [];

        // On crée un "faux don" temporaire pour tester la probabilité
        $dummyDon = new Don([
            'description' => $description,
            'quantity'    => $quantity,
            'notes'       => '',
            'donated_at'  => now(),
        ]);

        foreach ($associations as $assoc) {
            $dummyDon->association_id = $assoc->id;
            
            // L'IA prédit la probabilité pour cette association spécifique
            $probability = $this->predictProbability($dummyDon);

            // On garde uniquement les associations avec un minimum de chances (ex: > 30%)
            if ($probability > 0.3) {
                $recommendations[] = [
                    'id'          => $assoc->id,
                    'name'        => $assoc->name,
                    'city'        => $assoc->city,
                    'probability' => round($probability * 100), // En pourcentage
                ];
            }
        }

        // On trie par probabilité décroissante
        usort($recommendations, fn($a, $b) => $b['probability'] <=> $a['probability']);

        // On retourne le top 3
        return array_slice($recommendations, 0, 3);
    }
}