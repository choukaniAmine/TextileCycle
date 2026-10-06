@extends('layouts.admin')

@section('title', 'Ajouter un Service')

@section('content')
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Nouveau Service Textile</h3>
        <div class="card-tools">
            <a href="{{ route('admin.services.index') }}" class="btn btn-tool text-white">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <form action="{{ route('admin.services.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label for="atelier_id">Atelier prestataire <span class="text-danger">*</span></label>
                <select name="atelier_id" id="atelier_id" class="form-control @error('atelier_id') is-invalid @enderror">
                    <option value="">-- Choisir un atelier --</option>
                    @foreach ($ateliers as $atelier)
                        <option value="{{ $atelier->id }}" {{ old('atelier_id', $selectedAtelierId ?? '') == $atelier->id ? 'selected' : '' }}>
                            {{ $atelier->nom }} ({{ $atelier->ville }})
                        </option>
                    @endforeach
                </select>
                @error('atelier_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="row">
                <div class="col-md-7 form-group">
                    <label for="nom">Intitulé du service <span class="text-danger">*</span></label>
                    <input type="text" name="nom" id="nom" value="{{ old('nom') }}" class="form-control @error('nom') is-invalid @enderror" placeholder="Ex: Remplacement zip manteau">
                    @error('nom') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-5 form-group">
                    <label for="type_service">Type de prestation <span class="text-danger">*</span></label>
                    <select name="type_service" id="type_service" class="form-control @error('type_service') is-invalid @enderror">
                        <option value="">-- Choisir un type --</option>
                        @foreach ($typesServices as $type)
                            <option value="{{ $type }}" {{ old('type_service') === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('type_service') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description de la prestation</label>
                <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Détails du travail effectué, consignes...">{{ old('description') }}</textarea>
                @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="tarif_estime">Tarif estimé (DT) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" step="0.50" min="0" name="tarif_estime" id="tarif_estime" value="{{ old('tarif_estime') }}" class="form-control @error('tarif_estime') is-invalid @enderror" placeholder="Ex: 25.00">
                        <div class="input-group-append"><span class="input-group-text">DT</span></div>
                        @error('tarif_estime') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6 form-group">
                    <label for="duree_estimee">Délai estimé</label>
                    <input type="text" name="duree_estimee" id="duree_estimee" value="{{ old('duree_estimee', '48h') }}" class="form-control @error('duree_estimee') is-invalid @enderror" placeholder="Ex: 24h, 3 jours...">
                    @error('duree_estimee') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group custom-control custom-switch mt-2">
                <input type="checkbox" class="custom-control-input" id="disponible" name="disponible" value="1" {{ old('disponible', '1') == '1' ? 'checked' : '' }}>
                <label class="custom-control-label" for="disponible">Service disponible actuellement</label>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Enregistrer le service</button>
            <a href="{{ route('admin.services.index') }}" class="btn btn-default">Annuler</a>
        </div>
    </form>
</div>
@endsection
