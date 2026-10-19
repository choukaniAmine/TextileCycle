<?php

namespace App\Console\Commands;

use App\Services\DonIntelligenceService;
use Illuminate\Console\Command;

class ScoreDonsCommand extends Command
{
    protected $signature   = 'dons:score';
    protected $description = 'Recompute AI acceptance probability and priority score for pending donations';

    public function handle(DonIntelligenceService $service): int
    {
        $this->info('Training classifier...');
        $model = $service->train();

        if (! $model['ready']) {
            $this->warn("Model not ready ({$model['reason']}, samples: {$model['samples']}). Using neutral fallback (0.5).");
        } else {
            $this->info("Model trained on {$model['samples']} sample(s).");
        }

        $this->info('Scoring pending donations...');
        $count = $service->scoreAllPending();
        $this->info("Scored {$count} donation(s).");

        return self::SUCCESS;
    }
}