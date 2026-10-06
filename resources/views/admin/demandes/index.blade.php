@extends('layouts.admin')
@section('title', 'Demandes de réparation / transformation')
@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" class="form-inline">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control mr-2" placeholder="Titre, vêtement, client…">
            <select name="statut" class="form-control mr-2">
                <option value="">Tous les statuts</option>
                @foreach ($statuts as $s)<option value="{{ $s->value }}" @selected(request('statut') === $s->value)>{{ $s->label() }}</option>@endforeach
            </select>
            <select name="type" class="form-control mr-2">
                <option value="">Tous les types</option>
                @foreach ($types as $t)<option value="{{ $t->value }}" @selected(request('type') === $t->value)>{{ $t->label() }}</option>@endforeach
            </select>
            <button class="btn btn-secondary"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover">
            <thead><tr><th>Demande</th><th>Client</th><th>Type</th><th>Atelier</th><th>Interv.</th><th>Statut</th><th class="text-right">Actions</th></tr></thead>
            <tbody>
            @forelse ($demandes as $d)
                <tr>
                    <td>{{ $d->type->emoji() }} <strong>{{ $d->titre }}</strong> @if($d->urgent)<span class="badge badge-danger">Urgent</span>@endif
                        <br><small class="text-muted">{{ $d->vetement }}</small></td>
                    <td>{{ $d->user->name }}</td>
                    <td>{{ $d->type->label() }}</td>
                    <td>{{ $d->atelier?->organization ?? $d->atelier?->name ?? '—' }}</td>
                    <td><span class="badge badge-light">{{ $d->interventions_count }}</span></td>
                    <td><span class="badge badge-{{ $d->statut->color() }}">{{ $d->statut->label() }}</span></td>
                    <td class="text-right text-nowrap">
                        <a href="{{ route('admin.demandes.show', $d) }}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.demandes.edit', $d) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.demandes.destroy', $d) }}" class="d-inline" onsubmit="return confirm('Supprimer cette demande et ses interventions ?')">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Aucune demande.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $demandes->links('pagination::bootstrap-4') }}</div>
</div>
@endsection
