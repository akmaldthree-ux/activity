@extends('layouts.app')

@section('title', 'Atur Hierarki')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-diagram-2 me-1 text-primary"></i>Atur Hierarki</h5>
    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kelola User
    </a>
</div>

<p class="text-muted small mb-3">
    Tentukan atasan langsung (<em>reports to</em>) untuk setiap karyawan, leader, dan manager.
    Pengaturan ini menentukan siapa yang bisa memantau aktivitas mereka.
</p>

{{-- Filter divisi --}}
<form method="GET" class="d-flex align-items-center gap-2 mb-3 flex-wrap">
    <label class="fw-semibold small text-muted mb-0" for="divFilter">Filter Divisi:</label>
    <select id="divFilter" name="division_id" class="form-select form-select-sm" style="max-width:220px"
            onchange="this.form.submit()">
        <option value="">Semua Divisi</option>
        @foreach($divisions as $div)
            <option value="{{ $div->id }}" {{ $divisionId == $div->id ? 'selected' : '' }}>
                {{ $div->name }}
            </option>
        @endforeach
    </select>
    @if($divisionId)
        <a href="{{ route('admin.users.hierarki') }}" class="btn btn-sm btn-link text-muted p-0">Reset</a>
    @endif
</form>

<form action="{{ route('admin.users.hierarki.update') }}" method="POST" id="hierarkiForm">
    @csrf

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th class="d-none d-md-table-cell">Role</th>
                            <th class="d-none d-md-table-cell">Divisi</th>
                            <th>Atasan Langsung</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                        <tr>
                            <td class="fw-semibold">{{ $u->name }}</td>
                            <td class="d-none d-md-table-cell">
                                @php
                                    $badgeClass = match($u->role) {
                                        'manager' => 'bg-warning text-dark',
                                        'leader'  => 'bg-info text-dark',
                                        default   => 'bg-primary',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $u->roleLabel() }}</span>
                            </td>
                            <td class="d-none d-md-table-cell small text-muted">{{ $u->division?->name ?? '—' }}</td>
                            <td>
                                <select name="reports_to[{{ $u->id }}]"
                                        class="form-select form-select-sm"
                                        style="max-width:260px">
                                    <option value="">— Tidak ada atasan —</option>
                                    @foreach($supervisors as $sup)
                                        @if($sup->id !== $u->id)
                                        <option value="{{ $sup->id }}"
                                            {{ $u->reports_to == $sup->id ? 'selected' : '' }}>
                                            {{ $sup->name }}
                                            <span class="text-muted">({{ $sup->roleLabel() }})</span>
                                        </option>
                                        @endif
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                Tidak ada user ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($users->isNotEmpty())
    <div class="d-flex align-items-center gap-3">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check2-circle me-1"></i>Simpan Semua
        </button>
        <span class="text-muted small">{{ $users->count() }} user ditampilkan</span>
    </div>
    @endif
</form>

@push('scripts')
<script>
// Highlight changed rows
document.querySelectorAll('select[name^="reports_to"]').forEach(sel => {
    const original = sel.value;
    sel.addEventListener('change', function () {
        this.closest('tr').classList.toggle('table-warning', this.value !== original);
    });
});
</script>
@endpush
@endsection
