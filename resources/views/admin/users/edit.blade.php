@extends('layouts.admin')
@section('title', 'Modifier : '.$user->name)
@section('content')
<div class="card card-primary"><form method="POST" action="{{ route('admin.users.update', $user) }}">@method('PUT') @include('admin.users._form')</form></div>
@endsection
