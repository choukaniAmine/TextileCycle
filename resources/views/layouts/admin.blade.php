<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tableau de bord') – {{ config('app.name') }} Admin</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    {{-- Template back office : AdminLTE 3.2 (Bootstrap 4) --}}
    <link rel="stylesheet" href="{{ asset('templates/backoffice/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/demandes.css') }}">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></li>
            <li class="nav-item d-none d-sm-inline-block"><a href="{{ route('home') }}" class="nav-link">Voir le site</a></li>
        </ul>
        <ul class="navbar-nav ml-auto align-items-center">
            <li class="nav-item d-flex align-items-center">
                @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}" class="img-circle elevation-1 mr-2" style="width:30px;height:30px;object-fit:cover;">
                @endif
                <span class="nav-link">{{ auth()->user()->name }}</span>
            </li>
            <li class="nav-item">
                <button type="button" class="btn btn-link nav-link text-danger font-weight-bold" data-toggle="modal" data-target="#adminLogoutModal">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </button>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="{{ route('admin.dashboard') }}" class="brand-link">
            <img src="{{ asset('templates/backoffice/img/AdminLTELogo.png') }}" alt="Logo" class="brand-image img-circle elevation-3" style="opacity:.8">
            <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i><p>Tableau de bord</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i><p>Utilisateurs</p></a>
                    </li>

                    {{-- Module 2 : Ateliers et Services --}}
                    <li class="nav-header text-uppercase text-xs text-muted">Module 2 — Ateliers</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.ateliers.index') }}" class="nav-link {{ request()->routeIs('admin.ateliers.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-store"></i><p>Gestion Ateliers</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.services.index') }}" class="nav-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tools"></i><p>Gestion Services</p></a>
                    </li>

                    {{-- Module 3 : Associations et dons --}}
                    <li class="nav-header text-uppercase text-xs text-muted">Module 3 — Dons</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.associations.index') }}" class="nav-link {{ request()->routeIs('admin.associations.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-hands-helping"></i><p>Associations</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.dons.index') }}" class="nav-link {{ request()->routeIs('admin.dons.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-gift"></i><p>Dons</p></a>
                    </li>

                    {{-- Module 4 : Demandes & Interventions --}}
                    <li class="nav-header text-uppercase text-xs text-muted">Module 4 — Demandes</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.demandes.index') }}" class="nav-link {{ request()->routeIs('admin.demandes.*', 'admin.interventions.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cut"></i><p>Demandes</p></a>
                    </li>
                    {{-- Module 1 : Vêtements --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.vetements.index') }}" class="nav-link {{ request()->routeIs('admin.vetements.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tshirt"></i><p>Vêtements</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tags"></i><p>Catégories</p></a>
                    </li>
                    <a href="{{ route('admin.vetements.statistiques') }}" class="btn btn-info">
    <i class="fas fa-chart-pie"></i> Statistiques
</a>

                    {{-- Prochains modules : Réparations, Dons… --}}
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid"><h1>@yield('title', 'Tableau de bord')</h1></div>
        </section>
        <section class="content">
            <div class="container-fluid">
                @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                @if (session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
                @yield('content')
            </div>
        </section>
    </div>

    <footer class="main-footer"><strong>{{ config('app.name') }}</strong> – Back office</footer>
</div>

<!-- Modal Confirmation Déconnexion AdminLTE -->
<div class="modal fade" id="adminLogoutModal" tabindex="-1" role="dialog" aria-labelledby="adminLogoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-weight-bold" id="adminLogoutModalLabel">
                    <i class="fas fa-sign-out-alt mr-2"></i> Confirmation de déconnexion
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-2">Êtes-vous certain de vouloir quitter l'espace d'administration <strong>{{ config('app.name') }}</strong> ?</p>
                <div class="alert alert-light border mb-0 text-muted small">
                    <i class="fas fa-user-shield text-info mr-1"></i> Connecté en tant que <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->email }}).
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary px-3" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Annuler
                </button>
                <form method="POST" action="{{ route('logout') }}" id="adminLogoutForm" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-danger font-weight-bold px-3" id="adminLogoutSubmitBtn">
                        <i class="fas fa-sign-out-alt mr-1"></i> Se déconnecter
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('templates/backoffice/js/adminlte.min.js') }}"></script>
<script>
    document.getElementById('adminLogoutForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('adminLogoutSubmitBtn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Déconnexion en cours...';
        }
    });
</script>
@stack('scripts')
</body>
</html>