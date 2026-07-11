@extends('layouts.app')

@section('title', 'Rekap Mingguan')

@push('styles')
<style>
@media print {
    .n-topnav, .n-sidebar, .n-backdrop, .no-print { display: none !important; }
    .n-shell { display: block !important; }
    .n-main { padding: 0 !important; }
    .card { border: 1px solid #e6e6e6 !important; box-shadow: none !important; }
}
</style>
@endpush

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-file-earmark-text me-2"></i>Rekap Aktivitas</h5>
    <div class="d-flex gap-2 no-print flex-wrap">
        <form class="d-flex gap-2 align-items-center flex-wrap">
            <div class="d-flex align-items-center gap-1">
                <label class="small text-muted mb-0 text-nowrap">Dari</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ $dateFrom }}" style="width:auto">
            </div>
            <div class="d-flex align-items-center gap-1">
                <label class="small text-muted mb-0">s.d.</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ $dateTo }}" style="width:auto">
            </div>
            <button type="submit" class="btn btn-sm btn-primary">Tampilkan</button>
        </form>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-printer me-1"></i>Cetak / PDF
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white">
        <strong>{{ $weekStart->format('d M Y') }} – {{ $weekEnd->format('d M Y') }}</strong>
        <span class="text-muted ms-2 small">{{ $totalKaryawan }} karyawan · {{ count($weekDays) }} hari</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Karyawan</th>
                        <th>Divisi</th>
                        @foreach($weekDays as $day)
                            <th class="text-center small" style="min-width:70px">{{ $day->format('d/m') }}<br><span class="text-muted" style="font-size:.7rem">{{ $day->translatedFormat('D') }}</span></th>
                        @endforeach
                        <th class="text-center">Plan</th>
                        <th class="text-center">Report</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekap as $row)
                    <tr>
                        <td>
                            <span class="fw-semibold small">{{ $row['user']->name }}</span>
                        </td>
                        <td class="small">{{ $row['user']->division?->name ?? '—' }}</td>
                        @foreach($weekDays as $day)
                            @php
                                $dateStr = $day->format('Y-m-d');
                                $planData = $row['plans'][$dateStr] ?? null;
                            @endphp
                            <td class="text-center">
                                @if($row['holidays'][$dateStr] ?? false)
                                    <span class="text-muted small">—</span>
                                @elseif(!$planData)
                                    <span class="text-danger small">✗</span>
                                @elseif($planData->report_submitted_at)
                                    <span class="text-success">✓</span>
                                @elseif($planData->plan_submitted_at)
                                    <span class="text-primary small">P</span>
                                @else
                                    <span class="text-danger small">✗</span>
                                @endif
                            </td>
                        @endforeach
                        <td class="text-center">
                            <span class="{{ $row['plan_pct'] >= 80 ? 'text-success' : 'text-danger' }} fw-semibold small">
                                {{ $row['plan_pct'] }}%
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="{{ $row['report_pct'] >= 80 ? 'text-success' : 'text-danger' }} fw-semibold small">
                                {{ $row['report_pct'] }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="no-print small text-muted">
    Legenda: ✓ Lengkap (plan+report) · P Plan saja · ✗ Belum isi · — Hari Libur
</div>
@endsection
