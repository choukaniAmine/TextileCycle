{{-- Frise "couture" : En attente → En cours → Terminée. Usage : @include('partials.stepper', ['statut' => $demande->statut]) --}}
@if ($statut === \App\Enums\Statut::Annulee)
    <div class="alert alert-secondary mb-0">Cette demande a été annulée.</div>
@else
    <ol class="stitch-stepper">
        @foreach ([1 => 'En attente', 2 => 'En cours', 3 => 'Terminée'] as $n => $label)
            @php($done = $statut->step() > $n || $statut === \App\Enums\Statut::Terminee)
            <li class="{{ $done ? 'done' : ($statut->step() === $n ? 'current' : '') }}">
                <span class="dot">{{ $done ? '✓' : $n }}</span>
                <span class="lbl">{{ $label }}</span>
            </li>
        @endforeach
    </ol>
@endif
