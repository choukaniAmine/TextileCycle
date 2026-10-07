@extends('layouts.front')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <h1 class="h2 fw-bold mb-0">Mes vêtements</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('demandes.recues') }}" class="btn btn-outline-primary">Demandes reçues</a>
                <a href="{{ route('demandes.envoyees') }}" class="btn btn-outline-secondary">Mes demandes</a>
                <a href="{{ route('mes-vetements.create') }}" class="btn btn-primary">+ Ajouter</a>
            </div>
        </div>

        @include('front._flash')

        <div class="row g-4">
            @forelse($vetements as $v)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        @if($v->image)
                            <img src="{{ $v->imageUrl() }}" class="card-img-top" alt="{{ $v->nom }}" style="height:220px;object-fit:cover;">
                        @else
                            <div class="d-flex align-items-center justify-content-center text-white"
                                 style="height:220px;background:linear-gradient(135deg,#6c9a8b,#a8c3b8);font-size:4rem;">👕</div>
                        @endif
                        <div class="card-body d-flex flex-column">
                            <small class="text-muted text-uppercase">{{ $v->categorie->nom }}</small>
                            <h5 class="card-title mt-1">{{ $v->nom }}</h5>
                            <div class="mb-2">
                                <span class="badge bg-dark">{{ $v->type->label() }}</span>
                                <span class="badge bg-{{ $v->statut->badge() }}">{{ $v->statut->label() }}</span>
                                @if($v->demandes_en_attente_count > 0)
                                    <a href="{{ route('demandes.recues') }}" class="badge bg-warning text-dark text-decoration-none">
                                        {{ $v->demandes_en_attente_count }} demande(s)
                                    </a>
                                @endif
                            </div>
                            <p class="small text-muted mb-3 flex-grow-1">Taille {{ $v->taille }} · {{ $v->etat->label() }}</p>

                            <div class="d-flex gap-2">
                                <a href="{{ route('vetements.show', $v) }}" class="btn btn-sm btn-outline-secondary">Voir</a>
                                @can('update', $v)
                                    <a href="{{ route('mes-vetements.edit', $v) }}" class="btn btn-sm btn-warning">Modifier</a>
                                @endcan
                                @can('delete', $v)
                                    <form method="POST" action="{{ route('mes-vetements.destroy', $v) }}"
                                          onsubmit="return confirm('Supprimer ce vêtement ?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Supprimer</button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p class="fs-5 text-muted">Vous n'avez encore ajouté aucun vêtement.</p>
                    <a href="{{ route('mes-vetements.create') }}" class="btn btn-primary">Ajouter mon premier vêtement</a>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-5">
            {{ $vetements->links('pagination::bootstrap-5') }}
        </div>
    </div>
</section>
@endsection