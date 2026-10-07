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
    {{-- Thème ReFashion (après le template, avant les CSS des modules) --}}
    <link href="{{ asset('css/refashion.css') }}" rel="stylesheet">
    <link href="{{ asset('css/demandes.css') }}" rel="stylesheet">
    <link href="{{ asset('css/atelier.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-xl rf-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand rf-brand" href="{{ route('home') }}">
            <span class="rf-logo"><i class="bi bi-recycle"></i></span> {{ config('app.name') }}
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#rfNav" aria-controls="rfNav" aria-expanded="false" aria-label="Menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="rfNav">

            {{-- ============ Navigation principale ============ --}}
            <ul class="navbar-nav me-auto ms-xl-4 gap-xl-1 mt-3 mt-xl-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('vetements.*') ? 'active' : '' }}"
                       href="{{ route('vetements.index') }}"><i class="bi bi-bag-heart"></i> Vêtements</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('ateliers.*') ? 'active' : '' }}"
                       href="{{ route('ateliers.index') }}"><i class="bi bi-scissors"></i> Ateliers &amp; Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('associations.*') ? 'active' : '' }}"
                       href="{{ route('associations.index') }}"><i class="bi bi-people"></i> Associations</a>
                </li>

                @auth
                    {{-- Module 1 : espace particulier --}}
                    @if(auth()->user()->role === \App\Enums\UserRole::Particulier)
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('mes-vetements.*', 'demandes-don.*') ? 'active' : '' }}"
                               href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-handbag"></i> Mon dressing
                            </a>
                            <ul class="dropdown-menu rf-dropdown">
                                <li><a class="dropdown-item {{ request()->routeIs('mes-vetements.*') ? 'active' : '' }}"
                                       href="{{ route('mes-vetements.index') }}"><i class="bi bi-grid"></i> Mes vêtements</a></li>
                                <li><a class="dropdown-item {{ request()->routeIs('demandes-don.recues') ? 'active' : '' }}"
                                       href="{{ route('demandes-don.recues') }}"><i class="bi bi-inbox"></i> Demandes de dons reçues</a></li>
                                <li><a class="dropdown-item {{ request()->routeIs('demandes-don.envoyees') ? 'active' : '' }}"
                                       href="{{ route('demandes-don.envoyees') }}"><i class="bi bi-gift"></i> Mes demandes de dons</a></li>
                            </ul>
                        </li>
                    @endif

                    {{-- Module 4 : espace atelier --}}
                    @if(auth()->user()->role->value === 'atelier')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('atelier.demandes.*') ? 'active' : '' }}"
                               href="{{ route('atelier.demandes.disponibles') }}"><i class="bi bi-inbox"></i> Demandes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('atelier.travaux.*', 'atelier.interventions.*') ? 'active' : '' }}"
                               href="{{ route('atelier.travaux.index') }}"><i class="bi bi-tools"></i> Mes travaux</a>
                        </li>
                    @endif
                @endauth
            </ul>

            {{-- ============ Zone compte ============ --}}
            <div class="rf-actions mt-3 mt-xl-0">
                @auth
                    @php($me = auth()->user())
                    @php($nbNotifs = $me->unreadNotifications()->count())

                    {{-- Notifications --}}
                    <a class="rf-icon-btn" href="{{ route('notifications.index') }}" title="Notifications">
                        <i class="bi bi-bell"></i>
                        @if ($nbNotifs)<span class="rf-dot">{{ $nbNotifs > 9 ? '9+' : $nbNotifs }}</span>@endif
                    </a>

                    {{-- Menu utilisateur --}}
                    <div class="dropdown">
                        <a class="rf-user-toggle dropdown-toggle" href="#" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="rf-avatar">{{ mb_strtoupper(mb_substr($me->name, 0, 1)) }}</span>
                            <span class="rf-user-name">{{ \Illuminate\Support\Str::before($me->name, ' ') }}</span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end rf-dropdown rf-user-menu">
                            <li class="rf-dropdown-head">
                                <strong>{{ $me->name }}</strong>
                                <small class="d-block text-muted text-truncate">{{ $me->email }}</small>
                                <span class="badge bg-{{ $me->role->badge() }} mt-2">{{ $me->role->label() }}</span>
                            </li>
                            <li><hr class="dropdown-divider"></li>

                            <li><a class="dropdown-item {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                                   href="{{ route('profile.edit') }}"><i class="bi bi-person-circle"></i> Mon profil</a></li>

                            {{-- Module 4 : demandes de réparation / transformation --}}
                            @if (in_array($me->role->value, ['particulier', 'association']))
                                <li><a class="dropdown-item {{ request()->routeIs('demandes.*') ? 'active' : '' }}"
                                       href="{{ route('demandes.index') }}"><i class="bi bi-tools"></i> Mes demandes</a></li>
                            @endif

                            {{-- Module 3 : dons --}}
                            @if ($me->role === \App\Enums\UserRole::Association)
                                <li><a class="dropdown-item {{ request()->routeIs('espace.dons.*') ? 'active' : '' }}"
                                       href="{{ route('espace.dons.index') }}"><i class="bi bi-inbox"></i> Dons reçus</a></li>
                            @else
                                <li><a class="dropdown-item {{ request()->routeIs('dons.*') ? 'active' : '' }}"
                                       href="{{ route('dons.index') }}"><i class="bi bi-gift"></i> Mes dons</a></li>
                            @endif

                            @if ($me->isAdmin())
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-speedometer2"></i> Back office</a></li>
                            @endif

                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">@csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a class="btn btn-outline-primary btn-sm" href="{{ route('login') }}">Connexion</a>
                    <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Inscription</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

@if (session('success') || session('error'))
    <div class="container mt-3">
        <div class="alert rf-flash alert-{{ session('success') ? 'success' : 'danger' }} mb-0">
            <i class="bi {{ session('success') ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }} me-2"></i>
            {{ session('success') ?? session('error') }}
        </div>
    </div>
@endif

@yield('content')

<footer class="rf-footer">
    <div class="container text-center small">
        <div class="mb-1"><i class="bi bi-recycle"></i> <strong>{{ config('app.name') }}</strong></div>
        &copy; {{ date('Y') }} – Économie circulaire &amp; consommation textile responsable.
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('templates/frontoffice/js/scripts.js') }}"></script>
@stack('scripts')
</body>
</html>