@extends('layouts.admin')
@section('title', 'Modifier : '.$association->name)
@section('content')
<div class="card card-primary"><form method="POST" action="{{ route('admin.associations.update', $association) }}">@method('PUT') @include('admin.associations._form')</form></div>
@endsection
