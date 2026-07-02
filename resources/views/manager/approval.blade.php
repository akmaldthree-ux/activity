@extends('layouts.app')

@section('title', 'Persetujuan Akun')

@section('content')
<h5 class="fw-bold mb-3"><i class="bi bi-person-check me-1 text-primary"></i>Persetujuan Akun Baru</h5>

{{-- Pending --}}
@if($pending->count())
<div class="card border-0 shadow-sm mb-4" style="border-left: 3px solid #c47a00 !important;">
    <div class="card-header bg-white border-0 pt-3 pb-0">
        <h6 class="fw-bold mb-0" style="color:#c47a00">
            <i class="bi bi-clock me-1"></i>Menunggu Persetujuan
            <span class="badge ms-1" style="background:#c47a00">{{ $pending->count() }}</span>
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th class="d-none d-md-table-cell">Email</th>
                        <th class="d-none d-md-table-cell">Jabatan</th>
                        <th class="d-none d-md-table-cell">No. HP</th>
                        <th>Divisi</th>
                        <th>Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pending as $user)
                    <tr>
                        <td class="fw-semibold">{{ $user->name }}</td>
                        <td class="d-none d-md-table-cell small text-muted">{{ $user->email }}</td>
                        <td class="d-none d-md-table-cell small">{{ $user->jabatan ?? '—' }}</td>
                        <td class="d-none d-md-table-cell small">{{ $user->no_hp ?? '—' }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $user->division?->name ?? '—' }}</span></td>
                        <td class="small text-muted">{{ $user->created_at->diffForHumans() }}</td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                {{-- Approve --}}
                                <form action="{{ route('approval.approve', $user) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-success py-1 px-2"
                                            onclick="return confirm('Setujui akun {{ $user->name }}?')">
                                        <i class="bi bi-check-lg"></i> <span class="d-none d-md-inline">Setujui</span>
                                    </button>
                                </form>

                                {{-- Reject --}}
                                <button class="btn btn-sm btn-danger py-1 px-2"
                                        data-bs-toggle="modal" data-bs-target="#rejectModal{{ $user->id }}">
                                    <i class="bi bi-x-lg"></i> <span class="d-none d-md-inline">Tolak</span>
                                </button>
                            </div>

                            {{-- Modal Tolak --}}
                            <div class="modal fade" id="rejectModal{{ $user->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('approval.reject', $user) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold">Tolak Pendaftaran</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="text-muted small mb-3">
                                                    Anda akan menolak akun <strong>{{ $user->name }}</strong>.
                                                    Karyawan akan mendapat email notifikasi penolakan.
                                                </p>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Alasan Penolakan <span class="text-danger">*</span></label>
                                                    <textarea name="rejection_reason" rows="3" class="form-control"
                                                              placeholder="Tuliskan alasan penolakan..." required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Tolak Pendaftaran</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="alert alert-success border-0 shadow-sm mb-4">
    <i class="bi bi-check-circle me-1"></i>Tidak ada pendaftaran yang menunggu persetujuan.
</div>
@endif

{{-- Riwayat --}}
@if($recent->count())
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 pt-3 pb-0">
        <h6 class="fw-bold mb-0 text-muted"><i class="bi bi-clock-history me-1"></i>Riwayat Terbaru</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th class="d-none d-md-table-cell">Divisi</th>
                        <th class="d-none d-md-table-cell">Jabatan</th>
                        <th>Status</th>
                        <th class="d-none d-md-table-cell">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent as $user)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $user->name }}</div>
                            <div class="small text-muted">{{ $user->email }}</div>
                        </td>
                        <td class="d-none d-md-table-cell small">{{ $user->division?->name ?? '—' }}</td>
                        <td class="d-none d-md-table-cell small">{{ $user->jabatan ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $user->statusBadgeClass() }}">{{ $user->statusLabel() }}</span>
                        </td>
                        <td class="d-none d-md-table-cell small text-muted">
                            {{ $user->rejection_reason ?? '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
