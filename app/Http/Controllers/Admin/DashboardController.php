<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $parRole = collect(UserRole::cases())->mapWithKeys(
            fn (UserRole $r) => [$r->value => User::where('role', $r)->count()]
        );

        return view('admin.dashboard', [
            'total' => User::count(),
            'actifs' => User::where('is_active', true)->count(),
            'totalAteliers' => \App\Models\Atelier::count(),
            'totalServices' => \App\Models\Service::count(),
            'parRole' => $parRole,
            'derniers' => User::latest()->take(6)->get(),
            'demandes' => \App\Models\Demande::count(),
'demandesEnCours' => \App\Models\Demande::where('statut', 'en_cours')->count(),
        ]);
    }
}
