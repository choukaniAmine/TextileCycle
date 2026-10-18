<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\Ml\RepairTimeEstimator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Estimation en direct dans le formulaire (service local uniquement). */
class EstimationController extends Controller
{
    public function __invoke(Request $request, RepairTimeEstimator $estimator): JsonResponse
    {
        $data = $request->validate([
            'categorie_vetement' => ['required', Rule::in(array_keys(config('repair.categories')))],
            'type_intervention' => ['required', Rule::in(array_keys(config('repair.interventions')))],
            'type_demande' => ['required', Rule::in(['reparation', 'transformation'])],
            'niveau_difficulte' => ['required', 'integer', 'between:1,3'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        return response()->json($estimator->estimate([
            'categorie_vetement' => $data['categorie_vetement'],
            'type_intervention' => $data['type_intervention'],
            'type_demande' => $data['type_demande'],
            'niveau_difficulte' => (int) $data['niveau_difficulte'],
            'description_len' => mb_strlen($data['description'] ?? ''),
        ]));
    }
}
