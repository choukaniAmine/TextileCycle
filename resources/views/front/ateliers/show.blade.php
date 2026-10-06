@extends('layouts.front')

@section('title', $atelier->nom . ' – Ateliers TextileCycle')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ateliers.index') }}">Ateliers</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $atelier->nom }}</li>
            </ol>
        </nav>

        <div class="row g-4 mb-4">
            <!-- Informations Atelier -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4 p-md-5 bg-white rounded-3 mb-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold">
                            <i class="bi bi-patch-check-fill me-1"></i> Partenaire Agréé
                        </span>
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $atelier->ville }}
                        </span>
                    </div>

                    <h1 class="fw-bold mb-3">{{ $atelier->nom }}</h1>

                    <p class="lead text-muted fs-6 mb-4">
                        {{ $atelier->description ?? 'Atelier spécialisé dans les réparations et retouches de vêtements.' }}
                    </p>

                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-stars text-warning me-2"></i> Services & Tarifs proposés ({{ $atelier->services->count() }})
                    </h5>

                    @if ($atelier->services->count() > 0)
                        <div class="d-flex flex-column gap-3">
                            @foreach ($atelier->services as $service)
                                <div class="border rounded-3 p-3 bg-light d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <h6 class="fw-bold mb-0 text-dark">{{ $service->nom }}</h6>
                                            <span class="badge bg-primary-subtle text-primary small">{{ $service->type_service }}</span>
                                        </div>
                                        <p class="text-muted small mb-1">
                                            {{ $service->description ?? 'Prestation soignée et durable.' }}
                                        </p>
                                        <div class="small text-secondary">
                                            <i class="bi bi-hourglass-split me-1"></i> Délai moyen : <strong>{{ $service->duree_estimee ?? 'Variable' }}</strong>
                                        </div>
                                    </div>
                                    <div class="text-md-end text-nowrap">
                                        <div class="fs-5 fw-bold text-success">{{ number_format($service->tarif_estime, 2) }} DT</div>
                                        <span class="badge bg-success-subtle text-success small">Disponible</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-secondary text-center small mb-0">
                            Aucun service n'est listé pour cet atelier pour le moment.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Coordonnées -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 bg-white rounded-3 sticky-top" style="top: 20px;">
                    <h5 class="fw-bold mb-4 border-bottom pb-3">Coordonnées</h5>

                    <div class="d-flex flex-column gap-3 mb-4">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2 rounded-circle bg-light text-primary">
                                <i class="bi bi-geo-alt fs-5"></i>
                            </div>
                            <div>
                                <div class="small text-muted">Adresse</div>
                                <div class="fw-medium">{{ $atelier->adresse }}</div>
                                <div class="small text-secondary">{{ $atelier->code_postal }} {{ $atelier->ville }}</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2 rounded-circle bg-light text-success">
                                <i class="bi bi-telephone fs-5"></i>
                            </div>
                            <div>
                                <div class="small text-muted">Téléphone</div>
                                <a href="tel:{{ $atelier->telephone }}" class="fw-bold text-dark text-decoration-none">
                                    {{ $atelier->telephone }}
                                </a>
                            </div>
                        </div>

                        @if ($atelier->email)
                            <div class="d-flex align-items-start gap-3">
                                <div class="p-2 rounded-circle bg-light text-info">
                                    <i class="bi bi-envelope fs-5"></i>
                                </div>
                                <div>
                                    <div class="small text-muted">Email</div>
                                    <a href="mailto:{{ $atelier->email }}" class="text-decoration-none text-dark small fw-medium">
                                        {{ $atelier->email }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2 rounded-circle bg-light text-warning">
                                <i class="bi bi-clock fs-5"></i>
                            </div>
                            <div>
                                <div class="small text-muted">Horaires</div>
                                <div class="small text-dark">{{ $atelier->horaires ?? 'Non renseigné' }}</div>
                            </div>
                        </div>
                    </div>

                    <a href="tel:{{ $atelier->telephone }}" class="btn btn-primary w-100 rounded-pill py-2">
                        <i class="bi bi-telephone-fill me-2"></i> Contacter l'atelier
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
