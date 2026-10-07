@extends('layouts.front')
@section('title', 'Mes vêtements')

@section('content')
@include('front._hero', [
    'badge' => '👗 Mon dressing solidaire',
    'titre' => 'Mes vêtements',
    'sous' => 'Gérez vos annonces et répondez aux personnes intéressées.',
    'actions' => '<a href="'.route('mes-vetements.create').'" class="btn btn-light fw-bold"><i class="bi bi-plus-circle"></i> Ajouter un vêtement</a>
                  <a href="'.route('demandes-don.recues').'" class="btn btn-outline-light">Demandes reçues</a>
                  <a href="'.route('demandes-don.envoyees').'" class="btn btn-outline-light">Mes demandes</a>',
])

<section class="page-body">
    <div class="container">
        <div class="row g-4">
            @forelse($vetements as $v)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="rf-card rf-card-hover h-100 d-flex flex-column">
                        <div class="rf-thumb" style="height: 210px;">
                            @if($v->image)
                                <img src="{{ $v->imageUrl() }}" alt="{{ $v->nom }}">
                            @else
                                <div class="rf-placeholder">👕</div>
                            @endif
                            <span class="rf-chip {{ $v->type->value }}">{{ $v->type->label() }}</span>
                        </div>

                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <span class="rf-cat">{{ $v->categorie->nom }}</span>
                            <h5 class="fw-bold mt-1">{{ $v->nom }}</h5>

                            <div class="mb-2 d-flex flex-wrap gap-1">
                                <span class="badge bg-{{ $v->statut->badge() }}">{{ $v->statut->label() }}</span>
                                @if($v->demandes_en_attente_count > 0)
                                    <a href="{{ route('demandes-don.recues') }}" class="badge bg-warning text-decoration-none">
                                        🔔 {{ $v->demandes_en_attente_count }} demande(s)
                                    </a>
                                @endif
                            </div>
                            <p class="small text-muted flex-grow-1">Taille {{ $v->taille }} · {{ $v->etat->label() }}</p>

                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('vetements.show', $v) }}" class="btn btn-sm btn-outline-secondary">Voir</a>
                                @can('update', $v)
                                    <a href="{{ route('mes-vetements.edit', $v) }}" class="btn btn-sm btn-outline-primary">Modifier</a>
                                @endcan
                                @can('delete', $v)
                                    <form method="POST" action="{{ route('mes-vetements.destroy', $v) }}"
                                          onsubmit="return confirm('Supprimer ce vêtement ?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="rf-card text-center py-5 px-3">
                        <div class="display-3">🧺</div>
                        <p class="fs-5 text-muted my-3">Vous n'avez encore ajouté aucun vêtement.</p>
                        <a href="{{ route('mes-vetements.create') }}" class="btn btn-primary">Ajouter mon premier vêtement</a>
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