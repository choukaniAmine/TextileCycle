<?php

namespace App\Http\Controllers\Front;

use App\Enums\DonStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\DonRequest;
use App\Models\Association;
use App\Models\Don;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DonController extends Controller
{
    /** « Mes dons » : historique personnel, filtrable par statut. */
    public function index(Request $request)
    {
        $dons = $request->user()->dons()
            ->with('association')
            ->status($request->query('status'))
            ->latest('donated_at')->latest('id')
            ->paginate(8)->withQueryString();

        return view('front.dons.index', ['dons' => $dons, 'statuses' => DonStatus::cases()]);
    }

    public function create(Request $request)
    {
        return view('front.dons.create', [
            'don' => new Don(['association_id' => $request->query('association'), 'donated_at' => now(), 'quantity' => 1]),
            'associations' => Association::active()->orderBy('name')->get(['id', 'name', 'city']),
        ]);
    }

    public function store(DonRequest $request): RedirectResponse
    {
        $don = $request->user()->dons()->create($request->validated() + ['status' => DonStatus::EnAttente]);

        return redirect()->route('dons.show', $don)->with('success', 'Merci ! Votre don est enregistré et en attente de validation.');
    }

    public function show(Request $request, Don $don)
    {
        $this->authorizeOwner($request, $don);
        $don->load(['association', 'statusLogs']);

        return view('front.dons.show', compact('don'));
    }

    /** Le donateur peut annuler son don tant qu'il est « En attente ». */
    public function destroy(Request $request, Don $don): RedirectResponse
    {
        $this->authorizeOwner($request, $don);

        if ($don->status !== DonStatus::EnAttente) {
            return back()->with('error', 'Seuls les dons en attente peuvent être annulés.');
        }
        $don->delete();

        return redirect()->route('dons.index')->with('success', 'Don annulé.');
    }

    private function authorizeOwner(Request $request, Don $don): void
    {
        abort_unless($don->user_id === $request->user()->id, 403);
    }
}
