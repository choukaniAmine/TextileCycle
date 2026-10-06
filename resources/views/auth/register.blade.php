@extends('layouts.front')
@section('title', 'Inscription')
@section('content')
<div class="container py-5" style="max-width:600px">
    <h2 class="mb-4">Créer un compte</h2>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Vous êtes…</label>
            <select name="role" id="role" class="form-select @error('role') is-invalid @enderror">
                @foreach (\App\Enums\UserRole::cases() as $r)
                    @continue($r === \App\Enums\UserRole::Admin)
                    <option value="{{ $r->value }}" @selected(old('role', 'particulier') === $r->value)>{{ $r->label() }}</option>
                @endforeach
            </select>
            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        @include('front._fields', ['user' => null, 'withPassword' => true])
        <button class="btn btn-primary w-100">Créer mon compte</button>
        <p class="mt-3 text-center">Déjà inscrit ? <a href="{{ route('login') }}">Connexion</a></p>
    </form>
</div>
@endsection
