{{-- Affichage de l'estimation (IA n°1) et de la durée réelle confirmée. Usage : @include('partials.estimation-card', ['demande' => $demande]) --}}
@once
    @push('styles')<link rel="stylesheet" href="{{ asset('css/ai.css') }}">@endpush
@endonce
@php($est = $demande->estimation)
@php($reelle = $demande->duree_reelle_min)
<div class="est-card">
    <div class="est-title">⏱️ Durée de travail</div>

    @if ($demande->duree_estimee_min && ($est['disponible'] ?? false))
        <div class="est-main">≈ {{ \App\Support\Duree::format($demande->duree_estimee_min) }}</div>
        <div class="est-range">Fourchette probable : {{ \App\Support\Duree::format($est['min'] ?? null) }} – {{ \App\Support\Duree::format($est['max'] ?? null) }}</div>
        <div class="est-badges">
            <span class="badge-conf {{ $est['confiance'] ?? 'faible' }}">Confiance {{ ['faible' => 'faible', 'moyenne' => 'moyenne', 'elevee' => 'élevée'][$est['confiance'] ?? 'faible'] }}</span>
            <span class="badge-mode">{{ ($est['mode'] ?? '') === 'modele' ? 'Modèle ML local' : 'Médiane historique (pas du ML)' }}</span>
        </div>
        @if (! empty($est['message']))<small class="text-muted">{{ $est['message'] }}</small>@endif
        @if (! empty($est['donnees_demo']))
            <div class="est-warn">⚠ Estimation calculée avec des données de démonstration.</div>
        @endif
    @elseif ($est)
        <div class="text-muted small">Estimation indisponible : {{ $est['message'] ?? 'service injoignable' }}</div>
    @else
        <div class="text-muted small">Pas d’estimation : le type de vêtement ou d’intervention n’a pas été renseigné.</div>
    @endif

    @if ($reelle)
        @php($ecart = $demande->duree_estimee_min ? $reelle - $demande->duree_estimee_min : null)
        <div class="est-real">✅ Durée réelle confirmée par l’atelier : <strong>{{ \App\Support\Duree::format($reelle) }}</strong>
            @if ($ecart !== null)<span class="text-muted">({{ $ecart >= 0 ? '+' : '−' }}{{ abs($ecart) }} min par rapport à l’estimation)</span>@endif
        </div>
    @endif
</div>
