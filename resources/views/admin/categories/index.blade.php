@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h3>Catégories</h3>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Ajouter</a>
</div>

@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Description</th>
            <th width="120">Vêtements</th>
            <th width="160">Actions</th>
        </tr>
    </thead>
    <tbody>
    @forelse($categories as $c)
        <tr>
            <td>{{ $c->nom }}</td>
            <td>{{ \Illuminate\Support\Str::limit($c->description, 80) ?: '—' }}</td>
            <td><span class="badge badge-info">{{ $c->vetements_count }}</span></td>
            <td>
                <a href="{{ route('admin.categories.edit', $c) }}" class="btn btn-sm btn-warning">Modifier</a>
                <form method="POST" action="{{ route('admin.categories.destroy', $c) }}" class="d-inline"
                      onsubmit="return confirm('Supprimer cette catégorie ?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Supprimer</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="4" class="text-center">Aucune catégorie.</td></tr>
    @endforelse
    </tbody>
</table>

{{ $categories->links() }}
@endsection