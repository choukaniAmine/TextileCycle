@extends('layouts.admin')
@section('title', 'Nouveau don')
@section('content')
<div class="card card-primary"><form method="POST" action="{{ route('admin.dons.store') }}">@include('admin.dons._form')</form></div>
@endsection
