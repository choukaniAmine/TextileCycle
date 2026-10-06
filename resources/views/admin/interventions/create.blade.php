@extends('layouts.admin')
@section('title', 'Nouvelle intervention — '.$demande->titre)
@section('content')
<div class="card card-primary"><form method="POST" action="{{ route('admin.demandes.interventions.store', $demande) }}">@include('admin.interventions._form')</form></div>
@endsection
