<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DonStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\DonRequest;
use App\Models\Association;
use App\Models\Don;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DonController extends Controller
{
    /** Historique de tous les dons, filtrable par statut et par association. */
    public function index(Request $request)
    {
        $dons = Don::with(['association', 'donor'])
            ->status($request->query('status'))
            ->when($request->query('association'), fn ($q, $id) => $q->where('association_id', $id))
            ->search($request->query('q'))
            ->latest('donated_at')->latest('id')
            ->paginate(10)->withQueryString();

        // Compteurs par statut affichés au-dessus du tableau.
        $counts = Don::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.dons.index', [
            'dons' => $dons,
            'statuses' => DonStatus::cases(),
            'associations' => Association::orderBy('name')->get(['id', 'name']),
            'counts' => $counts,
        ]);
    }

    public function create(Request $request)
    {
        return view('admin.dons.create', $this->formData(new Don([
            'association_id' => $request->query('association'),
            'status' => DonStatus::EnAttente,
            'donated_at' => now(),
            'quantity' => 1,
        ])));
    }

    public function store(DonRequest $request): RedirectResponse
    {
        Don::create($request->validated());

        return redirect()->route('admin.dons.index')->with('success', 'Don enregistré.');
    }

    public function show(Don $don)
    {
        $don->load(['association.manager', 'donor', 'statusLogs.author']);

        return view('admin.dons.show', ['don' => $don]);
    }

    public function edit(Don $don)
    {
        return view('admin.dons.edit', $this->formData($don));
    }

    public function update(DonRequest $request, Don $don): RedirectResponse
    {
        $don->update($request->validated());

        return redirect()->route('admin.dons.show', $don)->with('success', 'Don mis à jour.');
    }

    public function destroy(Don $don): RedirectResponse
    {
        $don->delete();

        return redirect()->route('admin.dons.index')->with('success', 'Don supprimé.');
    }

    private function formData(Don $don): array
    {
        return [
            'don' => $don,
            'associations' => Association::orderBy('name')->get(['id', 'name']),
            'donors' => User::orderBy('name')->get(['id', 'name', 'email']),
        ];
    }
}
