@extends('layouts.app')

@section('title', 'Target Bulanan Divisi')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-bullseye me-2"></i>Target Bulanan Divisi</h5>
    <form class="d-flex gap-2">
        <select name="month" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
            @foreach($months as $m => $mName)
                <option value="{{ $m }}" @selected($m == $month)>{{ $mName }}</option>
            @endforeach
        </select>
        <select name="year" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
            @foreach(range(now()->year - 1, now()->year + 1) as $y)
                <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
            @endforeach
        </select>
    </form>
</div>

<div class="row g-4">
    @foreach($divisions as $div)
    @php $target = $targets[$div->id] ?? null; @endphp
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-diagram-3 me-1"></i>{{ $div->name }}
            </div>
            <div class="card-body">
                <form action="{{ route('targets.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="division_id" value="{{ $div->id }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <input type="hidden" name="month" value="{{ $month }}">

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Target Plan (%)</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="plan_target_pct" class="form-control"
                                       value="{{ old('plan_target_pct', $target?->plan_target_pct ?? 80) }}"
                                       min="0" max="100" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Target Report (%)</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="report_target_pct" class="form-control"
                                       value="{{ old('report_target_pct', $target?->report_target_pct ?? 80) }}"
                                       min="0" max="100" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Catatan (Opsional)</label>
                        <textarea name="note" rows="2" class="form-control form-control-sm"
                                  placeholder="Target/fokus bulan ini...">{{ old('note', $target?->note) }}</textarea>
                    </div>

                    @if($target)
                        <div class="text-muted small mb-2">
                            Diset oleh {{ $target->setter->name }} pada {{ $target->updated_at->translatedFormat('d M Y') }}
                        </div>
                    @endif

                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-check-lg me-1"></i>{{ $target ? 'Perbarui Target' : 'Simpan Target' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    @if($divisions->isEmpty())
    <div class="col-12">
        <div class="alert alert-info">Tidak ada divisi yang bisa dikelola.</div>
    </div>
    @endif
</div>
@endsection
