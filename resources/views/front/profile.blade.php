@extends('layouts.front')
@section('title', 'Mon profil')
@section('content')
<div class="container py-5">
    <div class="row">
        {{-- Colonne de gauche : Carte Résumé Utilisateur --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 text-center p-4">
                <div class="position-relative d-inline-block mx-auto mb-3">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle shadow-sm border" style="width: 120px; height: 120px; object-fit: cover;">
                    @if ($user->avatar)
                        <form method="POST" action="{{ route('profile.avatar.destroy') }}" class="position-absolute bottom-0 end-0">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger rounded-circle p-1" title="Supprimer la photo" onclick="return confirm('Supprimer votre photo de profil ?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    @endif
                </div>
                <h4 class="mb-1">{{ $user->name }}</h4>
                <p class="text-muted small mb-2">{{ $user->email }}</p>
                <div>
                    <span class="badge bg-{{ $user->role->badge() }} mb-2">{{ $user->role->label() }}</span>
                    @if ($user->hasVerifiedEmail())
                        <span class="badge bg-success mb-2"><i class="bi bi-check-circle"></i> E-mail vérifié</span>
                    @else
                        <span class="badge bg-warning text-dark mb-2"><i class="bi bi-exclamation-circle"></i> E-mail non vérifié</span>
                    @endif
                    @if ($user->hasEnabledTwoFactorAuthentication())
                        <span class="badge bg-primary mb-2"><i class="bi bi-shield-check"></i> 2FA Actif</span>
                    @else
                        <span class="badge bg-secondary mb-2"><i class="bi bi-shield"></i> 2FA Inactif</span>
                    @endif
                </div>
                @if ($user->organization)
                    <div class="small text-muted mt-2">
                        <i class="bi bi-building"></i> {{ $user->organization }}
                    </div>
                @endif
                @if ($user->city)
                    <div class="small text-muted">
                        <i class="bi bi-geo-alt"></i> {{ $user->city }}
                    </div>
                @endif
                <div class="small text-muted mt-2">
                    Inscrit le {{ $user->created_at->format('d/m/Y') }}
                </div>
            </div>
        </div>

        {{-- Colonne de droite : Formulaires & Sécurité --}}
        <div class="col-lg-8">
            {{-- 1. Informations générales & Avatar --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Informations du profil</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom complet</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Adresse e-mail</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Photo de profil (Avatar)</label>
                            <input type="file" name="avatar" accept="image/*" class="form-control @error('avatar') is-invalid @enderror">
                            <div class="form-text">Formats acceptés : JPG, PNG, WebP (max. 2 Mo).</div>
                            @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Téléphone</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control @error('phone') is-invalid @enderror">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ville</label>
                                <input type="text" name="city" value="{{ old('city', $user->city) }}" class="form-control @error('city') is-invalid @enderror">
                                @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nom de l’atelier ou association (si applicable)</label>
                            <input type="text" name="organization" value="{{ old('organization', $user->organization) }}" class="form-control @error('organization') is-invalid @enderror">
                            @error('organization')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Présentation / Biographie</label>
                            <textarea name="bio" rows="3" class="form-control @error('bio') is-invalid @enderror" placeholder="Présentez votre activité ou vos motivations...">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3"><i class="bi bi-key me-2 text-primary"></i>Modifier le mot de passe <small class="text-muted">(optionnel)</small></h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nouveau mot de passe</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="8 caractères minimum">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirmer le nouveau mot de passe</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Enregistrer les modifications
                        </button>
                    </form>
                </div>
            </div>

            {{-- 2. Authentification à Deux Facteurs (2FA TOTP) --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-shield-lock me-2 text-primary"></i>Authentification à deux facteurs (2FA)</h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small">
                        L'authentification à deux facteurs ajoute un niveau de sécurité supplémentaire à votre compte en requérant un code temporaire (TOTP via Google Authenticator, Authy, etc.) lors de la connexion.
                    </p>

                    @if (! $user->two_factor_secret)
                        {{-- 2FA Désactivé --}}
                        <div class="alert alert-light border d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <strong>Le 2FA n'est pas activé sur votre compte.</strong>
                                <div class="text-muted small">Protégez votre compte contre les accès non autorisés.</div>
                            </div>
                            <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-shield-plus me-1"></i> Activer le 2FA
                                </button>
                            </form>
                        </div>

                    @elseif (! $user->two_factor_confirmed_at)
                        {{-- 2FA En cours d'activation (Étape de confirmation du QR Code) --}}
                        <div class="alert alert-warning">
                            <strong><i class="bi bi-qr-code-scan me-1"></i> Finalisez l'activation de la double authentification :</strong>
                            <p class="small mb-3 mt-1">
                                1. Scannez le QR Code ci-dessous avec votre application d'authentification (Google Authenticator, Authy, Microsoft Authenticator...).<br>
                                2. Saisissez le code à 6 chiffres affiché par l'application pour valider l'activation.
                            </p>

                            <div class="text-center p-3 bg-white rounded border d-inline-block mb-3">
                                {!! $user->twoFactorQrCodeSvg() !!}
                            </div>

                            <form method="POST" action="{{ url('/user/confirmed-two-factor-authentication') }}" class="mt-2" style="max-width:320px;">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Code à 6 chiffres :</label>
                                    <input type="text" name="code" class="form-control text-center @error('code') is-invalid @enderror" placeholder="123456" maxlength="7" required autofocus>
                                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="bi bi-check-circle me-1"></i> Confirmer &amp; Activer
                                    </button>
                                </div>
                            </form>

                            <form method="POST" action="{{ url('/user/two-factor-authentication') }}" class="mt-2">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-link btn-sm text-danger p-0">Annuler l'activation</button>
                            </form>
                        </div>

                    @else
                        {{-- 2FA Activé et Confirmé --}}
                        <div class="alert alert-success d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <strong><i class="bi bi-shield-check me-1"></i> La double authentification est activée.</strong>
                                <div class="small">Votre compte est sécurisé par un code TOTP à chaque connexion.</div>
                            </div>
                            <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Voulez-vous vraiment désactiver l\'authentification à deux facteurs ?')">
                                    <i class="bi bi-shield-x me-1"></i> Désactiver le 2FA
                                </button>
                            </form>
                        </div>

                        {{-- Codes de récupération d'urgence --}}
                        <div class="mt-3">
                            <h6 class="fw-bold">Codes de récupération d'urgence</h6>
                            <p class="text-muted small">
                                Conservez ces codes de secours dans un gestionnaire de mots de passe sécurisé. Si vous perdez l'accès à votre téléphone, chaque code ne peut être utilisé qu'une seule fois pour vous connecter.
                            </p>
                            <div class="bg-light p-3 rounded font-monospace small">
                                <div class="row">
                                    @foreach ($user->recoveryCodes() as $code)
                                        <div class="col-sm-6 mb-1 text-dark"><code>{{ $code }}</code></div>
                                    @endforeach
                                </div>
                            </div>
                            <form method="POST" action="{{ url('/user/two-factor-recovery-codes') }}" class="mt-2">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-repeat me-1"></i> Régénérer de nouveaux codes de secours
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 3. Zone de danger : Suppression du compte --}}
            <div class="card shadow-sm border-danger">
                <div class="card-header bg-danger text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Zone dangereuse</h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small">
                        Une fois votre compte supprimé, toutes vos données et vos informations associées seront définitivement effacées. Cette opération est irréversible.
                    </p>

                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        <i class="bi bi-trash3 me-1"></i> Supprimer mon compte
                    </button>

                    <!-- Modal Confirmation -->
                    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('profile.destroy') }}">
                                    @csrf @method('DELETE')
                                    <div class="modal-header">
                                        <h5 class="modal-title text-danger" id="deleteAccountModalLabel">
                                            <i class="bi bi-exclamation-circle-fill me-1"></i> Confirmation de suppression
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="small text-muted">
                                            Êtes-vous sûr de vouloir supprimer votre compte ? Veuillez saisir votre mot de passe pour confirmer.
                                        </p>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Votre mot de passe actuel</label>
                                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Mot de passe">
                                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
