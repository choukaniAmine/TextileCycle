@extends('layouts.admin')
@section('title', 'Demande : '.$demande->titre)
@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card card-outline card-success">
            <div class="card-header"><h3 class="card-title">{{ $demande->type->emoji() }} {{ $demande->type->label() }} — {{ $demande->vetement }}</h3></div>
            <div class="card-body">
                @if ($demande->photo)<img src="{{ asset('storage/'.$demande->photo) }}" class="img-fluid rounded mb-3" alt="">@endif
                <p>{{ $demande->description }}</p>
                <dl class="mb-0">
                    <dt>Client</dt><dd>{{ $demande->user->name }} ({{ $demande->user->email }})</dd>
                    <dt>Atelier assigné</dt><dd>{{ $demande->atelier?->organization ?? $demande->atelier?->name ?? 'Non assigné' }}</dd>
                    <dt>Date souhaitée</dt><dd>{{ $demande->date_souhaitee?->format('d/m/Y') ?? '—' }}</dd>
                    <dt>Statut</dt><dd><span class="badge badge-{{ $demande->statut->color() }}">{{ $demande->statut->label() }}</span>
                        @if($demande->urgent)<span class="badge badge-danger">Urgent</span>@endif</dd>
                </dl>
            </div>
            <div class="card-footer"><a href="{{ route('admin.demandes.edit', $demande) }}" class="btn btn-info btn-sm">Modifier statut / atelier</a>
                <a href="{{ route('admin.demandes.index') }}" class="btn btn-default btn-sm">Retour</a></div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-body">
                <div class="text-center mb-2"><strong>Progression : {{ $demande->progression() }} %</strong></div>
                <div class="progress mb-3"><div class="progress-bar bg-success progress-bar-striped" style="width: {{ $demande->progression() }}%"></div></div>
                @include('partials.stepper', ['statut' => $demande->statut])
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Interventions ({{ $demande->interventions->count() }}) — total estimé : {{ number_format($demande->coutTotal(), 2, ',', ' ') }} DT</h3>
                <div class="card-tools"><a href="{{ route('admin.demandes.interventions.create', $demande) }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Ajouter</a></div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table">
                    <thead><tr><th>Intervention</th><th>Atelier</th><th>Coût</th><th>Statut</th><th class="text-right">Actions</th></tr></thead>
                    <tbody>
                    @forelse ($demande->interventions as $i)
                        <tr>
                            <td><strong>{{ $i->titre }}</strong><br><small class="text-muted">{{ $i->description }}</small></td>
                            <td>{{ $i->atelier?->organization ?? $i->atelier?->name ?? '—' }}</td>
                            <td>{{ $i->cout_estime ? number_format($i->cout_estime, 2, ',', ' ').' DT' : '—' }}</td>
                            <td><span class="badge badge-{{ $i->statut->color() }}">{{ $i->statut->label() }}</span></td>
                            <td class="text-right text-nowrap">
                                @if ($i->statut->next())
                                    <form method="POST" action="{{ route('admin.interventions.advance', $i) }}" class="d-inline">@csrf @method('PATCH')
                                        <button class="btn btn-sm btn-success" title="Faire avancer l'état"><i class="fas fa-forward"></i> {{ $i->statut->next()->label() }}</button></form>
                                @endif
                                <a href="{{ route('admin.interventions.edit', $i) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('admin.interventions.destroy', $i) }}" class="d-inline" onsubmit="return confirm('Supprimer cette intervention ?')">@csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Aucune intervention.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
