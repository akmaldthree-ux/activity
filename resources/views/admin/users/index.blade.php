@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-person-gear me-1 text-primary"></i>Kelola User</h5>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i>Tambah User
    </a>
</div>

{{-- Filter --}}
<form class="row g-2 mb-3" method="GET">
    <div class="col-auto">
        <select name="role" class="form-select form-select-sm">
            <option value="">Semua Role</option>
            <option value="karyawan" {{ request('role')=='karyawan'?'selected':'' }}>Karyawan</option>
            <option value="manager"  {{ request('role')=='manager' ?'selected':'' }}>Manager</option>
            <option value="admin"    {{ request('role')=='admin'   ?'selected':'' }}>Admin</option>
        </select>
    </div>
    <div class="col-auto">
        <select name="division_id" class="form-select form-select-sm">
            <option value="">Semua Divisi</option>
            @foreach($divisions as $div)
            <option value="{{ $div->id }}" {{ request('division_id')==$div->id?'selected':'' }}>{{ $div->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-auto">
        <button class="btn btn-sm btn-outline-secondary">Filter</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-link">Reset</a>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th class="d-none d-md-table-cell">Email</th>
                        <th>Role</th>
                        <th class="d-none d-md-table-cell">Divisi</th>
                        <th class="text-center">Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="fw-semibold">{{ $user->name }}</td>
                        <td class="d-none d-md-table-cell small text-muted">{{ $user->email }}</td>
                        <td>
                            <span class="badge {{ $user->role=='admin' ? 'bg-danger' : ($user->role=='manager' ? 'bg-warning' : 'bg-primary') }}">
                                {{ $user->roleLabel() }}
                            </span>
                        </td>
                        <td class="d-none d-md-table-cell small">{{ $user->division?->name ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-xs btn-outline-primary py-0 px-2">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.users.toggle', $user) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-xs {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} py-0 px-2" title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="bi bi-{{ $user->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus user {{ $user->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-outline-danger py-0 px-2">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $users->links() }}</div>
@endsection
