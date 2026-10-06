@extends('layouts.admin')
@section('title', 'Don '.$don->reference)
@section('content')
<div class="row">
    <div class="col-md-7">
        <div class="card card-primary card-outline">
            <div class="card-body">
                <h4>{{ $don->description }}</h4>
                <p class="text-muted">{{ $don->quantity }} pièce(s) · donné le {{ $don->donated_at->format('d/m/Y') }}</p>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Association</dt>
                    <dd class="col-sm-8"><a href="{{ route('admin.associations.show', $don->association) }}">{{ $don->association->name }}</a></dd>
                    <dt class="col-sm-4">Donateur</dt>
                    <dd class="col-sm-8">{{ $don->donor?->name ?? '—' }} @if($don->donor)<small class="text-muted">{{ $don->donor->email }}</small>@endif</dd>
                    <dt class="col-sm-4">Statut actuel</dt>
                    <dd class="col-sm-8"><span class="badge badge-{{ $don->status->badge() }}">{{ $don->status->label() }}</span></dd>
                    <dt class="col-sm-4">Notes</dt>
                    <dd class="col-sm-8">{{ $don->notes ?? '—' }}</dd>
                </dl>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.dons.edit', $don) }}" class="btn btn-info btn-sm"><i class="fas fa-edit"></i> Modifier</a>
                <a href="{{ route('admin.dons.index') }}" class="btn btn-default btn-sm">Retour à la liste</a>
            </div>
        </div>

        <div class="callout callout-info">
            <h5>Statut géré par l’association</h5>
            <p class="mb-0">
                Seule l’association bénéficiaire accepte, refuse et fait avancer ce don
                ({{ $don->association->manager ? 'responsable : '.$don->association->manager->name : 'aucun compte responsable n’est rattaché pour l’instant' }}).
            </p>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Suivi du don</h3></div>
            <div class="card-body">
                <div class="timeline timeline-inverse">
                    @foreach ($don->statusLogs as $log)
                        <div>
                            <i class="fas fa-circle bg-{{ $log->status->badge() }}"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="far fa-clock"></i> {{ $log->created_at->format('d/m/Y H:i') }}</span>
                                <h3 class="timeline-header">{{ $log->status->label() }}</h3>
                                <div class="timeline-body text-muted small">{{ $log->author ? 'Par '.$log->author->name : 'Système' }}</div>
                            </div>
                        </div>
                    @endforeach
                    <div><i class="far fa-clock bg-gray"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
