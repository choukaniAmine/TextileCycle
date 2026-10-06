<div class="row">
    <div class="col-md-8 mb-3">
        <label class="form-label">Titre de l’intervention</label>
        <input type="text" name="titre" value="{{ old('titre', $intervention->titre) }}" placeholder="Ex : Remplacement de la fermeture" class="form-control @error('titre') is-invalid @enderror">
        @error('titre')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Coût estimé (DT)</label>
        <input type="number" step="0.01" name="cout_estime" value="{{ old('cout_estime', $intervention->cout_estime) }}" class="form-control @error('cout_estime') is-invalid @enderror">
        @error('cout_estime')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
<div class="mb-3">
    <label class="form-label">Détails (optionnel)</label>
    <textarea name="description" rows="2" class="form-control @error('description') is-invalid @enderror">{{ old('description', $intervention->description) }}</textarea>
    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Début prévu</label>
        <input type="date" name="date_debut" value="{{ old('date_debut', $intervention->date_debut?->format('Y-m-d')) }}" class="form-control @error('date_debut') is-invalid @enderror">
        @error('date_debut')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Fin prévue</label>
        <input type="date" name="date_fin" value="{{ old('date_fin', $intervention->date_fin?->format('Y-m-d')) }}" class="form-control @error('date_fin') is-invalid @enderror">
        @error('date_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
