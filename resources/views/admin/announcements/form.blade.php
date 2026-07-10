@extends('layouts.app')

@section('title', isset($announcement) ? 'Edit Pengumuman' : 'Buat Pengumuman')

@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('admin.announcements.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="fw-bold mb-0">{{ isset($announcement) ? 'Edit' : 'Buat' }} Pengumuman</h5>
</div>

<div class="card border-0 shadow-sm" style="max-width:700px">
    <div class="card-body">
        <form action="{{ isset($announcement) ? route('admin.announcements.update', $announcement) : route('admin.announcements.store') }}"
              method="POST">
            @csrf
            @if(isset($announcement)) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label fw-semibold">Judul <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title', $announcement->title ?? '') }}" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Isi Pengumuman <span class="text-danger">*</span></label>
                <textarea name="body" rows="6" class="form-control @error('body') is-invalid @enderror"
                          required>{{ old('body', $announcement->body ?? '') }}</textarea>
                @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Sasaran Divisi</label>
                    @if($user->isManager())
                        {{-- Manager hanya bisa kirim ke divisinya sendiri --}}
                        <input type="text" class="form-control" value="{{ $user->division?->name ?? '—' }}" disabled>
                        <div class="form-text">Pengumuman otomatis ditargetkan ke divisi Anda.</div>
                    @else
                        <select name="division_id" class="form-select">
                            <option value="">Semua Divisi</option>
                            @foreach($divisions as $div)
                            <option value="{{ $div->id }}" @selected(old('division_id', $announcement->division_id ?? '') == $div->id)>
                                {{ $div->name }}
                            </option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tanggal Publikasi</label>
                    <input type="datetime-local" name="published_at" class="form-control"
                           value="{{ old('published_at', isset($announcement->published_at) ? $announcement->published_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
                    <div class="form-text">Kosongkan untuk simpan sebagai draft.</div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Simpan
                </button>
                <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
