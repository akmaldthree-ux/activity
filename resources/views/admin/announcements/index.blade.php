@extends('layouts.app')

@section('title', 'Kelola Pengumuman')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-megaphone me-2"></i>Pengumuman</h5>
    <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Buat Pengumuman
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($announcements->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="bi bi-megaphone fs-1 d-block mb-2 opacity-25"></i>
                Belum ada pengumuman.
            </div>
        @else
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Judul</th>
                        <th>Sasaran</th>
                        <th>Dipublikasi</th>
                        <th>Penulis</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($announcements as $a)
                    @php $canEdit = auth()->user()->isAdmin() || $a->user_id === auth()->id(); @endphp
                    <tr>
                        <td>
                            <span class="fw-semibold">{{ $a->title }}</span>
                        </td>
                        <td>{{ $a->division?->name ?? 'Semua Divisi' }}</td>
                        <td>
                            @if($a->published_at)
                                {{ $a->published_at->format('d M Y H:i') }}
                                @if($a->published_at->isFuture())
                                    <span class="badge bg-secondary ms-1">Terjadwal</span>
                                @endif
                            @else
                                <span class="text-muted">Draft</span>
                            @endif
                        </td>
                        <td class="small text-muted">{{ $a->author->name }}</td>
                        <td class="text-end">
                            @if($canEdit)
                            <a href="{{ route('admin.announcements.edit', $a) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.announcements.destroy', $a) }}" method="POST"
                                  class="d-inline" onsubmit="return confirm('Hapus pengumuman ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @else
                            <span class="text-muted small">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-3">{{ $announcements->links() }}</div>
        @endif
    </div>
</div>
@endsection
