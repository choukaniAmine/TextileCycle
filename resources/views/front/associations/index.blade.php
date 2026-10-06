@extends('layouts.front')
@section('title', 'Associations partenaires')
@section('content')
<div class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
        <div>
            <h2 class="mb-1">Associations partenaires</h2>
            <p class="text-muted mb-0">Choisissez l’association qui recevra vos vêtements.</p>
        </div>
        <form method="GET" class="d-flex gap-2 mt-3 mt-md-0">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Nom ou ville…">
            <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
        </form>
    </div>

    <div class="row g-4">
        @forelse ($associations as $a)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-heart-fill text-danger"></i> {{ $a->name }}</h5>
                        <p class="text-muted small mb-2"><i class="bi bi-geo-alt"></i> {{ $a->city ?? 'Tunisie' }}</p>
                        <p class="card-text">{{ \Illuminate\Support\Str::limit($a->description, 110) }}</p>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                        <span class="badge bg-success">{{ $a->livres_count }} don(s) livré(s)</span>
                        <a href="{{ route('associations.show', $a) }}" class="btn btn-sm btn-primary">Voir</a>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center text-muted py-5">Aucune association trouvée.</p>
        @endforelse
    </div>
    <div class="mt-4">{{ $associations->links('pagination::bootstrap-5') }}</div>
</div>
@endsection
