@extends('layouts.app')

@section('title', 'Leaderboard Divisi')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-trophy me-2 text-warning"></i>Leaderboard Divisi</h5>
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

<div class="row g-3">
    @forelse($divisions as $rank => $row)
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="fw-bold fs-3 text-muted" style="width:40px;text-align:center">
                    @if($rank === 0) 🥇
                    @elseif($rank === 1) 🥈
                    @elseif($rank === 2) 🥉
                    @else {{ $rank + 1 }}
                    @endif
                </div>
                <div class="flex-grow-1">
                    <div class="fw-semibold">{{ $row['division']->name }}</div>
                    <div class="text-muted small">{{ $row['total'] }} karyawan aktif</div>
                    <div class="progress mt-2" style="height:10px">
                        <div class="progress-bar {{ $row['score_pct'] >= 80 ? 'bg-success' : ($row['score_pct'] >= 50 ? 'bg-warning' : 'bg-danger') }}"
                             style="width:{{ $row['score_pct'] }}%"></div>
                    </div>
                </div>
                <div class="text-end">
                    <div class="fs-4 fw-bold {{ $row['score_pct'] >= 80 ? 'text-success' : ($row['score_pct'] >= 50 ? 'text-warning' : 'text-danger') }}">
                        {{ $row['score_pct'] }}%
                    </div>
                    <div class="text-muted small">
                        Plan: {{ $row['plan_ok'] }} · Report: {{ $row['report_ok'] }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info">Tidak ada data divisi untuk periode ini.</div>
    </div>
    @endforelse
</div>
@endsection
