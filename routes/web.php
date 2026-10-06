<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Front;
use Illuminate\Support\Facades\Route;

// ---------- Front office ----------
Route::get('/', [Front\HomeController::class, 'index'])->name('home');

// Module 2 — Ateliers & Services (Front)
Route::get('/ateliers', [Front\AtelierController::class, 'index'])->name('ateliers.index');
Route::get('/ateliers/{atelier}', [Front\AtelierController::class, 'show'])->name('ateliers.show');

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login']);
    Route::get('/inscription', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/inscription', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profil', [Front\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [Front\ProfileController::class, 'update'])->name('profile.update');
});

// ---------- Back office (admin uniquement) ----------
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');
    Route::patch('users/{user}/toggle', [Admin\UserController::class, 'toggle'])->name('users.toggle');
    Route::resource('users', Admin\UserController::class)->except('show');

    // Module 2 — CRUD Ateliers & Services (Admin)
    Route::resource('ateliers', Admin\AtelierController::class);
    Route::resource('services', Admin\ServiceController::class);
});
