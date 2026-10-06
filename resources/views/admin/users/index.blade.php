@extends('layouts.admin')
@section('title', 'Gestion des utilisateurs')
@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" class="form-inline">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control mr-2" placeholder="Nom, e-mail, ville…">
            <select name="role" class="form-control mr-2">
                <option value="">Tous les rôles</option>
                @foreach ($roles as $r)<option value="{{ $r->value }}" @selected(request('role') === $r->value)>{{ $r->label() }}</option>@endforeach
            </select>
            <button class="btn btn-secondary mr-2"><i class="fas fa-search"></i></button>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary ml-auto"><i class="fas fa-plus"></i> Nouvel utilisateur</a>
        </form>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover">
            <thead><tr><th>Nom</th><th>E-mail</th><th>Rôle</th><th>Ville</th><th>Statut</th><th class="text-right">Actions</th></tr></thead>
            <tbody>
            @forelse ($users as $u)
                <tr>
                    <td>{{ $u->name }}@if($u->organization)<br><small class="text-muted">{{ $u->organization }}</small>@endif</td>
                    <td>{{ $u->email }}</td>
                    <td><span class="badge badge-{{ $u->role->badge() }}">{{ $u->role->label() }}</span></td>
                    <td>{{ $u->city ?? '—' }}</td>
                    <td><span class="badge badge-{{ $u->is_active ? 'success' : 'secondary' }}">{{ $u->is_active ? 'Actif' : 'Désactivé' }}</span></td>
                    <td class="text-right text-nowrap">
                        <form method="POST" action="{{ route('admin.users.toggle', $u) }}" class="d-inline">@csrf @method('PATCH')
                            <button class="btn btn-sm btn-outline-secondary" title="Activer / désactiver"><i class="fas fa-power-off"></i></button></form>
                        <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="d-inline" onsubmit="return confirm('Supprimer cet utilisateur ?')">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Aucun utilisateur trouvé.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $users->links('pagination::bootstrap-4') }}</div>
</div>
@endsection
