@extends('layouts.front')
@section('title', 'Notifications')
@section('content')
<div class="demande-hero"><div class="container d-flex justify-content-between align-items-center">
    <h1>🔔 Notifications</h1>
    @if (auth()->user()->unreadNotifications()->count())
        <form method="POST" action="{{ route('notifications.readAll') }}">@csrf <button class="btn btn-light">Tout marquer comme lu</button></form>
    @endif
</div></div>
<div class="demande-page"><div class="container" style="max-width:760px">
    @forelse ($notifications as $n)
        <a href="{{ route('notifications.read', $n->id) }}" class="notif-item {{ $n->read_at ? '' : 'unread' }}">
            <span class="ico">{{ $n->data['icon'] ?? '🧵' }}</span>
            {{ $n->data['message'] }}
            <small class="d-block text-muted ms-4 ps-3">{{ $n->data['titre'] ?? '' }} · {{ $n->created_at->diffForHumans() }}</small>
        </a>
    @empty
        <div class="empty-state"><div class="big">🔕</div><p class="mb-0 text-muted">Aucune notification pour le moment.</p></div>
    @endforelse
    <div class="mt-3">{{ $notifications->links('pagination::bootstrap-5') }}</div>
</div></div>
@endsection
