@php($u = $user)
@foreach ([['name','Nom complet','text',true],['email','E-mail','email',true],['organization','Nom de l’atelier / association','text',false],['city','Ville','text',false],['phone','Téléphone','text',false]] as [$f,$label,$type,$req])
    <div class="mb-3">
        <label class="form-label">{{ $label }}</label>
        <input type="{{ $type }}" name="{{ $f }}" value="{{ old($f, $u?->{$f}) }}" class="form-control @error($f) is-invalid @enderror" @required($req)>
        @error($f)<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
@endforeach
@if ($u)
    <div class="mb-3">
        <label class="form-label">Présentation</label>
        <textarea name="bio" rows="3" class="form-control @error('bio') is-invalid @enderror">{{ old('bio', $u->bio) }}</textarea>
        @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
@endif
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Mot de passe @if($u)<small class="text-muted">(laisser vide = inchangé)</small>@endif</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" @required(! $u)>
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Confirmation</label>
        <input type="password" name="password_confirmation" class="form-control" @required(! $u)>
    </div>
</div>
