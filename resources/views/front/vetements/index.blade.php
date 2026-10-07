@extends('layouts.front')
@section('title', 'Vêtements')

@section('content')
@include('front._hero', [
    'badge' => '♻️ Seconde vie',
    'titre' => 'Donnez, réparez, transformez',
    'sous' => 'Découvrez les vêtements proposés par la communauté et offrez-leur une nouvelle histoire.',
])

<section class="page-body">
    <div class="container">

        {{-- Filtres --}}
        <form method="GET" action="{{ route('vetements.index') }}"
              class="rf-filters row g-2 align-items-center mx-auto mb-5" style="max-width: 900px;">
            <div class="col-12 col-md">
                <select name="categorie_id" class="form-select">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected(request('categorie_id') == $c->id)>{{ $c->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md">
                <select name="etat" class="form-select">
                    <option value="">Tous les états</option>
                    @foreach($etats as $e)
                        <option value="{{ $e->value }}" @selected(request('etat') === $e->value)>{{ $e->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-auto d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-search"></i> Filtrer</button>
                <a href="{{ route('vetements.index') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                    <i class="bi bi-x-circle"></i>
                </a>
            </div>
        </form>

        <p class="text-muted mb-4">{{ $vetements->total() }} vêtement(s) disponible(s)</p>

        <div class="row g-4">
            @forelse($vetements as $v)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="rf-card rf-card-hover h-100 d-flex flex-column">
                        <a href="{{ route('vetements.show', $v) }}" class="rf-thumb d-block">
                            @if($v->image)
                                <img src="{{ $v->imageUrl() }}" alt="{{ $v->nom }}">
                            @else
                                <div class="rf-placeholder">👕</div>
                            @endif
                            <span class="rf-chip {{ $v->type->value }}">{{ $v->type->label() }}</span>
                        </a>

                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <span class="rf-cat">{{ $v->categorie->nom }}</span>
                            <h5 class="fw-bold mt-1 mb-2">{{ $v->nom }}</h5>
                            <p class="text-muted small flex-grow-1">
                                {{ \Illuminate\Support\Str::limit($v->description, 80) ?: 'Aucune description.' }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge rf-soft-badge">{{ $v->etat->label() }}</span>
                                <span class="fw-semibold small"><i class="bi bi-rulers"></i> Taille {{ $v->taille }}</span>
                            </div>
                            <a href="{{ route('vetements.show', $v) }}" class="btn btn-outline-primary w-100">
                                Voir le détail <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="rf-card text-center py-5 px-3">
                        <div class="display-3">🔍</div>
                        <p class="fs-5 text-muted my-3">Aucun vêtement ne correspond à votre recherche.</p>
                        <a href="{{ route('vetements.index') }}" class="btn btn-primary">Voir tous les vêtements</a>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-5">
            {{ $vetements->links('pagination::bootstrap-5') }}
        </div>
    </div>
</section>
@endsection