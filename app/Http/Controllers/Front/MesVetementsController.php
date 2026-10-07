<?php

namespace App\Http\Controllers\Front;

use App\Enums\EtatVetement;
use App\Enums\StatutDemande;
use App\Enums\TypeVetement;
use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Vetement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MesVetementsController extends Controller
{
    public function index(Request $request)
    {
        $vetements = Vetement::with('categorie')
            ->where('user_id', $request->user()->id)
            ->withCount(['demandes as demandes_en_attente_count' => fn ($q) => $q->where('statut', StatutDemande::EnAttente)])
            ->latest()
            ->paginate(9);

        return view('front.mes-vetements.index', compact('vetements'));
    }

    public function create()
    {
        Gate::authorize('create', Vetement::class);
        return view('front.mes-vetements.create', $this->formData());
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Vetement::class);

        $data = $this->validated($request);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('vetements', 'public');
        }
        $data['user_id'] = $request->user()->id;

        Vetement::create($data);

        return redirect()->route('mes-vetements.index')->with('success', 'Vêtement ajouté.');
    }

    public function edit(Vetement $vetement)
    {
        Gate::authorize('update', $vetement);
        return view('front.mes-vetements.edit', ['vetement' => $vetement] + $this->formData());
    }

    public function update(Request $request, Vetement $vetement)
    {
        Gate::authorize('update', $vetement);

        $data = $this->validated($request);
        if ($request->hasFile('image')) {
            if ($vetement->image) {
                Storage::disk('public')->delete($vetement->image);
            }
            $data['image'] = $request->file('image')->store('vetements', 'public');
        }

        $vetement->update($data);

        return redirect()->route('mes-vetements.index')->with('success', 'Vêtement modifié.');
    }

    public function destroy(Vetement $vetement)
    {
        Gate::authorize('delete', $vetement);

        if ($vetement->image) {
            Storage::disk('public')->delete($vetement->image);
        }
        $vetement->delete();

        return redirect()->route('mes-vetements.index')->with('success', 'Vêtement supprimé.');
    }

    private function formData(): array
    {
        return [
            'categories' => Categorie::orderBy('nom')->get(),
            'etats' => EtatVetement::cases(),
            'types' => TypeVetement::cases(),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'taille' => ['required', 'string', 'max:10'],
            'etat' => ['required', Rule::enum(EtatVetement::class)],
            'type' => ['required', Rule::enum(TypeVetement::class)],
            'categorie_id' => ['required', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);
    }
}