@extends('layouts.front')

@section('content')
@php
    $couleurEtat = match($vetement->etat->value) {
        'neuf' => 'success',
        'tres_bon' => 'primary',
        'bon' => 'info',
        default => 'secondary',
    };
@endphp

<section class="py-5">
    <div class="container">

        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('vetements.index') }}">Vêtements</a></li>
                <li class="breadcrumb-item">
                    <a href="{{ route('vetements.index', ['categorie_id' => $vetement->categorie_id]) }}">
                        {{ $vetement->categorie->nom }}
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ $vetement->nom }}</li>
            </ol>
        </nav>

        <div class="row g-5">
            {{-- Image --}}
            <div class="col-12 col-lg-6">
                @if($vetement->image)
                    <img src="{{ $vetement->imageUrl() }}" alt="{{ $vetement->nom }}"
                         class="img-fluid rounded shadow w-100" style="max-height: 520px; object-fit: cover;">
                @else
                    <div class="d-flex align-items-center justify-content-center rounded shadow text-white"
                         style="height: 420px; background: linear-gradient(135deg, #6c9a8b, #a8c3b8); font-size: 8rem;">
                        👕
                    </div>
                @endif
            </div>

            {{-- Détails --}}
            <div class="col-12 col-lg-6">
                <span class="badge bg-dark mb-2">{{ $vetement->type->label() }}</span>
                <h1 class="fw-bold">{{ $vetement->nom }}</h1>
                <p class="text-muted">Catégorie : {{ $vetement->categorie->nom }}</p>

                <hr>

                <p class="lead">{{ $vetement->description ?: 'Aucune description fournie.' }}</p>

                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Taille</span>
                        <strong>{{ $vetement->taille }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">État</span>
                        <span class="badge bg-{{ $couleurEtat }}">{{ $vetement->etat->label() }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Type</span>
                        <strong>{{ $vetement->type->label() }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Ajouté le</span>
                        <strong>{{ $vetement->created_at->format('d/m/Y') }}</strong>
                    </li>
                </ul>
@include('front._flash')

@if($vetement->statut === \App\Enums\StatutVetement::Donne)
    <div class="alert alert-secondary">Ce vêtement a déjà été donné.</div>
@elseif($vetement->type === \App\Enums\TypeVetement::Don)
    @guest
        <div class="alert alert-info">
            <a href="{{ route('login') }}">Connectez-vous</a> pour demander ce vêtement.
        </div>
    @endguest

    @auth
        @if($vetement->user_id === auth()->id())
            <div class="alert alert-info">C'est votre vêtement.</div>
        @elseif($demandeEnCours)
            <div class="alert alert-warning">
                Votre demande est en attente de réponse.
                <a href="{{ route('demandes.envoyees') }}">Voir mes demandes</a>
            </div>
        @else
            @can('demander', $vetement)
                <form method="POST" action="{{ route('demandes.store', $vetement) }}" class="mb-4">
                    @csrf
                    <label class="form-label">Message au propriétaire (optionnel)</label>
                    <textarea name="message" rows="3" maxlength="500" class="form-control mb-2"
                              placeholder="Bonjour, ce vêtement m'intéresse...">{{ old('message') }}</textarea>
                    @error('message') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                    <button class="btn btn-success">Demander ce vêtement</button>
                </form>
            @endcan
        @endif
    @endauth
@endif
                <a href="{{ route('vetements.index') }}" class="btn btn-outline-secondary">
                    ← Retour aux vêtements
                </a>
            </div>
        </div>
    </div>
</section>
@endsection