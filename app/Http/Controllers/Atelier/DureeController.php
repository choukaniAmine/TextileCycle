<?php

namespace App\Http\Controllers\Atelier;

use App\Http\Controllers\Controller;
use App\Models\Demande;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** L'atelier confirme ou corrige la durée réelle : c'est cette donnée qui alimente l'apprentissage. */
class DureeController extends Controller
{
    public function update(Request $request, Demande $demande): RedirectResponse
    {
        abort_unless($demande->estAssigneA($request->user()), 403);

        if ($request->boolean('confirmer_estimation')) {
            abort_if($demande->duree_estimee_min === null, 422, 'Aucune estimation à confirmer.');
            $minutes = $demande->duree_estimee_min;
        } else {
            $data = $request->validate([
                'heures' => ['required', 'integer', 'min:0', 'max:168'],
                'minutes' => ['required', 'integer', 'min:0', 'max:59'],
            ]);
            $minutes = $data['heures'] * 60 + $data['minutes'];
            if ($minutes < 5) {
                return back()->withErrors(['minutes' => 'La durée doit être d’au moins 5 minutes.']);
            }
        }

        $demande->update([
            'duree_reelle_min' => $minutes,
            'duree_reelle_source' => 'atelier',
            'duree_reelle_confirmee_at' => now(),
        ]);

        return back()->with('success', 'Durée réelle enregistrée. Elle servira à améliorer les prochaines estimations.');
    }
}
