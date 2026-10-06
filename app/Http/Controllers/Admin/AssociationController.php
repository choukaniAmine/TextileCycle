<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DonStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssociationRequest;
use App\Models\Association;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AssociationController extends Controller
{
    public function index(Request $request)
    {
        $associations = Association::search($request->query('q'))
            ->withCount('dons')
            ->latest()->paginate(10)->withQueryString();

        return view('admin.associations.index', compact('associations'));
    }

    public function create()
    {
        return view('admin.associations.create', [
            'association' => new Association(['is_active' => true]),
            'managers' => $this->managers(),
        ]);
    }

    public function store(AssociationRequest $request): RedirectResponse
    {
        Association::create($request->validated() + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('admin.associations.index')->with('success', 'Association créée.');
    }

    /** Détail + historique des dons reçus par l'association (filtrable par statut). */
    public function show(Request $request, Association $association)
    {
        $dons = $association->dons()
            ->with('donor')
            ->status($request->query('status'))
            ->latest('donated_at')->latest('id')
            ->paginate(10)->withQueryString();

        return view('admin.associations.show', [
            'association' => $association,
            'dons' => $dons,
            'statuses' => DonStatus::cases(),
            'totalPieces' => $association->dons()->where('status', DonStatus::Livre)->sum('quantity'),
        ]);
    }

    public function edit(Association $association)
    {
        return view('admin.associations.edit', ['association' => $association, 'managers' => $this->managers()]);
    }

    public function update(AssociationRequest $request, Association $association): RedirectResponse
    {
        $association->update($request->validated() + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('admin.associations.index')->with('success', 'Association mise à jour.');
    }

    /** Comptes au rôle « association » pouvant gérer une association. */
    private function managers()
    {
        return User::where('role', UserRole::Association)->orderBy('name')->get(['id', 'name', 'email']);
    }

    public function destroy(Association $association): RedirectResponse
    {
        $count = $association->dons()->count();
        $association->delete(); // les dons liés sont supprimés en cascade

        return redirect()->route('admin.associations.index')
            ->with('success', 'Association supprimée'.($count ? " (ainsi que ses {$count} don(s))." : '.'));
    }
}
