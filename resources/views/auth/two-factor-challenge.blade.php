@extends('layouts.front')
@section('title', 'Authentification à deux facteurs')
@section('content')
<div class="container py-5" style="max-width:480px">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="text-center mb-4 text-primary">
                <i class="bi bi-shield-lock" style="font-size: 2.8rem;"></i>
                <h3 class="mt-2 mb-1">Double Authentification (2FA)</h3>
                <p class="text-muted small" id="challenge-desc">
                    Veuillez entrer le code de sécurité à 6 chiffres généré par votre application d'authentification (Google Authenticator, Authy...).
                </p>
            </div>

            <form method="POST" action="{{ route('two-factor.login') }}">
                @csrf

                {{-- Saisie par code TOTP --}}
                <div id="totp-group" class="mb-3">
                    <label class="form-label">Code d'authentification</label>
                    <input type="text" name="code" id="totp-code" inputmode="numeric" autocomplete="one-time-code" autofocus class="form-control form-control-lg text-center letter-spacing @error('code') is-invalid @enderror" placeholder="123456" maxlength="7">
                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Saisie par code de récupération --}}
                <div id="recovery-group" class="mb-3 d-none">
                    <label class="form-label">Code de récupération d'urgence</label>
                    <input type="text" name="recovery_code" id="recovery-code" autocomplete="one-time-code" class="form-control text-center @error('recovery_code') is-invalid @enderror" placeholder="xxxxxxxxxx-xxxxxxxxxx">
                    @error('recovery_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">
                    Valider la connexion
                </button>

                <div class="text-center">
                    <button type="button" class="btn btn-link btn-sm text-decoration-none" id="toggle-recovery">
                        <span id="toggle-text">Utiliser un code de récupération d'urgence</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const toggleBtn = document.getElementById('toggle-recovery');
    const totpGroup = document.getElementById('totp-group');
    const recoveryGroup = document.getElementById('recovery-group');
    const toggleText = document.getElementById('toggle-text');
    const challengeDesc = document.getElementById('challenge-desc');
    let usingRecovery = false;

    toggleBtn.addEventListener('click', function () {
        usingRecovery = !usingRecovery;
        if (usingRecovery) {
            totpGroup.classList.add('d-none');
            recoveryGroup.classList.remove('d-none');
            toggleText.textContent = "Utiliser un code à 6 chiffres (Google Authenticator)";
            challengeDesc.textContent = "Saisissez l'un de vos codes de secours d'urgence fournis lors de l'activation.";
            document.getElementById('recovery-code').focus();
        } else {
            recoveryGroup.classList.add('d-none');
            totpGroup.classList.remove('d-none');
            toggleText.textContent = "Utiliser un code de récupération d'urgence";
            challengeDesc.textContent = "Veuillez entrer le code de sécurité à 6 chiffres généré par votre application.";
            document.getElementById('totp-code').focus();
        }
    });
</script>
@endpush
@endsection
