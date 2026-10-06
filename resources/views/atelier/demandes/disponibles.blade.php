@extends('layouts.front')
@section('title', 'Demandes disponibles')
@section('content')
<div class="demande-hero"><div class="container d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div><h1>📥 Demandes disponibles</h1><p class="lead mb-0">Choisissez les vêtements que votre atelier peut réparer ou transformer.</p></div>
    <a href="{{ route('atelier.travaux.index') }}" class="btn btn-light btn-lg">🧵 Mes travaux</a>
</div></div>

<div class="demande-page"><div class="container">
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-5"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Titre, vêtement, client…"></div>
        <div class="col-md-3">
            <select name="type" class="form-select">
                <option value="">Tous les types</option>
                @foreach ($types as $t)<option value="{{ $t->value }}" @selected(request('type') === $t->value)>{{ $t->emoji() }} {{ $t->label() }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-center">
            <div class="form-check"><input type="checkbox" name="urgent" value="1" id="urgent" class="form-check-input" @checked(request('urgent'))>
                <label for="urgent" class="form-check-label">🔥 Urgentes</label></div>
        </div>
        <div class="col-md-2"><button class="btn btn-success w-100">Filtrer</button></div>
    </form>

    @if ($demandes->isEmpty())
        <div class="empty-state"><div class="big">🧶</div><h4>Aucune demande disponible</h4><p class="text-muted mb-0">Revenez bientôt : vous serez notifié dès qu’une nouvelle demande arrive.</p></div>
    @else
        <div class="row g-4">
            @foreach ($demandes as $d)
                <div class="col-md-6 col-lg-4">
                    <div class="card demande-card {{ $d->type->value }}"><div class="card-body pt-4 d-flex flex-column">
                        @if ($d->photo)<img src="{{ asset('storage/'.$d->photo) }}" class="thumb" alt="">@endif
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="type-emoji">{{ $d->type->emoji() }}</div>
                            <div><h5 class="mb-0">{{ $d->titre }}</h5><small class="text-muted">{{ $d->vetement }} · {{ $d->type->label() }}</small></div>
                        </div>
                        <p class="small flex-grow-1">{{ \Illuminate\Support\Str::limit($d->description, 110) }}</p>
                        <div class="mb-2">
                            @if ($d->urgent)<span class="badge-urgent">🔥 Urgent</span>@endif
                            @if ($d->enRetard())<span class="badge-late">En retard</span>@endif
                        </div>
                        <small class="text-muted mb-3">👤 {{ $d->user->name }}@if($d->user->city) · 📍 {{ $d->user->city }}@endif
                            @if ($d->date_souhaitee)<br>⏳ Pour le {{ $d->date_souhaitee->format('d/m/Y') }}@endif</small>
                        <form method="POST" action="{{ route('atelier.demandes.prendre', $d) }}">@csrf
                            <button class="btn btn-success w-100">✋ Prendre en charge</button></form>
                    </div></div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $demandes->links('pagination::bootstrap-5') }}</div>
    @endif
</div></div>
@endsection
