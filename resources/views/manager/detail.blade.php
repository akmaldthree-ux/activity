@extends('layouts.app')

@section('title', 'Detail — ' . $user->name)

@section('content')
<div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
    <a href="{{ route('monitoring.tim', ['date' => $parsedDate->format('Y-m-d')]) }}"
       class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h5 class="fw-bold mb-0">{{ $user->name }}</h5>
        <small class="text-muted">{{ $parsedDate->translatedFormat('l, d F Y') }} · {{ $user->division?->name ?? '—' }}</small>
    </div>
</div>

{{-- Goals --}}
@if($plan->goals->count())
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-0 pt-3 pb-0">
        <h6 class="fw-bold text-primary mb-0"><i class="bi bi-bullseye me-1"></i>Goal</h6>
    </div>
    <div class="card-body">
        @foreach($plan->goals as $goal)
        <div class="mb-2 p-2 bg-light rounded">
            <div class="fw-semibold">{{ $goal->description }}</div>
            @if($goal->target) <div class="small text-muted">Target: {{ $goal->target }}</div> @endif
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Aktivitas --}}
@if($plan->activities->count())
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-0 pt-3 pb-0">
        <h6 class="fw-bold mb-0"><i class="bi bi-list-check me-1"></i>Aktivitas</h6>
    </div>
    <div class="card-body">
        @foreach($plan->activities as $act)
        <div class="mb-2 p-3 border rounded">
            <div class="d-flex justify-content-between flex-wrap gap-1 mb-1">
                <span class="fw-semibold">{{ $act->description }}</span>
                <div>
                    <span class="badge
                        {{ $act->priority=='tinggi' ? 'bg-danger' : ($act->priority=='sedang' ? 'bg-warning text-dark' : 'bg-success') }}">
                        {{ $act->priorityLabel() }}
                    </span>
                    @if($act->status)
                    <span class="badge ms-1
                        {{ $act->status=='selesai' ? 'bg-success' : ($act->status=='sebagian' ? 'bg-warning text-dark' : 'bg-danger') }}">
                        {{ $act->statusLabel() }}
                    </span>
                    @endif
                </div>
            </div>
            @if($act->realisasi)  <div class="small text-muted">Realisasi: {{ $act->realisasi }}</div> @endif
            @if($act->keterangan) <div class="small text-muted">Keterangan: {{ $act->keterangan }}</div> @endif
        </div>
        @endforeach
        @if($plan->insight)
        <div class="mt-2 p-3 bg-light rounded">
            <span class="fw-semibold">💡 Insight:</span> {{ $plan->insight }}
        </div>
        @endif
    </div>
</div>
@endif

{{-- Feedback --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 pt-3 pb-0">
        <h6 class="fw-bold mb-0"><i class="bi bi-chat-dots me-1 text-warning"></i>Feedback Atasan</h6>
    </div>
    <div class="card-body">
        @if($feedback)
        <div class="mb-3 p-3 bg-light rounded">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="fw-semibold small">{{ $feedback->manager?->name ?? '—' }}</span>
                @if($feedback->rating)
                <span class="text-warning">
                    @for($i=1;$i<=5;$i++)
                        <i class="bi bi-star{{ $i <= $feedback->rating ? '-fill' : '' }} small"></i>
                    @endfor
                </span>
                @endif
            </div>
            <p class="mb-0">{{ $feedback->comment ?? '—' }}</p>
        </div>
        @endif

        <form action="{{ route('monitoring.feedback', $plan->id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Komentar</label>
                <textarea name="comment" rows="3" class="form-control"
                          placeholder="Berikan feedback untuk karyawan ini...">{{ $feedback?->comment }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Penilaian</label>
                <div class="d-flex gap-2">
                    @for($i=1;$i<=5;$i++)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="rating"
                               id="rating{{ $i }}" value="{{ $i }}"
                               {{ ($feedback?->rating ?? 0) == $i ? 'checked' : '' }}>
                        <label class="form-check-label" for="rating{{ $i }}">
                            {{ $i }} <i class="bi bi-star-fill text-warning"></i>
                        </label>
                    </div>
                    @endfor
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-send me-1"></i>Simpan Feedback
            </button>
        </form>
    </div>
</div>
@endsection
