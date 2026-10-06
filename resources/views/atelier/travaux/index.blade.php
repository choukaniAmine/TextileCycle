@extends('layouts.front')
@section('title', 'Mes travaux')
@section('content')
<div class="demande-hero"><div class="container d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div><h1>🧵 Mes travaux</h1><p class="lead mb-0">{{ auth()->user()->organization ?? auth()->user()->name }}</p></div>
    <a href="{{ route('atelier.demandes.disponibles') }}" class="btn btn-light btn-lg">📥 Trouver des demandes</a>
</div></div>

<div class="demande-page"><div class="container">
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3"><div class="kpi clay"><div class="n">{{ $stats['disponibles'] }}</div><small>demandes disponibles</small></div></div>
        <div class="col-6 col-md-3"><div class="kpi"><div class="n">{{ $stats['en_cours'] }}</div><small>travaux en cours</small></div></div>
        <div class="col-6 col-md-3"><div class="kpi"><div class="n">{{ $stats['terminees'] }}</div><small>terminés</small></div></div>
        <div class="col-6 col-md-3"><div class="kpi clay"><div class="n">{{ number_format($stats['revenu'], 0, ',', ' ') }} DT</div><small>interventions terminées</small></div></div>
    </div>

    <div class="mb-3">
        <a href="{{ route('atelier.travaux.index') }}" class="filter-pill {{ $statut ? '' : 'active' }}">Tous</a>
        @foreach ([\App\Enums\Statut::EnCours, \App\Enums\Statut::Terminee] as $s)
            <a href="{{ route('atelier.travaux.index', ['statut' => $s->value]) }}" class="filter-pill {{ $statut === $s->value ? 'active' : '' }}">{{ $s->label() }}</a>
        @endforeach
    </div>

    @if ($travaux->isEmpty())
        <div class="empty-state"><div class="big">🪡</div><h4>Aucun travail pour l’instant</h4>
            <a href="{{ route('atelier.demandes.disponibles') }}" class="btn btn-success mt-2">Voir les demandes disponibles</a></div>
    @else
        <div class="row g-4">
            @foreach ($travaux as $d)
                <div class="col-md-6 col-lg-4">
                    <div class="card demande-card {{ $d->type->value }}"><div class="card-body pt-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="type-emoji">{{ $d->type->emoji() }}</div>
                            <div><h5 class="mb-0">{{ $d->titre }}</h5><small class="text-muted">{{ $d->vetement }} · {{ $d->user->name }}</small></div>
                        </div>
                        <span class="badge bg-{{ $d->statut->color() }}"><i class="bi {{ $d->statut->icon() }}"></i> {{ $d->statut->label() }}</span>
                        @if ($d->urgent)<span class="badge-urgent">🔥 Urgent</span>@endif
                        @if ($d->enRetard())<span class="badge-late">En retard</span>@endif
                        <div class="progress stitch mt-3 mb-1"><div class="progress-bar" style="width: {{ $d->progression() }}%"></div></div>
                        <small class="text-muted">{{ $d->progression() }} % · {{ $d->interventions->count() }} intervention(s)</small>
                        <div class="text-end mt-3"><a href="{{ route('atelier.travaux.show', $d) }}" class="btn btn-sm btn-outline-success">Gérer →</a></div>
                    </div></div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $travaux->links('pagination::bootstrap-5') }}</div>
    @endif
</div></div>
@endsection
