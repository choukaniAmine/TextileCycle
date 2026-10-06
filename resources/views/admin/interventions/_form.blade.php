@csrf
<div class="card-body">
    <div class="row">
        <div class="form-group col-md-8">
            <label>Titre de l'intervention</label>
            <input type="text" name="titre" value="{{ old('titre', $intervention->titre) }}" placeholder="Ex : Remplacement de la fermeture" class="form-control @error('titre') is-invalid @enderror">
            @error('titre')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
        <div class="form-group col-md-4">
            <label>Statut</label>
            <select name="statut" class="form-control @error('statut') is-invalid @enderror">
                @foreach ([\App\Enums\Statut::EnAttente, \App\Enums\Statut::EnCours, \App\Enums\Statut::Terminee] as $s)
                    <option value="{{ $s->value }}" @selected(old('statut', $intervention->statut?->value) === $s->value)>{{ $s->label() }}</option>
                @endforeach
            </select>
            @error('statut')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="form-group">
        <label>Description</label>
        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $intervention->description) }}</textarea>
        @error('description')<span class="invalid-feedback">{{ $message }}</span>@enderror
    </div>
    <div class="row">
        <div class="form-group col-md-6">
            <label>Atelier</label>
            <select name="atelier_id" class="form-control @error('atelier_id') is-invalid @enderror">
                <option value="">— Aucun —</option>
                @foreach ($ateliers as $a)<option value="{{ $a->id }}" @selected((string) old('atelier_id', $intervention->atelier_id) === (string) $a->id)>{{ $a->organization ?? $a->name }}</option>@endforeach
            </select>
            @error('atelier_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
        <div class="form-group col-md-6">
            <label>Coût estimé (DT)</label>
            <input type="number" step="0.01" name="cout_estime" value="{{ old('cout_estime', $intervention->cout_estime) }}" class="form-control @error('cout_estime') is-invalid @enderror">
            @error('cout_estime')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
        <div class="form-group col-md-6">
            <label>Date de début</label>
            <input type="date" name="date_debut" value="{{ old('date_debut', $intervention->date_debut?->format('Y-m-d')) }}" class="form-control @error('date_debut') is-invalid @enderror">
            @error('date_debut')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
        <div class="form-group col-md-6">
            <label>Date de fin</label>
            <input type="date" name="date_fin" value="{{ old('date_fin', $intervention->date_fin?->format('Y-m-d')) }}" class="form-control @error('date_fin') is-invalid @enderror">
            @error('date_fin')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
    </div>
</div>
<div class="card-footer">
    <button class="btn btn-primary">Enregistrer</button>
    <a href="{{ route('admin.demandes.show', $demande) }}" class="btn btn-default">Annuler</a>
</div>
