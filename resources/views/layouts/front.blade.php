<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', config('app.name')) – Seconde vie pour vos vêtements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet">
    {{-- Template front office : Start Bootstrap "Landing Page" (inclut Bootstrap 5) --}}
    <link href="{{ asset('templates/frontoffice/css/styles.css') }}" rel="stylesheet">
    <link href="{{ asset('css/demandes.css') }}" rel="stylesheet">
    <link href="{{ asset('css/atelier.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light static-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}"><i class="bi bi-recycle text-success"></i> {{ config('app.name') }}</a>

        <div class="navbar-nav me-auto ms-3 d-none d-md-flex gap-2">
            <a class="nav-link {{ request()->routeIs('ateliers.*') ? 'active fw-bold text-success' : '' }}" href="{{ route('ateliers.index') }}">
                <i class="bi bi-scissors me-1"></i> Ateliers &amp; Services
            </a>
            <a class="nav-link {{ request()->routeIs('associations.*') ? 'active fw-bold text-success' : '' }}" href="{{ route('associations.index') }}">
                <i class="bi bi-hands-helping me-1"></i> Associations
            </a>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a class="btn btn-link btn-sm text-decoration-none d-md-none" href="{{ route('ateliers.index') }}">Ateliers</a>
            <a class="btn btn-link btn-sm text-decoration-none d-md-none" href="{{ route('associations.index') }}">Associations</a>

        {{-- Liens de navigation --}}
        <ul class="navbar-nav flex-row flex-wrap gap-3 me-auto ms-lg-4">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('vetements.*') ? 'active fw-bold' : '' }}"
                   href="{{ route('vetements.index') }}">Vêtements</a>
            </li>

            @auth
                @if(auth()->user()->role === \App\Enums\UserRole::Particulier)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('mes-vetements.*') ? 'active fw-bold' : '' }}"
                           href="{{ route('mes-vetements.index') }}">Mes vêtements</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('demandes.*') ? 'active fw-bold' : '' }}"
                           href="{{ route('demandes.recues') }}">Demandes reçues</a>
                    </li>
                @endif
            @endauth
        </ul>

        <div class="d-flex align-items-center gap-2">
            @auth
                @php($me = auth()->user())
                @php($nbNotifs = $me->unreadNotifications()->count())

                <span class="me-2 d-none d-lg-inline text-muted">{{ $me->name }}
                    <span class="badge bg-{{ $me->role->badge() }}">{{ $me->role->label() }}</span></span>

                @if ($me->isAdmin())
                    <a class="btn btn-outline-dark btn-sm" href="{{ route('admin.dashboard') }}">Back office</a>
                @endif

                {{-- Module 4 : demandes --}}
                @if (in_array($me->role->value, ['particulier', 'association']))
                    <a class="btn btn-success btn-sm" href="{{ route('demandes.index') }}">🧵 Mes demandes</a>
                @endif
                @if ($me->role->value === 'atelier')
                    <a class="btn btn-success btn-sm" href="{{ route('atelier.demandes.disponibles') }}">📥 Demandes</a>
                    <a class="btn btn-outline-success btn-sm" href="{{ route('atelier.travaux.index') }}">🧵 Mes travaux</a>
                @endif

                {{-- Module 3 : dons --}}
                @if ($me->role === \App\Enums\UserRole::Association)
                    <a class="btn btn-link btn-sm text-decoration-none" href="{{ route('espace.dons.index') }}">Dons reçus</a>
                @else
                    <a class="btn btn-link btn-sm text-decoration-none" href="{{ route('dons.index') }}">Mes dons</a>
                @endif

                <a class="btn btn-light btn-sm" href="{{ route('notifications.index') }}">🔔 @if ($nbNotifs)<span class="badge bg-danger rounded-pill">{{ $nbNotifs }}</span>@endif</a>
                <a class="btn btn-outline-primary btn-sm" href="{{ route('profile.edit') }}">Mon profil</a>

                <form method="POST" action="{{ route('logout') }}" class="m-0">@csrf
                    <button class="btn btn-primary btn-sm">Déconnexion</button>
                </form>
            @else
                <a class="btn btn-outline-primary btn-sm" href="{{ route('login') }}">Connexion</a>
                <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Inscription</a>
            @endauth
        </div>
    </div>
</nav>

@if (session('success') || session('error'))
    <div class="container mt-3">
        <div class="alert alert-{{ session('success') ? 'success' : 'danger' }} mb-0">{{ session('success') ?? session('error') }}</div>
    </div>
@endif

@yield('content')

<footer class="footer bg-light mt-auto">
    <div class="container text-center small text-muted">
        &copy; {{ date('Y') }} {{ config('app.name') }} – Économie circulaire &amp; consommation textile responsable.
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('templates/frontoffice/js/scripts.js') }}"></script>
@stack('scripts')
</body>
</html>