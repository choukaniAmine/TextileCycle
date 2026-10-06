@extends('layouts.admin')

@section('title', 'Détails Atelier - ' . $atelier->nom)

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center mb-3">
                    @if ($atelier->image && file_exists(public_path('storage/' . $atelier->image)))
                        <img class="profile-user-img img-fluid img-circle" src="{{ asset('storage/' . $atelier->image) }}" alt="{{ $atelier->nom }}" style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        <div class="d-inline-flex p-3 rounded-circle bg-light text-muted">
                            <i class="fas fa-store fa-3x"></i>
                        </div>
                    @endif
                </div>

                <h3 class="profile-username text-center font-weight-bold">{{ $atelier->nom }}</h3>
                <p class="text-muted text-center"><i class="fas fa-map-marker-alt text-danger"></i> {{ $atelier->ville }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Statut</b>
                        <span class="float-right badge {{ $atelier->est_actif ? 'badge-success' : 'badge-danger' }}">
                            {{ $atelier->est_actif ? 'Actif' : 'Inactif' }}
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>Téléphone</b> <span class="float-right">{{ $atelier->telephone }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Email</b> <span class="float-right">{{ $atelier->email ?? '-' }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Horaires</b> <span class="float-right small">{{ $atelier->horaires ?? '-' }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Adresse</b> <span class="float-right small">{{ $atelier->adresse }}</span>
                    </li>
                </ul>

                <a href="{{ route('admin.ateliers.edit', $atelier) }}" class="btn btn-warning btn-block">
                    <i class="fas fa-edit"></i> Modifier cet atelier
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Services proposés ({{ $atelier->services->count() }})</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.services.create', ['atelier_id' => $atelier->id]) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Ajouter un service
                    </a>
                </div>
            </div>

            <div class="card-body p-0 table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Type</th>
                            <th>Tarif</th>
                            <th>Délai</th>
                            <th>Disponibilité</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($atelier->services as $service)
                            <tr>
                                <td><strong>{{ $service->nom }}</strong></td>
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
                                    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce service ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Aucun service enregistré pour cet atelier.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
