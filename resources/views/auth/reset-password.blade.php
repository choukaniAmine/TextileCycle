@extends('layouts.front')
@section('title', 'Réinitialiser mon mot de passe')
@section('content')
<div class="container py-5" style="max-width:480px">
    <h2 class="mb-3">Nouveau mot de passe</h2>
    <p class="text-muted small">
        Veuillez choisir un nouveau mot de passe pour sécuriser votre compte.
    </p>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mb-3">
            <label class="form-label">E-mail</label>
            <input type="email" name="email" value="{{ old('email', $request->email) }}" class="form-control @error('email') is-invalid @enderror" required autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Nouveau mot de passe</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Confirmer le nouveau mot de passe</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <button class="btn btn-primary w-100">Mettre à jour mon mot de passe</button>
    </form>
</div>
@endsection
