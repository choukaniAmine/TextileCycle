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
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></li>
            <li class="nav-item d-none d-sm-inline-block"><a href="{{ route('home') }}" class="nav-link">Voir le site</a></li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><span class="nav-link">{{ auth()->user()->name }}</span></li>
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}" class="m-0">@csrf
                    <button class="btn btn-link nav-link"><i class="fas fa-sign-out-alt"></i> Déconnexion</button>
                </form>
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

                    {{-- Module 1 : Vêtements --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.vetements.index') }}" class="nav-link {{ request()->routeIs('admin.vetements.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tshirt"></i><p>Vêtements</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tags"></i><p>Catégories</p></a>
                    </li>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('templates/backoffice/js/adminlte.min.js') }}"></script>
@stack('scripts')
</body>
</html>