@extends('layouts.app')

@section('title', 'Pengumuman')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-megaphone me-2"></i>Pengumuman</h5>
</div>

@if($announcements->isEmpty())
    <div class="text-center text-muted py-5">
        <i class="bi bi-megaphone fs-1 d-block mb-3 opacity-25"></i>
        Belum ada pengumuman untuk Anda.
    </div>
@else
    <div class="row g-3">
        @foreach($announcements as $a)
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-2">
                        <h6 class="fw-bold mb-0">{{ $a->title }}</h6>
                        <div class="d-flex gap-2 align-items-center">
                            @if($a->division)
                                <span class="badge bg-info text-dark">{{ $a->division->name }}</span>
                            @else
                                <span class="badge bg-secondary">Semua Divisi</span>
                            @endif
                            <small class="text-muted">{{ $a->published_at->translatedFormat('d F Y') }}</small>
                        </div>
                    </div>
                    <p class="mb-1 text-muted small">Oleh: {{ $a->author->name }}</p>
                    <p class="mb-0">{!! nl2br(e(Str::limit($a->body, 200))) !!}</p>
                    @if(strlen($a->body) > 200)
                        <a href="{{ route('announcements.show', $a) }}" class="small">Baca selengkapnya</a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-3">{{ $announcements->links() }}</div>
@endif
@endsection
