@extends('layouts.front')
@section('title', 'Modifier la demande')
@section('content')
<div class="demande-hero"><div class="container"><h1>Modifier ma demande</h1><p class="lead mb-0">{{ $demande->titre }}</p></div></div>
<div class="demande-page"><div class="container" style="max-width:760px">
    <div class="panel-soft">
        <form method="POST" action="{{ route('demandes.update', $demande) }}" enctype="multipart/form-data">@method('PUT') @include('front.demandes._form')</form>
    </div>
</div></div>
@endsection
