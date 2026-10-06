@extends('layouts.front')
@section('title', $association->name)
@section('content')
<div class="container py-5" style="max-width:760px">
    <a href="{{ route('associations.index') }}" class="text-decoration-none small"><i class="bi bi-arrow-left"></i> Toutes les associations</a>
    <h2 class="mt-2">{{ $association->name }}</h2>
    <p class="lead text-muted">{{ $association->description }}</p>

    <ul class="list-unstyled">
        @if ($association->address || $association->city)<li><i class="bi bi-geo-alt"></i> {{ $association->address }} {{ $association->city }}</li>@endif
        @if ($association->email)<li><i class="bi bi-envelope"></i> {{ $association->email }}</li>@endif
        @if ($association->phone)<li><i class="bi bi-telephone"></i> {{ $association->phone }}</li>@endif
    </ul>

    <div class="alert alert-success d-flex justify-content-between align-items-center">
        <span><strong>{{ $piecesLivrees }}</strong> vêtement(s) déjà livré(s) à cette association.</span>
        <a href="{{ route('dons.create', ['association' => $association->id]) }}" class="btn btn-primary">Faire un don</a>
    </div>
</div>
@endsection
