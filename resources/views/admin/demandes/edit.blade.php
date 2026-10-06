@extends('layouts.admin')
@section('title', 'Modifier la demande : '.$demande->titre)
@section('content')
<div class="card card-primary">
    <form method="POST" action="{{ route('admin.demandes.update', $demande) }}">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="form-group">
                <label>Statut</label>
                <select name="statut" class="form-control @error('statut') is-invalid @enderror">
                    @foreach ($statuts as $s)<option value="{{ $s->value }}" @selected(old('statut', $demande->statut->value) === $s->value)>{{ $s->label() }}</option>@endforeach
                </select>
                @error('statut')<span class="invalid-feedback">{{ $message }}</span>@enderror
                <small class="text-muted">Le statut se met à jour automatiquement avec les interventions ; ce champ permet de forcer ou d'annuler.</small>
            </div>
            <div class="form-group">
                <label>Atelier assigné</label>
                <select name="atelier_id" class="form-control @error('atelier_id') is-invalid @enderror">
                    <option value="">— Aucun —</option>
                    @foreach ($ateliers as $a)<option value="{{ $a->id }}" @selected((string) old('atelier_id', $demande->atelier_id) === (string) $a->id)>{{ $a->organization ?? $a->name }} ({{ $a->city }})</option>@endforeach
                </select>
                @error('atelier_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="urgent" name="urgent" value="1" @checked(old('urgent', $demande->urgent))>
                <label class="custom-control-label" for="urgent">Demande urgente</label>
            </div>
        </div>
        <div class="card-footer"><button class="btn btn-primary">Enregistrer</button>
            <a href="{{ route('admin.demandes.show', $demande) }}" class="btn btn-default">Annuler</a></div>
    </form>
</div>
@endsection
