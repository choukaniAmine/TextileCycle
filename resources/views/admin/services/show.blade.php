@extends('layouts.admin')

@section('title', 'Détails Service - ' . $service->nom)

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">{{ $service->nom }}</h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ $service->type_service }}</span>
                </div>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border">
                            <small class="text-muted d-block">Atelier Prestataire</small>
                            <h5 class="font-weight-bold mb-1">
                                <a href="{{ route('admin.ateliers.show', $service->atelier) }}">
                                    <i class="fas fa-store text-primary"></i> {{ $service->atelier->nom }}
                                </a>
                            </h5>
                            <small class="text-muted">{{ $service->atelier->ville }} &bull; {{ $service->atelier->telephone }}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border">
                            <small class="text-muted d-block">Tarif & Disponibilité</small>
                            <h4 class="font-weight-bold text-success mb-1">{{ number_format($service->tarif_estime, 2) }} DT</h4>
                            <div>
                                @if ($service->disponible)
                                    <span class="badge badge-success">Disponible</span>
                                @else
                                    <span class="badge badge-secondary">Indisponible</span>
                                @endif
                                <span class="small text-muted ml-2">Délai estimé : {{ $service->duree_estimee ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Description :</label>
                    <p class="p-3 bg-light rounded border text-muted">
                        {{ $service->description ?? 'Aucune description spécifique renseignée.' }}
                    </p>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('admin.services.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Retour aux services
                </a>
                <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Modifier ce service
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
