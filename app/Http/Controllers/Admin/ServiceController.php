<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Atelier;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Liste des services dans le back-office.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $atelierId = $request->input('atelier_id');
        $typeService = $request->input('type_service');
        $disponible = $request->input('disponible');

        $query = Service::with('atelier');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($atelierId) {
            $query->where('atelier_id', $atelierId);
        }

        if ($typeService) {
            $query->where('type_service', $typeService);
        }

        if ($disponible !== null && $disponible !== '') {
            $query->where('disponible', (bool)$disponible);
        }

        $services = $query->latest()->paginate(10)->withQueryString();
        $ateliers = Atelier::orderBy('nom')->get();
        $typesServices = ['Reparation', 'Retouche', 'Transformation', 'Customisation', 'Upcycling', 'Autre'];

        return view('admin.services.index', compact('services', 'ateliers', 'typesServices', 'search', 'atelierId', 'typeService', 'disponible'));
    }

    /**
     * Formulaire d'ajout d'un service.
     */
    public function create(Request $request)
    {
        $ateliers = Atelier::orderBy('nom')->get();
        $typesServices = ['Reparation', 'Retouche', 'Transformation', 'Customisation', 'Upcycling', 'Autre'];
        $selectedAtelierId = $request->input('atelier_id');

        return view('admin.services.create', compact('ateliers', 'typesServices', 'selectedAtelierId'));
    }

    /**
     * Enregistrement d'un nouveau service.
     */
    public function store(StoreServiceRequest $request)
    {
        $validated = $request->validated();
        $validated['disponible'] = $request->has('disponible');

        $service = Service::create($validated);

        return redirect()->route('admin.services.index')
            ->with('success', "Le service « {$service->nom} » a été ajouté avec succès !");
    }

    /**
     * Affichage d'un service.
     */
    public function show(Service $service)
    {
        $service->load('atelier');
        return view('admin.services.show', compact('service'));
    }

    /**
     * Formulaire d'édition d'un service.
     */
    public function edit(Service $service)
    {
        $ateliers = Atelier::orderBy('nom')->get();
        $typesServices = ['Reparation', 'Retouche', 'Transformation', 'Customisation', 'Upcycling', 'Autre'];

        return view('admin.services.edit', compact('service', 'ateliers', 'typesServices'));
    }

    /**
     * Mise à jour d'un service.
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        $validated = $request->validated();
        $validated['disponible'] = $request->has('disponible');

        $service->update($validated);

        return redirect()->route('admin.services.index')
            ->with('success', "Le service « {$service->nom} » a été mis à jour avec succès !");
    }

    /**
     * Suppression d'un service.
     */
    public function destroy(Service $service)
    {
        $nom = $service->nom;
        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', "Le service « {$nom} » a été supprimé.");
    }
}
