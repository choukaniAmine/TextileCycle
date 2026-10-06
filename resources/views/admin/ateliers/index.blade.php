@extends('layouts.admin')

@section('title', 'Gestion des Ateliers')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Liste des ateliers partenaires</h3>
        <div class="card-tools">
            <a href="{{ route('admin.ateliers.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus-circle"></i> Ajouter un atelier
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card-body border-bottom bg-light">
        <form method="GET" action="{{ route('admin.ateliers.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="small text-muted font-weight-bold">Recherche</label>
                <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm" placeholder="Nom, téléphone, email...">
            </div>
            <div class="col-md-3">
                <label class="small text-muted font-weight-bold">Ville</label>
                <select name="ville" class="form-control form-control-sm">
                    <option value="">Toutes les villes</option>
                    @foreach ($villes as $v)
                        <option value="{{ $v }}" {{ ($ville ?? '') === $v ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="small text-muted font-weight-bold">Statut</label>
                <select name="statut" class="form-control form-control-sm">
                    <option value="">Tous les statuts</option>
                    <option value="1" {{ ($statut ?? '') === '1' ? 'selected' : '' }}>Actif</option>
                    <option value="0" {{ ($statut ?? '') === '0' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
            <div class="col-md-2 d-flex">
                <button type="submit" class="btn btn-dark btn-sm flex-fill">Filtrer</button>
                @if ($search || $ville || $statut !== null)
                    <a href="{{ route('admin.ateliers.index') }}" class="btn btn-outline-secondary btn-sm ml-1" title="Réinitialiser">
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
                    <th>Nom de l'atelier</th>
                    <th>Ville & Adresse</th>
                    <th>Contact</th>
                    <th>Services</th>
                    <th>Statut</th>
                    <th style="width: 140px;" class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ateliers as $atelier)
                    <tr>
                        <td>{{ $atelier->id }}</td>
                        <td>
                            <strong>{{ $atelier->nom }}</strong>
                            @if ($atelier->description)
                                <div class="small text-muted">{{ Str::limit($atelier->description, 35) }}</div>
                            @endif
                        </td>
                        <td>
                            <div>{{ $atelier->ville }}</div>
                            <small class="text-muted">{{ Str::limit($atelier->adresse, 25) }}</small>
                        </td>
                        <td>
                            <div><i class="fas fa-phone-alt fa-xs text-muted"></i> {{ $atelier->telephone }}</div>
                            @if ($atelier->email)
                                <small class="text-muted"><i class="fas fa-envelope fa-xs text-muted"></i> {{ $atelier->email }}</small>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.services.index', ['atelier_id' => $atelier->id]) }}" class="badge badge-info">
                                {{ $atelier->services_count }} service(s)
                            </a>
                        </td>
                        <td>
                            @if ($atelier->est_actif)
                                <span class="badge badge-success">Actif</span>
                            @else
                                <span class="badge badge-danger">Inactif</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('admin.ateliers.show', $atelier) }}" class="btn btn-sm btn-info" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.ateliers.edit', $atelier) }}" class="btn btn-sm btn-warning" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.ateliers.destroy', $atelier) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirmer la suppression ? Cela supprimera aussi ses services.')">
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
                        <td colspan="7" class="text-center py-4 text-muted">
                            Aucun atelier trouvé.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($ateliers->hasPages())
        <div class="card-footer d-flex justify-content-center">
            {{ $ateliers->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
@endsection
