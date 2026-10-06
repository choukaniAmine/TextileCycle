@extends('layouts.admin')

@section('title', 'Gestion des Services')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Catalogue des prestations textiles</h3>
        <div class="card-tools">
            <a href="{{ route('admin.services.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus-circle"></i> Ajouter un service
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card-body border-bottom bg-light">
        <form method="GET" action="{{ route('admin.services.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="small text-muted font-weight-bold">Recherche</label>
                <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm" placeholder="Nom du service...">
            </div>
            <div class="col-md-3">
                <label class="small text-muted font-weight-bold">Atelier</label>
                <select name="atelier_id" class="form-control form-control-sm">
                    <option value="">Tous les ateliers</option>
                    @foreach ($ateliers as $at)
                        <option value="{{ $at->id }}" {{ ($atelierId ?? '') == $at->id ? 'selected' : '' }}>{{ $at->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="small text-muted font-weight-bold">Type de prestation</label>
                <select name="type_service" class="form-control form-control-sm">
                    <option value="">Tous les types</option>
                    @foreach ($typesServices as $type)
                        <option value="{{ $type }}" {{ ($typeService ?? '') === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex">
                <button type="submit" class="btn btn-dark btn-sm flex-fill">Filtrer</button>
                @if ($search || $atelierId || $typeService)
                    <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary btn-sm ml-1" title="Réinitialiser">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="card-body p-0 table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Nom du service</th>
                    <th>Atelier prestataire</th>
                    <th>Type</th>
                    <th>Tarif estimé</th>
                    <th>Délai</th>
                    <th>Disponibilité</th>
                    <th style="width: 140px;" class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($services as $service)
                    <tr>
                        <td>{{ $service->id }}</td>
                        <td>
                            <strong>{{ $service->nom }}</strong>
                            @if ($service->description)
                                <div class="small text-muted">{{ Str::limit($service->description, 35) }}</div>
                            @endif
                        </td>
                        <td>
                            @if ($service->atelier)
                                <a href="{{ route('admin.ateliers.show', $service->atelier) }}">
                                    <i class="fas fa-store fa-xs"></i> {{ $service->atelier->nom }}
                                </a>
                                <small class="text-muted d-block">{{ $service->atelier->ville }}</small>
                            @else
                                <span class="text-muted fst-italic">Non rattaché</span>
                            @endif
                        </td>
                        <td><span class="badge badge-info">{{ $service->type_service }}</span></td>
                        <td class="font-weight-bold text-success">{{ number_format($service->tarif_estime, 2) }} DT</td>
                        <td>{{ $service->duree_estimee ?? '-' }}</td>
                        <td>
                            @if ($service->disponible)
                                <span class="badge badge-success">Disponible</span>
                            @else
                                <span class="badge badge-secondary">Indisponible</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('admin.services.show', $service) }}" class="btn btn-sm btn-info" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-warning" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce service ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            Aucun service trouvé.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($services->hasPages())
        <div class="card-footer d-flex justify-content-center">
            {{ $services->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
@endsection
