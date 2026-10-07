<?php

namespace App\Http\Controllers\Front;

use App\Enums\EtatVetement;
use App\Enums\StatutDemande;
use App\Enums\StatutVetement;
use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Vetement;
use Illuminate\Http\Request;

class VetementController extends Controller
{
    public function index(Request $request)
    {
        $vetements = Vetement::with('categorie')
            ->where('statut', StatutVetement::Disponible)
            ->when($request->filled('categorie_id'), fn ($q) => $q->where('categorie_id', $request->categorie_id))
            ->when($request->filled('etat'), fn ($q) => $q->where('etat', $request->etat))
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('front.vetements.index', [
            'vetements' => $vetements,
            'categories' => Categorie::orderBy('nom')->get(),
            'etats' => EtatVetement::cases(),
        ]);
    }

    public function show(Request $request, Vetement $vetement)
    {
        $vetement->load(['categorie', 'user']);

        $demandeEnCours = $request->user()
            ? $vetement->demandes()
                ->where('demandeur_id', $request->user()->id)
                ->where('statut', StatutDemande::EnAttente)
                ->exists()
            : false;

        return view('front.vetements.show', compact('vetement', 'demandeEnCours'));
    }
}