<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EtatVetement;
use App\Enums\TypeVetement;
use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Vetement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VetementController extends Controller
{
    public function index(Request $request)
    {
        $vetements = Vetement::with('categorie')
            ->when($request->filled('categorie_id'), fn ($q) => $q->where('categorie_id', $request->categorie_id))
            ->when($request->filled('etat'), fn ($q) => $q->where('etat', $request->etat))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.vetements.index', [
            'vetements' => $vetements,
            'categories' => Categorie::orderBy('nom')->get(),
            'etats' => EtatVetement::cases(),
        ]);
    }

    public function create()
    {
        return view('admin.vetements.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('vetements', 'public');
        }
        $data['user_id'] = $request->user()->id;

        Vetement::create($data);
        return redirect()->route('admin.vetements.index')->with('success', 'Vêtement ajouté.');
    }

    public function edit(Vetement $vetement)
    {
        return view('admin.vetements.edit', ['vetement' => $vetement] + $this->formData());
    }

    public function update(Request $request, Vetement $vetement)
    {
        $data = $this->validated($request);
        if ($request->hasFile('image')) {
            if ($vetement->image) {
                Storage::disk('public')->delete($vetement->image);
            }
            $data['image'] = $request->file('image')->store('vetements', 'public');
        }

        $vetement->update($data);
        return redirect()->route('admin.vetements.index')->with('success', 'Vêtement modifié.');
    }

    public function destroy(Vetement $vetement)
    {
        if ($vetement->image) {
            Storage::disk('public')->delete($vetement->image);
        }
        $vetement->delete();
        return redirect()->route('admin.vetements.index')->with('success', 'Vêtement supprimé.');
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