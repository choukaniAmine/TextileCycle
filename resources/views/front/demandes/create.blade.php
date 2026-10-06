@extends('layouts.front')
@section('title', 'Nouvelle demande')
@section('content')
<div class="demande-hero"><div class="container"><h1>Nouvelle demande</h1><p class="lead mb-0">Un vêtement abîmé ? Un projet de transformation ? Racontez-nous.</p></div></div>
<div class="demande-page"><div class="container" style="max-width:760px">
    <div class="panel-soft">
        <form method="POST" action="{{ route('demandes.store') }}" enctype="multipart/form-data">@include('front.demandes._form')</form>
    </div>
</div></div>
@endsection
