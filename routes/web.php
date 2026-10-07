<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\CategorieController;
use App\Http\Controllers\Admin\VetementController as AdminVetementController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Front;
use App\Http\Controllers\Front\DemandeDonController;
use App\Http\Controllers\Front\MesVetementsController;
use App\Http\Controllers\Front\VetementController as FrontVetementController;
use Illuminate\Support\Facades\Route;

// ---------- Front office ----------
Route::get('/', [Front\HomeController::class, 'index'])->name('home');

// Consultation publique des vêtements
Route::get('/vetements', [FrontVetementController::class, 'index'])->name('vetements.index');
Route::get('/vetements/{vetement}', [FrontVetementController::class, 'show'])->name('vetements.show');

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

// Espace particulier
Route::middleware(['auth', 'role:particulier'])->group(function () {
    Route::resource('mes-vetements', MesVetementsController::class)
        ->except('show')
        ->parameters(['mes-vetements' => 'vetement']);

    Route::post('/vetements/{vetement}/demander', [DemandeDonController::class, 'store'])->name('demandes.store');
    Route::get('/mes-demandes/recues', [DemandeDonController::class, 'recues'])->name('demandes.recues');
    Route::get('/mes-demandes/envoyees', [DemandeDonController::class, 'envoyees'])->name('demandes.envoyees');
    Route::patch('/demandes/{demande}/accepter', [DemandeDonController::class, 'accepter'])->name('demandes.accepter');
    Route::patch('/demandes/{demande}/refuser', [DemandeDonController::class, 'refuser'])->name('demandes.refuser');
    Route::patch('/demandes/{demande}/annuler', [DemandeDonController::class, 'annuler'])->name('demandes.annuler');
});

// ---------- Back office (admin uniquement) ----------
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');
    Route::patch('users/{user}/toggle', [Admin\UserController::class, 'toggle'])->name('users.toggle');
    Route::resource('users', Admin\UserController::class)->except('show');

    // Module 1 : Vêtements et catégories
    Route::resource('categories', CategorieController::class)
        ->except('show')
        ->parameters(['categories' => 'categorie']);
    Route::resource('vetements', AdminVetementController::class)->except('show');
});