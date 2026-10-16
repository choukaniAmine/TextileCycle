@extends('layouts.front')
@section('title', 'Confirmation du mot de passe')
@section('content')
<div class="container py-5" style="max-width:480px">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h3 class="card-title mb-3">Zone sécurisée</h3>
            <p class="text-muted small mb-4">
                Cette action est sensible. Veuillez confirmer votre mot de passe pour continuer.
            </p>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Mot de passe actuel</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autofocus>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn btn-primary w-100">Confirmer</button>
            </form>
        </div>
    </div>
</div>
@endsection
