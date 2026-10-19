<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Don;
use App\Services\DonIntelligenceService;

class DonPriorityController extends Controller
{
    public function __construct(protected DonIntelligenceService $service) {}

    public function index()
    {
        // 1. L'IA calcule les scores
        $this->service->scoreAllPending();

        // 2. On récupère TOUS les dons en attente (sans pagination) pour les graphiques
        $allPending = Don::query()
            ->whereIn('status', $this->service->pendingValues())
            ->get();

        // 3. Calcul des stats pour le graphique de Priorité
        $priorityStats = [
            'Urgent (Rouge)' => $allPending->where('priority_score', '>', 70)->count(),
            'Moyen (Orange)' => $allPending->whereBetween('priority_score', [40, 70])->count(),
            'Faible (Vert)'  => $allPending->where('priority_score', '<', 40)->count(),
        ];

        // 4. Calcul des stats pour le graphique de Probabilité
        $probabilityStats = [
            'Très faible (0-25%)' => $allPending->where('acceptance_probability', '<=', 0.25)->count(),
            'Faible (26-50%)'     => $allPending->whereBetween('acceptance_probability', [0.26, 0.50])->count(),
            'Élevée (51-75%)'     => $allPending->whereBetween('acceptance_probability', [0.51, 0.75])->count(),
            'Très élevée (76-100%)'=> $allPending->where('acceptance_probability', '>', 0.75)->count(),
        ];

        // 5. On récupère les dons paginés pour le tableau
        $dons = Don::query()
            ->whereIn('status', $this->service->pendingValues())
            ->orderByDesc('priority_score')
            ->orderBy('created_at')
            ->with(['association', 'donor'])
            ->paginate(20);

        // 6. On envoie tout à la vue
        return view('admin.dons.priority', compact('dons', 'priorityStats', 'probabilityStats'));
    }
}