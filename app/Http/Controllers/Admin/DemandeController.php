<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Statut;
use App\Enums\TypeDemande;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Demande;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DemandeController extends Controller
{
    public function index(Request $request)
    {
        $demandes = Demande::with(['user', 'atelier'])->withCount('interventions')
            ->search($request->query('q'))
            ->when($request->query('statut'), fn ($q, $s) => $q->where('statut', $s))
            ->when($request->query('type'), fn ($q, $t) => $q->where('type', $t))
            ->latest()->paginate(10)->withQueryString();

        return view('admin.demandes.index', [
            'demandes' => $demandes, 'statuts' => Statut::cases(), 'types' => TypeDemande::cases(),
        ]);
    }

    public function show(Demande $demande)
    {
        $demande->load('user', 'atelier', 'interventions.atelier');

        return view('admin.demandes.show', compact('demande'));
    }

    public function edit(Demande $demande)
    {
        return view('admin.demandes.edit', [
            'demande' => $demande,
            'statuts' => Statut::cases(),
            'ateliers' => User::where('role', UserRole::Atelier)->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Demande $demande): RedirectResponse
    {
        $data = $request->validate([
            'statut' => ['required', Rule::enum(Statut::class)],
            'atelier_id' => ['nullable', Rule::exists('users', 'id')->where('role', 'atelier')],
            'urgent' => ['nullable', 'boolean'],
        ]);
        $data['urgent'] = $request->boolean('urgent');
        $demande->update($data);

        return redirect()->route('admin.demandes.show', $demande)->with('success', 'Demande mise à jour.');
    }

    public function destroy(Demande $demande): RedirectResponse
    {
        if ($demande->photo) {
            Storage::disk('public')->delete($demande->photo);
        }
        $demande->delete();

        return redirect()->route('admin.demandes.index')->with('success', 'Demande supprimée.');
    }
}
