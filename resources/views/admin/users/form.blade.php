@extends('layouts.app')

@section('title', isset($user) ? 'Edit User' : 'Tambah User')

@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h5 class="fw-bold mb-0">{{ isset($user) ? 'Edit User' : 'Tambah User Baru' }}</h5>
</div>

<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-body">
        <form action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST">
            @csrf
            @if(isset($user)) @method('PUT') @endif

            @if($errors->any())
                <div class="alert alert-danger small">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user?->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user?->email) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Password {{ isset($user) ? '(kosongkan jika tidak diubah)' : '' }}</label>
                <input type="password" name="password" class="form-control" {{ isset($user) ? '' : 'required' }}>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Role</label>
                <select name="role" class="form-select" required>
                    <option value="karyawan" {{ old('role', $user?->role)=='karyawan'?'selected':'' }}>Karyawan</option>
                    <option value="manager"  {{ old('role', $user?->role)=='manager' ?'selected':'' }}>Manager</option>
                    <option value="admin"    {{ old('role', $user?->role)=='admin'   ?'selected':'' }}>Admin</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Divisi</label>
                <select name="division_id" class="form-select">
                    <option value="">— Tanpa Divisi —</option>
                    @foreach($divisions as $div)
                    <option value="{{ $div->id }}" {{ old('division_id', $user?->division_id)==$div->id?'selected':'' }}>{{ $div->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>{{ isset($user) ? 'Perbarui' : 'Simpan' }}
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
