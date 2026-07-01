@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h5 class="fw-bold mb-3"><i class="bi bi-speedometer2 me-1 text-primary"></i>Dashboard</h5>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-primary">{{ $totalKaryawan }}</div>
                <div class="small text-muted">Total Karyawan</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-success">{{ $sudahIsiHariIni }}</div>
                <div class="small text-muted">Sudah Isi Hari Ini</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-danger">{{ $belumIsiHariIni }}</div>
                <div class="small text-muted">Belum Isi Hari Ini</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="fs-2 fw-bold text-info">
                    {{ $hariKerjaBulan > 0 ? round($sudahIsiBulan / ($totalKaryawan * $hariKerjaBulan) * 100) : 0 }}%
                </div>
                <div class="small text-muted">Kepatuhan Bulan Ini</div>
            </div>
        </div>
    </div>
</div>

@if($perDivisi->count())
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold border-0 pt-3">
        <i class="bi bi-diagram-3 me-1 text-secondary"></i>Kehadiran Per Divisi Hari Ini
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Divisi</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Sudah Plan</th>
                        <th class="text-center">Lengkap</th>
                        <th>Progress</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($perDivisi as $row)
                    <tr>
                        <td>{{ $row['divisi'] }}</td>
                        <td class="text-center">{{ $row['total'] }}</td>
                        <td class="text-center">{{ $row['isi'] }}</td>
                        <td class="text-center">{{ $row['lengkap'] }}</td>
                        <td style="min-width:120px">
                            @php $pct = $row['total'] > 0 ? round($row['isi'] / $row['total'] * 100) : 0 @endphp
                            <div class="progress" style="height:8px">
                                <div class="progress-bar {{ $pct == 100 ? 'bg-success' : ($pct >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                     style="width:{{ $pct }}%"></div>
                            </div>
                            <small class="text-muted">{{ $pct }}%</small>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- Grafik Tren Kepatuhan --}}
@if(count($trendData['labels']))
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold border-0 pt-3">
        <i class="bi bi-graph-up me-1 text-primary"></i>Tren Kepatuhan 4 Minggu Terakhir
    </div>
    <div class="card-body">
        <canvas id="trendChart" height="80"></canvas>
    </div>
</div>
@endif

<div class="d-flex gap-2">
    <a href="{{ route('monitoring.tim') }}" class="btn btn-primary">
        <i class="bi bi-people me-1"></i>Lihat Detail Tim
    </a>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
@if(count($trendData['labels']))
const ctx = document.getElementById('trendChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($trendData['labels']),
        datasets: [
            {
                label: 'Plan (%)',
                data: @json($trendData['plan']),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13,110,253,.1)',
                tension: 0.3, fill: true, pointRadius: 3,
            },
            {
                label: 'Report (%)',
                data: @json($trendData['report']),
                borderColor: '#198754',
                backgroundColor: 'rgba(25,135,84,.1)',
                tension: 0.3, fill: true, pointRadius: 3,
            }
        ]
    },
    options: {
        scales: {
            y: { min: 0, max: 100, ticks: { callback: v => v + '%' } }
        },
        plugins: {
            tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': ' + ctx.parsed.y + '%' } }
        }
    }
});
@endif
</script>
@endpush
