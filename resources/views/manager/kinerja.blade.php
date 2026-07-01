@extends('layouts.app')

@section('title', 'Rekap Kinerja Karyawan')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-person-lines-fill me-2"></i>Rekap Kinerja Karyawan</h5>
    <form class="d-flex gap-2">
        <select name="month" class="form-select form-select-sm" style="width:auto">
            @foreach($months as $m => $mName)
                <option value="{{ $m }}" @selected($m == $month)>{{ $mName }}</option>
            @endforeach
        </select>
        <select name="year" class="form-select form-select-sm" style="width:auto">
            @foreach(range(now()->year - 1, now()->year + 1) as $y)
                <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-sm btn-primary">Tampilkan</button>
    </form>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Karyawan</th>
                        <th>Divisi</th>
                        <th class="text-center">Plan</th>
                        <th class="text-center">Report</th>
                        <th class="text-center">Avg Rating</th>
                        <th style="min-width:120px">Kepatuhan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekap as $i => $row)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <span class="fw-semibold">{{ $row['user']->name }}</span>
                            @if($row['user']->jabatan)
                                <div class="text-muted small">{{ $row['user']->jabatan }}</div>
                            @endif
                        </td>
                        <td>{{ $row['user']->division?->name ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge {{ $row['plan_pct'] >= 80 ? 'bg-success' : ($row['plan_pct'] >= 50 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                {{ $row['plan_pct'] }}%
                            </span>
                            <div class="text-muted small">{{ $row['plan_count'] }}/{{ $row['workdays'] }}</div>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $row['report_pct'] >= 80 ? 'bg-success' : ($row['report_pct'] >= 50 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                {{ $row['report_pct'] }}%
                            </span>
                            <div class="text-muted small">{{ $row['report_count'] }}/{{ $row['workdays'] }}</div>
                        </td>
                        <td class="text-center">
                            @if($row['avg_rating'])
                                <span class="text-warning">
                                    @for($s=1;$s<=5;$s++)
                                        <i class="bi bi-star{{ $s <= round($row['avg_rating']) ? '-fill' : '' }}" style="font-size:.7rem"></i>
                                    @endfor
                                </span>
                                <span class="small text-muted">({{ $row['avg_rating'] }})</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            @php $pct = $row['plan_pct'] @endphp
                            <div class="progress" style="height:8px">
                                <div class="progress-bar {{ $pct >= 80 ? 'bg-success' : ($pct >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                     style="width:{{ $pct }}%"></div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
