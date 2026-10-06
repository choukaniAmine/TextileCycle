@extends('layouts.front')
@section('title', 'Mon profil')
@section('content')
<div class="container py-5" style="max-width:640px">
    <h2 class="mb-1">Mon profil</h2>
    <p class="text-muted">Type de compte : <span class="badge bg-{{ $user->role->badge() }}">{{ $user->role->label() }}</span></p>
    <form method="POST" action="{{ route('profile.update') }}">
        @csrf @method('PUT')
        @include('front._fields', ['user' => $user])
        <button class="btn btn-primary">Enregistrer</button>
    </form>
</div>
@endsection
