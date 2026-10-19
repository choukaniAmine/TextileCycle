@extends('layouts.front')

@section('title', 'Ateliers & Services de Réparation')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* ── Hero ── */
    .hero-ateliers {
        background: linear-gradient(135deg, #0f7038 0%, #1a9e52 50%, #28c76f 100%);
        color: #fff;
        padding: 80px 0 60px;
        position: relative;
        overflow: hidden;
    }
    .hero-ateliers::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .hero-ateliers .hero-icon {
        font-size: 4rem;
        opacity: .15;
        position: absolute;
        right: 5%;
        top: 50%;
        transform: translateY(-50%);
    }

    /* ── Stats Bar ── */
    .stats-bar { background: #fff; border-bottom: 1px solid #e9ecef; }
    .stat-item { text-align: center; padding: 16px 0; }
    .stat-item .stat-number { font-size: 1.8rem; font-weight: 800; color: #0f7038; line-height:1; }
    .stat-item .stat-label  { font-size: .75rem; color: #6c757d; text-transform: uppercase; letter-spacing: .5px; }

    /* ── Filtre card ── */
    .filter-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 20px 24px;
        box-shadow: 0 10px 30px rgba(15,112,56,.06);
        margin-top: -30px;
        position: relative;
        z-index: 10;
    }
    .filter-card .form-control,
    .filter-card .form-select {
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        padding: 8px 14px;
        font-size: .88rem;
        height: 42px;
        transition: all .2s ease;
    }
    .filter-card .form-control:focus,
    .filter-card .form-select:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16,185,129,.15);
    }
    .btn-filter {
        background: linear-gradient(135deg, #0f7038, #1a9e52);
        border: 1px solid #0f7038;
        color: #ffffff !important;
        border-radius: 12px;
        padding: 8px 16px;
        font-weight: 600;
        font-size: .88rem;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        box-shadow: 0 4px 12px rgba(15,112,56,.2);
        transition: all .2s ease;
    }
    .btn-filter:hover {
        background: #0d5f30 !important;
        border-color: #0d5f30 !important;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(15,112,56,.3);
    }
    .btn-reset-filter {
        border-radius: 12px;
        height: 42px;
        width: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid #e2e8f0;
        color: #64748b;
        background: #fff;
        transition: all .2s ease;
    }
    .btn-reset-filter:hover {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #cbd5e1;
    }

    /* ── Tags filtres actifs ── */
    .active-filters .badge {
        font-size:.8rem; padding:.45em .8em; border-radius:50px;
        background:#e8f5e9; color:#0f7038; border:1px solid #c8e6c9;
        cursor:pointer; transition:background .2s;
    }
    .active-filters .badge:hover { background:#c8e6c9; }

    /* ── Atelier Card ── */
    .atelier-card {
        border: 1px solid #edf2f7;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 4px 18px rgba(0,0,0,.04);
        transition: transform .25s, box-shadow .25s, border-color .25s;
        height: 100%;
        background: #fff;
    }
    .atelier-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 14px 34px rgba(15,112,56,.12);
        border-color: #86efac;
    }
    .atelier-card .card-img-wrap {
        position: relative;
        height: 180px;
        background: linear-gradient(135deg, #e8f5e9, #dcfce7);
        overflow: hidden;
    }
    .atelier-card .card-img-wrap img {
        width:100%; height:100%; object-fit:cover;
        transition: transform .4s;
    }
    .atelier-card:hover .card-img-wrap img { transform: scale(1.05); }
    .atelier-card .card-img-wrap .placeholder-icon {
        position:absolute; inset:0;
        display:flex; flex-direction:column;
        align-items:center; justify-content:center;
        color:#0f7038; opacity:.6;
    }
    .atelier-card .ville-badge {
        position: absolute; top:12px; right:12px;
        background:#fff; color:#1e293b;
        border-radius:50px; padding:4px 12px;
        font-size:.75rem; font-weight:700;
        box-shadow: 0 2px 10px rgba(0,0,0,.12);
    }
    .atelier-card .type-badge {
        position:absolute; top:12px; left:12px;
        background:#0f7038; color:#fff;
        border-radius:50px; padding:4px 11px;
        font-size:.7rem; font-weight:700; letter-spacing:.3px;
        box-shadow: 0 2px 8px rgba(15,112,56,.3);
    }
    .btn-card-action {
        background: linear-gradient(135deg, #0f7038, #1a9e52);
        color: #ffffff !important;
        border: none;
        transition: all .2s ease;
    }
    .btn-card-action:hover {
        background: #0d5f30 !important;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(15,112,56,.25);
    }

    /* ── Stars ── */
    .stars { color:#f5a623; font-size:.85rem; letter-spacing:1px; }
    .stars .empty { color:#dee2e6; }

    /* ── Loading spinner ── */
    #ateliers-loading {
        display:none; text-align:center; padding:60px 0;
    }
    .spinner-green { color:#0f7038; }

    /* ── Empty state ── */
    .empty-state {
        text-align:center; padding:80px 20px;
        background:#fff; border-radius:16px;
        box-shadow:0 2px 12px rgba(0,0,0,.06);
    }
    .empty-state .empty-icon {
        font-size:4rem; color:#dee2e6; margin-bottom:16px; display:block;
    }

    /* ── Scroll to top ── */
    #scrollTop {
        position:fixed; bottom:24px; right:24px; z-index:999;
        width:44px; height:44px; border-radius:50%;
        background:linear-gradient(135deg,#0f7038,#28c76f);
        color:#fff; border:none; font-size:1.1rem;
        box-shadow:0 4px 14px rgba(15,112,56,.4);
        display:none; align-items:center; justify-content:center;
        cursor:pointer; transition:opacity .2s;
    }
    #scrollTop.visible { display:flex; }

    /* ── Leaflet Carte Interactive ── */
    #map-section {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,.06);
        margin-bottom: 28px;
    }
    #map-section .map-header {
        padding: 14px 20px;
        border-bottom: 1px solid #e8f5e9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        background: #f8fffe;
    }
    #leaflet-map {
        height: 400px;
        min-height: 400px;
        width: 100%;
        z-index: 1;
        background: #e5e3df;
    }
    .leaflet-container {
        font-family: inherit !important;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 14px !important;
        box-shadow: 0 6px 24px rgba(0,0,0,.15) !important;
        border: none !important;
    }
    .leaflet-popup-content { margin: 0 !important; padding: 0 !important; }
    .map-popup {
        padding: 14px 16px;
        min-width: 200px;
        font-family: inherit;
    }
    .map-popup .map-popup-title {
        font-weight: 700;
        font-size: .95rem;
        color: #0f7038;
        margin-bottom: 4px;
    }
    .map-popup .map-popup-ville {
        font-size: .8rem;
        color: #6c757d;
        margin-bottom: 8px;
    }
    .map-popup .map-popup-stars { color: #f5a623; font-size: .8rem; }
    .map-popup .map-popup-services { font-size: .78rem; color: #495057; margin-top: 4px; }
    .map-popup .btn-popup {
        display: block;
        background: linear-gradient(135deg,#0f7038,#1a9e52);
        color: #ffffff !important;
        border-radius: 8px;
        padding: 7px 12px;
        text-align: center;
        text-decoration: none;
        font-size: .82rem;
        font-weight: 600;
        margin-top: 10px;
        transition: background .2s ease;
    }
    .map-popup .btn-popup:hover {
        background: #0d5f30 !important;
        color: #ffffff !important;
    }
    #btn-locate {
        background: linear-gradient(135deg,#0f7038,#1a9e52);
        border: none;
        color:#ffffff !important;
        border-radius: 10px; padding: 8px 16px;
        font-weight: 600; font-size: .88rem;
        cursor: pointer; transition: all .2s;
        display: flex; align-items: center; gap: 6px;
    }
    #btn-locate:hover {
        background: #0d5f30 !important;
        color: #ffffff !important;
        transform: translateY(-1px);
    }
    .action-pill-btn {
        cursor: pointer;
        font-size: .85rem;
        font-weight: 600;
        border-radius: 50rem;
        padding: 6px 14px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all .2s ease;
        border: 1.5px solid transparent;
        text-decoration: none;
    }
    .action-pill-btn.pill-outline-map {
        background: #fff;
        border-color: #c8e6c9;
        color: #0f7038;
    }
    .action-pill-btn.pill-outline-map:hover,
    .action-pill-btn.pill-outline-map.active {
        background: #0f7038;
        border-color: #0f7038;
        color: #ffffff !important;
    }
    .action-pill-btn.pill-outline-ai {
        background: #fff;
        border-color: #86efac;
        color: #0f7038;
    }
    .action-pill-btn.pill-outline-ai:hover,
    .action-pill-btn.pill-outline-ai.active {
        background: #0f7038;
        border-color: #0f7038;
        color: #ffffff !important;
    }

    /* ── Diagnostic IA ── */
    .ai-diag-banner {
        background: linear-gradient(135deg, #f0fdf4 0%, #e6f7ec 100%);
        border: 2px dashed #86efac;
        border-radius: 20px;
        padding: 24px;
        transition: border-color .3s, transform .2s;
    }
    .ai-diag-banner:hover {
        border-color: #22c55e;
    }
    .ai-dropzone {
        background: #fff;
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        padding: 28px 20px;
        text-align: center;
        cursor: pointer;
        transition: all .2s ease;
    }
    .ai-dropzone:hover, .ai-dropzone.dragover {
        border-color: #10b981;
        background: #f8fafc;
        transform: scale(1.01);
    }
    .ai-badge-score {
        width: 58px;
        height: 58px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        font-weight: 900;
        color: #fff;
        box-shadow: 0 4px 14px rgba(16,185,129,.4);
    }
</style>
@endpush

@section('content')

{{-- ── Hero ── --}}
<section class="hero-ateliers">
    <i class="bi bi-scissors hero-icon"></i>
    <div class="container text-center position-relative">
        <span class="badge rounded-pill px-3 py-2 mb-3 fw-semibold"
              style="background:rgba(255,255,255,.18); color:#fff; font-size:.8rem; letter-spacing:.5px;">
            <i class="bi bi-patch-check-fill me-1"></i> Économie Circulaire Textile
        </span>
        <h1 class="fw-bold display-5 mb-3">Trouver un atelier partenaire</h1>
        <p class="lead mb-0 mx-auto" style="max-width:600px; opacity:.9; font-size:1rem;">
            Réparez, retouchez ou customisez vos vêtements auprès de couturiers engagés pour un textile durable.
        </p>
    </div>
</section>

{{-- ── Stats Bar ── --}}
<div class="stats-bar shadow-sm">
    <div class="container">
        <div class="row g-0 divide-x" id="statsBar">
            <div class="col-4 col-md-4 stat-item border-end">
                <div class="stat-number" data-target="{{ $totalAteliers }}">0</div>
                <div class="stat-label"><i class="bi bi-shop me-1"></i>Ateliers</div>
            </div>
            <div class="col-4 col-md-4 stat-item border-end">
                <div class="stat-number" data-target="{{ $totalServices }}">0</div>
                <div class="stat-label"><i class="bi bi-tools me-1"></i>Services</div>
            </div>
            <div class="col-4 col-md-4 stat-item">
                <div class="stat-number" data-target="{{ $totalVilles }}">0</div>
                <div class="stat-label"><i class="bi bi-geo-alt me-1"></i>Villes</div>
            </div>
        </div>
    </div>
</div>

<section class="py-5 bg-light">
    <div class="container">

        {{-- ── Filtre Card ── --}}
        <div class="filter-card mb-4">
            <div class="row g-3 align-items-end" id="filterForm">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold text-secondary mb-1">
                        <i class="bi bi-search me-1 text-success"></i> Mot-clé
                    </label>
                    <input type="text" id="f-search" value="{{ $search ?? '' }}"
                           class="form-control" placeholder="Nom, quartier, spécialité…">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-bold text-secondary mb-1">
                        <i class="bi bi-geo-alt me-1 text-danger"></i> Ville
                    </label>
                    <select id="f-ville" class="form-select">
                        <option value="">Toutes les villes</option>
                        @foreach ($villes as $v)
                            <option value="{{ $v }}" {{ ($ville ?? '') === $v ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-bold text-secondary mb-1">
                        <i class="bi bi-funnel me-1 text-primary"></i> Type de service
                    </label>
                    <select id="f-type" class="form-select">
                        <option value="">Tous les types</option>
                        @foreach ($typesServices as $type)
                            <option value="{{ $type }}" {{ ($typeService ?? '') === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button id="btn-search" class="btn btn-filter flex-grow-1">
                        <i class="bi bi-funnel-fill"></i> Filtrer
                    </button>
                    <button id="btn-reset" class="btn btn-reset-filter" title="Réinitialiser les filtres">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                </div>
            </div>

            {{-- Tags filtres actifs --}}
            <div class="active-filters mt-3 d-flex flex-wrap gap-2" id="activeTags"></div>
        </div>

        {{-- ── Résultats header ── --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center">
                Ateliers disponibles
                <span class="badge rounded-pill ms-2 px-3 py-1"
                      id="result-count"
                      style="background:#0f7038; font-size:.8rem; color:#fff;">{{ $ateliers->total() }}</span>
            </h5>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <button class="action-pill-btn pill-outline-ai shadow-sm" id="btn-toggle-ai" title="Diagnostic IA de vos vêtements">
                    <i class="bi bi-stars"></i>
                    <span id="ai-toggle-label">Diagnostic IA par Photo</span>
                </button>
                <button class="action-pill-btn pill-outline-map shadow-sm" id="btn-toggle-map" title="Afficher / Masquer la carte">
                    <i class="bi bi-map-fill"></i>
                    <span id="map-toggle-label">Carte</span>
                </button>
                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="view-grid" title="Affichage Grille" style="height:36px;">
                    <i class="bi bi-grid-3x3-gap"></i>
                </button>
                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="view-list" title="Affichage Liste" style="height:36px;">
                    <i class="bi bi-list-ul"></i>
                </button>
            </div>
        </div>

        {{-- ── Section Diagnostic IA ReFashion (Option IA Vision) ── --}}
        <div id="ai-section" class="mb-4" style="display:none;">
            <div class="ai-diag-banner shadow-sm">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-5">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge rounded-pill px-3 py-1 fw-bold text-white" style="background:linear-gradient(135deg,#0f7038,#28c76f);font-size:.75rem;">
                                <i class="bi bi-stars me-1"></i> IA Vision Multimodale
                            </span>
                            <small class="text-muted">Propulsé par Google Gemini</small>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Diagnostic Intelligent de Vêtement Abîmé</h4>
                        <p class="text-muted small mb-3">
                            Téléversez la photo d'un accroc, d'un trou ou d'une fermeture cassée. Notre IA détecte la matière, estime la réparabilité et vous oriente vers les meilleurs ateliers.
                        </p>

                        {{-- Zone de téléversement --}}
                        <div class="ai-dropzone mb-2" id="aiDropzone">
                            <input type="file" id="aiFileInput" accept="image/jpeg,image/png,image/jpg,image/webp" class="d-none">
                            <div id="aiDropzonePrompt">
                                <i class="bi bi-cloud-arrow-up fs-1 text-success mb-2 d-block"></i>
                                <span class="fw-bold text-dark d-block mb-1">Glissez votre photo ici ou cliquez</span>
                                <span class="text-muted small">JPG, PNG, WEBP (Max 10 Mo)</span>
                            </div>
                            <div id="aiImagePreviewWrap" class="d-none position-relative">
                                <img id="aiImagePreview" src="" alt="Aperçu vêtement" style="max-height: 180px; width: 100%; object-fit: contain; border-radius: 12px;">
                                <button type="button" id="aiBtnChangePhoto" class="btn btn-sm btn-dark rounded-pill px-3 position-absolute bottom-0 start-50 translate-middle-x mb-2 shadow">
                                    <i class="bi bi-arrow-repeat me-1"></i> Changer la photo
                                </button>
                            </div>
                        </div>

                        <button type="button" id="btnLaunchAiDiagnosis" class="btn btn-success w-100 py-2 rounded-pill fw-bold shadow-sm" style="background:linear-gradient(135deg,#0f7038,#28c76f);border:none;" disabled>
                            <i class="bi bi-cpu me-2"></i> Lancer l'analyse IA
                        </button>
                    </div>

                    {{-- Résultat IA --}}
                    <div class="col-lg-7">
                        {{-- État initial d'attente --}}
                        <div id="aiWaitingState" class="text-center p-4 bg-white rounded-4 shadow-sm border border-light">
                            <i class="bi bi-shield-check fs-1 text-success opacity-50 mb-2 d-block"></i>
                            <h6 class="fw-bold text-dark mb-1">En attente d'une photo</h6>
                            <p class="text-muted small mb-0">Sélectionnez une photo nette de la zone abîmée pour obtenir un diagnostic complet instantané.</p>
                        </div>

                        {{-- Spinner de chargement IA --}}
                        <div id="aiLoadingState" class="text-center p-5 bg-white rounded-4 shadow-sm border border-light d-none">
                            <div class="spinner-border text-success mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
                            <h6 class="fw-bold text-dark mb-1">Analyse IA en cours...</h6>
                            <p class="text-muted small mb-0">L'IA examine les fibres, le type d'usure et calcule l'indice de réparabilité.</p>
                        </div>

                        {{-- État de Rejet IA (Pas un vêtement ou aucun dommage) --}}
                        <div id="aiRejectionCard" class="bg-white rounded-4 p-4 shadow-sm border border-light d-none text-center">
                            <div class="mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle"
                                 id="aiRejectionIconWrap"
                                 style="width: 68px; height: 68px; background: #fff4e5; color: #f59e0b;">
                                <i class="bi bi-question-circle-fill fs-1" id="aiRejectionIcon"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2" id="aiRejectionTitle">Analyse impossible</h5>
                            <p class="text-secondary small mb-3 mx-auto" style="max-width: 440px;" id="aiRejectionMessage">
                                L'image fournie ne semble pas correspondre à un vêtement ou ne présente aucune déchirure détectable.
                            </p>
                            <div class="d-inline-flex align-items-center gap-2 p-2 px-3 rounded-pill" style="background: #f1f5f9; font-size: .8rem; color: #475569;">
                                <i class="bi bi-info-circle-fill text-primary"></i>
                                <span>Conseil : Prenez une photo nette et rapprochée de la zone textile abîmée.</span>
                            </div>
                        </div>

                        {{-- Panneau Résultat IA --}}
                        <div id="aiResultCard" class="bg-white rounded-4 p-4 shadow-sm border border-light d-none">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold small mb-2 d-inline-block">
                                        ✅ Diagnostic terminé
                                    </span>
                                    <h5 class="fw-bold text-dark mb-0" id="aiGarmentType">Jean en denim</h5>
                                    <small class="text-muted" id="aiMaterialDetected">Matière : Coton épais</small>
                                </div>
                                <div class="text-center">
                                    <div class="ai-badge-score" id="aiScoreBadge" style="background: linear-gradient(135deg, #10b981, #059669);">9</div>
                                    <small class="d-block text-muted fw-bold mt-1" style="font-size: .7rem;">Réparabilité /10</small>
                                </div>
                            </div>

                            <div class="p-3 rounded-3 mb-3" style="background: #f8fafc; border-left: 4px solid #10b981;">
                                <div class="fw-bold text-dark small mb-1" id="aiDamageType">Déchirure sur couture</div>
                                <p class="text-muted small mb-0" id="aiDamageDesc"></p>
                            </div>

                            <div class="mb-3">
                                <strong class="small text-dark d-block mb-1"><i class="bi bi-lightbulb text-warning me-1"></i> Conseil de réparation :</strong>
                                <p class="small text-secondary mb-0" id="aiAdvice"></p>
                            </div>

                            <div class="d-flex align-items-center gap-2 p-2 rounded-3 mb-3" style="background:#e8f5e9; color:#0f7038; font-size:.82rem;">
                                <i class="bi bi-recycle fs-5 flex-shrink-0"></i>
                                <span id="aiEcoImpact">Impact écologique préservé</span>
                            </div>

                            <div class="border-top pt-3">
                                <div class="fw-bold small text-dark mb-2"><i class="bi bi-shop text-success me-1"></i> Ateliers partenaires recommandés pour cette retouche :</div>
                                <div class="d-flex flex-column gap-2" id="aiMatchingServicesList"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Carte Leaflet Interactive (Option C) ── --}}
        <div id="map-section" style="display:none;">
            <div class="map-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-map-fill text-success fs-5"></i>
                    <div>
                        <div class="fw-bold text-dark" style="font-size:.95rem;">Carte interactive des ateliers</div>
                        <div class="text-muted" style="font-size:.75rem;">
                            <span id="map-marker-count">{{ count($ateliersGeo) }}</span> atelier(s) géolocalisé(s) · OpenStreetMap
                        </div>
                    </div>
                </div>
                <button id="btn-locate" type="button">
                    <i class="bi bi-crosshair2"></i> Me géolocaliser
                </button>
            </div>
            <div id="leaflet-map"></div>
        </div>

        {{-- ── Loading ── --}}
        <div id="ateliers-loading">
            <div class="spinner-border spinner-green" role="status">
                <span class="visually-hidden">Chargement…</span>
            </div>
            <p class="text-muted mt-3 small">Recherche en cours…</p>
        </div>

        {{-- ── Grille ateliers ── --}}
        <div id="ateliers-grid" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
            @forelse ($ateliers as $atelier)
                @include('front.ateliers._card', compact('atelier'))
            @empty
                @include('front.ateliers._empty')
            @endforelse
        </div>

        {{-- ── Pagination ── --}}
        <div class="d-flex justify-content-center" id="pagination-wrap">
            {{ $ateliers->links('pagination::bootstrap-5') }}
        </div>

    </div>
</section>

{{-- ── Scroll to top ── --}}
<button id="scrollTop" title="Haut de page"><i class="bi bi-arrow-up"></i></button>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ─── Compteur animé ─── */
    document.querySelectorAll('[data-target]').forEach(el => {
        const target = parseInt(el.dataset.target);
        if (target === 0) return;
        let current = 0;
        const step = Math.ceil(target / 40);
        const timer = setInterval(() => {
            current = Math.min(current + step, target);
            el.textContent = current;
            if (current >= target) clearInterval(timer);
        }, 30);
    });

    /* ─── Scroll to top ─── */
    const scrollBtn = document.getElementById('scrollTop');
    window.addEventListener('scroll', () => {
        scrollBtn.classList.toggle('visible', window.scrollY > 300);
    });
    scrollBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

    /* ─── Vue grille / liste ─── */
    const grid = document.getElementById('ateliers-grid');
    document.getElementById('view-grid').addEventListener('click', () => {
        grid.classList.remove('row-cols-md-1');
        grid.classList.add('row-cols-md-2', 'row-cols-lg-3');
    });
    document.getElementById('view-list').addEventListener('click', () => {
        grid.classList.remove('row-cols-md-2', 'row-cols-lg-3');
        grid.classList.add('row-cols-md-1');
    });

    /* ─── AJAX Live Filter ─── */
    const searchInput = document.getElementById('f-search');
    const villeSelect = document.getElementById('f-ville');
    const typeSelect  = document.getElementById('f-type');
    const loading     = document.getElementById('ateliers-loading');
    const resultCount = document.getElementById('result-count');
    const activeTags  = document.getElementById('activeTags');
    const pagination  = document.getElementById('pagination-wrap');
    let debounceTimer = null;

    function getFilters() {
        return {
            search:       searchInput.value.trim(),
            ville:        villeSelect.value,
            type_service: typeSelect.value,
        };
    }

    function renderTags(f) {
        activeTags.innerHTML = '';
        if (f.search) {
            activeTags.innerHTML += `<span class="badge" data-clear="search">🔍 ${f.search} &times;</span>`;
        }
        if (f.ville) {
            activeTags.innerHTML += `<span class="badge" data-clear="ville">📍 ${f.ville} &times;</span>`;
        }
        if (f.type_service) {
            activeTags.innerHTML += `<span class="badge" data-clear="type">🧵 ${f.type_service} &times;</span>`;
        }
        activeTags.querySelectorAll('[data-clear]').forEach(badge => {
            badge.addEventListener('click', () => {
                const target = badge.dataset.clear;
                if (target === 'search') searchInput.value = '';
                if (target === 'ville')  villeSelect.value = '';
                if (target === 'type')   typeSelect.value  = '';
                fetchAteliers();
            });
        });
    }

    function fetchAteliers(page = 1) {
        const f = getFilters();
        renderTags(f);

        const params = new URLSearchParams({ ...f, page });
        const url    = `{{ route('ateliers.index') }}?${params.toString()}`;

        // Mettre à jour l'URL sans rechargement
        history.replaceState(null, '', url);

        loading.style.display = 'block';
        grid.style.opacity    = '0.3';
        pagination.style.display = 'none';

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                loading.style.display = 'none';
                grid.style.opacity    = '1';
                grid.innerHTML        = data.html;
                resultCount.textContent = data.total;
                pagination.innerHTML  = data.pagination;
                pagination.style.display = 'block';
                // Rebind pagination clicks
                bindPagination();
                // Sync carte Leaflet avec les résultats filtrés
                if (data.ateliers_geo && window.leafletMap) {
                    updateMapMarkers(data.ateliers_geo);
                }
            })
            .catch(() => {
                loading.style.display = 'none';
                grid.style.opacity    = '1';
            });
    }

    function bindPagination() {
        pagination.querySelectorAll('a[href]').forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();
                const page = new URL(link.href).searchParams.get('page') || 1;
                fetchAteliers(page);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    }

    // Déclencheur live (debounce 350ms sur le texte)
    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => fetchAteliers(), 350);
    });
    villeSelect.addEventListener('change', () => fetchAteliers());
    typeSelect.addEventListener('change',  () => fetchAteliers());

    document.getElementById('btn-search').addEventListener('click', () => fetchAteliers());
    document.getElementById('btn-reset').addEventListener('click', () => {
        searchInput.value = '';
        villeSelect.value = '';
        typeSelect.value  = '';
        fetchAteliers();
    });

    bindPagination();
    renderTags(getFilters()); // Tags initiaux

    /* ─── Diagnostic IA par Photo (Google Gemini) ─── */
    const aiSection       = document.getElementById('ai-section');
    const btnToggleAi     = document.getElementById('btn-toggle-ai');
    const aiToggleLabel   = document.getElementById('ai-toggle-label');
    const aiDropzone      = document.getElementById('aiDropzone');
    const aiFileInput     = document.getElementById('aiFileInput');
    const aiPrompt        = document.getElementById('aiDropzonePrompt');
    const aiPreviewWrap   = document.getElementById('aiImagePreviewWrap');
    const aiPreview       = document.getElementById('aiImagePreview');
    const aiBtnChange     = document.getElementById('aiBtnChangePhoto');
    const btnLaunchAi     = document.getElementById('btnLaunchAiDiagnosis');

    const aiWaitingState    = document.getElementById('aiWaitingState');
    const aiLoadingState    = document.getElementById('aiLoadingState');
    const aiResultCard      = document.getElementById('aiResultCard');
    const aiRejectionCard   = document.getElementById('aiRejectionCard');
    const aiRejectionTitle  = document.getElementById('aiRejectionTitle');
    const aiRejectionMsg    = document.getElementById('aiRejectionMessage');
    const aiRejectionIcon   = document.getElementById('aiRejectionIcon');
    const aiRejectionWrap   = document.getElementById('aiRejectionIconWrap');

    let selectedAiFile    = null;

    if (btnToggleAi && aiSection) {
        btnToggleAi.addEventListener('click', function () {
            const isVisible = aiSection.style.display !== 'none';
            if (isVisible) {
                aiSection.style.display = 'none';
                aiToggleLabel.textContent = 'Diagnostic IA par Photo';
                btnToggleAi.classList.remove('active');
            } else {
                aiSection.style.display = 'block';
                aiToggleLabel.textContent = 'Masquer le Diagnostic IA';
                btnToggleAi.classList.add('active');
                aiSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    if (aiDropzone && aiFileInput) {
        aiDropzone.addEventListener('click', (e) => {
            if (e.target !== aiBtnChange && !aiBtnChange.contains(e.target)) {
                aiFileInput.click();
            }
        });

        aiBtnChange.addEventListener('click', (e) => {
            e.stopPropagation();
            aiFileInput.click();
        });

        aiDropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            aiDropzone.classList.add('dragover');
        });

        aiDropzone.addEventListener('dragleave', () => {
            aiDropzone.classList.remove('dragover');
        });

        aiDropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            aiDropzone.classList.remove('dragover');
            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                handleFileSelect(e.dataTransfer.files[0]);
            }
        });

        aiFileInput.addEventListener('change', () => {
            if (aiFileInput.files && aiFileInput.files[0]) {
                handleFileSelect(aiFileInput.files[0]);
            }
        });
    }

    function handleFileSelect(file) {
        if (!file.type.match('image.*')) {
            alert('Veuillez sélectionner un fichier image valide (JPG, PNG, WEBP).');
            return;
        }
        selectedAiFile = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            aiPreview.src = e.target.result;
            aiPrompt.classList.add('d-none');
            aiPreviewWrap.classList.remove('d-none');
            btnLaunchAi.disabled = false;
        };
        reader.readAsDataURL(file);
    }

    if (btnLaunchAi) {
        btnLaunchAi.addEventListener('click', async function () {
            if (!selectedAiFile) return;

            btnLaunchAi.disabled = true;
            btnLaunchAi.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Analyse IA en cours...';

            aiWaitingState.classList.add('d-none');
            aiResultCard.classList.add('d-none');
            if (aiRejectionCard) aiRejectionCard.classList.add('d-none');
            aiLoadingState.classList.remove('d-none');

            const formData = new FormData();
            formData.append('photo', selectedAiFile);

            try {
                const response = await fetch("{{ route('ateliers.ai.diagnose') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const json = await response.json();

                if (json.success && json.data) {
                    renderAiResult(json.data);
                } else {
                    alert(json.message || 'Une erreur est survenue lors de l\'analyse IA.');
                    aiWaitingState.classList.remove('d-none');
                }
            } catch (err) {
                console.error(err);
                alert('Erreur lors de la communication avec le serveur IA.');
                aiWaitingState.classList.remove('d-none');
            } finally {
                aiLoadingState.classList.add('d-none');
                btnLaunchAi.disabled = false;
                btnLaunchAi.innerHTML = '<i class="bi bi-cpu me-2"></i> Lancer l\'analyse IA';
            }
        });
    }

    function renderAiResult(data) {
        // Cas 1 : Rejet (Pas un vêtement ou aucun dommage textile)
        if (data.is_valid_garment_damage === false) {
            aiResultCard.classList.add('d-none');
            if (aiRejectionCard) {
                if (data.rejection_reason === 'not_a_garment') {
                    aiRejectionTitle.textContent = 'Objet non textile ou non reconnu';
                    aiRejectionWrap.style.background = '#fee2e2';
                    aiRejectionWrap.style.color = '#ef4444';
                    aiRejectionIcon.className = 'bi bi-x-octagon-fill fs-1';
                } else {
                    aiRejectionTitle.textContent = 'Aucun dommage détecté';
                    aiRejectionWrap.style.background = '#e0f2fe';
                    aiRejectionWrap.style.color = '#0284c7';
                    aiRejectionIcon.className = 'bi bi-check-circle-fill fs-1';
                }
                aiRejectionMsg.textContent = data.rejection_message || 'Impossible d\'identifier une déchirure ou un dommage nécessitant retouche.';
                aiRejectionCard.classList.remove('d-none');
            }
            return;
        }

        // Cas 2 : Vêtement abîmé valide
        if (aiRejectionCard) aiRejectionCard.classList.add('d-none');

        document.getElementById('aiGarmentType').textContent = data.garment_type || 'Vêtement analysé';
        document.getElementById('aiMaterialDetected').textContent = 'Matière détectée : ' + (data.material_detected || 'Textile standard');
        document.getElementById('aiDamageType').textContent = (data.damage_type || 'Dégât détecté') + (data.severity ? ' (' + data.severity + ')' : '');
        document.getElementById('aiDamageDesc').textContent = data.damage_description || '';
        document.getElementById('aiAdvice').textContent = data.repair_advice || data.suggested_repair || '';
        document.getElementById('aiEcoImpact').textContent = data.eco_impact_benefit || 'Économie de ressources textiles';

        const score = data.repairability_score || 8;
        const scoreBadge = document.getElementById('aiScoreBadge');
        scoreBadge.textContent = score;

        if (score >= 8) {
            scoreBadge.style.background = 'linear-gradient(135deg, #10b981, #059669)';
        } else if (score >= 5) {
            scoreBadge.style.background = 'linear-gradient(135deg, #f59e0b, #d97706)';
        } else {
            scoreBadge.style.background = 'linear-gradient(135deg, #ef4444, #b91c1c)';
        }

        const servicesWrap = document.getElementById('aiMatchingServicesList');
        servicesWrap.innerHTML = '';

        if (data.matching_ateliers && data.matching_ateliers.length > 0) {
            data.matching_ateliers.slice(0, 3).forEach(a => {
                const card = document.createElement('div');
                card.className = 'd-flex align-items-center justify-content-between p-2 px-3 rounded-3 border bg-light w-100';
                card.innerHTML = `
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-shop text-success fs-5"></i>
                        <div>
                            <strong class="d-block small text-dark">${a.nom}</strong>
                            <small class="text-muted"><i class="bi bi-geo-alt text-danger me-1"></i>${a.ville} ${a.adresse ? '· ' + a.adresse : ''}</small>
                        </div>
                    </div>
                    <a href="${a.url}" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold text-white text-decoration-none shadow-sm" style="background:linear-gradient(135deg,#0f7038,#1a9e52); border:none; font-size:.78rem;">
                        Consulter & Devis &rarr;
                    </a>
                `;
                servicesWrap.appendChild(card);
            });
        } else if (data.matching_services && data.matching_services.length > 0) {
            data.matching_services.slice(0, 2).forEach(s => {
                const card = document.createElement('div');
                card.className = 'd-flex align-items-center justify-content-between p-2 px-3 rounded-3 border bg-light w-100';
                card.innerHTML = `
                    <div>
                        <strong class="d-block small text-dark">${s.nom}</strong>
                        <small class="text-muted">${s.atelier_nom} · ${s.atelier_ville} · ~${s.tarif_estime} DT</small>
                    </div>
                    <a href="${s.atelier_url}" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold text-white text-decoration-none shadow-sm" style="background:linear-gradient(135deg,#0f7038,#1a9e52); border:none; font-size:.78rem;">
                        Voir l'atelier &rarr;
                    </a>
                `;
                servicesWrap.appendChild(card);
            });
        } else {
            servicesWrap.innerHTML = '<span class="text-muted small">Consultez le catalogue pour choisir votre atelier partenaire.</span>';
        }

        aiResultCard.classList.remove('d-none');
    }
});
</script>

{{-- ── Leaflet JS (chargé avant le script carte) ── --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@push('scripts')
{{-- ── Carte Interactive Leaflet (Option C) ── --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Données géographiques depuis PHP
    const ateliersGeo = @json($ateliersGeo);

    let map         = null;
    let markers     = [];
    let mapInited   = false;
    let greenIcon   = null;
    let userIcon    = null;
    let userMarker  = null;

    const mapSection  = document.getElementById('map-section');
    const toggleBtn   = document.getElementById('btn-toggle-map');
    const toggleLabel = document.getElementById('map-toggle-label');
    const locateBtn   = document.getElementById('btn-locate');

    if (!mapSection || !toggleBtn) return;

    // Construire le HTML du popup
    function buildPopup(a) {
        const stars = Array.from({length: 5}, (_, i) =>
            i < Math.round(a.moyenne_note) ? '★' : '☆'
        ).join('');
        return `<div class="map-popup">
            <div class="map-popup-title">${a.nom}</div>
            <div class="map-popup-ville">📍 ${a.ville} · ${a.adresse}</div>
            <div class="map-popup-stars" style="color:#f5a623;">${stars}
                <span style="color:#6c757d;font-size:.75rem;margin-left:4px;">
                    ${a.moyenne_note > 0 ? a.moyenne_note + '/5' : 'Nouveau'}
                    ${a.nombre_avis > 0 ? '(' + a.nombre_avis + ' avis)' : ''}
                </span>
            </div>
            <div class="map-popup-services">🧵 ${a.services_count} service(s) disponible(s)</div>
            <a href="${a.url}" class="btn-popup">Voir cet atelier →</a>
        </div>`;
    }

    // Placer / rafraîchir les marqueurs
    function updateMapMarkers(data) {
        if (!map) return;
        markers.forEach(m => map.removeLayer(m));
        markers = [];
        if (!data || data.length === 0) return;

        const bounds = [];
        data.forEach(a => {
            if (a.latitude == null || a.longitude == null) return;
            const m = L.marker([a.latitude, a.longitude], { icon: greenIcon })
                .bindPopup(buildPopup(a), { maxWidth: 260 })
                .addTo(map);
            markers.push(m);
            bounds.push([a.latitude, a.longitude]);
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [40, 40], maxZoom: 13 });
        }
        const counter = document.getElementById('map-marker-count');
        if (counter) counter.textContent = data.length;
    }

    // Exposer globalement pour sync AJAX
    window.leafletMap       = null;
    window.updateMapMarkers = updateMapMarkers;

    // Initialiser la carte (une seule fois)
    function initMap() {
        if (mapInited) return;
        mapInited = true;

        // Créer les icônes ici (après que Leaflet soit chargé)
        greenIcon = L.divIcon({
            className: '',
            html: `<div style="
                width:32px;height:32px;
                background:linear-gradient(135deg,#0f7038,#28c76f);
                border-radius:50% 50% 50% 0;
                transform:rotate(-45deg);
                border:3px solid #fff;
                box-shadow:0 2px 8px rgba(15,112,56,.5);
            "></div>`,
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -34],
        });

        userIcon = L.divIcon({
            className: '',
            html: `<div style="
                width:28px;height:28px;
                background:linear-gradient(135deg,#1e88e5,#42a5f5);
                border-radius:50%;
                border:3px solid #fff;
                box-shadow:0 2px 8px rgba(30,136,229,.6);
            "></div>`,
            iconSize: [28, 28],
            iconAnchor: [14, 14],
        });

        map = L.map('leaflet-map', {
            center: [36.0, 9.5],
            zoom: 7,
        });

        window.leafletMap = map;

        const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            subdomains: ['a', 'b', 'c'],
            maxZoom: 19,
            crossOrigin: true
        });

        tileLayer.on('tileerror', function(error, tile) {
            console.warn('Erreur chargement tuile OSM, bascule vers CartoDB:', error);
            // Fallback silencieux vers CartoDB si OpenStreetMap bloque
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                subdomains: 'abcd',
                maxZoom: 19
            }).addTo(map);
        });

        tileLayer.addTo(map);

        updateMapMarkers(ateliersGeo);
    }

    // ── Toggle carte ──
    toggleBtn.addEventListener('click', function () {
        const isVisible = mapSection.style.display !== 'none';
        if (isVisible) {
            mapSection.style.display = 'none';
            toggleLabel.textContent = 'Carte';
            toggleBtn.classList.remove('active');
        } else {
            mapSection.style.display = 'block';
            toggleLabel.textContent = 'Masquer';
            toggleBtn.classList.add('active');
            initMap();
            // Multi-pass invalidateSize pour synchroniser parfaitement l'affichage du conteneur Leaflet
            requestAnimationFrame(() => {
                if (map) map.invalidateSize(true);
            });
            setTimeout(() => {
                if (map) {
                    map.invalidateSize(true);
                    if (markers.length > 0) {
                        const bounds = markers.map(m => m.getLatLng());
                        map.fitBounds(bounds, { padding: [40, 40], maxZoom: 13 });
                    }
                }
            }, 250);
            setTimeout(() => {
                if (map) map.invalidateSize(true);
            }, 600);
        }
    });

    // ── Géolocalisation ──
    if (locateBtn) {
        locateBtn.addEventListener('click', function () {
            if (!map) {
                mapSection.style.display = 'block';
                toggleLabel.textContent = 'Masquer';
                initMap();
                setTimeout(() => {
                    map.invalidateSize();
                    geolocate();
                }, 200);
                return;
            }
            geolocate();
        });
    }

    function geolocate() {
        if (!locateBtn) return;
        locateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Localisation…';
        locateBtn.disabled  = true;

        if (!navigator.geolocation) {
            alert('Géolocalisation non supportée par votre navigateur.');
            locateBtn.innerHTML = '<i class="bi bi-crosshair2"></i> Me géolocaliser';
            locateBtn.disabled  = false;
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function (pos) {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;

                if (userMarker) map.removeLayer(userMarker);
                userMarker = L.marker([lat, lng], { icon: userIcon })
                    .addTo(map)
                    .bindPopup('<div style="padding:8px;font-weight:600;color:#1e88e5;">📍 Vous êtes ici</div>')
                    .openPopup();

                // Atelier le plus proche
                let nearest = null, minDist = Infinity;
                (ateliersGeo || []).forEach(a => {
                    if (a.latitude == null) return;
                    const d = Math.hypot(a.latitude - lat, a.longitude - lng);
                    if (d < minDist) { minDist = d; nearest = a; }
                });

                if (nearest) {
                    map.fitBounds([[lat, lng], [nearest.latitude, nearest.longitude]], { padding: [60, 60], maxZoom: 13 });
                    const nm = markers.find(m => {
                        const ll = m.getLatLng();
                        return Math.abs(ll.lat - nearest.latitude) < 0.001 && Math.abs(ll.lng - nearest.longitude) < 0.001;
                    });
                    if (nm) setTimeout(() => nm.openPopup(), 700);
                } else {
                    map.setView([lat, lng], 12);
                }

                locateBtn.innerHTML = '✅ Géolocalisé !';
                setTimeout(() => {
                    locateBtn.innerHTML = '<i class="bi bi-crosshair2"></i> Me géolocaliser';
                    locateBtn.disabled  = false;
                }, 3000);
            },
            function () {
                alert('Impossible d\'accéder à votre position.');
                locateBtn.innerHTML = '<i class="bi bi-crosshair2"></i> Me géolocaliser';
                locateBtn.disabled  = false;
            },
            { timeout: 10000, maximumAge: 60000 }
        );
    }
});
</script>
@endpush
