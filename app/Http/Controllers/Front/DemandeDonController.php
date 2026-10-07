<?php

namespace App\Http\Controllers\Front;

use App\Enums\StatutDemande;
use App\Enums\StatutVetement;
use App\Http\Controllers\Controller;
use App\Models\DemandeDon;
use App\Models\Vetement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class DemandeDonController extends Controller
{
    /** Demandes reçues pour MES vêtements. */
    public function recues(Request $request)
    {
        $demandes = DemandeDon::with(['vetement', 'demandeur'])
            ->whereHas('vetement', fn ($q) => $q->where('user_id', $request->user()->id))
            ->latest()
            ->paginate(10);

        return view('front.mes-dons-demandes.recues', compact('demandes'));
    }

    /** Demandes que J'AI envoyées. */
    public function envoyees(Request $request)
    {
        $demandes = DemandeDon::with(['vetement.user'])
            ->where('demandeur_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('front.mes-dons-demandes.envoyees', compact('demandes'));
    }

    public function store(Request $request, Vetement $vetement)
    {
        Gate::authorize('demander', $vetement);

        $data = $request->validate([
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        $dejaDemande = $vetement->demandes()
            ->where('demandeur_id', $request->user()->id)
            ->where('statut', StatutDemande::EnAttente)
            ->exists();

        if ($dejaDemande) {
            return back()->with('error', 'Vous avez déjà une demande en attente pour ce vêtement.');
        }

        $vetement->demandes()->create([
            'demandeur_id' => $request->user()->id,
            'message' => $data['message'] ?? null,
            'statut' => StatutDemande::EnAttente,
        ]);

        return redirect()->route('demandes-don.envoyees')->with('success', 'Votre demande a été envoyée.');
    }

    public function accepter(DemandeDon $demande)
    {
        Gate::authorize('repondre', $demande);

        DB::transaction(function () use ($demande) {
            $demande->update(['statut' => StatutDemande::Acceptee]);
            $demande->vetement->update(['statut' => StatutVetement::Donne]);

            // Les autres demandes en attente pour ce vêtement sont refusées
            DemandeDon::where('vetement_id', $demande->vetement_id)
                ->where('id', '!=', $demande->id)
                ->where('statut', StatutDemande::EnAttente)
                ->update(['statut' => StatutDemande::Refusee]);
        });

        return back()->with('success', 'Demande acceptée. Le vêtement est marqué comme donné.');
    }

    public function refuser(DemandeDon $demande)
    {
        Gate::authorize('repondre', $demande);

        $demande->update(['statut' => StatutDemande::Refusee]);

        return back()->with('success', 'Demande refusée.');
    }

    public function annuler(DemandeDon $demande)
    {
        Gate::authorize('annuler', $demande);

        $demande->update(['statut' => StatutDemande::Annulee]);

        return back()->with('success', 'Demande annulée.');
    }
}