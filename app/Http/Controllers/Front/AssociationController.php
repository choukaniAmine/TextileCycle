<?php

namespace App\Http\Controllers\Front;

use App\Enums\DonStatus;
use App\Http\Controllers\Controller;
use App\Models\Association;
use Illuminate\Http\Request;

class AssociationController extends Controller
{
    public function index(Request $request)
    {
        $associations = Association::active()
            ->search($request->query('q'))
            ->withCount(['dons as livres_count' => fn ($q) => $q->where('status', DonStatus::Livre)])
            ->orderBy('name')->paginate(9)->withQueryString();

        return view('front.associations.index', compact('associations'));
    }

    public function show(Association $association)
    {
        abort_unless($association->is_active, 404);

        return view('front.associations.show', [
            'association' => $association,
            'piecesLivrees' => $association->dons()->where('status', DonStatus::Livre)->sum('quantity'),
        ]);
    }
}
