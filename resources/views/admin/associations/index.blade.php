@extends('layouts.admin')
@section('title', 'Gestion des associations')
@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" class="form-inline">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control mr-2" placeholder="Nom ou ville…">
            <button class="btn btn-secondary mr-2"><i class="fas fa-search"></i></button>
            <a href="{{ route('admin.associations.create') }}" class="btn btn-primary ml-auto"><i class="fas fa-plus"></i> Nouvelle association</a>
        </form>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover">
            <thead><tr><th>Association</th><th>Contact</th><th>Ville</th><th class="text-center">Dons</th><th>Statut</th><th class="text-right">Actions</th></tr></thead>
            <tbody>
            @forelse ($associations as $a)
                <tr>
                    <td><a href="{{ route('admin.associations.show', $a) }}"><strong>{{ $a->name }}</strong></a></td>
                    <td>{{ $a->email ?? '—' }}@if($a->phone)<br><small class="text-muted">{{ $a->phone }}</small>@endif</td>
                    <td>{{ $a->city ?? '—' }}</td>
                    <td class="text-center"><span class="badge badge-info">{{ $a->dons_count }}</span></td>
                    <td><span class="badge badge-{{ $a->is_active ? 'success' : 'secondary' }}">{{ $a->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="text-right text-nowrap">
                        <a href="{{ route('admin.associations.show', $a) }}" class="btn btn-sm btn-secondary" title="Détail et historique"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.associations.edit', $a) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.associations.destroy', $a) }}" class="d-inline"
                              onsubmit="return confirm('Supprimer {{ addslashes($a->name) }} et ses {{ $a->dons_count }} don(s) ?')">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Aucune association trouvée.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $associations->links('pagination::bootstrap-4') }}</div>
</div>
@endsection
