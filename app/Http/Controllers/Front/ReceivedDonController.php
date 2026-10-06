<?php

namespace App\Http\Controllers\Front;

use App\Enums\DonStatus;
use App\Http\Controllers\Controller;
use App\Models\Don;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Espace association : l'association accepte, refuse et suit les dons qu'elle reçoit. */
class ReceivedDonController extends Controller
{
    public function index(Request $request)
    {
        $association = $request->user()->managedAssociation;

        if (! $association) {
            return view('front.espace.dons', ['association' => null]);
        }

        $dons = $association->dons()
            ->with('donor')
            ->status($request->query('status'))
            ->latest('donated_at')->latest('id')
            ->paginate(10)->withQueryString();

        $counts = $association->dons()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('front.espace.dons', [
            'association' => $association,
            'dons' => $dons,
            'statuses' => DonStatus::cases(),
            'counts' => $counts,
        ]);
    }

    public function updateStatus(Request $request, Don $don): RedirectResponse
    {
        $association = $request->user()->managedAssociation;

        // Une association ne traite que ses propres dons.
        abort_unless($association && $don->association_id === $association->id, 403);

        $data = $request->validate(['status' => ['required', Rule::enum(DonStatus::class)]]);
        $next = DonStatus::from($data['status']);

        if (! $don->status->canBecome($next)) {
            return back()->with('error', "Passage impossible de « {$don->status->label()} » à « {$next->label()} ».");
        }

        $don->update(['status' => $next]);

        return back()->with('success', "Don {$don->reference} : {$next->label()}.");
    }
}
