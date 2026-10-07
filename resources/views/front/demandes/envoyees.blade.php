@extends('layouts.front')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 fw-bold mb-0">Mes demandes</h1>
            <a href="{{ route('vetements.index') }}" class="btn btn-outline-secondary">Parcourir les vêtements</a>
        </div>

        @include('front._flash')

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Vêtement</th><th>Propriétaire</th><th>Date</th><th>Statut</th><th style="width:130px;">Action</th></tr>
                    </thead>
                    <tbody>
                    @forelse($demandes as $d)
                        <tr>
                            <td><a href="{{ route('vetements.show', $d->vetement) }}">{{ $d->vetement->nom }}</a></td>
                            <td>
                                {{ $d->vetement->user->name }}<br>
                                <small class="text-muted">{{ $d->vetement->user->city }}</small>
                                @if($d->statut === \App\Enums\StatutDemande::Acceptee)
                                    <br><small>{{ $d->vetement->user->email }}</small>
                                @endif
                            </td>
                            <td class="small">{{ $d->created_at->format('d/m/Y H:i') }}</td>
                            <td><span class="badge bg-{{ $d->statut->badge() }}">{{ $d->statut->label() }}</span></td>
                            <td>
                                @can('annuler', $d)
                                    <form method="POST" action="{{ route('demandes.annuler', $d) }}"
                                          onsubmit="return confirm('Annuler cette demande ?')">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-outline-danger">Annuler</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Vous n'avez envoyé aucune demande.</td></tr>
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