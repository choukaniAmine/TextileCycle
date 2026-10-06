@extends('layouts.admin')
@section('title', 'Gestion des dons')
@section('content')
{{-- Filtre rapide par statut avec compteurs --}}
<div class="mb-3">
    <a href="{{ route('admin.dons.index', request()->except('status', 'page')) }}"
       class="btn btn-sm {{ request('status') ? 'btn-outline-secondary' : 'btn-secondary' }}">Tous <span class="badge badge-light">{{ $counts->sum() }}</span></a>
    @foreach ($statuses as $s)
        <a href="{{ route('admin.dons.index', array_merge(request()->except('page'), ['status' => $s->value])) }}"
           class="btn btn-sm {{ request('status') === $s->value ? 'btn-'.$s->badge() : 'btn-outline-'.$s->badge() }}">
            {{ $s->label() }} <span class="badge badge-light">{{ $counts[$s->value] ?? 0 }}</span></a>
    @endforeach
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" class="form-inline">
            @if (request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <input type="text" name="q" value="{{ request('q') }}" class="form-control mr-2" placeholder="Référence ou contenu…">
            <select name="association" class="form-control mr-2">
                <option value="">Toutes les associations</option>
                @foreach ($associations as $a)<option value="{{ $a->id }}" @selected((string) request('association') === (string) $a->id)>{{ $a->name }}</option>@endforeach
            </select>
            <button class="btn btn-secondary mr-2"><i class="fas fa-search"></i></button>
            <a href="{{ route('admin.dons.create') }}" class="btn btn-primary ml-auto"><i class="fas fa-plus"></i> Nouveau don</a>
        </form>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover">
            <thead><tr><th>Référence</th><th>Association</th><th>Contenu</th><th>Donateur</th><th>Date</th><th>Statut</th><th class="text-right">Actions</th></tr></thead>
            <tbody>
            @forelse ($dons as $d)
                <tr>
                    <td><a href="{{ route('admin.dons.show', $d) }}">{{ $d->reference }}</a></td>
                    <td>{{ $d->association->name }}</td>
                    <td>{{ $d->description }} <small class="text-muted">({{ $d->quantity }} pièce(s))</small></td>
                    <td>{{ $d->donor?->name ?? '—' }}</td>
                    <td>{{ $d->donated_at->format('d/m/Y') }}</td>
                    <td><span class="badge badge-{{ $d->status->badge() }}">{{ $d->status->label() }}</span></td>
                    <td class="text-right text-nowrap">
                        <a href="{{ route('admin.dons.show', $d) }}" class="btn btn-sm btn-secondary"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.dons.edit', $d) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.dons.destroy', $d) }}" class="d-inline" onsubmit="return confirm('Supprimer le don {{ $d->reference }} ?')">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Aucun don trouvé.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $dons->links('pagination::bootstrap-4') }}</div>
</div>
@endsection
