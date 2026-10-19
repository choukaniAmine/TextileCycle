@extends('layouts.admin')
@section('title', 'Statistiques des vêtements')
@section('content')

{{-- Cartes d'impact --}}
<div class="row">
    <div class="col-md-3 col-6">
        <div class="small-box bg-info"><div class="inner"><h3>{{ $total }}</h3><p>Vêtements sur la plateforme</p></div><div class="icon"><i class="fas fa-tshirt"></i></div></div>
    </div>
    <div class="col-md-3 col-6">
        <div class="small-box bg-success"><div class="inner"><h3>{{ number_format($kg, 1, ',', ' ') }} kg</h3><p>Textile sauvé (estimation)</p></div><div class="icon"><i class="fas fa-weight-hanging"></i></div></div>
    </div>
    <div class="col-md-3 col-6">
        <div class="small-box bg-primary"><div class="inner"><h3>{{ number_format($eau, 0, ',', ' ') }} L</h3><p>Eau économisée (estimation)</p></div><div class="icon"><i class="fas fa-tint"></i></div></div>
    </div>
    <div class="col-md-3 col-6">
        <div class="small-box bg-teal"><div class="inner"><h3>{{ number_format($joursEau, 0, ',', ' ') }}</h3><p>Jours d'eau d'une personne</p></div><div class="icon"><i class="fas fa-user-clock"></i></div></div>
    </div>
</div>

{{-- Graphiques --}}
<div class="row">
    <div class="col-lg-6">
        <div class="card"><div class="card-header"><h3 class="card-title">Vêtements par catégorie</h3></div>
            <div class="card-body"><canvas id="chartCategorie" height="200"></canvas></div></div>
    </div>
    <div class="col-lg-6">
        <div class="card"><div class="card-header"><h3 class="card-title">Évolution des ajouts (12 mois)</h3></div>
            <div class="card-body"><canvas id="chartMois" height="200"></canvas></div></div>
    </div>
    <div class="col-lg-6">
        <div class="card"><div class="card-header"><h3 class="card-title">Répartition par état</h3></div>
            <div class="card-body"><canvas id="chartEtat" height="200"></canvas></div></div>
    </div>
    <div class="col-lg-6">
        <div class="card"><div class="card-header"><h3 class="card-title">Répartition par type</h3></div>
            <div class="card-body"><canvas id="chartType" height="200"></canvas></div></div>
    </div>
</div>

<p class="text-muted small">
    <i class="fas fa-info-circle"></i> Les valeurs d'impact sont des estimations basées sur des coefficients moyens par catégorie
    (poids moyen et eau nécessaire à la fabrication, ex. : un jean ≈ 7 500 L).
</p>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const palette = ['#17a2b8','#28a745','#ffc107','#dc3545','#6f42c1','#fd7e14','#20c997','#007bff','#e83e8c','#6c757d'];

    const cat = @json($parCategorie);
    const mois = @json($parMois);
    const etat = @json($parEtat);
    const type = @json($parType);

    new Chart(document.getElementById('chartCategorie'), {
        type: 'bar',
        data: { labels: Object.keys(cat), datasets: [{ label: 'Vêtements', data: Object.values(cat), backgroundColor: palette }] },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });

    new Chart(document.getElementById('chartMois'), {
        type: 'line',
        data: { labels: Object.keys(mois), datasets: [{ label: 'Ajouts', data: Object.values(mois), borderColor: '#28a745', backgroundColor: 'rgba(40,167,69,.15)', fill: true, tension: .3 }] },
        options: { scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });

    new Chart(document.getElementById('chartEtat'), {
        type: 'doughnut',
        data: { labels: Object.keys(etat), datasets: [{ data: Object.values(etat), backgroundColor: palette }] }
    });

    new Chart(document.getElementById('chartType'), {
        type: 'pie',
        data: { labels: Object.keys(type), datasets: [{ data: Object.values(type), backgroundColor: palette }] }
    });
</script>
@endsection