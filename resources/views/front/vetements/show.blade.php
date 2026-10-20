@extends('layouts.front')
@section('title', $vetement->nom)

@section('content')
@php
    $couleurEtat = match($vetement->etat->value) {
        'neuf' => 'success', 'tres_bon' => 'primary', 'bon' => 'info', default => 'secondary',
    };
@endphp

<header class="page-hero" style="padding-bottom: 6rem;">
    <div class="container position-relative">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item"><a href="{{ route('vetements.index') }}">Vêtements</a></li>
                <li class="breadcrumb-item">
                    <a href="{{ route('vetements.index', ['categorie_id' => $vetement->categorie_id]) }}">{{ $vetement->categorie->nom }}</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ $vetement->nom }}</li>
            </ol>
        </nav>
        <span class="eyebrow mb-2">{{ $vetement->type->label() }}</span>
        <h1 class="display-5 mb-0">{{ $vetement->nom }}</h1>
    </div>
</header>

<section class="page-body">
    <div class="container">
        <div class="row g-4">
            {{-- Image --}}
            <div class="col-12 col-lg-6">
                <div class="rf-card overflow-hidden">
                    @if($vetement->image)
                        <img src="{{ $vetement->imageUrl() }}" alt="{{ $vetement->nom }}" class="w-100"
                             style="max-height: 560px; object-fit: cover;">
                    @else
                        <div class="rf-placeholder" style="height: 420px; font-size: 8rem;">👕</div>
                    @endif
                </div>
            </div>

            {{-- Détails --}}
            <div class="col-12 col-lg-6">
                <div class="rf-card p-4 p-md-5">
                    <span class="rf-cat">{{ $vetement->categorie->nom }}</span>
                    <p class="lead mt-2">{{ $vetement->description ?: 'Aucune description fournie.' }}</p>

                    <div class="my-4">
                        <div class="rf-info-row"><span>Taille</span><strong>{{ $vetement->taille }}</strong></div>
                        <div class="rf-info-row"><span>État</span><span class="badge bg-{{ $couleurEtat }}">{{ $vetement->etat->label() }}</span></div>
                        <div class="rf-info-row"><span>Type</span><strong>{{ $vetement->type->label() }}</strong></div>
                        <div class="rf-info-row"><span>Statut</span><span class="badge bg-{{ $vetement->statut->badge() }}">{{ $vetement->statut->label() }}</span></div>
                        <div class="rf-info-row"><span>Proposé par</span><strong>{{ $vetement->user?->name }}</strong></div>
                        <div class="rf-info-row"><span>Ajouté le</span><strong>{{ $vetement->created_at->format('d/m/Y') }}</strong></div>
                    </div>

                    {{-- Demande de don --}}
                    @if($vetement->statut === \App\Enums\StatutVetement::Donne)
                        <div class="alert alert-secondary rf-flash">Ce vêtement a déjà été donné.</div>
                    @elseif($vetement->type === \App\Enums\TypeVetement::Don)
                        @guest
                            <div class="alert alert-success rf-flash">
                                <a href="{{ route('login') }}" class="fw-bold">Connectez-vous</a> pour demander ce vêtement.
                            </div>
                        @endguest

                        @auth
                            @if($vetement->user_id === auth()->id())
                                <div class="alert alert-success rf-flash">C'est votre vêtement.</div>
                            @elseif($demandeEnCours)
                                <div class="alert alert-warning rf-flash">
                                    Votre demande est en attente de réponse.
                                    <a href="{{ route('demandes-don.envoyees') }}" class="fw-bold">Voir mes demandes</a>
                                </div>
                            @else
                                @can('demander', $vetement)
                                    <form method="POST" action="{{ route('demandes-don.store', $vetement) }}">
                                        @csrf
                                        <label class="form-label">Message au propriétaire (optionnel)</label>
                                        <textarea name="message" rows="3" maxlength="500" class="form-control mb-3"
                                                  placeholder="Bonjour, ce vêtement m'intéresse...">{{ old('message') }}</textarea>
                                        @error('message') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                                        <button type="submit" class="btn btn-primary btn-lg w-100">
                                            <i class="bi bi-gift"></i> Demander ce vêtement
                                        </button>
                                    </form>
                                @endcan
                            @endif
                        @endauth
                    @endif
@auth
    @if($vetement->type === \App\Enums\TypeVetement::Transformation || $vetement->etat === \App\Enums\EtatVetement::Use)
        <div class="mt-4 p-3 rounded-4" style="background: var(--rf-green-soft);">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h6 class="fw-bold mb-0">♻️ Idées de seconde vie</h6>
                    <small class="text-muted">3 idées d'upcycling générées par l'IA</small>
                </div>
                <button type="button" id="btnIdees" class="btn btn-primary btn-sm"
                        data-url="{{ route('vetements.idees', $vetement) }}"
                        data-token="{{ csrf_token() }}">
                    <i class="bi bi-lightbulb"></i> Voir les idées
                </button>
            </div>
            <div id="ideesBox" class="mt-3 d-grid gap-3"></div>
        </div>
    @endif
@endauth
                    <a href="{{ route('vetements.index') }}" class="btn btn-outline-secondary mt-4">
                        <i class="bi bi-arrow-left"></i> Retour aux vêtements
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('scripts')
<script>
document.getElementById('btnIdees')?.addEventListener('click', async (e) => {
    const btn = e.currentTarget;
    const box = document.getElementById('ideesBox');
    const old = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Réflexion...';
    box.replaceChildren();

    const el = (tag, cls, text) => {
        const n = document.createElement(tag);
        if (cls) n.className = cls;
        if (text) n.textContent = text;
        return n;
    };
    const couleurs = { facile: 'success', moyen: 'warning', difficile: 'danger' };

    try {
        const r = await fetch(btn.dataset.url, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': btn.dataset.token },
        });
        const d = await r.json();
        if (!r.ok) throw new Error(d.message || 'Erreur IA');

        d.idees.forEach((i) => {
            const card = el('div', 'bg-white rounded-4 p-3 shadow-sm');
            card.appendChild(el('div', 'fw-bold', i.titre));
            card.appendChild(el('p', 'small text-muted mb-2', i.description));
            const meta = el('div', 'd-flex flex-wrap gap-2 align-items-center small');
            meta.appendChild(el('span', 'badge bg-' + couleurs[i.difficulte], i.difficulte));
            if (i.duree) meta.appendChild(el('span', '', '⏱ ' + i.duree));
            card.appendChild(meta);
            if (i.materiel.length) card.appendChild(el('div', 'small mt-2', '🧵 ' + i.materiel.join(', ')));
            box.appendChild(card);
        });
        btn.classList.replace('btn-primary', 'btn-outline-primary');
    } catch (err) {
        box.appendChild(el('div', 'alert alert-danger mb-0', err.message));
    } finally {
        btn.disabled = false;
        btn.innerHTML = old;
    }
});
</script>
@endpush