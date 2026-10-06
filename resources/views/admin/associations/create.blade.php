@extends('layouts.admin')
@section('title', 'Nouvelle association')
@section('content')
<div class="card card-primary"><form method="POST" action="{{ route('admin.associations.store') }}">@include('admin.associations._form')</form></div>
@endsection
