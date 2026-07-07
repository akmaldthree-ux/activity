@extends('layouts.app')

@section('title', $announcement->title)

@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('announcements.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="fw-bold mb-0">Pengumuman</h5>
</div>

<div class="card border-0 shadow-sm" style="max-width:700px">
    <div class="card-body">
        <h5 class="fw-bold mb-1">{{ $announcement->title }}</h5>
        <div class="d-flex gap-3 text-muted small mb-3">
            <span><i class="bi bi-person me-1"></i>{{ $announcement->author->name }}</span>
            <span><i class="bi bi-calendar me-1"></i>{{ $announcement->published_at?->translatedFormat('d F Y, H:i') ?? '—' }}</span>
            @if($announcement->division)
                <span><i class="bi bi-people me-1"></i>{{ $announcement->division->name }}</span>
            @endif
        </div>
        <div style="white-space:pre-wrap">{{ $announcement->body }}</div>
    </div>
</div>
@endsection
