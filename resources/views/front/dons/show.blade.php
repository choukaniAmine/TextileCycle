@extends('layouts.front')
@section('title', 'Don '.$don->reference)
@section('content')
<div class="container py-5" style="max-width:720px">
    <a href="{{ route('dons.index') }}" class="text-decoration-none small"><i class="bi bi-arrow-left"></i> Mes dons</a>
    <h2 class="mt-2">Don {{ $don->reference }}</h2>
    <p class="mb-1"><span class="badge bg-{{ $don->status->badge() }} fs-6">{{ $don->status->label() }}</span></p>

    <dl class="row mt-3">
        <dt class="col-sm-4">Association</dt>
        <dd class="col-sm-8"><a href="{{ route('associations.show', $don->association) }}">{{ $don->association->name }}</a></dd>
        <dt class="col-sm-4">Vêtements</dt>
        <dd class="col-sm-8">{{ $don->description }} ({{ $don->quantity }} pièce(s))</dd>
        <dt class="col-sm-4">Date</dt>
        <dd class="col-sm-8">{{ $don->donated_at->format('d/m/Y') }}</dd>
        @if ($don->notes)<dt class="col-sm-4">Remarques</dt><dd class="col-sm-8">{{ $don->notes }}</dd>@endif
    </dl>

    <h5 class="mt-4">Suivi</h5>
    <ul class="list-group mb-4">
        @foreach ($don->statusLogs as $log)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="badge bg-{{ $log->status->badge() }}">{{ $log->status->label() }}</span>
                <small class="text-muted">{{ $log->created_at->format('d/m/Y H:i') }}</small>
            </li>
        @endforeach
    </ul>

    @if ($don->status === \App\Enums\DonStatus::EnAttente)
        <form method="POST" action="{{ route('dons.destroy', $don) }}" onsubmit="return confirm('Annuler ce don ?')">@csrf @method('DELETE')
            <button class="btn btn-outline-danger">Annuler ce don</button>
        </form>
    @endif
</div>
@endsection
