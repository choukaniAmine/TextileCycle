@extends('layouts.front')
@section('title', 'Dons reçus')
@section('content')
<div class="container py-5">
    @if (! $association)
        <div class="alert alert-warning">
            <h5 class="alert-heading">Compte non rattaché</h5>
            Votre compte n’est lié à aucune association pour le moment. Demandez à l’administrateur de vous désigner
            comme responsable d’une association pour pouvoir traiter ses dons.
        </div>
    @else
        <h2 class="mb-1">Dons reçus</h2>
        <p class="text-muted">{{ $association->name }} — acceptez ou refusez les dons, puis suivez leur collecte jusqu’à la livraison.</p>

        {{-- Filtre par statut avec compteurs --}}
        <div class="mb-3">
            <a href="{{ route('espace.dons.index') }}" class="btn btn-sm {{ request('status') ? 'btn-outline-secondary' : 'btn-secondary' }}">Tous ({{ $counts->sum() }})</a>
            @foreach ($statuses as $s)
                <a href="{{ route('espace.dons.index', ['status' => $s->value]) }}"
                   class="btn btn-sm {{ request('status') === $s->value ? 'btn-'.$s->badge() : 'btn-outline-'.$s->badge() }}">{{ $s->label() }} ({{ $counts[$s->value] ?? 0 }})</a>
            @endforeach
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Référence</th><th>Donateur</th><th>Vêtements</th><th>Date</th><th>Statut</th><th class="text-end">Action</th></tr></thead>
                <tbody>
                @forelse ($dons as $d)
                    <tr>
                        <td>{{ $d->reference }}</td>
                        <td>{{ $d->donor?->name ?? '—' }}@if($d->donor?->phone)<br><small class="text-muted">{{ $d->donor->phone }}</small>@endif</td>
                        <td>{{ $d->description }} <small class="text-muted">({{ $d->quantity }})</small>@if($d->notes)<br><small class="text-muted">{{ $d->notes }}</small>@endif</td>
                        <td>{{ $d->donated_at->format('d/m/Y') }}</td>
                        <td><span class="badge bg-{{ $d->status->badge() }}">{{ $d->status->label() }}</span></td>
                        <td class="text-end text-nowrap">
                            @forelse ($d->status->transitions() as $next)
                                <form method="POST" action="{{ route('espace.dons.status', $d) }}" class="d-inline"
                                      @if($next === \App\Enums\DonStatus::Refuse) onsubmit="return confirm('Refuser ce don ?')" @endif>
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $next->value }}">
                                    <button class="btn btn-sm btn-{{ $next === \App\Enums\DonStatus::Refuse ? 'outline-danger' : 'success' }}">{{ $next->actionLabel() }}</button>
                                </form>
                            @empty
                                <span class="text-muted small">Terminé</span>
                            @endforelse
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Aucun don pour ce filtre.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $dons->links('pagination::bootstrap-5') }}
    @endif
</div>
@endsection
