@extends('layouts.front')
@section('title', 'Faire un don')
@section('content')
<div class="container py-5" style="max-width:640px">
    <h2 class="mb-1">Faire un don de vêtements</h2>
    <p class="text-muted">Indiquez ce que vous donnez : l’association validera votre don.</p>

    <form method="POST" action="{{ route('dons.store') }}" novalidate>
        @csrf
        <div class="mb-3">
            <label for="association_id" class="form-label">Association *</label>
            <select id="association_id" name="association_id" class="form-select @error('association_id') is-invalid @enderror">
                <option value="">— Choisir une association —</option>
                @foreach ($associations as $a)
                    <option value="{{ $a->id }}" @selected((string) old('association_id', $don->association_id) === (string) $a->id)>{{ $a->name }}@if($a->city) ({{ $a->city }})@endif</option>
                @endforeach
            </select>
            @error('association_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Vêtements donnés *</label>
            <textarea id="description" name="description" rows="3" placeholder="Ex. 3 pantalons, 2 vestes" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="quantity" class="form-label">Nombre de pièces *</label>
                <input id="quantity" type="number" min="1" name="quantity" value="{{ old('quantity', $don->quantity) }}" class="form-control @error('quantity') is-invalid @enderror">
                @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="donated_at" class="form-label">Date *</label>
                <input id="donated_at" type="date" name="donated_at" value="{{ old('donated_at', $don->donated_at->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}" class="form-control @error('donated_at') is-invalid @enderror">
                @error('donated_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="mb-3">
            <label for="notes" class="form-label">Remarques</label>
            <textarea id="notes" name="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button class="btn btn-primary">Envoyer mon don</button>
        <a href="{{ route('dons.index') }}" class="btn btn-link">Annuler</a>
    </form>
</div>
@endsection
