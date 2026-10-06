@extends('layouts.front')
@section('title', 'Mes dons')
@section('content')
<div class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Mes dons</h2>
        <a href="{{ route('dons.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Faire un don</a>
    </div>

    {{-- Filtre par statut --}}
    <div class="mb-3">
        <a href="{{ route('dons.index') }}" class="btn btn-sm {{ request('status') ? 'btn-outline-secondary' : 'btn-secondary' }}">Tous</a>
        @foreach ($statuses as $s)
            <a href="{{ route('dons.index', ['status' => $s->value]) }}"
               class="btn btn-sm {{ request('status') === $s->value ? 'btn-'.$s->badge() : 'btn-outline-'.$s->badge() }}">{{ $s->label() }}</a>
        @endforeach
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Référence</th><th>Association</th><th>Vêtements</th><th>Date</th><th>Statut</th><th></th></tr></thead>
            <tbody>
            @forelse ($dons as $d)
                <tr>
                    <td>{{ $d->reference }}</td>
                    <td>{{ $d->association->name }}</td>
                    <td>{{ $d->description }}</td>
                    <td>{{ $d->donated_at->format('d/m/Y') }}</td>
                    <td><span class="badge bg-{{ $d->status->badge() }}">{{ $d->status->label() }}</span></td>
                    <td class="text-end"><a href="{{ route('dons.show', $d) }}" class="btn btn-sm btn-outline-primary">Suivre</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Aucun don pour le moment.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $dons->links('pagination::bootstrap-5') }}
</div>
@endsection
