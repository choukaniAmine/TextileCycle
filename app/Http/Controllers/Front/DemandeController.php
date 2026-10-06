<?php

namespace App\Http\Controllers\Front;

use App\Enums\Statut;
use App\Http\Controllers\Controller;
use App\Http\Requests\DemandeRequest;
use App\Models\Demande;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Enums\UserRole;
use App\Models\User;
use App\Notifications\DemandeNotification;
use Illuminate\Support\Facades\Notification;
class DemandeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $statut = $request->query('statut');

        $demandes = $user->demandes()->with('interventions')
            ->when($statut, fn ($q) => $q->where('statut', $statut))
            ->latest()->paginate(9)->withQueryString();

        $compteurs = $user->demandes()->pluck('statut')->countBy(fn ($s) => $s->value);

        return view('front.demandes.index', compact('demandes', 'compteurs', 'statut'));
    }

    public function create()
    {
        return view('front.demandes.create', ['demande' => new Demande()]);
    }

    public function store(DemandeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['urgent'] = $request->boolean('urgent');
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('demandes', 'public');
        }

        $demande = $request->user()->demandes()->create($data);
Notification::send(
    User::where('role', UserRole::Atelier)->where('is_active', true)->get(),
    new DemandeNotification($demande, "Nouvelle demande disponible : {$demande->titre}", '📥', route('atelier.demandes.disponibles', [], false))
);
        return redirect()->route('demandes.show', $demande)->with('success', 'Votre demande a bien été envoyée 🧵');
    }

    public function show(Demande $demande)
    {
        $this->authorizeOwner($demande);
        $demande->load('interventions.atelier', 'atelier');

        return view('front.demandes.show', compact('demande'));
    }

    public function edit(Demande $demande)
    {
        $this->authorizeOwner($demande);
        if (! $demande->peutEtreModifiee()) {
            return redirect()->route('demandes.show', $demande)->with('error', 'Cette demande est déjà prise en charge : elle ne peut plus être modifiée.');
        }

        return view('front.demandes.edit', compact('demande'));
    }

    public function update(DemandeRequest $request, Demande $demande): RedirectResponse
    {
        $this->authorizeOwner($demande);
        abort_unless($demande->peutEtreModifiee(), 403);

        $data = $request->validated();
        $data['urgent'] = $request->boolean('urgent');
        if ($request->hasFile('photo')) {
            if ($demande->photo) {
                Storage::disk('public')->delete($demande->photo);
            }
            $data['photo'] = $request->file('photo')->store('demandes', 'public');
        }
        $demande->update($data);

        return redirect()->route('demandes.show', $demande)->with('success', 'Demande mise à jour.');
    }

    public function cancel(Demande $demande): RedirectResponse
    {
        $this->authorizeOwner($demande);
        abort_unless($demande->peutEtreModifiee(), 403);
        $demande->update(['statut' => Statut::Annulee]);

        return redirect()->route('demandes.index')->with('success', 'Demande annulée.');
    }

    public function destroy(Demande $demande): RedirectResponse
    {
        $this->authorizeOwner($demande);
        abort_unless($demande->peutEtreSupprimee(), 403);
        if ($demande->photo) {
            Storage::disk('public')->delete($demande->photo);
        }
        $demande->delete();

        return redirect()->route('demandes.index')->with('success', 'Demande supprimée.');
    }

 private function authorizeOwner(Demande $demande): void
{
    abort_unless($demande->user_id === request()->user()->getAuthIdentifier(), 403);
}
}
