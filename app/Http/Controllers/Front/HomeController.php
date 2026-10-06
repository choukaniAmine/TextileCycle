<?php

namespace App\Http\Controllers\Front;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        return view('front.home', [
            'ateliers' => User::where('role', UserRole::Atelier)->where('is_active', true)->count(),
            'associations' => User::where('role', UserRole::Association)->where('is_active', true)->count(),
            'membres' => User::where('is_active', true)->count(),
        ]);
    }
}
