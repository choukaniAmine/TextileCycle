@extends('layouts.admin')

@section('title', 'Ajouter un Atelier')

@section('content')
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Nouveau profil Atelier</h3>
        <div class="card-tools">
            <a href="{{ route('admin.ateliers.index') }}" class="btn btn-tool text-white">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <form action="{{ route('admin.ateliers.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-8 form-group">
                    <label for="nom">Nom de l'atelier <span class="text-danger">*</span></label>
                    <input type="text" name="nom" id="nom" value="{{ old('nom') }}" class="form-control @error('nom') is-invalid @enderror" placeholder="Ex: EcoStyle Retouche">
                    @error('nom') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-4 form-group">
                    <label for="ville">Ville <span class="text-danger">*</span></label>
                    <input type="text" name="ville" id="ville" value="{{ old('ville') }}" class="form-control @error('ville') is-invalid @enderror" placeholder="Ex: Tunis, Sousse...">
                    @error('ville') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description de l'atelier</label>
                <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Présentation, spécialités...">{{ old('description') }}</textarea>
                @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="row">
                <div class="col-md-8 form-group">
                    <label for="adresse">Adresse complète <span class="text-danger">*</span></label>
                    <input type="text" name="adresse" id="adresse" value="{{ old('adresse') }}" class="form-control @error('adresse') is-invalid @enderror" placeholder="Ex: 15 Rue de la Liberté">
                    @error('adresse') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-4 form-group">
                    <label for="code_postal">Code Postal</label>
                    <input type="text" name="code_postal" id="code_postal" value="{{ old('code_postal') }}" class="form-control @error('code_postal') is-invalid @enderror" placeholder="Ex: 1000">
                    @error('code_postal') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="telephone">Téléphone <span class="text-danger">*</span></label>
                    <input type="text" name="telephone" id="telephone" value="{{ old('telephone') }}" class="form-control @error('telephone') is-invalid @enderror" placeholder="Ex: +216 71 000 000">
                    @error('telephone') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6 form-group">
                    <label for="email">Adresse Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="Ex: contact@atelier.tn">
                    @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="horaires">Horaires d'ouverture</label>
                    <input type="text" name="horaires" id="horaires" value="{{ old('horaires', 'Lun - Sam: 09h00 - 18h00') }}" class="form-control @error('horaires') is-invalid @enderror">
                    @error('horaires') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6 form-group">
                    <label for="image">Photo / Logo</label>
                    <input type="file" name="image" id="image" class="form-control-file @error('image') is-invalid @enderror" accept="image/*">
                    @error('image') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group custom-control custom-switch mt-2">
                <input type="checkbox" class="custom-control-input" id="est_actif" name="est_actif" value="1" {{ old('est_actif', '1') == '1' ? 'checked' : '' }}>
                <label class="custom-control-label" for="est_actif">Atelier actif (visible publiquement)</label>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Enregistrer l'atelier</button>
            <a href="{{ route('admin.ateliers.index') }}" class="btn btn-default">Annuler</a>
        </div>
    </form>
</div>
@endsection
