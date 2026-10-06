@extends('layouts.admin')
@section('title', 'Modifier l’intervention — '.$demande->titre)
@section('content')
<div class="card card-primary"><form method="POST" action="{{ route('admin.interventions.update', $intervention) }}">@method('PUT') @include('admin.interventions._form')</form></div>
@endsection
