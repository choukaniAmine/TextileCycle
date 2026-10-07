@extends('layouts.front')

@section('content')
<section class="py-5 bg-light border-bottom">
    <div class="container text-center">
        <h1 class="fw-bold mb-2">Nos vêtements</h1>
        <p class="text-muted mb-0">Donnez, réparez ou transformez : choisissez une seconde vie pour vos vêtements.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">

        {{-- Filtres --}}
        <form method="GET" action="{{ route('vetements.index') }}" class="row g-2 justify-content-center mb-5">
            <div class="col-12 col-md-4">
                <select name="categorie_id" class="form-select">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected(request('categorie_id') == $c->id)>{{ $c->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3">
                <select name="etat" class="form-select">
                    <option value="">Tous les états</option>
                    @foreach($etats as $e)
                        <option value="{{ $e->value }}" @selected(request('etat') === $e->value)>{{ $e->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-auto d-flex gap-2">
                <button class="btn btn-primary">Filtrer</button>
                <a href="{{ route('vetements.index') }}" class="btn btn-outline-secondary">Réinitialiser</a>
            </div>
        </form>

        {{-- Cartes --}}
        <div class="row g-4">
            @forelse($vetements as $v)
                @php
                    $couleurEtat = match($v->etat->value) {
                        'neuf' => 'success',
                        'tres_bon' => 'primary',
                        'bon' => 'info',
                        default => 'secondary',
                    };
                    $couleurType = match($v->type->value) {
                        'don' => 'success',
                        'reparation' => 'warning',
                        default => 'dark',
                    };
                @endphp
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm vetement-card">
                        <div class="position-relative">
                            @if($v->image)
                                <img src="{{ $v->imageUrl() }}" class="card-img-top" alt="{{ $v->nom }}"
                                     style="height: 250px; object-fit: cover;">
                            @else
                                <div class="d-flex align-items-center justify-content-center text-white"
                                     style="height: 250px; background: linear-gradient(135deg, #6c9a8b, #a8c3b8); font-size: 4rem;">
                                    👕
                                </div>
                            @endif
                            <span class="badge bg-{{ $couleurType }} position-absolute top-0 start-0 m-3">
                                {{ $v->type->label() }}
                            </span>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <small class="text-muted text-uppercase">{{ $v->categorie->nom }}</small>
                            <h5 class="card-title mt-1">{{ $v->nom }}</h5>
                            <p class="card-text text-muted small flex-grow-1">
                                {{ \Illuminate\Support\Str::limit($v->description, 80) ?: 'Aucune description.' }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-{{ $couleurEtat }}">{{ $v->etat->label() }}</span>
                                <span class="fw-semibold">Taille : {{ $v->taille }}</span>
                            </div>
                            <a href="{{ route('vetements.show', $v) }}" class="btn btn-outline-primary w-100">
                                Voir le détail
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p class="fs-5 text-muted mb-3">Aucun vêtement ne correspond à votre recherche.</p>
                    <a href="{{ route('vetements.index') }}" class="btn btn-primary">Voir tous les vêtements</a>
                </div>
            @endforelse
        </div>

        {{-- Pagination (Bootstrap 5) --}}
        <div class="d-flex justify-content-center mt-5">
            {{ $vetements->links('pagination::bootstrap-5') }}
        </div>
    </div>
</section>

<style>
    .vetement-card { transition: transform .2s ease, box-shadow .2s ease; }
    .vetement-card:hover { transform: translateY(-6px); box-shadow: 0 .75rem 1.5rem rgba(0,0,0,.15) !important; }
</style>
@endsection