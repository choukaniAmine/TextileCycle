<?php

namespace App\Http\Controllers\Atelier;

use App\Enums\Statut;
use App\Enums\TypeDemande;
use App\Http\Controllers\Controller;
use App\Models\Demande;
use App\Models\Intervention;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DemandeController extends Controller
{
    /** Vitrine : demandes libres, les urgentes en premier. */
    public function disponibles(Request $request)
    {
        $demandes = Demande::disponibles()->with('user')
            ->search($request->query('q'))
            ->when($request->query('type'), fn ($q, $t) => $q->where('type', $t))
            ->when($request->boolean('urgent'), fn ($q) => $q->where('urgent', true))
            ->orderByDesc('urgent')->latest()->paginate(9)->withQueryString();

        return view('atelier.demandes.disponibles', ['demandes' => $demandes, 'types' => TypeDemande::cases()]);
    }

    public function prendre(Request $request, Demande $demande): RedirectResponse
    {
        if (! $demande->prendreEnCharge($request->user())) {
            return back()->with('error', 'Trop tard : cette demande a déjà été prise en charge.');
        }

        return redirect()->route('atelier.travaux.show', $demande)
            ->with('success', 'Demande prise en charge ! Le client est prévenu. Ajoutez vos interventions.');
    }

    /** Tableau de bord de l'atelier : KPI + ses demandes. */
    public function mesTravaux(Request $request)
    {
        $atelier = $request->user();
        $statut = $request->query('statut');

        $travaux = Demande::where('atelier_id', $atelier->id)->with(['user', 'interventions'])
            ->when($statut, fn ($q) => $q->where('statut', $statut))
            ->latest()->paginate(9)->withQueryString();

        $mes = fn () => Demande::where('atelier_id', $atelier->id);
        $stats = [
            'disponibles' => Demande::disponibles()->count(),
            'en_cours' => $mes()->where('statut', Statut::EnCours->value)->count(),
            'terminees' => $mes()->where('statut', Statut::Terminee->value)->count(),
            'revenu' => (float) Intervention::where('atelier_id', $atelier->id)
                ->where('statut', Statut::Terminee->value)->sum('cout_estime'),
        ];

        return view('atelier.travaux.index', compact('travaux', 'stats', 'statut'));
    }

    public function show(Request $request, Demande $demande)
    {
        abort_unless($demande->estAssigneA($request->user()), 403);
        $demande->load('user', 'interventions.atelier');

        return view('atelier.travaux.show', ['demande' => $demande, 'intervention' => new Intervention()]);
    }

    public function liberer(Request $request, Demande $demande): RedirectResponse
    {
        abort_unless($demande->estAssigneA($request->user()), 403);

        if (! $demande->liberer()) {
            return back()->with('error', 'Des interventions ont déjà démarré : impossible de libérer cette demande.');
        }

        return redirect()->route('atelier.demandes.disponibles')->with('success', 'Demande libérée.');
    }
}
