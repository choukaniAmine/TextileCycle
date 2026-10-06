@extends('layouts.admin')
@section('title', 'Nouvel utilisateur')
@section('content')
<div class="card card-primary"><form method="POST" action="{{ route('admin.users.store') }}">@include('admin.users._form')</form></div>
@endsection
