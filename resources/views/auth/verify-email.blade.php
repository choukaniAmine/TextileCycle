@extends('layouts.front')
@section('title', 'Vérification de l\'adresse e-mail')
@section('content')
<div class="container py-5" style="max-width:540px">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4 text-center">
            <div class="mb-3 text-primary">
                <i class="bi bi-envelope-check" style="font-size: 3rem;"></i>
            </div>
            <h3 class="card-title mb-3">Vérifiez votre boîte mail</h3>
            <p class="text-muted mb-4">
                Merci pour votre inscription ! Avant de continuer, veuillez confirmer votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer.
            </p>

            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success text-start small mb-4" role="alert">
                    Un nouveau lien de vérification vous a été envoyé à l'adresse fournie lors de l'inscription.
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        Renvoyer l'e-mail de vérification
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary">
                        Se déconnecter
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
