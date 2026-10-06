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
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light static-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}"><i class="bi bi-recycle text-success"></i> {{ config('app.name') }}</a>
        <div class="d-flex align-items-center gap-2">
            @auth
                <span class="me-2 d-none d-md-inline text-muted">{{ auth()->user()->name }}
                    <span class="badge bg-{{ auth()->user()->role->badge() }}">{{ auth()->user()->role->label() }}</span></span>
                @if (auth()->user()->isAdmin())
                    <a class="btn btn-outline-dark btn-sm" href="{{ route('admin.dashboard') }}">Back office</a>
                @endif
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
