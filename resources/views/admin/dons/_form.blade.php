@csrf
<div class="card-body">
    @if ($errors->any())
        <div class="alert alert-danger py-2">Le formulaire contient {{ $errors->count() }} erreur(s), corrigez-les ci-dessous.</div>
    @endif
    @if ($don->exists)
        <div class="alert alert-info py-2">Statut actuel : <strong>{{ $don->status->label() }}</strong>. Il ne peut être modifié que par l’association bénéficiaire.</div>
    @endif
    <div class="row">
        <div class="form-group col-md-6">
            <label for="association_id">Association bénéficiaire *</label>
            <select id="association_id" name="association_id" class="form-control @error('association_id') is-invalid @enderror">
                <option value="">— Choisir —</option>
                @foreach ($associations as $a)<option value="{{ $a->id }}" @selected((string) old('association_id', $don->association_id) === (string) $a->id)>{{ $a->name }}</option>@endforeach
            </select>
            @error('association_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
        <div class="form-group col-md-6">
            <label for="user_id">Donateur</label>
            <select id="user_id" name="user_id" class="form-control @error('user_id') is-invalid @enderror">
                <option value="">— Anonyme / non renseigné —</option>
                @foreach ($donors as $u)<option value="{{ $u->id }}" @selected((string) old('user_id', $don->user_id) === (string) $u->id)>{{ $u->name }} ({{ $u->email }})</option>@endforeach
            </select>
            @error('user_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
        <div class="form-group col-md-12">
            <label for="description">Vêtements donnés *</label>
            <textarea id="description" name="description" rows="2" placeholder="Ex. 3 pantalons, 2 vestes" class="form-control @error('description') is-invalid @enderror">{{ old('description', $don->description) }}</textarea>
            @error('description')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
        <div class="form-group col-md-6">
            <label for="quantity">Nombre de pièces *</label>
            <input id="quantity" type="number" min="1" name="quantity" value="{{ old('quantity', $don->quantity) }}" class="form-control @error('quantity') is-invalid @enderror">
            @error('quantity')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
        <div class="form-group col-md-6">
            <label for="donated_at">Date du don *</label>
            <input id="donated_at" type="date" name="donated_at" value="{{ old('donated_at', $don->donated_at?->format('Y-m-d')) }}" class="form-control @error('donated_at') is-invalid @enderror">
            @error('donated_at')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
        <div class="form-group col-md-12">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $don->notes) }}</textarea>
            @error('notes')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
    </div>
</div>
<div class="card-footer">
    <button class="btn btn-primary">Enregistrer</button>
    <a href="{{ route('admin.dons.index') }}" class="btn btn-default">Annuler</a>
</div>
