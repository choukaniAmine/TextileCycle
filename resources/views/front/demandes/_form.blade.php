@csrf
<div class="demande-form">

    {{-- Étape 1 : type de demande --}}
    <div class="form-section-title"><span class="num">1</span> Que souhaitez-vous faire ?</div>
    <div class="row g-3">
        @foreach (\App\Enums\TypeDemande::cases() as $t)
            <div class="col-md-6">
                <label class="type-card {{ $t->value }}">
                    <input type="radio" name="type" value="{{ $t->value }}"
                           @checked(old('type', $demande->type?->value ?? 'reparation') === $t->value)>
                    <span class="box">
                        <span class="check">✓</span>
                        <span class="emoji">{{ $t->emoji() }}</span>
                        <span class="titre">{{ $t->label() }}</span>
                        <span class="desc">{{ $t->description() }}</span>
                    </span>
                </label>
            </div>
        @endforeach
    </div>
    @error('type')<div class="text-danger small mt-2">{{ $message }}</div>@enderror

    <hr class="form-divider">

    {{-- Étape 2 : le vêtement --}}
    <div class="form-section-title"><span class="num">2</span> Parlez-nous de votre vêtement</div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label" for="titre">Titre de la demande</label>
            <input type="text" id="titre" name="titre" value="{{ old('titre', $demande->titre) }}"
                   placeholder="Ex : Réparer une veste déchirée"
                   class="form-control @error('titre') is-invalid @enderror">
            @error('titre')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label" for="vetement">Vêtement concerné</label>
            <input type="text" id="vetement" name="vetement" value="{{ old('vetement', $demande->vetement) }}"
                   placeholder="Ex : Veste en jean"
                   class="form-control @error('vetement') is-invalid @enderror">
            @error('vetement')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label" for="description">Décrivez le besoin</label>
        <textarea id="description" name="description" rows="4"
                  placeholder="Où est le défaut ? Quel résultat espérez-vous ?"
                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $demande->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <hr class="form-divider">

    {{-- Étape 3 : détails pratiques --}}
    <div class="form-section-title"><span class="num">3</span> Détails pratiques</div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label" for="photo">Photo (optionnelle, 2 Mo max)</label>
            <input type="file" id="photo" name="photo" accept="image/*"
                   class="form-control @error('photo') is-invalid @enderror">
            @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            @if ($demande->photo)
                <img src="{{ asset('storage/'.$demande->photo) }}" class="img-thumbnail mt-2" style="max-height:90px" alt="Photo actuelle">
            @endif
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label" for="date_souhaitee">Date souhaitée (optionnelle)</label>
            <input type="date" id="date_souhaitee" name="date_souhaitee"
                   value="{{ old('date_souhaitee', $demande->date_souhaitee?->format('Y-m-d')) }}"
                   class="form-control @error('date_souhaitee') is-invalid @enderror">
            @error('date_souhaitee')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="urgent-box mb-4">
        <div class="form-check form-switch p-0 m-0">
            <input class="form-check-input" type="checkbox" id="urgent" name="urgent" value="1"
                   @checked(old('urgent', $demande->urgent))>
        </div>
        <label for="urgent">🔥 C’est urgent
            <small>Les ateliers verront votre demande en premier.</small>
        </label>
    </div>

    <button class="btn btn-send btn-lg">Envoyer ma demande</button>
    <a href="{{ route('demandes.index') }}" class="btn btn-link text-muted">Annuler</a>
</div>