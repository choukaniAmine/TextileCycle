@extends('layouts.front')

@section('title', 'Ateliers & Services de Réparation')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
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
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 24px rgba(0,0,0,.06);
        margin-top: -30px;
        position: relative;
        z-index: 10;
    }
    .filter-card .form-control,
    .filter-card .form-select {
        border-radius: 10px;
        border: 1.5px solid #dee2e6;
        padding: 10px 14px;
        font-size: .9rem;
        transition: border-color .2s, box-shadow .2s;
    }
    .filter-card .form-control:focus,
    .filter-card .form-select:focus {
        border-color: #0f7038;
        box-shadow: 0 0 0 3px rgba(15,112,56,.12);
    }
    .btn-filter {
        background: linear-gradient(135deg,#0f7038,#28c76f);
        border: none;
        color: #fff;
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 600;
        transition: opacity .2s, transform .15s;
    }
    .btn-filter:hover { opacity:.9; transform: translateY(-1px); }

    /* ── Tags filtres actifs ── */
    .active-filters .badge {
        font-size:.8rem; padding:.45em .8em; border-radius:50px;
        background:#e8f5e9; color:#0f7038; border:1px solid #c8e6c9;
        cursor:pointer; transition:background .2s;
    }
    .active-filters .badge:hover { background:#c8e6c9; }

    /* ── Atelier Card ── */
    .atelier-card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,.07);
        transition: transform .25s, box-shadow .25s;
        height: 100%;
    }
    .atelier-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 32px rgba(15,112,56,.18);
    }
    .atelier-card .card-img-wrap {
        position: relative;
        height: 190px;
        background: linear-gradient(135deg,#e8f5e9,#c8e6c9);
        overflow: hidden;
    }
    .atelier-card .card-img-wrap img {
        width:100%; height:100%; object-fit:cover;
        transition: transform .4s;
    }
    .atelier-card:hover .card-img-wrap img { transform: scale(1.06); }
    .atelier-card .card-img-wrap .placeholder-icon {
        position:absolute; inset:0;
        display:flex; flex-direction:column;
        align-items:center; justify-content:center;
        color:#0f7038; opacity:.5;
    }
    .atelier-card .ville-badge {
        position: absolute; top:12px; right:12px;
        background:#fff; color:#333;
        border-radius:50px; padding:4px 10px;
        font-size:.75rem; font-weight:600;
        box-shadow: 0 2px 8px rgba(0,0,0,.15);
    }
    .atelier-card .type-badge {
        position:absolute; top:12px; left:12px;
        background:rgba(15,112,56,.85); color:#fff;
        border-radius:50px; padding:3px 10px;
        font-size:.7rem; font-weight:700; letter-spacing:.3px;
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
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,.06);
        margin-bottom: 28px;
    }
    #map-section .map-header {
        padding: 16px 20px;
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
        width: 100%;
        z-index: 1;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 12px !important;
        box-shadow: 0 4px 20px rgba(0,0,0,.15) !important;
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
        background: linear-gradient(135deg,#0f7038,#28c76f);
        color: #fff !important;
        border-radius: 8px;
        padding: 6px 12px;
        text-align: center;
        text-decoration: none;
        font-size: .82rem;
        font-weight: 600;
        margin-top: 10px;
    }
    #btn-locate {
        background: linear-gradient(135deg,#0f7038,#28c76f);
        border: none; color:#fff;
        border-radius: 10px; padding: 8px 16px;
        font-weight: 600; font-size: .88rem;
        cursor: pointer; transition: opacity .2s;
        display: flex; align-items: center; gap: 6px;
    }
    #btn-locate:hover { opacity: .85; }
    .map-toggle-btn {
        cursor: pointer;
        color: #0f7038;
        font-size: .85rem;
        font-weight: 600;
        border: none;
        background: none;
        display: flex;
        align-items: center;
        gap: 5px;
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
                    <button id="btn-search" class="btn btn-filter w-100">
                        <i class="bi bi-search me-1"></i> Filtrer
                    </button>
                    <button id="btn-reset" class="btn btn-outline-secondary px-3" title="Réinitialiser" style="border-radius:10px;">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            {{-- Tags filtres actifs --}}
            <div class="active-filters mt-3 d-flex flex-wrap gap-2" id="activeTags"></div>
        </div>

        {{-- ── Résultats header ── --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0 text-dark">
                Ateliers disponibles
                <span class="badge rounded-pill ms-1 px-3 py-1"
                      id="result-count"
                      style="background:#0f7038; font-size:.8rem;">{{ $ateliers->total() }}</span>
            </h5>
            <div class="d-flex gap-2 align-items-center">
                <button class="map-toggle-btn" id="btn-toggle-map" title="Afficher / Masquer la carte">
                    <i class="bi bi-map-fill text-success"></i>
                    <span id="map-toggle-label">Carte</span>
                </button>
                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="view-grid" title="Grille">
                    <i class="bi bi-grid-3x3-gap"></i>
                </button>
                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="view-list" title="Liste">
                    <i class="bi bi-list-ul"></i>
                </button>
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
});
</script>

{{-- ── Leaflet JS & Carte Interactive (Option C) ── --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV/XN2GqaI=" crossorigin=""></script>
<script>
(function () {
    // Données géographiques depuis PHP
    const ateliersGeo = @json($ateliersGeo);

    if (!ateliersGeo || ateliersGeo.length === 0) return;

    let map         = null;
    let markers     = [];
    let mapInited   = false;
    const mapSection = document.getElementById('map-section');
    const toggleBtn  = document.getElementById('btn-toggle-map');
    const toggleLabel = document.getElementById('map-toggle-label');

    // Icône personnalisée verte
    const greenIcon = L.divIcon({
        className: '',
        html: `<div style="
            width:32px; height:32px;
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

    const userIcon = L.divIcon({
        className: '',
        html: `<div style="
            width:28px; height:28px;
            background:linear-gradient(135deg,#1e88e5,#42a5f5);
            border-radius:50%;
            border:3px solid #fff;
            box-shadow:0 2px 8px rgba(30,136,229,.6);
        "></div>`,
        iconSize: [28, 28],
        iconAnchor: [14, 14],
    });

    // Construire le HTML du popup
    function buildPopup(a) {
        const stars = Array.from({length: 5}, (_, i) =>
            `<i>${i < Math.round(a.moyenne_note) ? '★' : '☆'}</i>`
        ).join('');

        return `<div class="map-popup">
            <div class="map-popup-title">${a.nom}</div>
            <div class="map-popup-ville"><i>📍</i> ${a.ville} · ${a.adresse}</div>
            <div class="map-popup-stars">${stars}
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
        // Supprimer anciens marqueurs
        markers.forEach(m => map.removeLayer(m));
        markers = [];

        if (!data || data.length === 0) return;

        const bounds = [];
        data.forEach(a => {
            if (!a.latitude || !a.longitude) return;
            const m = L.marker([a.latitude, a.longitude], { icon: greenIcon })
                .bindPopup(buildPopup(a), { maxWidth: 260 })
                .addTo(map);
            markers.push(m);
            bounds.push([a.latitude, a.longitude]);
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [40, 40], maxZoom: 13 });
        }

        // Mettre à jour le compteur
        const counter = document.getElementById('map-marker-count');
        if (counter) counter.textContent = data.length;
    }

    // Exposer globalement pour le AJAX filter sync
    window.leafletMap    = null;
    window.updateMapMarkers = updateMapMarkers;

    // Initialiser la carte (une seule fois, au premier affichage)
    function initMap() {
        if (mapInited) return;
        mapInited = true;

        map = L.map('leaflet-map', {
            center: [36.8065, 10.1815],
            zoom: 7,
            zoomControl: true,
        });

        window.leafletMap = map;

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 18,
        }).addTo(map);

        updateMapMarkers(ateliersGeo);
    }

    // Toggle affichage carte
    toggleBtn.addEventListener('click', function () {
        const visible = mapSection.style.display !== 'none';
        if (visible) {
            mapSection.style.display = 'none';
            toggleLabel.textContent = 'Carte';
            toggleBtn.style.color = '#0f7038';
        } else {
            mapSection.style.display = 'block';
            toggleLabel.textContent = 'Masquer';
            toggleBtn.style.color = '#dc3545';
            // Initialiser la carte si pas encore fait
            initMap();
            // Invalider la taille Leaflet (car la div était cachée)
            setTimeout(() => map && map.invalidateSize(), 100);
        }
    });

    // Géolocalisation : "Me géolocaliser"
    let userMarker = null;
    document.getElementById('btn-locate').addEventListener('click', function () {
        if (!map) {
            // Ouvrir la carte d'abord
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

    function geolocate() {
        const btn = document.getElementById('btn-locate');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Localisation…';
        btn.disabled = true;

        if (!navigator.geolocation) {
            alert('La géolocalisation n\'est pas supportée par votre navigateur.');
            btn.innerHTML = '<i class="bi bi-crosshair2"></i> Me géolocaliser';
            btn.disabled = false;
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function (pos) {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;

                // Retirer l'ancien marqueur utilisateur
                if (userMarker) map.removeLayer(userMarker);

                userMarker = L.marker([lat, lng], { icon: userIcon })
                    .addTo(map)
                    .bindPopup('<div style="padding:8px;font-weight:600;color:#1e88e5;">📍 Vous êtes ici</div>')
                    .openPopup();

                // Calculer l'atelier le plus proche
                let nearest = null;
                let minDist = Infinity;
                ateliersGeo.forEach(a => {
                    if (!a.latitude) return;
                    const d = Math.sqrt(Math.pow(a.latitude - lat, 2) + Math.pow(a.longitude - lng, 2));
                    if (d < minDist) { minDist = d; nearest = a; }
                });

                if (nearest) {
                    // Centrer entre l'utilisateur et l'atelier le plus proche
                    map.fitBounds([[lat, lng], [nearest.latitude, nearest.longitude]], { padding: [60, 60], maxZoom: 13 });
                    // Ouvrir le popup de l'atelier le plus proche
                    const nearestMarker = markers.find(m => {
                        const ll = m.getLatLng();
                        return Math.abs(ll.lat - nearest.latitude) < 0.0001 && Math.abs(ll.lng - nearest.longitude) < 0.0001;
                    });
                    if (nearestMarker) {
                        setTimeout(() => nearestMarker.openPopup(), 600);
                    }
                } else {
                    map.setView([lat, lng], 12);
                }

                btn.innerHTML = '✅ Géolocalisé !';
                setTimeout(() => {
                    btn.innerHTML = '<i class="bi bi-crosshair2"></i> Me géolocaliser';
                    btn.disabled = false;
                }, 3000);
            },
            function () {
                alert('Impossible d\'accéder à votre position. Veuillez autoriser la géolocalisation.');
                btn.innerHTML = '<i class="bi bi-crosshair2"></i> Me géolocaliser';
                btn.disabled = false;
            },
            { timeout: 10000, maximumAge: 60000 }
        );
    }
})();
</script>
@endpush
