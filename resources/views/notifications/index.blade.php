@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-bell me-2"></i>Notifikasi</h5>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($notifications->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="bi bi-bell fs-1 d-block mb-2 opacity-25"></i>
                Tidak ada notifikasi.
            </div>
        @else
            @foreach($notifications as $notif)
            <div class="d-flex align-items-start gap-3 p-3 border-bottom {{ $notif->isUnread() ? 'bg-light' : '' }}">
                <div class="text-primary mt-1">
                    <i class="bi bi-{{ $notif->type === 'feedback' ? 'chat-dots' : ($notif->type === 'announcement' ? 'megaphone' : 'clock') }} fs-5"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-semibold small">{{ $notif->title }}</div>
                    @if($notif->body) <div class="text-muted small">{{ $notif->body }}</div> @endif
                    <div class="text-muted" style="font-size:.7rem">{{ $notif->created_at->diffForHumans() }}</div>
                </div>
                @if($notif->url)
                <a href="{{ route('notifications.read', $notif) }}" class="btn btn-sm btn-outline-primary">
                    Lihat
                </a>
                @endif
            </div>
            @endforeach
            <div class="p-3">{{ $notifications->links() }}</div>
        @endif
    </div>
</div>
@endsection
