@extends('layouts.admin')

@section('title', 'Modifier l\'Atelier')

@section('content')
<div class="card card-warning">
    <div class="card-header">
        <h3 class="card-title">Modifier l'atelier : {{ $atelier->nom }}</h3>
        <div class="card-tools">
            <a href="{{ route('admin.ateliers.index') }}" class="btn btn-tool">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <form action="{{ route('admin.ateliers.update', $atelier) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-8 form-group">
                    <label for="nom">Nom de l'atelier <span class="text-danger">*</span></label>
                    <input type="text" name="nom" id="nom" value="{{ old('nom', $atelier->nom) }}" class="form-control @error('nom') is-invalid @enderror">
                    @error('nom') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-4 form-group">
                    <label for="ville">Ville <span class="text-danger">*</span></label>
                    <input type="text" name="ville" id="ville" value="{{ old('ville', $atelier->ville) }}" class="form-control @error('ville') is-invalid @enderror">
                    @error('ville') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description de l'atelier</label>
                <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $atelier->description) }}</textarea>
                @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="row">
                <div class="col-md-8 form-group">
                    <label for="adresse">Adresse complète <span class="text-danger">*</span></label>
                    <input type="text" name="adresse" id="adresse" value="{{ old('adresse', $atelier->adresse) }}" class="form-control @error('adresse') is-invalid @enderror">
                    @error('adresse') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-4 form-group">
                    <label for="code_postal">Code Postal</label>
                    <input type="text" name="code_postal" id="code_postal" value="{{ old('code_postal', $atelier->code_postal) }}" class="form-control @error('code_postal') is-invalid @enderror">
                    @error('code_postal') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="telephone">Téléphone <span class="text-danger">*</span></label>
                    <input type="text" name="telephone" id="telephone" value="{{ old('telephone', $atelier->telephone) }}" class="form-control @error('telephone') is-invalid @enderror">
                    @error('telephone') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6 form-group">
                    <label for="email">Adresse Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $atelier->email) }}" class="form-control @error('email') is-invalid @enderror">
                    @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="horaires">Horaires d'ouverture</label>
                    <input type="text" name="horaires" id="horaires" value="{{ old('horaires', $atelier->horaires) }}" class="form-control @error('horaires') is-invalid @enderror">
                    @error('horaires') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6 form-group">
                    <label for="image">Changer la Photo / Logo</label>
                    <input type="file" name="image" id="image" class="form-control-file @error('image') is-invalid @enderror" accept="image/*">
                    @if ($atelier->image)
                        <small class="text-muted d-block mt-1">Image actuelle enregistrée</small>
                    @endif
                    @error('image') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group custom-control custom-switch mt-2">
                <input type="checkbox" class="custom-control-input" id="est_actif" name="est_actif" value="1" {{ old('est_actif', $atelier->est_actif) ? 'checked' : '' }}>
                <label class="custom-control-label" for="est_actif">Atelier actif (visible publiquement)</label>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Mettre à jour l'atelier</button>
            <a href="{{ route('admin.ateliers.index') }}" class="btn btn-default">Annuler</a>
        </div>
    </form>
</div>
@endsection
