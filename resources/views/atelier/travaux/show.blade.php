@extends('layouts.front')
@section('title', $demande->titre)
@section('content')
<div class="demande-hero"><div class="container">
    <a href="{{ route('atelier.travaux.index') }}" class="text-white-50 text-decoration-none">← Mes travaux</a>
    <h1 class="mt-2">{{ $demande->type->emoji() }} {{ $demande->titre }}</h1>
    <p class="lead mb-0">{{ $demande->vetement }} · {{ $demande->type->label() }}
        @if ($demande->urgent) · 🔥 Urgent @endif @if ($demande->enRetard()) · <span class="badge-late">En retard</span> @endif</p>
</div></div>

<div class="demande-page"><div class="container"><div class="row g-4">
    <div class="col-lg-5">
        <div class="panel-soft mb-4">
            @if ($demande->photo)<img src="{{ asset('storage/'.$demande->photo) }}" class="img-fluid rounded mb-3" alt="Photo du vêtement">@endif
            <p>{{ $demande->description }}</p>
            @if ($demande->date_souhaitee)<p class="small text-muted mb-3">⏳ Souhaitée pour le {{ $demande->date_souhaitee->format('d/m/Y') }}</p>@endif
            <div class="client-chip mb-3">👤 <strong>{{ $demande->user->name }}</strong>
                @if ($demande->user->city)<br>📍 {{ $demande->user->city }}@endif
                @if ($demande->user->phone)<br>📞 {{ $demande->user->phone }}@endif
                <br>✉️ {{ $demande->user->email }}</div>
            @if ($demande->statut !== \App\Enums\Statut::Terminee)
                <form method="POST" action="{{ route('atelier.travaux.liberer', $demande) }}" onsubmit="return confirm('Libérer cette demande ? Elle redeviendra disponible pour les autres ateliers.')">@csrf
                    <button class="btn btn-outline-secondary btn-sm">Me désister</button></form>
            @endif
        </div>
    </div>

    <div class="col-lg-7">
        <div class="panel-soft mb-4">
            <div class="row align-items-center">
                <div class="col-sm-4"><div class="ring" style="--p: {{ $demande->progression() }}"><span>{{ $demande->progression() }}%</span></div></div>
                <div class="col-sm-8">@include('partials.stepper', ['statut' => $demande->statut])</div>
            </div>
            <div class="text-center mt-2">💶 Total estimé : <strong>{{ number_format($demande->coutTotal(), 2, ',', ' ') }} DT</strong></div>
        </div>

        <h5 class="mb-3">Interventions</h5>
        @forelse ($demande->interventions as $i)
            <div class="timeline"><div class="item">
                <div class="d-flex justify-content-between align-items-start">
                    <strong>{{ $i->titre }}</strong>
                    <span class="badge bg-{{ $i->statut->color() }}">{{ $i->statut->label() }}</span>
                </div>
                @if ($i->description)<p class="small mb-1">{{ $i->description }}</p>@endif
                <small class="text-muted">
                    @if ($i->cout_estime){{ number_format($i->cout_estime, 2, ',', ' ') }} DT @endif
                    @if ($i->date_debut) · début {{ $i->date_debut->format('d/m') }} @endif
                    @if ($i->date_fin) · fin {{ $i->date_fin->format('d/m') }} @endif
                </small>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    @if ($i->statut->next())
                        <form method="POST" action="{{ route('atelier.interventions.advance', $i) }}">@csrf @method('PATCH')
                            <button class="btn btn-sm btn-success">{{ $i->statut->next()->label() }} →</button></form>
                    @endif
                    <a href="{{ route('atelier.interventions.edit', $i) }}" class="btn btn-sm btn-outline-primary">Modifier</a>
                    <form method="POST" action="{{ route('atelier.interventions.destroy', $i) }}" onsubmit="return confirm('Supprimer cette intervention ?')">@csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Supprimer</button></form>
                </div>
            </div></div>
        @empty
            <div class="empty-state mb-3"><div class="big">🪡</div><p class="mb-0 text-muted">Découpez le travail en interventions pour que le client suive l’avancement.</p></div>
        @endforelse

        <div class="panel-soft mt-4">
            <h6 class="mb-3">➕ Ajouter une intervention</h6>
            <form method="POST" action="{{ route('atelier.interventions.store', $demande) }}">
                @csrf
                @include('atelier.interventions._fields')
                <button class="btn btn-success">Ajouter</button>
            </form>
        </div>
    </div>
</div></div></div>
@endsection
