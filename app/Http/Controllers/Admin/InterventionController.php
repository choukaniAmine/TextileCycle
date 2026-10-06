<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Statut;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\InterventionRequest;
use App\Models\Demande;
use App\Models\Intervention;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class InterventionController extends Controller
{
    public function create(Demande $demande)
    {
        return view('admin.interventions.create', [
            'demande' => $demande,
            'intervention' => new Intervention(['statut' => Statut::EnAttente, 'atelier_id' => $demande->atelier_id]),
            'ateliers' => $this->ateliers(),
        ]);
    }

    public function store(InterventionRequest $request, Demande $demande): RedirectResponse
    {
        $demande->interventions()->create($request->validated());

        return redirect()->route('admin.demandes.show', $demande)->with('success', 'Intervention ajoutée à la demande.');
    }

    public function edit(Intervention $intervention)
    {
        return view('admin.interventions.edit', [
            'demande' => $intervention->demande, 'intervention' => $intervention, 'ateliers' => $this->ateliers(),
        ]);
    }

    public function update(InterventionRequest $request, Intervention $intervention): RedirectResponse
    {
        $intervention->update($request->validated());

        return redirect()->route('admin.demandes.show', $intervention->demande_id)->with('success', 'Intervention mise à jour.');
    }

    public function destroy(Intervention $intervention): RedirectResponse
    {
        $demandeId = $intervention->demande_id;
        $intervention->delete();

        return redirect()->route('admin.demandes.show', $demandeId)->with('success', 'Intervention supprimée.');
    }

    /** Valeur ajoutée : fait avancer l'intervention d'un cran (En attente → En cours → Terminée). */
    public function advance(Intervention $intervention): RedirectResponse
    {
        $next = $intervention->statut->next();
        if (! $next) {
            return back()->with('error', 'Cette intervention est déjà terminée.');
        }

        $data = ['statut' => $next, 'date_debut' => $intervention->date_debut ?? today()];
        if ($next === Statut::Terminee) {
            $data['date_fin'] = today();
        }
        $intervention->update($data);

        return back()->with('success', "Intervention passée à « {$next->label()} ».");
    }

    private function ateliers()
    {
        return User::where('role', UserRole::Atelier)->where('is_active', true)->orderBy('name')->get();
    }
}
