@csrf
<div class="form-group">
    <label>Nom</label>
    <input name="nom" class="form-control @error('nom') is-invalid @enderror"
           value="{{ old('nom', $categorie->nom ?? '') }}">
    @error('nom') <span class="invalid-feedback">{{ $message }}</span> @enderror
</div>

<div class="form-group">
    <label>Description</label>
    <textarea name="description" rows="4"
              class="form-control @error('description') is-invalid @enderror">{{ old('description', $categorie->description ?? '') }}</textarea>
    @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
</div>

<button class="btn btn-primary">Enregistrer</button>
<a href="{{ route('admin.categories.index') }}" class="btn btn-light">Annuler</a>