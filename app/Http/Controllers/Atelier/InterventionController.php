<?php

namespace App\Http\Controllers\Atelier;

use App\Enums\Statut;
use App\Http\Controllers\Controller;
use App\Http\Requests\AtelierInterventionRequest;
use App\Models\Demande;
use App\Models\Intervention;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InterventionController extends Controller
{
    public function store(AtelierInterventionRequest $request, Demande $demande): RedirectResponse
    {
        abort_unless($demande->estAssigneA($request->user()), 403);

        $demande->interventions()->create([
            ...$request->validated(),
            'atelier_id' => $request->user()->id,
            'statut' => Statut::EnAttente,
        ]);

        return back()->with('success', 'Intervention ajoutée. Le client a été prévenu.');
    }

    public function edit(Request $request, Intervention $intervention)
    {
        $this->authorizeIntervention($request, $intervention);

        return view('atelier.interventions.edit', ['intervention' => $intervention, 'demande' => $intervention->demande]);
    }

    public function update(AtelierInterventionRequest $request, Intervention $intervention): RedirectResponse
    {
        $this->authorizeIntervention($request, $intervention);
        $intervention->update($request->validated());

        return redirect()->route('atelier.travaux.show', $intervention->demande_id)->with('success', 'Intervention mise à jour.');
    }

    public function advance(Request $request, Intervention $intervention): RedirectResponse
    {
        $this->authorizeIntervention($request, $intervention);

        if (! $intervention->avancer()) {
            return back()->with('error', 'Cette intervention est déjà terminée.');
        }

        return back()->with('success', "Intervention passée à « {$intervention->statut->label()} ». Le client est prévenu.");
    }

    public function destroy(Request $request, Intervention $intervention): RedirectResponse
    {
        $this->authorizeIntervention($request, $intervention);
        $intervention->delete();

        return back()->with('success', 'Intervention supprimée.');
    }

    private function authorizeIntervention(Request $request, Intervention $intervention): void
    {
        abort_unless($intervention->demande->estAssigneA($request->user()), 403);
    }
}
