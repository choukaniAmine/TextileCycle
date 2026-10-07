@csrf
<div class="mb-3">
    <label class="form-label">Nom</label>
    <input name="nom" class="form-control @error('nom') is-invalid @enderror"
           value="{{ old('nom', $vetement->nom ?? '') }}">
    @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Description</label>
    <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $vetement->description ?? '') }}</textarea>
    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <label class="form-label">Taille</label>
        <input name="taille" class="form-control @error('taille') is-invalid @enderror"
               value="{{ old('taille', $vetement->taille ?? '') }}">
        @error('taille') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">État</label>
        <select name="etat" class="form-select @error('etat') is-invalid @enderror">
            @foreach($etats as $e)
                <option value="{{ $e->value }}" @selected(old('etat', isset($vetement) ? $vetement->etat->value : '') === $e->value)>{{ $e->label() }}</option>
            @endforeach
        </select>
        @error('etat') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Type</label>
        <select name="type" class="form-select @error('type') is-invalid @enderror">
            @foreach($types as $t)
                <option value="{{ $t->value }}" @selected(old('type', isset($vetement) ? $vetement->type->value : '') === $t->value)>{{ $t->label() }}</option>
            @endforeach
        </select>
        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Catégorie</label>
        <select name="categorie_id" class="form-select @error('categorie_id') is-invalid @enderror">
            @foreach($categories as $c)
                <option value="{{ $c->id }}" @selected(old('categorie_id', $vetement->categorie_id ?? '') == $c->id)>{{ $c->nom }}</option>
            @endforeach
        </select>
        @error('categorie_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-4">
    <label class="form-label">Image</label>
    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
    @if(isset($vetement) && $vetement->image)
        <img src="{{ $vetement->imageUrl() }}" width="120" class="mt-2 rounded">
    @endif
</div>

<button class="btn btn-primary">Enregistrer</button>
<a href="{{ route('mes-vetements.index') }}" class="btn btn-outline-secondary">Annuler</a>