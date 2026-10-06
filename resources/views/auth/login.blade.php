@extends('layouts.front')
@section('title', 'Connexion')
@section('content')
<div class="container py-5" style="max-width:480px">
    <h2 class="mb-4">Connexion</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">E-mail</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-check mb-3">
            <input type="checkbox" name="remember" id="remember" class="form-check-input">
            <label for="remember" class="form-check-label">Se souvenir de moi</label>
        </div>
        <button class="btn btn-primary w-100">Se connecter</button>
        <p class="mt-3 text-center">Pas encore de compte ? <a href="{{ route('register') }}">Inscrivez-vous</a></p>
    </form>
</div>
@endsection
