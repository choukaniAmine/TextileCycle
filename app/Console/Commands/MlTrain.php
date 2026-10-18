<?php

namespace App\Console\Commands;

use App\Enums\Statut;
use App\Models\Demande;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class MlTrain extends Command
{
    protected $signature = 'ml:train
        {--reel-seulement : exclure les données de démonstration}
        {--export-seulement : exporter le CSV sans lancer l\'entraînement}';

    protected $description = 'Exporte les durées réelles confirmées par les ateliers et entraîne le modèle local (Python).';

    public function handle(): int
    {
        $demandes = Demande::where('statut', Statut::Terminee->value)
            ->whereNotNull('duree_reelle_min')
            ->whereNotNull('categorie_vetement')
            ->whereNotNull('type_intervention')
            ->get();

        $dir = storage_path('app/ml');
        File::ensureDirectoryExists($dir);
        $csv = $dir.DIRECTORY_SEPARATOR.'durees.csv';

        $h = fopen($csv, 'w');
        fputcsv($h, ['demande_id', 'categorie_vetement', 'type_intervention', 'type_demande', 'niveau_difficulte', 'description_len', 'duree_reelle_min', 'source'], ',', '"', '');
        foreach ($demandes as $d) {
            fputcsv($h, [
                $d->id, $d->categorie_vetement, $d->type_intervention, $d->type->value,
                $d->niveau_difficulte ?? 2, mb_strlen($d->description), $d->duree_reelle_min,
                $d->duree_reelle_source ?? 'atelier',
            ], ',', '"', '');
        }
        fclose($h);

        $demo = $demandes->where('duree_reelle_source', 'demo')->count();
        $this->info("{$demandes->count()} demandes exportées ({$demo} de démonstration) → {$csv}");

        if ($this->option('export-seulement')) {
            return self::SUCCESS;
        }

        $cmd = [config('ai.ml.python'), 'train.py', '--csv', $csv];
        if ($this->option('reel-seulement')) {
            $cmd[] = '--exclude-demo';
        }

        $result = Process::path(base_path('ml_service'))->timeout(300)->run($cmd);
        $this->line(trim($result->output()));
        if ($result->failed()) {
            $this->error(trim($result->errorOutput()) ?: 'Échec de l’entraînement (Python installé ? dépendances installées ?)');

            return self::FAILURE;
        }
        $this->info('Terminé. Le service (python app.py) recharge le modèle automatiquement.');

        return self::SUCCESS;
    }
}
