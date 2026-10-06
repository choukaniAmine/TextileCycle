<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAtelierRequest;
use App\Http\Requests\UpdateAtelierRequest;
use App\Models\Atelier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AtelierController extends Controller
{
    /**
     * Liste des ateliers dans le back-office.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $ville = $request->input('ville');
        $statut = $request->input('statut');

        $query = Atelier::withCount('services');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($ville) {
            $query->where('ville', $ville);
        }

        if ($statut !== null && $statut !== '') {
            $query->where('est_actif', (bool)$statut);
        }

        $ateliers = $query->latest()->paginate(10)->withQueryString();
        $villes = Atelier::distinct()->orderBy('ville')->pluck('ville');

        return view('admin.ateliers.index', compact('ateliers', 'villes', 'search', 'ville', 'statut'));
    }

    /**
     * Formulaire de création d'un atelier.
     */
    public function create()
    {
        return view('admin.ateliers.create');
    }

    /**
     * Enregistrement d'un nouvel atelier.
     */
    public function store(StoreAtelierRequest $request)
    {
        $validated = $request->validated();
        $validated['est_actif'] = $request->has('est_actif');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('ateliers', 'public');
        }

        $atelier = Atelier::create($validated);

        return redirect()->route('admin.ateliers.index')
            ->with('success', "L'atelier « {$atelier->nom} » a été créé avec succès !");
    }

    /**
     * Affichage détaillé d'un atelier et de ses services.
     */
    public function show(Atelier $atelier)
    {
        $atelier->load('services');
        return view('admin.ateliers.show', compact('atelier'));
    }

    /**
     * Formulaire d'édition d'un atelier.
     */
    public function edit(Atelier $atelier)
    {
        return view('admin.ateliers.edit', compact('atelier'));
    }

    /**
     * Mise à jour d'un atelier.
     */
    public function update(UpdateAtelierRequest $request, Atelier $atelier)
    {
        $validated = $request->validated();
        $validated['est_actif'] = $request->has('est_actif');

        if ($request->hasFile('image')) {
            if ($atelier->image && Storage::disk('public')->exists($atelier->image)) {
                Storage::disk('public')->delete($atelier->image);
            }
            $validated['image'] = $request->file('image')->store('ateliers', 'public');
        }

        $atelier->update($validated);

        return redirect()->route('admin.ateliers.index')
            ->with('success', "L'atelier « {$atelier->nom} » a été mis à jour avec succès !");
    }

    /**
     * Suppression d'un atelier.
     */
    public function destroy(Atelier $atelier)
    {
        $nom = $atelier->nom;

        if ($atelier->image && Storage::disk('public')->exists($atelier->image)) {
            Storage::disk('public')->delete($atelier->image);
        }

        $atelier->delete();

        return redirect()->route('admin.ateliers.index')
            ->with('success', "L'atelier « {$nom} » a été supprimé.");
    }
}
