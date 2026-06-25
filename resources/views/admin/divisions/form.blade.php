@extends('layouts.app')

@section('title', isset($division) ? 'Edit Divisi' : 'Tambah Divisi')

@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('admin.divisions.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h5 class="fw-bold mb-0">{{ isset($division) ? 'Edit Divisi' : 'Tambah Divisi Baru' }}</h5>
</div>

<div class="card border-0 shadow-sm" style="max-width:500px">
    <div class="card-body">
        <form action="{{ isset($division) ? route('admin.divisions.update', $division) : route('admin.divisions.store') }}" method="POST">
            @csrf
            @if(isset($division)) @method('PUT') @endif

            @if($errors->any())
                <div class="alert alert-danger small">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Divisi</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $division?->name) }}" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea name="description" rows="3" class="form-control">{{ old('description', $division?->description) }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>{{ isset($division) ? 'Perbarui' : 'Simpan' }}
                </button>
                <a href="{{ route('admin.divisions.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
