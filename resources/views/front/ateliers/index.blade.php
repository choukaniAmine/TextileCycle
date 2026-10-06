@extends('layouts.front')

@section('title', 'Ateliers & Services de Réparation')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <!-- Hero Header -->
        <div class="text-center mb-5">
            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill text-uppercase mb-2">
                <i class="bi bi-patch-check-fill me-1"></i> Économie Circulaire Textile
            </span>
            <h1 class="fw-bold display-6">Trouver un atelier partenaire</h1>
            <p class="lead text-muted mx-auto" style="max-width: 650px;">
                Faites réparer, retoucher ou customiser vos vêtements auprès de couturiers et ateliers engagés dans le développement durable.
            </p>
        </div>

        <!-- Recherche & Filtres -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <form action="{{ route('ateliers.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-secondary">
                            <i class="bi bi-search me-1"></i> Recherche par mot-clé
                        </label>
                        <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Nom d'atelier, quartier...">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-secondary">
                            <i class="bi bi-geo-alt me-1"></i> Ville
                        </label>
                        <select name="ville" class="form-select">
                            <option value="">Toutes les villes</option>
                            @foreach ($villes as $v)
                                <option value="{{ $v }}" {{ ($ville ?? '') === $v ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-secondary">
                            <i class="bi bi-funnel me-1"></i> Type de service
                        </label>
                        <select name="type_service" class="form-select">
                            <option value="">Tous les types</option>
                            @foreach ($typesServices as $type)
                                <option value="{{ $type }}" {{ ($typeService ?? '') === $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            Filtrer
                        </button>
                        @if ($search || $ville || $typeService)
                            <a href="{{ route('ateliers.index') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des Ateliers -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">
                Ateliers disponibles <span class="badge bg-secondary rounded-pill ms-1">{{ $ateliers->total() }}</span>
            </h5>
        </div>

        @if ($ateliers->count() > 0)
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
                @foreach ($ateliers as $atelier)
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 d-flex flex-column rounded-3 overflow-hidden">
                            <div style="height: 180px; background-color: #e9ecef;" class="position-relative d-flex align-items-center justify-content-center">
                                @if ($atelier->image && file_exists(public_path('storage/' . $atelier->image)))
                                    <img src="{{ asset('storage/' . $atelier->image) }}" class="w-100 h-100 object-fit-cover" alt="{{ $atelier->nom }}">
                                @else
                                    <div class="text-center text-secondary">
                                        <i class="bi bi-scissors fs-1 d-block mb-1"></i>
                                        <span class="small fw-semibold">{{ $atelier->nom }}</span>
                                    </div>
                                @endif
                                <span class="position-absolute top-0 end-0 m-3 badge bg-white text-dark shadow-sm px-2 py-1 rounded-pill">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $atelier->ville }}
                                </span>
                            </div>

                            <div class="card-body d-flex flex-column p-4">
                                <h5 class="card-title fw-bold text-dark mb-2">{{ $atelier->nom }}</h5>
                                <p class="card-text text-muted small mb-3 flex-grow-1">
                                    {{ Str::limit($atelier->description ?? 'Atelier partenaire engagé dans la retouche et la transformation textile durable.', 110) }}
                                </p>

                                <div class="d-flex flex-column gap-2 small text-secondary mb-3 pt-2 border-top">
                                    <div><i class="bi bi-clock text-primary me-2"></i> {{ $atelier->horaires ?? 'Horaires non renseignés' }}</div>
                                    <div><i class="bi bi-telephone text-primary me-2"></i> {{ $atelier->telephone }}</div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                                    <span class="badge bg-primary-subtle text-primary">
                                        <i class="bi bi-tools me-1"></i> {{ $atelier->services_count }} service(s)
                                    </span>
                                    <a href="{{ route('ateliers.show', $atelier) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                        Voir les services <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center">
                {{ $ateliers->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="text-center py-5 bg-white rounded-3 shadow-sm">
                <i class="bi bi-search fs-1 text-muted d-block mb-2"></i>
                <h5 class="fw-bold">Aucun atelier trouvé</h5>
                <p class="text-muted small">Essayez d'ajuster vos filtres de recherche.</p>
                <a href="{{ route('ateliers.index') }}" class="btn btn-primary btn-sm rounded-pill px-4">Tous les ateliers</a>
            </div>
        @endif
    </div>
</section>
@endsection
