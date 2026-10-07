@csrf
<div class="form-group">
    <label>Nom</label>
    <input name="nom" class="form-control @error('nom') is-invalid @enderror"
           value="{{ old('nom', $vetement->nom ?? '') }}">
    @error('nom') <span class="invalid-feedback">{{ $message }}</span> @enderror
</div>

<div class="form-group">
    <label>Description</label>
    <textarea name="description" class="form-control">{{ old('description', $vetement->description ?? '') }}</textarea>
</div>

<div class="form-row">
    <div class="form-group col-md-3">
        <label>Taille</label>
        <input name="taille" class="form-control @error('taille') is-invalid @enderror"
               value="{{ old('taille', $vetement->taille ?? '') }}">
        @error('taille') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
    <div class="form-group col-md-3">
        <label>État</label>
        <select name="etat" class="form-control @error('etat') is-invalid @enderror">
            @foreach($etats as $e)
                <option value="{{ $e->value }}" @selected(old('etat', isset($vetement) ? $vetement->etat->value : '') === $e->value)>{{ $e->label() }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-3">
        <label>Type</label>
        <select name="type" class="form-control @error('type') is-invalid @enderror">
            @foreach($types as $t)
                <option value="{{ $t->value }}" @selected(old('type', isset($vetement) ? $vetement->type->value : '') === $t->value)>{{ $t->label() }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-3">
        <label>Catégorie</label>
        <select name="categorie_id" class="form-control @error('categorie_id') is-invalid @enderror">
            @foreach($categories as $c)
                <option value="{{ $c->id }}" @selected(old('categorie_id', $vetement->categorie_id ?? '') == $c->id)>{{ $c->nom }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group">
    <label>Image</label>
    <input type="file" name="image" class="form-control-file">
    @error('image') <div class="text-danger">{{ $message }}</div> @enderror
    @if(isset($vetement) && $vetement->image)
        <img src="{{ $vetement->imageUrl() }}" width="100" class="mt-2">
    @endif
</div>

<button class="btn btn-primary">Enregistrer</button>
<a href="{{ route('admin.vetements.index') }}" class="btn btn-light">Annuler</a>