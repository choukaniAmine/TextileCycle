@extends('layouts.front')
@section('title', 'Mes demandes de dons')

@section('content')
@include('front._hero', [
    'badge' => '🎁 Suivi',
    'titre' => 'Mes demandes de dons',
    'sous' => 'Suivez les vêtements que vous avez demandés.',
    'actions' => '<a href="'.route('vetements.index').'" class="btn btn-light fw-bold">Parcourir les vêtements</a>',
])

<section class="page-body">
    <div class="container">
        <div class="rf-card overflow-hidden">
            <div class="table-responsive">
                <table class="table rf-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Vêtement</th>
                            <th>Propriétaire</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th style="width:130px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($demandes as $d)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if($d->vetement->image)
                                        <img src="{{ $d->vetement->imageUrl() }}" class="rf-thumb-sm" alt="">
                                    @else
                                        <div class="rf-thumb-sm-ph">👕</div>
                                    @endif
                                    <a class="fw-semibold" href="{{ route('vetements.show', $d->vetement) }}">{{ $d->vetement->nom }}</a>
                                </div>
                            </td>
                            <td>
                                <strong>{{ $d->vetement->user->name }}</strong><br>
                                <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $d->vetement->user->city }}</small>
                                @if($d->statut === \App\Enums\StatutDemande::Acceptee)
                                    <br><small><i class="bi bi-envelope"></i> {{ $d->vetement->user->email }}</small>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $d->created_at->format('d/m/Y H:i') }}</td>
                            <td><span class="badge bg-{{ $d->statut->badge() }}">{{ $d->statut->label() }}</span></td>
                            <td>
                                @can('annuler', $d)
                                    <form method="POST" action="{{ route('demandes-don.annuler', $d) }}"
                                          onsubmit="return confirm('Annuler cette demande ?')">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-outline-danger">Annuler</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-5">🛍️ Vous n'avez envoyé aucune demande.</td></tr>
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