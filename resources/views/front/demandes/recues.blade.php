@extends('layouts.front')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 fw-bold mb-0">Demandes reçues</h1>
            <a href="{{ route('mes-vetements.index') }}" class="btn btn-outline-secondary">← Mes vêtements</a>
        </div>

        @include('front._flash')

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Vêtement</th><th>Demandeur</th><th>Message</th><th>Date</th><th>Statut</th><th style="width:190px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($demandes as $d)
                        <tr>
                            <td><a href="{{ route('vetements.show', $d->vetement) }}">{{ $d->vetement->nom }}</a></td>
                            <td>
                                {{ $d->demandeur->name }}<br>
                                <small class="text-muted">{{ $d->demandeur->city }}</small>
                                @if($d->statut === \App\Enums\StatutDemande::Acceptee)
                                    <br><small>{{ $d->demandeur->email }}</small>
                                @endif
                            </td>
                            <td class="small">{{ $d->message ?: '—' }}</td>
                            <td class="small">{{ $d->created_at->format('d/m/Y H:i') }}</td>
                            <td><span class="badge bg-{{ $d->statut->badge() }}">{{ $d->statut->label() }}</span></td>
                            <td>
                                @can('repondre', $d)
                                    <div class="d-flex gap-1">
                                        <form method="POST" action="{{ route('demandes.accepter', $d) }}">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-sm btn-success">Accepter</button>
                                        </form>
                                        <form method="POST" action="{{ route('demandes.refuser', $d) }}">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-sm btn-outline-danger">Refuser</button>
                                        </form>
                                    </div>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Aucune demande reçue.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $demandes->links('pagination::bootstrap-5') }}
        </div>
    </div>
</section>
@endsection