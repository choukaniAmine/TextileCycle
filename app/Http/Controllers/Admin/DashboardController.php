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
            'parRole' => $parRole,
            'derniers' => User::latest()->take(6)->get(),
        ]);
    }
}
