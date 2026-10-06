@extends('layouts.front')

@section('title', $atelier->nom . ' – Ateliers TextileCycle')

@push('styles')
<style>
    /* ── Hero atelier ── */
    .atelier-hero {
        background: linear-gradient(135deg,#0f7038 0%,#1a9e52 60%,#28c76f 100%);
        padding: 50px 0 80px;
        color:#fff; position:relative; overflow:hidden;
    }
    .atelier-hero::before {
        content:'';
        position:absolute; inset:0;
        background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none'%3E%3Cg fill='%23ffffff' fill-opacity='0.06'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    /* ── Card contenu ── */
    .content-card {
        background:#fff;
        border-radius:20px;
        box-shadow:0 4px 24px rgba(0,0,0,.08);
        border:none;
        margin-top:-40px;
        position:relative;
        z-index:10;
    }

    /* ── Service item ── */
    .service-item {
        border:1.5px solid #e8f5e9;
        border-radius:14px;
        padding:20px;
        background:#fff;
        transition:border-color .2s, box-shadow .2s, transform .2s;
        position:relative;
        overflow:hidden;
    }
    .service-item::before {
        content:'';
        position:absolute; left:0; top:0; bottom:0;
        width:4px;
        background:linear-gradient(135deg,#0f7038,#28c76f);
        border-radius:4px 0 0 4px;
    }
    .service-item:hover {
        border-color:#0f7038;
        box-shadow:0 4px 16px rgba(15,112,56,.12);
        transform:translateX(4px);
    }
    .service-type-badge {
        background:#e8f5e9; color:#0f7038;
        border:1px solid #c8e6c9;
        border-radius:50px; padding:3px 12px;
        font-size:.72rem; font-weight:700;
    }
    .price-display {
        background:linear-gradient(135deg,#0f7038,#28c76f);
        -webkit-background-clip:text;
        -webkit-text-fill-color:transparent;
        background-clip:text;
        font-size:1.4rem; font-weight:800;
    }

    /* ── Info sidebar ── */
    .info-card {
        background:#fff;
        border-radius:16px;
        box-shadow:0 4px 20px rgba(0,0,0,.07);
        border:none;
        position:sticky;
        top:20px;
    }
    .info-item {
        display:flex; align-items:flex-start; gap:14px;
        padding:12px 0;
        border-bottom:1px solid #f1f5f9;
    }
    .info-item:last-child { border-bottom:none; }
    .info-icon {
        width:38px; height:38px;
        border-radius:10px;
        display:flex; align-items:center; justify-content:center;
        font-size:1rem; flex-shrink:0;
    }

    /* ── Similaires ── */
    .similaire-card {
        border-radius:12px;
        border:1.5px solid #e8f5e9;
        padding:16px;
        transition:border-color .2s, box-shadow .2s;
        text-decoration:none; color:inherit;
        display:block;
    }
    .similaire-card:hover {
        border-color:#0f7038;
        box-shadow:0 4px 14px rgba(15,112,56,.12);
        color:inherit;
    }

    .stars { color:#f5a623; }
    .stars .empty { color:#dee2e6; }

    /* ── Avis ── */
    .avis-card {
        border:1.5px solid #f1f5f9;
        border-radius:14px; padding:20px;
        background:#fff; transition:box-shadow .2s;
    }
    .avis-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.07); }
    .note-bar-wrap { height:8px; background:#e9ecef; border-radius:50px; overflow:hidden; }
    .note-bar { height:100%; background:linear-gradient(90deg,#f5a623,#f7c948); border-radius:50px; }
    .star-select label { cursor:pointer; font-size:1.8rem; color:#dee2e6; transition:color .15s; }
    .star-select input[type="radio"] { display:none; }
    .star-select input[type="radio"]:checked ~ label,
    .star-select label:hover,
    .star-select label:hover ~ label { color:#f5a623; }
    .star-select { display:flex; flex-direction:row-reverse; justify-content:flex-end; gap:4px; }

    /* ── CTA Banner ── */
    .cta-banner {
        background:linear-gradient(135deg,#0f7038,#28c76f);
        border-radius:16px; padding:30px; color:#fff; text-align:center;
    }
</style>
@endpush

@section('content')

{{-- ── Hero ── --}}
<div class="atelier-hero">
    <div class="container position-relative">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="--bs-breadcrumb-divider-color:rgba(255,255,255,.6);">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Accueil</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ateliers.index') }}" class="text-white-50 text-decoration-none">Ateliers</a></li>
                <li class="breadcrumb-item active text-white">{{ $atelier->nom }}</li>
            </ol>
        </nav>
        <div class="d-flex flex-wrap align-items-center gap-3 mt-3">
            <span class="badge rounded-pill px-3 py-2 fw-semibold"
                  style="background:rgba(255,255,255,.2); color:#fff;">
                <i class="bi bi-patch-check-fill me-1"></i> Partenaire Agréé
            </span>
            <span class="badge rounded-pill px-3 py-2 fw-semibold"
                  style="background:rgba(255,255,255,.2); color:#fff;">
                <i class="bi bi-geo-alt-fill me-1"></i> {{ $atelier->ville }}
            </span>
            <span class="badge rounded-pill px-3 py-2 fw-semibold"
                  style="background:rgba(255,255,255,.2); color:#fff;">
                <i class="bi bi-tools me-1"></i> {{ $atelier->services->count() }} service(s)
            </span>
        </div>
        <h1 class="fw-bold mt-3 mb-0" style="font-size:2rem;">{{ $atelier->nom }}</h1>

        {{-- Étoiles réelles depuis la BDD ── --}}
        @php
            $moyenne = $atelier->moyenneNote();
            $nbAvis  = $atelier->nombreAvis();
            $etoiles = (int) round($moyenne);
        @endphp
        <div class="stars mt-2 d-flex align-items-center gap-2">
            @for ($i = 1; $i <= 5; $i++)
                <i class="bi bi-star{{ $i <= $etoiles ? '-fill' : ' empty' }}"></i>
            @endfor
            <span style="opacity:.85; font-size:.9rem;">
                <strong>{{ $moyenne > 0 ? $moyenne : 'Nouveau' }}</strong>
                @if($nbAvis > 0) / 5 &nbsp;·&nbsp; {{ $nbAvis }} avis @endif
            </span>
        </div>
    </div>
</div>

<section class="py-4 bg-light">
    <div class="container">
        <div class="row g-4">

            {{-- ── Colonne principale ── --}}
            <div class="col-lg-8">

                {{-- Description --}}
                <div class="content-card p-4 p-md-5 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-info-circle text-success me-2"></i> À propos
                    </h5>
                    <p class="text-muted mb-0" style="line-height:1.8;">
                        {{ $atelier->description ?? 'Atelier spécialisé dans les réparations et retouches de vêtements avec un engagement fort pour le textile durable.' }}
                    </p>
                </div>

                {{-- Services --}}
                <div class="content-card p-4 p-md-5 mb-4" style="margin-top:0;">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-stars text-warning me-2"></i>
                        Services & Tarifs
                        <span class="badge rounded-pill ms-2 px-3"
                              style="background:#e8f5e9; color:#0f7038; font-size:.75rem; border:1px solid #c8e6c9;">
                            {{ $atelier->services->count() }} disponible(s)
                        </span>
                    </h5>

                    @forelse ($atelier->services as $service)
                        <div class="service-item mb-3">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                                        <h6 class="fw-bold mb-0 text-dark">{{ $service->nom }}</h6>
                                        <span class="service-type-badge">{{ $service->type_service }}</span>
                                    </div>
                                    <p class="text-muted small mb-2">
                                        {{ $service->description ?? 'Prestation soignée et durable.' }}
                                    </p>
                                    <div class="d-flex flex-wrap gap-3 small text-secondary">
                                        <span><i class="bi bi-hourglass-split text-success me-1"></i>
                                            Délai : <strong>{{ $service->duree_estimee ?? 'Variable' }}</strong>
                                        </span>
                                        @if($service->prix_min)
                                            <span><i class="bi bi-tag text-success me-1"></i>
                                                À partir de <strong>{{ number_format($service->prix_min, 0) }} DT</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-md-end flex-shrink-0">
                                    <div class="price-display">
                                        {{ number_format($service->tarif_estime ?? $service->prix_min ?? 0, 0) }} DT
                                    </div>
                                    <span class="badge rounded-pill px-2 py-1 mt-1"
                                          style="background:#e8f5e9; color:#0f7038; font-size:.7rem; border:1px solid #c8e6c9;">
                                        <i class="bi bi-check-circle me-1"></i>Disponible
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            <i class="bi bi-exclamation-circle fs-3 d-block mb-2"></i>
                            Aucun service disponible pour le moment.
                        </div>
                    @endforelse
                </div>

                {{-- ⭐ SECTION AVIS CLIENTS (VALEUR AJOUTÉE) ── --}}
                <div class="content-card p-4 p-md-5 mb-4" style="margin-top:0;">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-chat-square-text text-warning me-2"></i>
                        Avis clients
                        @if($nbAvis > 0)
                            <span class="badge rounded-pill ms-2 px-3"
                                  style="background:#fff3e0; color:#e65100; font-size:.75rem; border:1px solid #ffe0b2;">
                                {{ $nbAvis }} avis · {{ $moyenne }}/5
                            </span>
                        @endif
                    </h5>

                    {{-- Résumé notation + barres --}}
                    @if($nbAvis > 0)
                    <div class="row g-4 align-items-center mb-4 pb-4 border-bottom">
                        <div class="col-auto text-center">
                            <div style="font-size:3.5rem; font-weight:900; line-height:1; color:#f5a623;">{{ $moyenne }}</div>
                            <div class="stars my-1">
                                @for($i=1;$i<=5;$i++)
                                    <i class="bi bi-star{{ $i<=$etoiles?'-fill':' empty' }}" style="font-size:.85rem;"></i>
                                @endfor
                            </div>
                            <div class="text-muted small">{{ $nbAvis }} avis</div>
                        </div>
                        <div class="col">
                            @foreach($repartition as $note => $count)
                                @php $pct = $nbAvis > 0 ? round($count/$nbAvis*100) : 0; @endphp
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="small text-muted" style="width:10px;">{{ $note }}</span>
                                    <i class="bi bi-star-fill text-warning" style="font-size:.7rem;"></i>
                                    <div class="note-bar-wrap flex-grow-1">
                                        <div class="note-bar" style="width:{{ $pct }}%;"></div>
                                    </div>
                                    <span class="small text-muted" style="width:28px;">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Formulaire laisser un avis ── --}}
                    @auth
                        <div class="mb-4 pb-4 border-bottom">
                            <h6 class="fw-bold mb-3">
                                {{ $monAvis ? '✏️ Modifier votre avis' : '⭐ Laisser un avis' }}
                            </h6>
                            <form action="{{ route('ateliers.avis.store', $atelier) }}" method="POST">
                                @csrf
                                {{-- Sélecteur d'étoiles interactif --}}
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-secondary">Votre note</label>
                                    <div class="star-select">
                                        @for($i=5;$i>=1;$i--)
                                            <input type="radio" name="note" id="star{{ $i }}" value="{{ $i }}"
                                                   {{ ($monAvis && $monAvis->note == $i) ? 'checked' : '' }}>
                                            <label for="star{{ $i }}" title="{{ $i }} étoile(s)">
                                                <i class="bi bi-star-fill"></i>
                                            </label>
                                        @endfor
                                    </div>
                                    @error('note') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-secondary">Commentaire <span class="text-muted fw-normal">(optionnel)</span></label>
                                    <textarea name="commentaire" rows="3"
                                              class="form-control @error('commentaire') is-invalid @enderror"
                                              placeholder="Partagez votre expérience avec cet atelier…"
                                              maxlength="500"
                                              style="border-radius:10px; border:1.5px solid #dee2e6; font-size:.9rem;">{{ old('commentaire', $monAvis?->commentaire) }}</textarea>
                                    @error('commentaire') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn rounded-pill px-4 fw-semibold"
                                            style="background:linear-gradient(135deg,#0f7038,#28c76f); color:#fff; border:none;">
                                        <i class="bi bi-send me-2"></i>{{ $monAvis ? 'Mettre à jour mon avis' : 'Publier mon avis' }}
                                    </button>
                                    @if($monAvis)
                                        <button type="button" class="btn btn-outline-danger rounded-pill px-3"
                                                onclick="if(confirm('Supprimer votre avis ?')) document.getElementById('delete-avis-form').submit();"
                                                title="Supprimer mon avis">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </form>

                            @if($monAvis)
                                <form id="delete-avis-form" action="{{ route('ateliers.avis.destroy', $atelier) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endif
                        </div>
                    @else
                        <div class="alert border-0 mb-4 rounded-3 small"
                             style="background:#f0fdf4; color:#0f7038; border:1.5px solid #c8e6c9 !important;">
                            <i class="bi bi-info-circle me-2"></i>
                            <a href="{{ route('login') }}" class="fw-bold text-success">Connectez-vous</a>
                            pour noter et donner votre avis sur cet atelier.
                        </div>
                    @endauth

                    {{-- Liste des avis ── --}}
                    @forelse($atelier->avis as $avis)
                        <div class="avis-card mb-3">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                                         style="width:38px;height:38px;background:linear-gradient(135deg,#0f7038,#28c76f);font-size:.85rem;flex-shrink:0;">
                                        {{ strtoupper(substr($avis->auteur->name ?? 'A', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold small text-dark">{{ $avis->auteur->name ?? 'Utilisateur' }}</div>
                                        <div class="text-muted" style="font-size:.72rem;">{{ $avis->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                                <div class="stars" style="font-size:.8rem;">
                                    @for($i=1;$i<=5;$i++)
                                        <i class="bi bi-star{{ $i<=$avis->note?'-fill':' empty' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            @if($avis->commentaire)
                                <p class="text-muted small mb-0 ms-1" style="line-height:1.7;">
                                    "{{ $avis->commentaire }}"
                                </p>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            <i class="bi bi-chat-dots fs-3 d-block mb-2"></i>
                            Aucun avis pour le moment. Soyez le premier à donner votre avis !
                        </div>
                    @endforelse
                </div>

                {{-- Ateliers similaires --}}
                @if($similaires->count() > 0)
                    <div class="content-card p-4 mb-4" style="margin-top:0;">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-shop text-primary me-2"></i> Ateliers similaires à {{ $atelier->ville }}
                        </h5>
                        <div class="row g-3">
                            @foreach($similaires as $sim)
                                <div class="col-md-4">
                                    <a href="{{ route('ateliers.show', $sim) }}" class="similaire-card">
                                        <div class="fw-bold small text-dark mb-1">{{ Str::limit($sim->nom, 22) }}</div>
                                        <div class="text-muted" style="font-size:.75rem;">
                                            <i class="bi bi-tools me-1 text-success"></i>{{ $sim->services_count }} service(s)
                                        </div>
                                        <div class="text-muted" style="font-size:.75rem;">
                                            <i class="bi bi-geo-alt me-1 text-danger"></i>{{ $sim->ville }}
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            {{-- ── Sidebar ── --}}
            <div class="col-lg-4">
                <div class="info-card p-4 mb-4">
                    <h6 class="fw-bold mb-3 text-dark border-bottom pb-2">
                        <i class="bi bi-card-list text-success me-2"></i> Coordonnées
                    </h6>

                    <div class="info-item">
                        <div class="info-icon" style="background:#e8f5e9;">
                            <i class="bi bi-geo-alt text-success"></i>
                        </div>
                        <div>
                            <div class="small text-muted">Adresse</div>
                            <div class="fw-medium small">{{ $atelier->adresse }}</div>
                            <div class="text-muted" style="font-size:.75rem;">{{ $atelier->code_postal ?? '' }} {{ $atelier->ville }}</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon" style="background:#fff3e0;">
                            <i class="bi bi-telephone text-warning"></i>
                        </div>
                        <div>
                            <div class="small text-muted">Téléphone</div>
                            <a href="tel:{{ $atelier->telephone }}" class="fw-bold small text-dark text-decoration-none">
                                {{ $atelier->telephone }}
                            </a>
                        </div>
                    </div>

                    @if($atelier->email)
                    <div class="info-item">
                        <div class="info-icon" style="background:#e3f2fd;">
                            <i class="bi bi-envelope text-primary"></i>
                        </div>
                        <div>
                            <div class="small text-muted">Email</div>
                            <a href="mailto:{{ $atelier->email }}" class="small text-dark text-decoration-none">
                                {{ $atelier->email }}
                            </a>
                        </div>
                    </div>
                    @endif

                    <div class="info-item">
                        <div class="info-icon" style="background:#fce4ec;">
                            <i class="bi bi-clock text-danger"></i>
                        </div>
                        <div>
                            <div class="small text-muted">Horaires</div>
                            <div class="small text-dark">{{ $atelier->horaires ?? 'Non renseigné' }}</div>
                        </div>
                    </div>

                    <a href="tel:{{ $atelier->telephone }}"
                       class="btn w-100 rounded-pill py-2 fw-semibold mt-3"
                       style="background:linear-gradient(135deg,#0f7038,#28c76f); color:#fff; border:none;">
                        <i class="bi bi-telephone-fill me-2"></i> Contacter l'atelier
                    </a>
                    <a href="{{ route('ateliers.index') }}"
                       class="btn btn-outline-secondary w-100 rounded-pill py-2 mt-2 fw-semibold">
                        <i class="bi bi-arrow-left me-2"></i> Retour aux ateliers
                    </a>
                </div>

                {{-- CTA Banner --}}
                <div class="cta-banner">
                    <i class="bi bi-recycle fs-2 mb-2 d-block"></i>
                    <h6 class="fw-bold mb-1">Agissons ensemble</h6>
                    <p class="small mb-3" style="opacity:.85;">
                        Chaque vêtement réparé, c'est de l'eau économisée et du CO₂ évité.
                    </p>
                    <a href="{{ route('ateliers.index') }}"
                       class="btn btn-sm btn-light rounded-pill px-4 fw-semibold text-success">
                        Voir tous les ateliers
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
