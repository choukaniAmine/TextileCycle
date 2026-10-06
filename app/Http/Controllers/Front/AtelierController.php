<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Atelier;
use Illuminate\Http\Request;

class AtelierController extends Controller
{
    /**
     * Catalogue public des ateliers avec recherche et filtres.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $ville = $request->input('ville');
        $typeService = $request->input('type_service');

        $query = Atelier::actif()->withCount(['services' => function ($q) {
            $q->where('disponible', true);
        }]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('adresse', 'like', "%{$search}%");
            });
        }

        if ($ville) {
            $query->where('ville', $ville);
        }

        if ($typeService) {
            $query->whereHas('services', function ($q) use ($typeService) {
                $q->where('type_service', $typeService)
                  ->where('disponible', true);
            });
        }

        $ateliers = $query->latest()->paginate(6)->withQueryString();
        $villes = Atelier::distinct()->orderBy('ville')->pluck('ville');
        $typesServices = ['Reparation', 'Retouche', 'Transformation', 'Customisation', 'Upcycling'];

        return view('front.ateliers.index', compact('ateliers', 'villes', 'typesServices', 'search', 'ville', 'typeService'));
    }

    /**
     * Détails d'un atelier et ses prestations.
     */
    public function show(Atelier $atelier)
    {
        $atelier->load(['services' => function ($q) {
            $q->where('disponible', true)->latest();
        }]);

        return view('front.ateliers.show', compact('atelier'));
    }
}
