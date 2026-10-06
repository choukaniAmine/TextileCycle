@extends('layouts.admin')
@section('title', $association->name)
@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body">
                <p class="text-muted">{{ $association->description ?? 'Aucune description.' }}</p>
                <ul class="list-unstyled mb-0">
                    <li><i class="fas fa-user-check fa-fw"></i> Responsable : {{ $association->manager?->name ?? 'aucun compte rattaché' }}</li>
                    <li><i class="fas fa-envelope fa-fw"></i> {{ $association->email ?? '—' }}</li>
                    <li><i class="fas fa-phone fa-fw"></i> {{ $association->phone ?? '—' }}</li>
                    <li><i class="fas fa-map-marker-alt fa-fw"></i> {{ $association->address ?? '' }} {{ $association->city }}</li>
                </ul>
                <hr>
                <span class="badge badge-{{ $association->is_active ? 'success' : 'secondary' }}">{{ $association->is_active ? 'Active' : 'Inactive' }}</span>
                <span class="badge badge-info">{{ $totalPieces }} pièce(s) livrée(s)</span>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.associations.edit', $association) }}" class="btn btn-info btn-sm"><i class="fas fa-edit"></i> Modifier</a>
                <a href="{{ route('admin.dons.create', ['association' => $association->id]) }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Ajouter un don</a>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mt-2">Historique des dons reçus</h3>
                <form method="GET" class="form-inline float-right">
                    <select name="status" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                        <option value="">Tous les statuts</option>
                        @foreach ($statuses as $s)<option value="{{ $s->value }}" @selected(request('status') === $s->value)>{{ $s->label() }}</option>@endforeach
                    </select>
                </form>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                    <thead><tr><th>Référence</th><th>Contenu</th><th>Donateur</th><th>Date</th><th>Statut</th></tr></thead>
                    <tbody>
                    @forelse ($dons as $d)
                        <tr>
                            <td><a href="{{ route('admin.dons.show', $d) }}">{{ $d->reference }}</a></td>
                            <td>{{ $d->description }} <small class="text-muted">({{ $d->quantity }})</small></td>
                            <td>{{ $d->donor?->name ?? '—' }}</td>
                            <td>{{ $d->donated_at->format('d/m/Y') }}</td>
                            <td><span class="badge badge-{{ $d->status->badge() }}">{{ $d->status->label() }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Aucun don pour ce filtre.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $dons->links('pagination::bootstrap-4') }}</div>
        </div>
    </div>
</div>
<a href="{{ route('admin.associations.index') }}" class="btn btn-default"><i class="fas fa-arrow-left"></i> Retour</a>
@endsection
