@extends('layouts.app')

@section('title', 'Monitoring Tim')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-people me-1 text-primary"></i>Monitoring Tim</h5>
    <form class="d-flex gap-2 align-items-center" method="GET">
        <input type="date" name="date" value="{{ $date }}" class="form-control form-control-sm" style="width:160px">
        <button class="btn btn-sm btn-primary">Tampilkan</button>
    </form>
</div>

{{-- Ringkasan --}}
<div class="row g-3 mb-4">
    <div class="col-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-2">
                <div class="fs-4 fw-bold text-primary">{{ $total }}</div>
                <div class="small text-muted">Total</div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-2">
                <div class="fs-4 fw-bold text-info">{{ $sudahPlan }}</div>
                <div class="small text-muted">Sudah Plan</div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-2">
                <div class="fs-4 fw-bold text-success">{{ $lengkap }}</div>
                <div class="small text-muted">Lengkap</div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th class="d-none d-md-table-cell">Divisi</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Plan</th>
                        <th class="text-center">Report</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($karyawans as $k)
                    @php
                        $plan   = $plans[$k->id] ?? null;
                        $status = $plan ? $plan->getCalendarStatus() : 'belum_isi';
                        $statusMap = [
                            'belum_isi'     => ['text' => 'Belum Isi',  'badge' => 'bg-secondary'],
                            'plan_ok'       => ['text' => 'Plan Terkirim','badge' => 'bg-primary'],
                            'lengkap'       => ['text' => 'Lengkap',    'badge' => 'bg-success'],
                            'plan_telat'    => ['text' => 'Plan Telat', 'badge' => 'bg-danger'],
                            'laporan_telat' => ['text' => 'Report Telat','badge' => 'bg-warning text-dark'],
                        ];
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $k->name }}</div>
                        </td>
                        <td class="d-none d-md-table-cell text-muted small">{{ $k->division?->name ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge {{ $statusMap[$status]['badge'] }}">{{ $statusMap[$status]['text'] }}</span>
                        </td>
                        <td class="text-center small">
                            @if($plan?->plan_submitted_at)
                                {{ \Carbon\Carbon::instance($plan->plan_submitted_at)->setTimezone('Asia/Jakarta')->format('H:i') }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center small">
                            @if($plan?->report_submitted_at)
                                {{ \Carbon\Carbon::instance($plan->report_submitted_at)->setTimezone('Asia/Jakarta')->format('H:i') }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($plan)
                                <a href="{{ route('monitoring.detail', [$k->id, $date]) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> <span class="d-none d-md-inline">Detail</span>
                                </a>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
