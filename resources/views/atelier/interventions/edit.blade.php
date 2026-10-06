@extends('layouts.front')
@section('title', 'Modifier l’intervention')
@section('content')
<div class="demande-hero"><div class="container"><h1>Modifier l’intervention</h1><p class="lead mb-0">{{ $demande->titre }}</p></div></div>
<div class="demande-page"><div class="container" style="max-width:720px"><div class="panel-soft">
    <form method="POST" action="{{ route('atelier.interventions.update', $intervention) }}">
        @csrf @method('PUT')
        @include('atelier.interventions._fields')
        <button class="btn btn-success">Enregistrer</button>
        <a href="{{ route('atelier.travaux.show', $demande) }}" class="btn btn-link">Annuler</a>
    </form>
</div></div></div>
@endsection
