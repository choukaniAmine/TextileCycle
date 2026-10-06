<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Atelier;
use App\Models\Service;
use Illuminate\Http\Request;

class AtelierController extends Controller
{
    /**
     * Catalogue public des ateliers avec recherche et filtres.
     * Supporte les requêtes AJAX (X-Requested-With: XMLHttpRequest).
     */
    public function index(Request $request)
    {
        $search      = $request->input('search');
        $ville       = $request->input('ville');
        $typeService = $request->input('type_service');

        $query = Atelier::actif()
            ->with(['services' => fn($q) => $q->where('disponible', true)->latest()])
            ->withCount(['services' => fn($q) => $q->where('disponible', true)]);

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
                $q->where('type_service', $typeService)->where('disponible', true);
            });
        }

        $ateliers     = $query->latest()->paginate(6)->withQueryString();
        $villes       = Atelier::actif()->distinct()->orderBy('ville')->pluck('ville');
        $typesServices = ['Reparation', 'Retouche', 'Transformation', 'Customisation', 'Upcycling'];

        // Statistiques globales pour la stats bar
        $totalAteliers = Atelier::actif()->count();
        $totalServices = Service::where('disponible', true)->count();
        $totalVilles   = Atelier::actif()->distinct('ville')->count('ville');

        // Requête AJAX → retourner JSON (HTML partiel + total + pagination)
        if ($request->ajax()) {
            $html = '';
            foreach ($ateliers as $atelier) {
                $html .= view('front.ateliers._card', compact('atelier'))->render();
            }
            if ($ateliers->isEmpty()) {
                $html = view('front.ateliers._empty')->render();
            }

            return response()->json([
                'html'       => $html,
                'total'      => $ateliers->total(),
                'pagination' => $ateliers->links('pagination::bootstrap-5')->toHtml(),
            ]);
        }

        return view('front.ateliers.index', compact(
            'ateliers', 'villes', 'typesServices',
            'search', 'ville', 'typeService',
            'totalAteliers', 'totalServices', 'totalVilles'
        ));
    }

    /**
     * Détails d'un atelier et ses prestations.
     */
    public function show(Atelier $atelier)
    {
        $atelier->load(['services' => fn($q) => $q->where('disponible', true)->latest()]);

        // Ateliers similaires (même ville, différent)
        $similaires = Atelier::actif()
            ->where('ville', $atelier->ville)
            ->where('id', '!=', $atelier->id)
            ->withCount(['services' => fn($q) => $q->where('disponible', true)])
            ->limit(3)
            ->get();

        return view('front.ateliers.show', compact('atelier', 'similaires'));
    }
}
