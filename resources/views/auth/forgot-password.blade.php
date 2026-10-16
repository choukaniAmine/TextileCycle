@extends('layouts.front')
@section('title', 'Mot de passe oublié')
@section('content')
<div class="container py-5" style="max-width:480px">
    <h2 class="mb-3">Récupération de mot de passe</h2>
    <p class="text-muted small">
        Saisissez l'adresse e-mail associée à votre compte. Nous vous enverrons un lien sécurisé avec un jeton temporaire pour réinitialiser votre mot de passe.
    </p>



    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">E-mail</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button class="btn btn-primary w-100">Envoyer le lien de réinitialisation</button>
        <p class="mt-3 text-center">
            <a href="{{ route('login') }}" class="text-decoration-none">Retour à la connexion</a>
        </p>
    </form>
</div>
@endsection
