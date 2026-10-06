<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Atelier;
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

// ---------- Utilisateur connecté (tous les rôles) ----------
Route::middleware('auth')->group(function () {
    Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profil', [Front\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [Front\ProfileController::class, 'update'])->name('profile.update');

    // Notifications : accessibles à tous les rôles
    Route::get('notifications', [Front\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/{id}', [Front\NotificationController::class, 'read'])->name('notifications.read');
    Route::post('notifications/lues', [Front\NotificationController::class, 'readAll'])->name('notifications.readAll');

    // Client : particulier / association
    Route::middleware('role:particulier,association')->group(function () {
        Route::resource('mes-demandes', Front\DemandeController::class)
            ->parameters(['mes-demandes' => 'demande'])->names('demandes');
        Route::patch('mes-demandes/{demande}/annuler', [Front\DemandeController::class, 'cancel'])->name('demandes.cancel');
    }); // <-- le groupe client se ferme ICI

    // Atelier : en dehors du groupe client
    Route::prefix('atelier')->name('atelier.')->middleware('role:atelier')->group(function () {
        Route::get('demandes', [Atelier\DemandeController::class, 'disponibles'])->name('demandes.disponibles');
        Route::post('demandes/{demande}/prendre', [Atelier\DemandeController::class, 'prendre'])->name('demandes.prendre');
        Route::get('travaux', [Atelier\DemandeController::class, 'mesTravaux'])->name('travaux.index');
        Route::get('travaux/{demande}', [Atelier\DemandeController::class, 'show'])->name('travaux.show');
        Route::post('travaux/{demande}/liberer', [Atelier\DemandeController::class, 'liberer'])->name('travaux.liberer');
        Route::post('travaux/{demande}/interventions', [Atelier\InterventionController::class, 'store'])->name('interventions.store');
        Route::get('interventions/{intervention}/edit', [Atelier\InterventionController::class, 'edit'])->name('interventions.edit');
        Route::put('interventions/{intervention}', [Atelier\InterventionController::class, 'update'])->name('interventions.update');
        Route::patch('interventions/{intervention}/avancer', [Atelier\InterventionController::class, 'advance'])->name('interventions.advance');
        Route::delete('interventions/{intervention}', [Atelier\InterventionController::class, 'destroy'])->name('interventions.destroy');
    });
});

// ---------- Back office (admin uniquement) ----------
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');
    Route::patch('users/{user}/toggle', [Admin\UserController::class, 'toggle'])->name('users.toggle');
    Route::resource('users', Admin\UserController::class)->except('show');

    Route::resource('demandes', Admin\DemandeController::class)->except(['create', 'store']);
    Route::resource('demandes.interventions', Admin\InterventionController::class)
        ->shallow()->except(['index', 'show']);
    Route::patch('interventions/{intervention}/avancer', [Admin\InterventionController::class, 'advance'])
        ->name('interventions.advance');
});