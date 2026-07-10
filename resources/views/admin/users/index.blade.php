@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')

{{-- Alert reset password --}}
@if(session('reset_password'))
@php $rp = session('reset_password'); @endphp
<div class="alert alert-warning alert-dismissible mb-4" role="alert" id="resetAlert">
    <div class="d-flex align-items-start gap-2">
        <i class="bi bi-key-fill fs-5 mt-1 flex-shrink-0"></i>
        <div>
            <div class="fw-semibold mb-1">Password {{ $rp['name'] }} berhasil direset</div>
            <div class="mb-2">Berikan password sementara ini kepada karyawan:</div>
            <div class="d-flex align-items-center gap-2">
                <code id="newPwDisplay" class="fs-5 px-3 py-1 rounded"
                      style="background:rgba(0,0,0,.08);letter-spacing:.15em;user-select:all">{{ $rp['password'] }}</code>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyPw()">
                    <i class="bi bi-clipboard" id="copyIcon"></i>
                </button>
            </div>
            <div class="form-text mt-2">Minta karyawan segera ganti password setelah login.</div>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-person-gear me-1 text-primary"></i>Kelola User</h5>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i>Tambah User
    </a>
</div>

{{-- Filter --}}
<form class="row g-2 mb-3 align-items-center" method="GET">
    <div class="col-12 col-md-auto flex-grow-1" style="max-width:280px">
        <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Cari nama / email…" value="{{ request('search') }}">
        </div>
    </div>
    <div class="col-auto">
        <select name="role" class="form-select form-select-sm">
            <option value="">Semua Role</option>
            <option value="karyawan" {{ request('role')=='karyawan'?'selected':'' }}>Karyawan</option>
            <option value="leader"   {{ request('role')=='leader'  ?'selected':'' }}>Leader</option>
            <option value="manager"  {{ request('role')=='manager' ?'selected':'' }}>Manager</option>
            <option value="direksi"  {{ request('role')=='direksi' ?'selected':'' }}>Direksi</option>
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
        <select name="sort" class="form-select form-select-sm">
            <option value="name_asc"  {{ request('sort','name_asc')=='name_asc'  ?'selected':'' }}>Nama A–Z</option>
            <option value="name_desc" {{ request('sort')=='name_desc'?'selected':'' }}>Nama Z–A</option>
            <option value="newest"    {{ request('sort')=='newest'   ?'selected':'' }}>Terbaru Daftar</option>
            <option value="oldest"    {{ request('sort')=='oldest'   ?'selected':'' }}>Terlama Daftar</option>
        </select>
    </div>
    <div class="col-auto">
        <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-funnel me-1"></i>Filter</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-link text-muted">Reset</a>
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
                        <th class="d-none d-lg-table-cell text-center">Tgl Daftar</th>
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
                            @php
                                $badgeClass = match($user->role) {
                                    'admin'    => 'bg-danger',
                                    'direksi'  => 'bg-dark',
                                    'manager'  => 'bg-warning text-dark',
                                    'leader'   => 'bg-info text-dark',
                                    default    => 'bg-primary',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $user->roleLabel() }}</span>
                        </td>
                        <td class="d-none d-md-table-cell small">{{ $user->division?->name ?? '—' }}</td>
                        <td class="d-none d-lg-table-cell text-center small text-muted">{{ $user->created_at->format('d/m/Y') }}</td>
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
                                    <button class="btn btn-xs {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} py-0 px-2"
                                            title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="bi bi-{{ $user->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.users.resetPassword', $user) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Reset password {{ addslashes($user->name) }}? Password lama tidak bisa dikembalikan.')">
                                    @csrf
                                    <button class="btn btn-xs btn-outline-secondary py-0 px-2" title="Reset Password">
                                        <i class="bi bi-key"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus user {{ addslashes($user->name) }}?')">
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

@push('scripts')
<script>
function copyPw() {
    const text = document.getElementById('newPwDisplay').textContent.trim();
    navigator.clipboard.writeText(text).then(() => {
        const icon = document.getElementById('copyIcon');
        icon.className = 'bi bi-check2';
        setTimeout(() => icon.className = 'bi bi-clipboard', 2000);
    });
}
</script>
@endpush
@endsection
