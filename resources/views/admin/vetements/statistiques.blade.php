@extends('layouts.admin')
@section('title', 'Statistiques des vêtements')
@section('content')

@php
    // Indicateurs calculés à partir des données du contrôleur
    $topCategorie  = $parCategorie->sortDesc();
    $nomTopCat     = $topCategorie->keys()->first();
    $nbTopCat      = $topCategorie->first() ?? 0;
    $partTopCat    = $total > 0 ? round($nbTopCat / $total * 100) : 0;

    $topType       = $parType->sortDesc();
    $nomTopType    = $topType->keys()->first();

    $meilleurMois  = $parMois->sortDesc();
    $nomMeilleurMois = $meilleurMois->first() > 0 ? $meilleurMois->keys()->first() : null;

    $valeursMois   = $parMois->values();
    $ceMois        = (int) $valeursMois->last();
    $moisPrecedent = (int) $valeursMois->get($valeursMois->count() - 2, 0);
    $variation     = $moisPrecedent > 0 ? round(($ceMois - $moisPrecedent) / $moisPrecedent * 100) : null;

    $douches       = (int) round($eau / 60);   // ~60 L par douche
@endphp

<style>
    .st { --vert: #1e9e5a; --vert-fonce: #0f6b3c; --bleu: #2f80ed; --texte: #1f2d3d; --gris: #6b7785; }
    .st-hero { background: linear-gradient(135deg, #0f6b3c 0%, #1e9e5a 55%, #34c38f 100%); border-radius: 16px; color: #fff;
        padding: 28px 32px; margin-bottom: 24px; position: relative; overflow: hidden; }
    .st-hero::after { content: ""; position: absolute; right: -60px; top: -60px; width: 260px; height: 260px;
        background: rgba(255,255,255,.08); border-radius: 50%; }
    .st-hero h2 { font-weight: 700; margin: 0 0 6px; font-size: 1.7rem; }
    .st-hero p { margin: 0; opacity: .9; max-width: 640px; }
    .st-hero .btn-hero { background: rgba(255,255,255,.18); color: #fff; border: 1px solid rgba(255,255,255,.4); border-radius: 10px; }
    .st-hero .btn-hero:hover { background: #fff; color: var(--vert-fonce); }

    .st-kpi { background: #fff; border-radius: 14px; padding: 20px; height: 100%; box-shadow: 0 2px 12px rgba(31,45,61,.07);
        display: flex; align-items: center; gap: 16px; transition: transform .2s, box-shadow .2s; border: 1px solid #eef1f4; }
    .st-kpi:hover { transform: translateY(-3px); box-shadow: 0 8px 22px rgba(31,45,61,.12); }
    .st-ico { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center;
        font-size: 24px; flex-shrink: 0; }
    .st-kpi .val { font-size: 1.75rem; font-weight: 700; color: var(--texte); line-height: 1.1; }
    .st-kpi .val small { font-size: .95rem; font-weight: 600; color: var(--gris); margin-left: 3px; }
    .st-kpi .lib { color: var(--gris); font-size: .85rem; margin-top: 3px; }
    .st-badge { display: inline-block; margin-top: 6px; padding: 2px 9px; border-radius: 20px; font-size: .75rem; font-weight: 600; }
    .st-up { background: #e3f6ec; color: #168a4a; } .st-down { background: #fde8e8; color: #c0392b; } .st-neutre { background: #eef1f4; color: var(--gris); }

    .st-card { background: #fff; border-radius: 14px; box-shadow: 0 2px 12px rgba(31,45,61,.07); border: 1px solid #eef1f4;
        margin-bottom: 24px; height: calc(100% - 24px); }
    .st-card-h { padding: 16px 20px 0; display: flex; justify-content: space-between; align-items: flex-start; }
    .st-card-h h3 { font-size: 1.05rem; font-weight: 700; color: var(--texte); margin: 0; }
    .st-card-h span { font-size: .8rem; color: var(--gris); }
    .st-card-b { padding: 14px 20px 20px; position: relative; }
    .st-chart { position: relative; height: 300px; }

    .st-insight { display: flex; gap: 12px; align-items: flex-start; padding: 14px 0; border-bottom: 1px solid #f0f2f5; }
    .st-insight:last-child { border-bottom: 0; }
    .st-insight .st-ico { width: 40px; height: 40px; font-size: 17px; border-radius: 10px; }
    .st-insight b { color: var(--texte); display: block; font-size: .95rem; }
    .st-insight small { color: var(--gris); }

    .st-eq { background: linear-gradient(135deg, #eaf6ff, #e6f9f0); border-radius: 14px; padding: 20px; margin-bottom: 24px; border: 1px solid #dcebf5; }
    .st-eq h4 { font-size: 1rem; font-weight: 700; color: var(--texte); margin-bottom: 14px; }
    .st-eq-item { text-align: center; padding: 6px 10px; }
    .st-eq-item .n { font-size: 1.6rem; font-weight: 700; color: var(--vert-fonce); }
    .st-eq-item .t { font-size: .82rem; color: var(--gris); }

    .st-vide { text-align: center; padding: 60px 20px; color: var(--gris); }
    .st-vide i { font-size: 54px; color: #c9d3dc; margin-bottom: 14px; }

    @media print {
        .st-hero .btn-hero, .main-sidebar, .main-header, .main-footer { display: none !important; }
        .content-wrapper { margin-left: 0 !important; }
        .st-card, .st-kpi { box-shadow: none; break-inside: avoid; }
    }
</style>

<div class="st">

    {{-- Bandeau --}}
    <div class="st-hero d-flex justify-content-between align-items-center flex-wrap">
        <div class="mb-2 mb-md-0">
            <h2><i class="fas fa-leaf mr-2"></i>Impact &amp; statistiques</h2>
            <p>Vue d'ensemble des vêtements de la plateforme et de leur impact environnemental estimé.
               Mise à jour le {{ now()->translatedFormat('d F Y à H:i') }}.</p>
        </div>
        <div>
            <a href="{{ route('admin.vetements.index') }}" class="btn btn-hero mr-1"><i class="fas fa-list"></i> Liste</a>
            <button type="button" class="btn btn-hero" onclick="window.print()"><i class="fas fa-print"></i> Imprimer</button>
        </div>
    </div>

    @if($total === 0)
        <div class="st-card"><div class="st-vide">
            <i class="fas fa-tshirt d-block"></i>
            <h5>Aucun vêtement pour le moment</h5>
            <p>Ajoutez ou importez des vêtements pour voir apparaître les statistiques.</p>
            <a href="{{ route('admin.vetements.import') }}" class="btn btn-success"><i class="fas fa-file-csv"></i> Importer un CSV</a>
        </div></div>
    @else

    {{-- Indicateurs clés --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-3 mb-xl-0">
            <div class="st-kpi">
                <div class="st-ico" style="background:#e8f1fe;color:#2f80ed"><i class="fas fa-tshirt"></i></div>
                <div>
                    <div class="val"><span data-count="{{ $total }}">{{ $total }}</span></div>
                    <div class="lib">Vêtements sur la plateforme</div>
                    @if($variation === null)
                        <span class="st-badge st-neutre">{{ $ceMois }} ajouté(s) ce mois</span>
                    @else
                        <span class="st-badge {{ $variation >= 0 ? 'st-up' : 'st-down' }}">
                            <i class="fas fa-arrow-{{ $variation >= 0 ? 'up' : 'down' }}"></i> {{ abs($variation) }} % vs mois dernier
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-3 mb-xl-0">
            <div class="st-kpi">
                <div class="st-ico" style="background:#e3f6ec;color:#1e9e5a"><i class="fas fa-weight-hanging"></i></div>
                <div>
                    <div class="val"><span data-count="{{ $kg }}" data-dec="1">{{ $kg }}</span><small>kg</small></div>
                    <div class="lib">Textile sauvé</div>
                    <span class="st-badge st-neutre">Estimation</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-3 mb-xl-0">
            <div class="st-kpi">
                <div class="st-ico" style="background:#e0f4fb;color:#0aa2c0"><i class="fas fa-tint"></i></div>
                <div>
                    <div class="val"><span data-count="{{ $eau }}">{{ $eau }}</span><small>L</small></div>
                    <div class="lib">Eau économisée</div>
                    <span class="st-badge st-neutre">Estimation</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="st-kpi">
                <div class="st-ico" style="background:#fff4e0;color:#f2994a"><i class="fas fa-user-clock"></i></div>
                <div>
                    <div class="val"><span data-count="{{ $joursEau }}">{{ $joursEau }}</span><small>jours</small></div>
                    <div class="lib">d'eau pour une personne</div>
                    <span class="st-badge st-neutre">~150 L / jour</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphiques principaux --}}
    <div class="row">
        <div class="col-lg-7">
            <div class="st-card">
                <div class="st-card-h"><h3>Évolution des ajouts</h3><span>12 derniers mois</span></div>
                <div class="st-card-b"><div class="st-chart"><canvas id="chartMois"></canvas></div></div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="st-card">
                <div class="st-card-h"><h3>À retenir</h3><span>Points clés</span></div>
                <div class="st-card-b">
                    <div class="st-insight">
                        <div class="st-ico" style="background:#e8f1fe;color:#2f80ed"><i class="fas fa-crown"></i></div>
                        <div><b>{{ $nomTopCat }}</b><small>Catégorie la plus représentée : {{ $nbTopCat }} vêtement(s), soit {{ $partTopCat }} % du total.</small></div>
                    </div>
                    <div class="st-insight">
                        <div class="st-ico" style="background:#e3f6ec;color:#1e9e5a"><i class="fas fa-recycle"></i></div>
                        <div><b>{{ $nomTopType }}</b><small>Type le plus fréquent : {{ $topType->first() }} vêtement(s).</small></div>
                    </div>
                    <div class="st-insight">
                        <div class="st-ico" style="background:#fff4e0;color:#f2994a"><i class="fas fa-calendar-check"></i></div>
                        <div><b>{{ $nomMeilleurMois ?? 'Aucune donnée' }}</b><small>Meilleur mois d'ajouts sur la période{{ $nomMeilleurMois ? ' : '.$meilleurMois->first().' vêtement(s)' : '' }}.</small></div>
                    </div>
                    <div class="st-insight">
                        <div class="st-ico" style="background:#f3e8fd;color:#9b51e0"><i class="fas fa-th-large"></i></div>
                        <div><b>{{ $parCategorie->filter()->count() }} catégorie(s) active(s)</b><small>sur {{ $parCategorie->count() }} catégorie(s) au total.</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="st-card">
                <div class="st-card-h"><h3>Vêtements par catégorie</h3><span>Classement</span></div>
                <div class="st-card-b"><div class="st-chart"><canvas id="chartCategorie"></canvas></div></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="st-card">
                <div class="st-card-h"><h3>Par état</h3></div>
                <div class="st-card-b"><div class="st-chart"><canvas id="chartEtat"></canvas></div></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="st-card">
                <div class="st-card-h"><h3>Par type</h3></div>
                <div class="st-card-b"><div class="st-chart"><canvas id="chartType"></canvas></div></div>
            </div>
        </div>
    </div>

    {{-- Équivalences --}}
    <div class="st-eq">
        <h4><i class="fas fa-globe-africa mr-1"></i> Ce que cela représente</h4>
        <div class="row">
            <div class="col-md-4 st-eq-item"><div class="n">{{ number_format($douches, 0, ',', ' ') }}</div><div class="t">douches d'eau préservées</div></div>
            <div class="col-md-4 st-eq-item"><div class="n">{{ number_format($joursEau, 0, ',', ' ') }}</div><div class="t">jours de consommation d'eau d'une personne</div></div>
            <div class="col-md-4 st-eq-item"><div class="n">{{ number_format($kg, 1, ',', ' ') }} kg</div><div class="t">de textile détournés des déchets</div></div>
        </div>
    </div>

    <p class="text-muted small mb-0">
        <i class="fas fa-info-circle"></i> Les valeurs d'impact sont des estimations basées sur des coefficients moyens par catégorie
        (poids moyen et eau nécessaire à la fabrication, par exemple un jean ≈ 7 500 L, une douche ≈ 60 L).
    </p>
    @endif
</div>

@if($total > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    const palette = ['#1e9e5a', '#2f80ed', '#f2994a', '#9b51e0', '#eb5757', '#0aa2c0', '#f2c94c', '#56ccf2', '#bb6bd9', '#6b7785'];
    const cat = @json($parCategorie->sortDesc());
    const mois = @json($parMois);
    const etat = @json($parEtat);
    const type = @json($parType);

    Chart.defaults.font.family = "'Source Sans Pro', -apple-system, 'Segoe UI', Roboto, sans-serif";
    Chart.defaults.color = '#6b7785';
    Chart.defaults.plugins.tooltip.backgroundColor = '#1f2d3d';
    Chart.defaults.plugins.tooltip.padding = 10;
    Chart.defaults.plugins.tooltip.cornerRadius = 8;
    Chart.defaults.maintainAspectRatio = false;

    // Texte au centre des anneaux
    const centre = {
        id: 'centre',
        afterDraw(chart) {
            const { ctx, chartArea: { left, right, top, bottom } } = chart;
            const x = (left + right) / 2, y = (top + bottom) / 2;
            const total = chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
            ctx.save();
            ctx.textAlign = 'center';
            ctx.fillStyle = '#1f2d3d'; ctx.font = '700 26px sans-serif';
            ctx.fillText(total, x, y + 4);
            ctx.fillStyle = '#6b7785'; ctx.font = '12px sans-serif';
            ctx.fillText('vêtements', x, y + 22);
            ctx.restore();
        }
    };

    const anneau = (id, donnees, couleurs) => new Chart(document.getElementById(id), {
        type: 'doughnut',
        data: { labels: Object.keys(donnees), datasets: [{ data: Object.values(donnees), backgroundColor: couleurs, borderWidth: 3, borderColor: '#fff', hoverOffset: 8 }] },
        options: {
            cutout: '68%',
            plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, padding: 14 } } }
        },
        plugins: [centre]
    });

    // 1. Évolution : ligne avec dégradé
    new Chart(document.getElementById('chartMois'), {
        type: 'line',
        data: {
            labels: Object.keys(mois),
            datasets: [{
                label: 'Ajouts',
                data: Object.values(mois),
                borderColor: '#1e9e5a', borderWidth: 3, tension: .4, fill: true,
                pointBackgroundColor: '#fff', pointBorderColor: '#1e9e5a', pointBorderWidth: 2, pointRadius: 4, pointHoverRadius: 7,
                backgroundColor: (c) => {
                    const { ctx, chartArea } = c.chart;
                    if (!chartArea) return 'rgba(30,158,90,.15)';
                    const g = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                    g.addColorStop(0, 'rgba(30,158,90,.35)'); g.addColorStop(1, 'rgba(30,158,90,0)');
                    return g;
                }
            }]
        },
        options: {
            interaction: { intersect: false, mode: 'index' },
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#eef1f4' }, border: { display: false } }
            }
        }
    });

    // 2. Catégories : barres horizontales arrondies
    new Chart(document.getElementById('chartCategorie'), {
        type: 'bar',
        data: {
            labels: Object.keys(cat),
            datasets: [{ label: 'Vêtements', data: Object.values(cat), backgroundColor: palette, borderRadius: 8, borderSkipped: false, barThickness: 'flex', maxBarThickness: 26 }]
        },
        options: {
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#eef1f4' }, border: { display: false } },
                y: { grid: { display: false } }
            }
        }
    });

    // 3 et 4. Anneaux
    anneau('chartEtat', etat, ['#1e9e5a', '#2f80ed', '#f2994a', '#eb5757', '#9b51e0']);
    anneau('chartType', type, ['#2f80ed', '#f2994a', '#9b51e0', '#1e9e5a']);

    // Compteurs animés
    document.querySelectorAll('[data-count]').forEach(el => {
        const cible = parseFloat(el.dataset.count), dec = parseInt(el.dataset.dec || 0);
        const fmt = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: dec, maximumFractionDigits: dec });
        const debut = performance.now(), duree = 1100;
        (function pas(t) {
            const p = Math.min((t - debut) / duree, 1), e = 1 - Math.pow(1 - p, 3);
            el.textContent = fmt.format(cible * e);
            if (p < 1) requestAnimationFrame(pas);
        })(debut);
    });
})();
</script>
@endif

@endsection