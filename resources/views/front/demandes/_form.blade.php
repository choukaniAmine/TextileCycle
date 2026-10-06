@csrf
<div class="mb-4">
    <label class="form-label fw-bold">Que souhaitez-vous faire ?</label>
    <div class="row g-3">
        @foreach (\App\Enums\TypeDemande::cases() as $t)
            <div class="col-6">
                <label class="type-card w-100 h-100">
                    <input type="radio" name="type" value="{{ $t->value }}" @checked(old('type', $demande->type?->value ?? 'reparation') === $t->value)>
                    <span class="box"><span class="big">{{ $t->emoji() }}</span><strong>{{ $t->label() }}</strong><br><small class="text-muted">{{ $t->description() }}</small></span>
                </label>
            </div>
        @endforeach
    </div>
    @error('type')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Titre de la demande</label>
        <input type="text" name="titre" value="{{ old('titre', $demande->titre) }}" placeholder="Ex : Réparer une veste déchirée" class="form-control @error('titre') is-invalid @enderror">
        @error('titre')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Vêtement concerné</label>
        <input type="text" name="vetement" value="{{ old('vetement', $demande->vetement) }}" placeholder="Ex : Veste en jean" class="form-control @error('vetement') is-invalid @enderror">
        @error('vetement')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Décrivez le besoin</label>
    <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" placeholder="Où est le défaut ? Quel résultat espérez-vous ?">{{ old('description', $demande->description) }}</textarea>
    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Photo (optionnelle, 2 Mo max)</label>
        <input type="file" name="photo" accept="image/*" class="form-control @error('photo') is-invalid @enderror">
        @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if ($demande->photo)<img src="{{ asset('storage/'.$demande->photo) }}" class="img-thumbnail mt-2" style="max-height:90px" alt="">@endif
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Date souhaitée</label>
        <input type="date" name="date_souhaitee" value="{{ old('date_souhaitee', $demande->date_souhaitee?->format('Y-m-d')) }}" class="form-control @error('date_souhaitee') is-invalid @enderror">
        @error('date_souhaitee')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="form-check form-switch mb-4">
    <input class="form-check-input" type="checkbox" id="urgent" name="urgent" value="1" @checked(old('urgent', $demande->urgent))>
    <label class="form-check-label" for="urgent">🔥 C’est urgent</label>
</div>
<button class="btn btn-success btn-lg">Envoyer ma demande</button>
<a href="{{ route('demandes.index') }}" class="btn btn-link">Annuler</a>
