@extends('layouts.front')
@section('title', 'Demandes de dons reçues')

@section('content')
@include('front._hero', [
    'badge' => '📬 Boîte de réception',
    'titre' => 'Demandes de dons reçues',
    'sous' => 'Acceptez ou refusez les demandes faites pour vos vêtements.',
    'actions' => '<a href="'.route('mes-vetements.index').'" class="btn btn-outline-light"><i class="bi bi-arrow-left"></i> Mes vêtements</a>',
])

<section class="page-body">
    <div class="container">
        <div class="rf-card overflow-hidden">
            <div class="table-responsive">
                <table class="table rf-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Vêtement</th>
                            <th>Demandeur</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th style="width:210px;">Actions</th>
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
                                <strong>{{ $d->demandeur->name }}</strong><br>
                                <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $d->demandeur->city }}</small>
                                @if($d->statut === \App\Enums\StatutDemande::Acceptee)
                                    <br><small><i class="bi bi-envelope"></i> {{ $d->demandeur->email }}</small>
                                @endif
                            </td>
                            <td class="small">{{ $d->message ?: '—' }}</td>
                            <td class="small text-muted">{{ $d->created_at->format('d/m/Y H:i') }}</td>
                            <td><span class="badge bg-{{ $d->statut->badge() }}">{{ $d->statut->label() }}</span></td>
                            <td>
                                @can('repondre', $d)
                                    <div class="d-flex gap-1">
                                        <form method="POST" action="{{ route('demandes-don.accepter', $d) }}">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-sm btn-primary"><i class="bi bi-check2"></i> Accepter</button>
                                        </form>
                                        <form method="POST" action="{{ route('demandes-don.refuser', $d) }}"
                                              onsubmit="return confirm('Refuser cette demande ?')">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-sm btn-outline-danger">Refuser</button>
                                        </form>
                                    </div>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5">📭 Aucune demande reçue.</td></tr>
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