{{-- Confirmation / correction de la durée réelle par l'atelier. --}}
@php($prefill = $demande->duree_reelle_min ?? $demande->duree_estimee_min ?? 0)
<div class="panel-soft mb-4">
    <h6 class="mb-1">⏱️ Durée réelle du travail</h6>
    <p class="small text-muted">Confirmez ou corrigez la durée : elle sert à entraîner le modèle d’estimation.</p>

    @if ($demande->duree_estimee_min)
        <form method="POST" action="{{ route('atelier.travaux.duree', $demande) }}" class="mb-3">
            @csrf @method('PATCH')
            <input type="hidden" name="confirmer_estimation" value="1">
            <button class="btn btn-outline-success btn-sm">✔ Confirmer l’estimation ({{ \App\Support\Duree::format($demande->duree_estimee_min) }})</button>
        </form>
    @endif

    <form method="POST" action="{{ route('atelier.travaux.duree', $demande) }}">
        @csrf @method('PATCH')
        <div class="row g-2 align-items-end">
            <div class="col-4"><label class="form-label small">Heures</label>
                <input type="number" name="heures" min="0" max="168" value="{{ old('heures', intdiv($prefill, 60)) }}" class="form-control @error('heures') is-invalid @enderror"></div>
            <div class="col-4"><label class="form-label small">Minutes</label>
                <input type="number" name="minutes" min="0" max="59" value="{{ old('minutes', $prefill % 60) }}" class="form-control @error('minutes') is-invalid @enderror"></div>
            <div class="col-4"><button class="btn btn-success w-100">Enregistrer</button></div>
        </div>
        @error('minutes')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        @error('heures')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </form>
</div>
