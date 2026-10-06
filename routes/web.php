<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Front;
use Illuminate\Support\Facades\Route;

// ---------- Front office ----------
Route::get('/', [Front\HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login']);
    Route::get('/inscription', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/inscription', [AuthController::class, 'register']);
});

// Module 3 – associations (publiques)
Route::get('/associations', [Front\AssociationController::class, 'index'])->name('associations.index');
Route::get('/associations/{association}', [Front\AssociationController::class, 'show'])->name('associations.show');

Route::middleware('auth')->group(function () {
    Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profil', [Front\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [Front\ProfileController::class, 'update'])->name('profile.update');

    // Module 3 – dons du connecté
    Route::get('/mes-dons', [Front\DonController::class, 'index'])->name('dons.index');
    Route::get('/mes-dons/nouveau', [Front\DonController::class, 'create'])->name('dons.create');
    Route::post('/mes-dons', [Front\DonController::class, 'store'])->name('dons.store');
    Route::get('/mes-dons/{don}', [Front\DonController::class, 'show'])->name('dons.show');
    Route::delete('/mes-dons/{don}', [Front\DonController::class, 'destroy'])->name('dons.destroy');
});

// Module 3 – espace association : traite les dons reçus (le statut ne se change qu'ici)
Route::middleware(['auth', 'role:association'])->prefix('espace-association')->name('espace.')->group(function () {
    Route::get('/dons', [Front\ReceivedDonController::class, 'index'])->name('dons.index');
    Route::patch('/dons/{don}/statut', [Front\ReceivedDonController::class, 'updateStatus'])->name('dons.status');
});

// ---------- Back office (admin uniquement) ----------
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');
    Route::patch('users/{user}/toggle', [Admin\UserController::class, 'toggle'])->name('users.toggle');
    Route::resource('users', Admin\UserController::class)->except('show');

    // Module 3 – associations et dons
    Route::resource('associations', Admin\AssociationController::class);
    Route::resource('dons', Admin\DonController::class);
});
