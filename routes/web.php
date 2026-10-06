<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Atelier;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Front;
use Illuminate\Support\Facades\Route;

// ==========================================
// 1. FRONT OFFICE — ROUTES PUBLIQUES
// ==========================================
Route::get('/', [Front\HomeController::class, 'index'])->name('home');

// Module 2 — Ateliers & Services (Public)
Route::get('/ateliers', [Front\AtelierController::class, 'index'])->name('ateliers.index');
Route::get('/ateliers/{atelier}', [Front\AtelierController::class, 'show'])->name('ateliers.show');

// Module 3 — Associations (Public)
Route::get('/associations', [Front\AssociationController::class, 'index'])->name('associations.index');
Route::get('/associations/{association}', [Front\AssociationController::class, 'show'])->name('associations.show');

// Auth — Invités uniquement
Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login']);
    Route::get('/inscription', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/inscription', [AuthController::class, 'register']);
});

// ==========================================
// 2. FRONT OFFICE — UTILISATEUR CONNECTÉ
// ==========================================
Route::middleware('auth')->group(function () {
    Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profil', [Front\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [Front\ProfileController::class, 'update'])->name('profile.update');

    // Notifications (tous les rôles)
    Route::get('notifications', [Front\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/{id}', [Front\NotificationController::class, 'read'])->name('notifications.read');
    Route::post('notifications/lues', [Front\NotificationController::class, 'readAll'])->name('notifications.readAll');

    // Module 2 — Avis sur les ateliers
    Route::post('/ateliers/{atelier}/avis', [Front\AvisController::class, 'store'])->name('ateliers.avis.store');
    Route::delete('/ateliers/{atelier}/avis', [Front\AvisController::class, 'destroyForAtelier'])->name('ateliers.avis.destroy');
    Route::delete('/avis/{avis}', [Front\AvisController::class, 'destroy'])->name('avis.destroy');

    // Module 3 — Dons (donateur connecté)
    Route::get('/mes-dons', [Front\DonController::class, 'index'])->name('dons.index');
    Route::get('/mes-dons/nouveau', [Front\DonController::class, 'create'])->name('dons.create');
    Route::post('/mes-dons', [Front\DonController::class, 'store'])->name('dons.store');
    Route::get('/mes-dons/{don}', [Front\DonController::class, 'show'])->name('dons.show');
    Route::delete('/mes-dons/{don}', [Front\DonController::class, 'destroy'])->name('dons.destroy');

    // Module 4 — Demandes (particulier / association)
    Route::middleware('role:particulier,association')->group(function () {
        Route::resource('mes-demandes', Front\DemandeController::class)
            ->parameters(['mes-demandes' => 'demande'])->names('demandes');
        Route::patch('mes-demandes/{demande}/annuler', [Front\DemandeController::class, 'cancel'])->name('demandes.cancel');
    });

    // Module 4 — Espace Atelier
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

// Module 3 — Espace Association : traite les dons reçus
Route::middleware(['auth', 'role:association'])->prefix('espace-association')->name('espace.')->group(function () {
    Route::get('/dons', [Front\ReceivedDonController::class, 'index'])->name('dons.index');
    Route::patch('/dons/{don}/statut', [Front\ReceivedDonController::class, 'updateStatus'])->name('dons.status');
});

// ==========================================
// 3. BACK OFFICE — ADMINISTRATEUR
// ==========================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');

    // Module commun — Gestion des utilisateurs
    Route::patch('users/{user}/toggle', [Admin\UserController::class, 'toggle'])->name('users.toggle');
    Route::resource('users', Admin\UserController::class)->except('show');

    // Module 2 — Ateliers & Services
    Route::resource('ateliers', Admin\AtelierController::class);
    Route::resource('services', Admin\ServiceController::class);

    // Module 3 — Associations & Dons
    Route::resource('associations', Admin\AssociationController::class);
    Route::resource('dons', Admin\DonController::class);

    // Module 4 — Demandes & Interventions
    Route::resource('demandes', Admin\DemandeController::class)->except(['create', 'store']);
    Route::resource('demandes.interventions', Admin\InterventionController::class)
        ->shallow()->except(['index', 'show']);
    Route::patch('interventions/{intervention}/avancer', [Admin\InterventionController::class, 'advance'])
        ->name('interventions.advance');
});
