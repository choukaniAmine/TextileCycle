@csrf
<div class="card-body">
    @if ($errors->any())
        <div class="alert alert-danger py-2">Le formulaire contient {{ $errors->count() }} erreur(s), corrigez-les ci-dessous.</div>
    @endif
    <div class="row">
        @foreach ([['name','Nom de l’association *','text','col-md-12'],['email','E-mail','email','col-md-6'],['phone','Téléphone','text','col-md-6'],['city','Ville','text','col-md-6'],['address','Adresse','text','col-md-6']] as [$f,$label,$type,$col])
            <div class="form-group {{ $col }}">
                <label for="{{ $f }}">{{ $label }}</label>
                <input id="{{ $f }}" type="{{ $type }}" name="{{ $f }}" value="{{ old($f, $association->{$f}) }}" class="form-control @error($f) is-invalid @enderror">
                @error($f)<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
        @endforeach
        <div class="form-group col-md-12">
            <label for="manager_id">Compte responsable des dons</label>
            <select id="manager_id" name="manager_id" class="form-control @error('manager_id') is-invalid @enderror">
                <option value="">— Aucun (l’association ne peut pas encore traiter ses dons) —</option>
                @foreach ($managers as $m)<option value="{{ $m->id }}" @selected((string) old('manager_id', $association->manager_id) === (string) $m->id)>{{ $m->name }} ({{ $m->email }})</option>@endforeach
            </select>
            @error('manager_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
            <small class="text-muted">Ce compte (rôle « Association ») accepte ou refuse les dons reçus.</small>
        </div>
        <div class="form-group col-md-12">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $association->description) }}</textarea>
            @error('description')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
    </div>
    <div class="custom-control custom-switch">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" @checked(old('is_active', $association->is_active))>
        <label class="custom-control-label" for="is_active">Association active (visible et recevant des dons)</label>
    </div>
</div>
<div class="card-footer">
    <button class="btn btn-primary">Enregistrer</button>
    <a href="{{ route('admin.associations.index') }}" class="btn btn-default">Annuler</a>
</div>
