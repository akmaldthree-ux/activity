@extends('layouts.app')

@section('title', 'Kalender Saya')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0">
        <i class="bi bi-calendar3 me-1 text-primary"></i>Kalender Saya
    </h5>
    <div class="d-flex gap-2 align-items-center">
        <a href="{{ route('karyawan.kalender', ['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}"
           class="btn btn-sm btn-outline-secondary"><i class="bi bi-chevron-left"></i></a>
        <span class="fw-semibold">{{ $firstDay->translatedFormat('F Y') }}</span>
        <a href="{{ route('karyawan.kalender', ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}"
           class="btn btn-sm btn-outline-secondary"><i class="bi bi-chevron-right"></i></a>
        <a href="{{ route('karyawan.kalender') }}" class="btn btn-sm btn-primary">Hari Ini</a>
    </div>
</div>

{{-- Legenda --}}
<div class="d-flex flex-wrap gap-3 mb-3 small">
    <span><span class="indicator indicator-belum"></span> Belum isi</span>
    <span><span class="indicator indicator-plan_ok"></span> Plan terkirim</span>
    <span><span class="indicator indicator-lengkap"></span> Lengkap</span>
    <span><span class="indicator indicator-plan_telat"></span> Telat / lewat deadline</span>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-2 p-md-3">
        {{-- Header hari --}}
        <div class="row g-1 mb-1 text-center">
            @foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $i => $h)
                <div class="col {{ $i >= 5 ? 'text-muted' : 'fw-semibold' }}" style="font-size:.8rem">{{ $h }}</div>
            @endforeach
        </div>

        {{-- Grid kalender --}}
        @php
            // Isi padding awal (Senin = 1, isoWeekday)
            $startPad = $firstDay->dayOfWeekIso - 1; // 0=Sen
            $daysInMonth = $lastDay->day;
            $totalCells = $startPad + $daysInMonth;
            $rows = (int) ceil($totalCells / 7);
        @endphp

        @for($row = 0; $row < $rows; $row++)
            <div class="row g-1 mb-1">
                @for($col = 0; $col < 7; $col++)
                    @php
                        $cellIndex = $row * 7 + $col;
                        $dayNum = $cellIndex - $startPad + 1;
                        $isCurrentMonth = $dayNum >= 1 && $dayNum <= $daysInMonth;
                        $isWeekend = $col >= 5; // Sab & Min
                        $dateStr = $isCurrentMonth ? \Carbon\Carbon::create($year, $month, $dayNum)->format('Y-m-d') : null;
                        $plan = $dateStr ? ($plans[$dateStr] ?? null) : null;
                        $isToday = $dateStr === $now->toDateString();
                        $status = $plan ? $plan->getCalendarStatus() : ($isCurrentMonth && !$isWeekend ? 'belum_isi' : '');
                    @endphp
                    <div class="col">
                        @if($isCurrentMonth && !$isWeekend)
                            <a href="{{ route('karyawan.daily', $dateStr) }}"
                               class="d-block cal-day border p-1 p-md-2 text-decoration-none text-dark {{ $isToday ? 'today' : '' }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <span class="day-num {{ $isToday ? 'text-primary' : '' }}">{{ $dayNum }}</span>
                                    <span class="indicator indicator-{{ $status }}"></span>
                                </div>
                                @if($plan)
                                    <div class="text-muted" style="font-size:.68rem;line-height:1.2;margin-top:2px">
                                        {{ $plan->goals->count() }} goal
                                        · {{ $plan->activities->count() }} aktivitas
                                    </div>
                                @endif
                            </a>
                        @elseif($isCurrentMonth && $isWeekend)
                            <div class="cal-day weekend border p-1 p-md-2">
                                <span class="day-num">{{ $dayNum }}</span>
                                <div style="font-size:.65rem;color:#ccc">Libur</div>
                            </div>
                        @else
                            <div class="cal-day border p-1 p-md-2 other-month">
                                <span class="day-num text-muted">{{ $dayNum > 0 ? $dayNum : '' }}</span>
                            </div>
                        @endif
                    </div>
                @endfor
            </div>
        @endfor
    </div>
</div>

{{-- Shortcut hari ini --}}
@if($now->month === $month && $now->year === $year && !$now->isWeekend())
<div class="mt-3">
    <a href="{{ route('karyawan.daily', $now->format('Y-m-d')) }}" class="btn btn-primary">
        <i class="bi bi-pencil-square me-1"></i>Isi Catatan Hari Ini
    </a>
</div>
@endif
@endsection
