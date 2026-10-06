@csrf
<div class="card-body">
    <div class="row">
        @foreach ([['name','Nom complet','text'],['email','E-mail','email'],['organization','Atelier / association','text'],['city','Ville','text'],['phone','Téléphone','text']] as [$f,$label,$type])
            <div class="form-group col-md-6">
                <label>{{ $label }}</label>
                <input type="{{ $type }}" name="{{ $f }}" value="{{ old($f, $user->{$f}) }}" class="form-control @error($f) is-invalid @enderror">
                @error($f)<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
        @endforeach
        <div class="form-group col-md-6">
            <label>Rôle</label>
            <select name="role" class="form-control @error('role') is-invalid @enderror">
                @foreach ($roles as $r)<option value="{{ $r->value }}" @selected(old('role', $user->role?->value) === $r->value)>{{ $r->label() }}</option>@endforeach
            </select>
            @error('role')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
        <div class="form-group col-md-6">
            <label>Mot de passe @if($user->exists)<small class="text-muted">(vide = inchangé)</small>@endif</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
            @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>
        <div class="form-group col-md-6">
            <label>Confirmation</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>
    </div>
    <div class="custom-control custom-switch">
        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" @checked(old('is_active', $user->is_active))>
        <label class="custom-control-label" for="is_active">Compte actif</label>
    </div>
</div>
<div class="card-footer">
    <button class="btn btn-primary">Enregistrer</button>
    <a href="{{ route('admin.users.index') }}" class="btn btn-default">Annuler</a>
</div>
