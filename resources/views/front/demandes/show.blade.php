@extends('layouts.front')
@section('title', $demande->titre)
@section('content')
<div class="demande-hero"><div class="container">
    <a href="{{ route('demandes.index') }}" class="text-white-50 text-decoration-none">← Mes demandes</a>
    <h1 class="mt-2">{{ $demande->type->emoji() }} {{ $demande->titre }}</h1>
    <p class="lead mb-0">{{ $demande->vetement }} · {{ $demande->type->label() }}</p>
</div></div>

<div class="demande-page"><div class="container"><div class="row g-4">
    <div class="col-lg-5">
        <div class="panel-soft mb-4">
            @if ($demande->photo)<img src="{{ asset('storage/'.$demande->photo) }}" class="img-fluid rounded mb-3" alt="Photo du vêtement">@endif
            <p>{{ $demande->description }}</p>
            <ul class="list-unstyled small text-muted mb-3">
                <li>📅 Créée le {{ $demande->created_at->format('d/m/Y') }}</li>
                @if ($demande->date_souhaitee)<li>⏳ Souhaitée pour le {{ $demande->date_souhaitee->format('d/m/Y') }}</li>@endif
                @if ($demande->atelier)<li>🏠 Atelier : <strong>{{ $demande->atelier->organization ?? $demande->atelier->name }}</strong></li>@endif
                @if ($demande->urgent)<li>🔥 Marquée comme urgente</li>@endif
            </ul>
            <div class="d-flex flex-wrap gap-2">
                @if ($demande->peutEtreModifiee())
                    <a href="{{ route('demandes.edit', $demande) }}" class="btn btn-outline-primary btn-sm">Modifier</a>
                    <form method="POST" action="{{ route('demandes.cancel', $demande) }}" onsubmit="return confirm('Annuler cette demande ?')">@csrf @method('PATCH')
                        <button class="btn btn-outline-secondary btn-sm">Annuler la demande</button></form>
                @endif
                @if ($demande->peutEtreSupprimee())
                    <form method="POST" action="{{ route('demandes.destroy', $demande) }}" onsubmit="return confirm('Supprimer définitivement cette demande ?')">@csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm">Supprimer</button></form>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="panel-soft mb-4">
            <div class="row align-items-center">
                <div class="col-sm-4"><div class="ring" style="--p: {{ $demande->progression() }}"><span>{{ $demande->progression() }}%</span></div></div>
                <div class="col-sm-8">@include('partials.stepper', ['statut' => $demande->statut])</div>
            </div>
            @if ($demande->coutTotal() > 0)
                <div class="text-center mt-2">💶 Coût total estimé : <strong>{{ number_format($demande->coutTotal(), 2, ',', ' ') }} DT</strong></div>
            @endif
        </div>

        <h5 class="mb-3">Interventions</h5>
        @forelse ($demande->interventions as $i)
            <div class="timeline"><div class="item">
                <div class="d-flex justify-content-between">
                    <strong>{{ $i->titre }}</strong>
                    <span class="badge bg-{{ $i->statut->color() }}">{{ $i->statut->label() }}</span>
                </div>
                @if ($i->description)<p class="small mb-1">{{ $i->description }}</p>@endif
                <small class="text-muted">
                    {{ $i->atelier?->organization ?? $i->atelier?->name ?? 'Atelier à définir' }}
                    @if ($i->cout_estime) · {{ number_format($i->cout_estime, 2, ',', ' ') }} DT @endif
                    @if ($i->date_debut) · début {{ $i->date_debut->format('d/m') }} @endif
                    @if ($i->date_fin) · fin {{ $i->date_fin->format('d/m') }} @endif
                </small>
            </div></div>
        @empty
            <div class="empty-state"><div class="big">🪡</div><p class="mb-0 text-muted">Aucune intervention pour le moment : un atelier va bientôt s’en charger.</p></div>
        @endforelse
    </div>
</div></div></div>
@endsection
