@extends('layouts.admin')
@section('title', 'Modifier le don '.$don->reference)
@section('content')
<div class="card card-primary"><form method="POST" action="{{ route('admin.dons.update', $don) }}">@method('PUT') @include('admin.dons._form')</form></div>
@endsection
