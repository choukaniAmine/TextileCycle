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

    html {
        scroll-behavior: smooth;
    }

    /* ── Card contenu ── */
    .content-card {
        background:#fff;
        border-radius:20px;
        box-shadow:0 4px 24px rgba(0,0,0,.08);
        border:none;
        position:relative;
        margin-bottom:28px;
    }
    .hero-overlap-card {
        margin-top:-50px;
        position:relative;
        z-index:10;
    }

    /* ── Service item ── */
    .service-item {
        border:1.5px solid #e8f5e9;
        border-radius:14px;
        padding:20px;
        background:#fff;
        transition:border-color .2s, box-shadow .2s;
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
    .sidebar-sticky {
        position: -webkit-sticky;
        position: sticky;
        top: 90px;
        z-index: 10;
    }
    .info-card {
        background:#fff;
        border-radius:16px;
        box-shadow:0 4px 20px rgba(0,0,0,.07);
        border:none;
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

    /* ── Simulateur de devis (Fonctionnalité Avancée) ── */
    .devis-simulator-box {
        background: #ffffff;
        border: 2px solid #e8f5e9;
        border-radius: 18px;
        box-shadow: 0 4px 20px rgba(15,112,56,.06);
    }
    .eco-metric-card {
        background: linear-gradient(135deg, #f0fff4 0%, #e6fffa 100%);
        border: 1.5px solid #b2f5ea;
        border-radius: 12px;
        padding: 12px 14px;
        text-align: center;
    }
    .service-select-row {
        border-bottom: 1px dashed #e2e8f0;
        padding: 12px 0;
        transition: background-color .15s;
    }
    .service-select-row:last-child {
        border-bottom: none;
    }
    .qty-counter {
        display: inline-flex;
        align-items: center;
        border: 1px solid #cbd5e0;
        border-radius: 8px;
        overflow: hidden;
        background: #fff;
    }
    .qty-counter input {
        width: 44px;
        text-align: center;
        border: none;
        font-weight: bold;
        font-size: .95rem;
    }
    .qty-counter button {
        border: none;
        background: #edf2f7;
        width: 32px;
        height: 32px;
        font-weight: bold;
        color: #4a5568;
        cursor: pointer;
        transition: background .15s;
    }
    .qty-counter button:hover {
        background: #e2e8f0;
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
                <div class="content-card hero-overlap-card p-4 p-md-5 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-info-circle text-success me-2"></i> À propos
                    </h5>
                    <p class="text-muted mb-0" style="line-height:1.8;">
                        {{ $atelier->description ?? 'Atelier spécialisé dans les réparations et retouches de vêtements avec un engagement fort pour le textile durable.' }}
                    </p>
                </div>

                {{-- Services --}}
                <div class="content-card p-4 p-md-5 mb-4">
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

                {{-- 🧮 SECTION SIMULATEUR DE DEVIS & IMPACT ÉCOLOGIQUE (FONCTIONNALITÉ AVANCÉE) ── --}}
                <div class="content-card p-4 p-md-5 mb-4 devis-simulator-box" id="simulateur-devis">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1 text-success">
                                <i class="bi bi-calculator-fill me-2"></i> Simulateur de Devis &amp; Éco-Impact
                            </h5>
                            <p class="text-muted small mb-0">
                                Estimez vos réparations et générez instantanément votre devis officiel PDF.
                            </p>
                        </div>
                        <span class="badge rounded-pill px-3 py-2 fw-semibold"
                              style="background:#e8f5e9; color:#0f7038; border:1px solid #c8e6c9;">
                            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF officiel
                        </span>
                    </div>

                    @if($atelier->services->count() > 0)
                        <form id="devisForm" action="{{ route('ateliers.devis.store', $atelier) }}" method="POST">
                            @csrf

                            {{-- Liste des prestations avec sélection & quantité --}}
                            <div class="mb-4">
                                <label class="fw-bold small text-uppercase text-muted mb-2 d-block">
                                    1. Choisissez vos prestations
                                </label>

                                <div class="border rounded-3 p-3 bg-light">
                                    @foreach($atelier->services as $service)
                                        <div class="service-select-row d-flex flex-wrap align-items-center justify-content-between gap-3">
                                            <div class="d-flex align-items-center gap-3 flex-grow-1">
                                                <div class="form-check m-0">
                                                    <input class="form-check-input service-check"
                                                           type="checkbox"
                                                           id="srv_{{ $service->id }}"
                                                           data-id="{{ $service->id }}"
                                                           data-price="{{ (float)$service->tarif_estime }}">
                                                </div>
                                                <div>
                                                    <label class="form-check-label fw-bold text-dark mb-0 cursor-pointer" for="srv_{{ $service->id }}">
                                                        {{ $service->nom }}
                                                    </label>
                                                    <span class="badge bg-white text-muted border ms-2" style="font-size:.7rem;">
                                                        {{ $service->type_service }}
                                                    </span>
                                                    <div class="small text-muted">
                                                        Tarif indicatif : <strong class="text-success">{{ number_format($service->tarif_estime, 2) }} DT</strong>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center gap-2">
                                                <span class="small text-muted d-none d-sm-inline">Quantité :</span>
                                                <div class="qty-counter">
                                                    <button type="button" class="btn-qty-minus" data-id="{{ $service->id }}">-</button>
                                                    <input type="number"
                                                           name="services[{{ $service->id }}]"
                                                           id="qty_{{ $service->id }}"
                                                           value="0"
                                                           min="0"
                                                           max="20"
                                                           class="service-qty"
                                                           data-id="{{ $service->id }}"
                                                           data-price="{{ (float)$service->tarif_estime }}">
                                                    <button type="button" class="btn-qty-plus" data-id="{{ $service->id }}">+</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Coordonnées du client --}}
                            <div class="mb-4">
                                <label class="fw-bold small text-uppercase text-muted mb-2 d-block">
                                    2. Vos coordonnées pour le devis
                                </label>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Nom complet <span class="text-danger">*</span></label>
                                        <input type="text"
                                               name="nom_client"
                                               class="form-control form-control-sm"
                                               value="{{ old('nom_client', auth()->user()?->name ?? '') }}"
                                               placeholder="Ex: Amel Ben Salem"
                                               required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Adresse e-mail <span class="text-danger">*</span></label>
                                        <input type="email"
                                               name="email_client"
                                               class="form-control form-control-sm"
                                               value="{{ old('email_client', auth()->user()?->email ?? '') }}"
                                               placeholder="amel@exemple.tn"
                                               required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Téléphone</label>
                                        <input type="text"
                                               name="telephone_client"
                                               class="form-control form-control-sm"
                                               value="{{ old('telephone_client', auth()->user()?->phone ?? '') }}"
                                               placeholder="Ex: +216 98 000 000">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Précisions (matière, couleur, accroc...)</label>
                                        <input type="text"
                                               name="remarques"
                                               class="form-control form-control-sm"
                                               placeholder="Ex: Jean Levi's à raccourcir avec l'ourlet d'origine">
                                    </div>
                                </div>
                            </div>

                            {{-- Résumé dynamique & Impact Écologique en direct --}}
                            <div class="card border-0 mb-4" style="background:#f8fafc; border-radius:14px; border:1px solid #e2e8f0;">
                                <div class="card-body p-3 p-md-4">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-sm-6 col-md-4">
                                            <div class="small text-muted text-uppercase fw-semibold">Total Estimé</div>
                                            <div class="fs-3 fw-bold text-success" id="liveTotal">0,00 DT</div>
                                            <div class="small text-muted" id="livePieces">0 prestation(s) sélectionnée(s)</div>
                                        </div>
                                        <div class="col-sm-6 col-md-4">
                                            <div class="eco-metric-card">
                                                <div class="small text-success fw-bold">🌱 CO₂ économisé</div>
                                                <div class="fs-5 fw-bold text-dark" id="liveCo2">0,0 kg</div>
                                                <div class="small text-muted" style="font-size:.72rem;">vs textile neuf</div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-4">
                                            <div class="eco-metric-card">
                                                <div class="small text-info fw-bold">💧 Eau préservée</div>
                                                <div class="fs-5 fw-bold text-dark" id="liveEau">0 L</div>
                                                <div class="small text-muted" style="font-size:.72rem;">sauvegardée</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Boutons d'action --}}
                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit"
                                        id="btnSubmitDevis"
                                        class="btn btn-success fw-semibold px-4 py-2 rounded-pill flex-grow-1 flex-md-grow-0"
                                        style="background:linear-gradient(135deg,#0f7038,#28c76f); border:none;">
                                    <i class="bi bi-file-earmark-pdf me-2"></i> Télécharger mon Devis PDF
                                </button>
                                <button type="button"
                                        id="btnApercuDevis"
                                        class="btn btn-outline-success fw-semibold px-4 py-2 rounded-pill">
                                    <i class="bi bi-eye me-1"></i> Aperçu direct
                                </button>
                            </div>

                            {{-- Boîte de résultat AJAX --}}
                            <div id="devisResult" class="alert alert-success mt-3 d-none border-0 shadow-sm" style="border-radius:12px;">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div>
                                        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                        <strong id="devisRef">Devis généré !</strong>
                                        <span class="d-block small text-muted mt-1" id="devisSummary"></span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="#" id="devisDownloadLink" class="btn btn-sm btn-success fw-bold rounded-pill px-3">
                                            <i class="bi bi-download me-1"></i> Télécharger PDF
                                        </a>
                                        <a href="#" id="devisStreamLink" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> Ouvrir
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Modal ReFashion personnalisé pour messages & alertes --}}
                            <div class="modal fade" id="devisAlertModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
                                    <div class="modal-content border-0 shadow-lg text-center p-3" style="border-radius: 20px;">
                                        <div class="modal-body py-4 px-3">
                                            <div class="mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle"
                                                 id="devisModalIconWrap"
                                                 style="width: 64px; height: 64px; background: #fff4e5; color: #ff9800;">
                                                <i class="bi bi-exclamation-triangle-fill fs-2" id="devisModalIcon"></i>
                                            </div>
                                            <h5 class="fw-bold mb-2 text-dark" id="devisModalTitle">Sélection requise</h5>
                                            <p class="text-muted small mb-4" id="devisModalMessage">
                                                Veuillez sélectionner au moins une prestation en cochant ou augmentant la quantité.
                                            </p>
                                            <button type="button" class="btn fw-semibold px-4 py-2 rounded-pill text-white shadow-sm"
                                                    data-bs-dismiss="modal"
                                                    style="background: linear-gradient(135deg, #0f7038, #28c76f); border: none; min-width: 140px;">
                                                Compris
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @else
                        <p class="text-muted small mb-0">Cet atelier ne propose pas encore de prestations configurées pour la simulation.</p>
                    @endif
                </div>

                {{-- ⭐ SECTION AVIS CLIENTS (VALEUR AJOUTÉE) ── --}}
                <div class="content-card p-4 p-md-5 mb-4">
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
                <div class="sidebar-sticky">
                    <div class="info-card hero-overlap-card p-4 mb-4">
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

                        <a href="#simulateur-devis"
                           class="btn btn-outline-success w-100 rounded-pill py-2 mt-2 fw-semibold">
                            <i class="bi bi-calculator me-2"></i> Simuler un devis PDF
                        </a>
                        <a href="tel:{{ $atelier->telephone }}"
                           class="btn w-100 rounded-pill py-2 fw-semibold mt-2"
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
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const devisForm = document.getElementById('devisForm');
    if (!devisForm) return;

    const serviceChecks = document.querySelectorAll('.service-check');
    const serviceQtys = document.querySelectorAll('.service-qty');
    const liveTotal = document.getElementById('liveTotal');
    const livePieces = document.getElementById('livePieces');
    const liveCo2 = document.getElementById('liveCo2');
    const liveEau = document.getElementById('liveEau');
    const devisResult = document.getElementById('devisResult');
    const devisRef = document.getElementById('devisRef');
    const devisSummary = document.getElementById('devisSummary');
    const devisDownloadLink = document.getElementById('devisDownloadLink');
    const devisStreamLink = document.getElementById('devisStreamLink');
    const btnSubmitDevis = document.getElementById('btnSubmitDevis');
    const btnApercuDevis = document.getElementById('btnApercuDevis');

    function calculateLive() {
        let total = 0;
        let pieces = 0;

        serviceQtys.forEach(input => {
            const qty = parseInt(input.value) || 0;
            const price = parseFloat(input.dataset.price) || 0;
            if (qty > 0) {
                total += qty * price;
                pieces += qty;
            }
        });

        const co2 = (pieces * 6.5).toFixed(1);
        const eau = (pieces * 2500).toLocaleString('fr-FR');

        liveTotal.textContent = total.toFixed(2).replace('.', ',') + ' DT';
        livePieces.textContent = pieces + ' prestation(s) sélectionnée(s)';
        liveCo2.textContent = co2 + ' kg';
        liveEau.textContent = eau + ' L';
    }

    // Cases à cocher
    serviceChecks.forEach(check => {
        check.addEventListener('change', function () {
            const id = this.dataset.id;
            const qtyInput = document.getElementById('qty_' + id);
            if (this.checked) {
                if (parseInt(qtyInput.value) === 0) qtyInput.value = 1;
            } else {
                qtyInput.value = 0;
            }
            calculateLive();
        });
    });

    // Compteurs + / -
    document.querySelectorAll('.btn-qty-plus').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const input = document.getElementById('qty_' + id);
            const check = document.getElementById('srv_' + id);
            let val = parseInt(input.value) || 0;
            if (val < 20) {
                val++;
                input.value = val;
                if (check) check.checked = (val > 0);
                calculateLive();
            }
        });
    });

    document.querySelectorAll('.btn-qty-minus').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const input = document.getElementById('qty_' + id);
            const check = document.getElementById('srv_' + id);
            let val = parseInt(input.value) || 0;
            if (val > 0) {
                val--;
                input.value = val;
                if (check) check.checked = (val > 0);
                calculateLive();
            }
        });
    });

    serviceQtys.forEach(input => {
        input.addEventListener('input', function () {
            const id = this.dataset.id;
            const check = document.getElementById('srv_' + id);
            const val = parseInt(this.value) || 0;
            if (check) check.checked = (val > 0);
            calculateLive();
        });
    });

    function showDevisModalAlert(message, title = 'Sélection requise', type = 'warning') {
        const modalEl = document.getElementById('devisAlertModal');
        if (!modalEl) {
            alert(message);
            return;
        }
        const modalTitle = document.getElementById('devisModalTitle');
        const modalMessage = document.getElementById('devisModalMessage');
        const modalIcon = document.getElementById('devisModalIcon');
        const modalIconWrap = document.getElementById('devisModalIconWrap');

        if (modalTitle) modalTitle.textContent = title;
        if (modalMessage) modalMessage.textContent = message;

        if (modalIconWrap && modalIcon) {
            if (type === 'warning') {
                modalIconWrap.style.background = '#fff4e5';
                modalIconWrap.style.color = '#ff9800';
                modalIcon.className = 'bi bi-exclamation-triangle-fill fs-2';
            } else if (type === 'danger') {
                modalIconWrap.style.background = '#fde8e8';
                modalIconWrap.style.color = '#e53e3e';
                modalIcon.className = 'bi bi-x-circle-fill fs-2';
            } else {
                modalIconWrap.style.background = '#e8f5e9';
                modalIconWrap.style.color = '#0f7038';
                modalIcon.className = 'bi bi-check-circle-fill fs-2';
            }
        }

        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }

    // Soumission AJAX
    async function submitDevis(actionType = 'download') {
        let totalQty = 0;
        serviceQtys.forEach(input => {
            totalQty += parseInt(input.value) || 0;
        });

        if (totalQty === 0) {
            showDevisModalAlert(
                'Veuillez sélectionner au moins une prestation en cochant la case ou en augmentant la quantité avec les boutons + / -.',
                'Aucune prestation sélectionnée',
                'warning'
            );
            return;
        }

        if (!devisForm.checkValidity()) {
            devisForm.reportValidity();
            return;
        }

        const originalBtnText = btnSubmitDevis.innerHTML;
        btnSubmitDevis.disabled = true;
        btnSubmitDevis.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Génération du PDF...';

        const formData = new FormData(devisForm);

        try {
            const response = await fetch(devisForm.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                devisResult.classList.remove('d-none');
                devisRef.textContent = 'Devis ' + data.reference + ' généré avec succès !';
                devisSummary.textContent = 'Montant : ' + data.total + ' · Délai estimé : ' + data.delai + ' · Bilan : ' + data.co2 + ' CO₂ & ' + data.eau + ' d\'eau économisés';
                devisDownloadLink.href = data.download_url;
                devisStreamLink.href = data.stream_url;

                if (actionType === 'download') {
                    window.location.href = data.download_url;
                } else if (actionType === 'stream') {
                    window.open(data.stream_url, '_blank');
                }
            } else {
                showDevisModalAlert(
                    data.message || 'Une erreur est survenue lors de la création du devis.',
                    'Erreur',
                    'danger'
                );
            }
        } catch (err) {
            console.error(err);
            devisForm.submit();
        } finally {
            btnSubmitDevis.disabled = false;
            btnSubmitDevis.innerHTML = originalBtnText;
        }
    }

    devisForm.addEventListener('submit', function (e) {
        e.preventDefault();
        submitDevis('download');
    });

    if (btnApercuDevis) {
        btnApercuDevis.addEventListener('click', function () {
            submitDevis('stream');
        });
    }
});
</script>
@endpush
@endsection
