@extends('layouts.front')
@section('title', 'Mes demandes')
@section('content')
<div class="demande-hero">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div><h1>Mes demandes 🧵</h1><p class="lead mb-0">Réparation ou transformation : suivez chaque étape de la seconde vie de vos vêtements.</p></div>
        <a href="{{ route('demandes.create') }}" class="btn btn-light btn-lg">+ Nouvelle demande</a>
    </div>
</div>
<div class="demande-page"><div class="container">
    <div class="mb-3">
        <a href="{{ route('demandes.index') }}" class="filter-pill {{ $statut ? '' : 'active' }}">Toutes ({{ $compteurs->sum() }})</a>
        @foreach (\App\Enums\Statut::cases() as $s)
            <a href="{{ route('demandes.index', ['statut' => $s->value]) }}" class="filter-pill {{ $statut === $s->value ? 'active' : '' }}">
                <i class="bi {{ $s->icon() }}"></i> {{ $s->label() }} ({{ $compteurs[$s->value] ?? 0 }})</a>
        @endforeach
    </div>

    @if ($demandes->isEmpty())
        <div class="empty-state"><div class="big">🧥</div><h4>Rien ici pour l’instant</h4>
            <p class="text-muted">Déposez votre première demande, un atelier s’en occupera.</p>
            <a href="{{ route('demandes.create') }}" class="btn btn-success">Créer une demande</a></div>
    @else
        <div class="row g-4">
            @foreach ($demandes as $d)
                <div class="col-md-6 col-lg-4">
                    <div class="card demande-card {{ $d->type->value }}"><div class="card-body pt-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="type-emoji">{{ $d->type->emoji() }}</div>
                            <div class="flex-grow-1">
                                <h5 class="mb-0">{{ $d->titre }}</h5>
                                <small class="text-muted">{{ $d->vetement }} · {{ $d->type->label() }}</small>
                            </div>
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-{{ $d->statut->color() }}"><i class="bi {{ $d->statut->icon() }}"></i> {{ $d->statut->label() }}</span>
                            @if ($d->urgent)<span class="badge-urgent">🔥 Urgent</span>@endif
                        </div>
                        <div class="progress stitch mb-1"><div class="progress-bar" style="width: {{ $d->progression() }}%"></div></div>
                        <small class="text-muted">{{ $d->progression() }} % · {{ $d->interventions->count() }} intervention(s)</small>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small class="text-muted">{{ $d->created_at->format('d/m/Y') }}</small>
                            <a href="{{ route('demandes.show', $d) }}" class="btn btn-sm btn-outline-success">Suivre →</a>
                        </div>
                    </div></div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $demandes->links('pagination::bootstrap-5') }}</div>
    @endif
</div></div>
@endsection
