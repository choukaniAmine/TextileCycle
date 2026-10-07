@extends('layouts.admin')
@section('title', 'Vêtements')
@section('content')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('admin.vetements.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Ajouter
    </a>
</div>

<form method="GET" class="form-inline mb-3">
    <select name="categorie_id" class="form-control mr-2">
        <option value="">Toutes les catégories</option>
        @foreach($categories as $c)
            <option value="{{ $c->id }}" @selected(request('categorie_id') == $c->id)>{{ $c->nom }}</option>
        @endforeach
    </select>
    <select name="etat" class="form-control mr-2">
        <option value="">Tous les états</option>
        @foreach($etats as $e)
            <option value="{{ $e->value }}" @selected(request('etat') === $e->value)>{{ $e->label() }}</option>
        @endforeach
    </select>
    <button class="btn btn-secondary mr-2">Filtrer</button>
    <a href="{{ route('admin.vetements.index') }}" class="btn btn-light">Réinitialiser</a>
</form>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-bordered mb-0">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Taille</th>
                    <th>État</th>
                    <th>Type</th>
                    <th width="160">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($vetements as $v)
                <tr>
                    <td>{{ $v->nom }}</td>
                    <td>{{ $v->categorie->nom }}</td>
                    <td>{{ $v->taille }}</td>
                    <td>{{ $v->etat->label() }}</td>
                    <td>{{ $v->type->label() }}</td>
                    <td>
                        <a href="{{ route('admin.vetements.edit', $v) }}" class="btn btn-sm btn-warning">Modifier</a>
                        <form method="POST" action="{{ route('admin.vetements.destroy', $v) }}" class="d-inline"
                              onsubmit="return confirm('Supprimer ce vêtement ?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">Aucun vêtement.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $vetements->links() }}
</div>
@endsection