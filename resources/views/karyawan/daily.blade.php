@extends('layouts.app')

@section('title', 'Catatan ' . $parsedDate->translatedFormat('d F Y'))

@push('styles')
<style>
    .section-card   { border-left: 3px solid; }
    .section-plan   { border-left-color: var(--n-primary); }
    .section-report { border-left-color: var(--n-green); }
    .activity-row   { background: var(--n-canvas-soft); border-radius: var(--n-r-md); }
    .priority-badge-tinggi { background: #fee2e2; color: #991b1b; border-radius: 4px; font-size:11px; font-weight:600; padding:2px 7px; }
    .priority-badge-sedang { background: #fef3c7; color: #92400e; border-radius: 4px; font-size:11px; font-weight:600; padding:2px 7px; }
    .priority-badge-rendah { background: #d1fae5; color: #065f46; border-radius: 4px; font-size:11px; font-weight:600; padding:2px 7px; }

    @media (max-width: 767px) {
        /* Description stacks full-width; controls + delete share the next row */
        .goal-row .row > div:first-child,
        .activity-row-item .row > div:first-child {
            width: 100% !important; flex: 0 0 100% !important;
        }
        .goal-row .row > div:nth-child(2),
        .activity-row-item .row > div:nth-child(2),
        .activity-row-item .row > div:nth-child(3) {
            flex: 1 1 0% !important; width: auto !important;
        }
        .goal-row .row > div:last-child,
        .activity-row-item .row > div:last-child {
            flex: 0 0 auto !important; width: auto !important;
        }
        .goal-row .btn-remove-goal,
        .activity-row-item .btn-remove-activity { width: auto !important; }
    }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
    <a href="{{ route('karyawan.kalender', ['year' => $parsedDate->year, 'month' => $parsedDate->month]) }}"
       class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h5 class="fw-bold mb-0">
            {{ $parsedDate->translatedFormat('l, d F Y') }}
            @if($isToday) <span class="badge bg-primary ms-1">Hari Ini</span> @endif
        </h5>
        @if(!$isToday)
            <small class="text-muted">Catatan hari lampau (hanya bisa dibaca)</small>
        @endif
    </div>
</div>

{{-- ============================================================ PLAN PAGI ============================================================ --}}
<div class="card border-0 shadow-sm section-card section-plan mb-4">
    <div class="card-header bg-white border-0 pt-3 pb-0 d-flex align-items-center justify-content-between">
        <h6 class="fw-bold text-primary mb-0"><i class="bi bi-sun me-1"></i>Plan Pagi</h6>
        @if(!$canEditPlan && $plan->plan_submitted_at)
            <small class="text-muted">Dikirim: {{ \Carbon\Carbon::instance($plan->plan_submitted_at)->setTimezone('Asia/Jakarta')->format('H:i') }}
                @if(\Carbon\Carbon::instance($plan->plan_submitted_at)->setTimezone('Asia/Jakarta')->hour >= \App\Models\DailyPlan::PLAN_DEADLINE_HOUR)
                    <span class="badge bg-danger ms-1">Telat</span>
                @else
                    <span class="badge bg-success ms-1">Tepat Waktu</span>
                @endif
            </small>
        @elseif($isToday && !$plan->plan_submitted_at)
            <small class="{{ $planDeadlinePassed ? 'text-danger' : 'text-muted' }}">
                <i class="bi bi-clock me-1"></i>Deadline 09:00
                @if($planDeadlinePassed) <span class="badge bg-danger">Sudah Lewat</span> @endif
            </small>
        @endif
    </div>

    <div class="card-body">
        @if($canEditPlan)
        {{-- Carry over aktivitas belum selesai --}}
        @if($carryOverActivities->count())
        <div class="alert alert-info py-2 mb-3">
            <div class="fw-semibold mb-1"><i class="bi bi-arrow-repeat me-1"></i>Ada {{ $carryOverActivities->count() }} aktivitas belum selesai kemarin:</div>
            @foreach($carryOverActivities as $co)
            <div class="d-flex align-items-center justify-content-between mb-1">
                <small>{{ $co->description }} <span class="badge bg-secondary">{{ $co->statusLabel() }}</span></small>
                <button type="button" class="btn btn-xs btn-sm btn-outline-primary py-0 px-2"
                        onclick="addCarryOver({{ json_encode($co->description) }}, {{ json_encode($co->priority) }})">
                    + Tambah
                </button>
            </div>
            @endforeach
            <button type="button" class="btn btn-sm btn-primary mt-1"
                    onclick="addAllCarryOver()">
                <i class="bi bi-plus-circle me-1"></i>Tambahkan Semua
            </button>
        </div>
        @endif

        {{-- Form plan --}}
        <form action="{{ route('karyawan.plan.store', $parsedDate->format('Y-m-d')) }}" method="POST" id="formPlan">
            @csrf
            {{-- Goals --}}
            <div class="mb-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <label class="fw-semibold">🎯 Goal Hari Ini</label>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddGoal">
                        <i class="bi bi-plus"></i> Tambah Goal
                    </button>
                </div>
                <div id="goalContainer">
                    @forelse($plan->goals ?? [] as $goal)
                    <div class="goal-row mb-2 p-2 activity-row">
                        <div class="row g-2">
                            <div class="col-md-7">
                                <input type="text" name="goals[{{ $loop->index }}][description]"
                                       class="form-control form-control-sm" placeholder="Deskripsi goal"
                                       value="{{ $goal->description }}" required>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="goals[{{ $loop->index }}][target]"
                                       class="form-control form-control-sm" placeholder="Target (opsional)"
                                       value="{{ $goal->target }}">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-goal w-100">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="goal-row mb-2 p-2 activity-row">
                        <div class="row g-2">
                            <div class="col-md-7">
                                <input type="text" name="goals[0][description]" class="form-control form-control-sm"
                                       placeholder="Contoh: Menyelesaikan laporan mingguan" required>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="goals[0][target]" class="form-control form-control-sm"
                                       placeholder="Target (opsional)">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-goal w-100">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Aktivitas --}}
            <div class="mb-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <label class="fw-semibold">📌 Aktivitas Prioritas</label>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalTemplate">
                            <i class="bi bi-bookmark me-1"></i>Template
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success" id="btnAddActivity">
                            <i class="bi bi-plus"></i> Tambah
                        </button>
                    </div>
                </div>
                <div id="activityContainer">
                    @php $tags = \App\Models\Activity::TAGS; @endphp
                    @forelse($plan->activities ?? [] as $activity)
                    <div class="activity-row-item mb-2 p-2 activity-row">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-6">
                                <input type="text" name="activities[{{ $loop->index }}][description]"
                                       class="form-control form-control-sm" placeholder="Deskripsi aktivitas"
                                       value="{{ $activity->description }}" required>
                            </div>
                            <div class="col-md-2">
                                <select name="activities[{{ $loop->index }}][tag]" class="form-select form-select-sm">
                                    <option value="">Tag</option>
                                    @foreach($tags as $t)
                                    <option value="{{ $t }}" {{ $activity->tag==$t?'selected':'' }}>{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="activities[{{ $loop->index }}][priority]" class="form-select form-select-sm">
                                    <option value="tinggi" {{ $activity->priority=='tinggi'?'selected':'' }}>🔴 Tinggi</option>
                                    <option value="sedang" {{ $activity->priority=='sedang'?'selected':'' }}>🟡 Sedang</option>
                                    <option value="rendah" {{ $activity->priority=='rendah'?'selected':'' }}>🟢 Rendah</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-activity w-100">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="activity-row-item mb-2 p-2 activity-row">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-6">
                                <input type="text" name="activities[0][description]" class="form-control form-control-sm"
                                       placeholder="Contoh: Meeting dengan tim" required>
                            </div>
                            <div class="col-md-2">
                                <select name="activities[0][tag]" class="form-select form-select-sm">
                                    <option value="">Tag</option>
                                    @foreach($tags as $t)
                                    <option value="{{ $t }}">{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="activities[0][priority]" class="form-select form-select-sm">
                                    <option value="tinggi">🔴 Tinggi</option>
                                    <option value="sedang" selected>🟡 Sedang</option>
                                    <option value="rendah">🟢 Rendah</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-activity w-100">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-send me-1"></i>
                {{ $plan->plan_submitted_at ? 'Perbarui Plan' : 'Kirim Plan Pagi' }}
            </button>
        </form>
        @else
        {{-- Tampilan read-only plan --}}
        @if($plan->goals && $plan->goals->count())
            <h6 class="text-muted small fw-semibold mb-2">🎯 GOAL</h6>
            @foreach($plan->goals as $goal)
            <div class="mb-2 p-2 activity-row rounded">
                <div class="fw-semibold">{{ $goal->description }}</div>
                @if($goal->target) <div class="small text-muted">Target: {{ $goal->target }}</div> @endif
            </div>
            @endforeach
        @endif
        @if($plan->activities && $plan->activities->count())
            <h6 class="text-muted small fw-semibold mt-3 mb-2">📌 AKTIVITAS PRIORITAS</h6>
            @foreach($plan->activities as $act)
            <div class="mb-2 p-2 activity-row rounded d-flex justify-content-between align-items-start">
                <span>{{ $act->description }}</span>
                <div class="d-flex gap-1 flex-shrink-0 ms-2">
                    @if($act->tag) <span class="badge bg-light text-secondary border">{{ $act->tag }}</span> @endif
                    <span class="badge priority-badge-{{ $act->priority }}">{{ $act->priorityLabel() }}</span>
                </div>
            </div>
            @endforeach
        @endif
        @if(!$plan->exists || (!$plan->goals?->count() && !$plan->activities?->count()))
            <p class="text-muted">Belum ada plan untuk hari ini.</p>
        @endif
        @endif
    </div>
</div>

{{-- ============================================================ REPORT SORE ============================================================ --}}
<div class="card border-0 shadow-sm section-card section-report mb-4">
    <div class="card-header bg-white border-0 pt-3 pb-0 d-flex align-items-center justify-content-between">
        <h6 class="fw-bold text-success mb-0"><i class="bi bi-moon me-1"></i>Report Sore</h6>
        @if($plan->report_submitted_at)
            <small class="text-muted">Dikirim: {{ \Carbon\Carbon::instance($plan->report_submitted_at)->setTimezone('Asia/Jakarta')->format('H:i') }}
                @if(\Carbon\Carbon::instance($plan->report_submitted_at)->setTimezone('Asia/Jakarta')->hour >= \App\Models\DailyPlan::REPORT_DEADLINE_HOUR)
                    <span class="badge bg-danger ms-1">Telat</span>
                @else
                    <span class="badge bg-success ms-1">Tepat Waktu</span>
                @endif
            </small>
        @elseif($isToday && $plan->plan_submitted_at)
            <small class="{{ $reportDeadlinePassed ? 'text-danger' : 'text-muted' }}">
                <i class="bi bi-clock me-1"></i>Deadline 20:00
                @if($reportDeadlinePassed) <span class="badge bg-danger">Sudah Lewat</span> @endif
            </small>
        @endif
    </div>

    <div class="card-body">
        @if(!$plan->exists || !$plan->plan_submitted_at)
            <p class="text-muted">Isi plan pagi terlebih dahulu.</p>
        @elseif($canEditReport)
        {{-- Form report --}}
        <form action="{{ route('karyawan.report.store', $parsedDate->format('Y-m-d')) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @foreach($plan->activities as $idx => $activity)
            <input type="hidden" name="activities[{{ $idx }}][id]" value="{{ $activity->id }}">
            <div class="mb-3 p-3 border rounded">
                <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-1">
                    <span class="fw-semibold">{{ $activity->description }}</span>
                    <span class="badge priority-badge-{{ $activity->priority }}">{{ $activity->priorityLabel() }}</span>
                </div>
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Status</label>
                        <select name="activities[{{ $idx }}][status]" class="form-select form-select-sm" required>
                            <option value="">-- Pilih --</option>
                            <option value="selesai"  {{ $activity->status=='selesai'  ? 'selected' : '' }}>✅ Selesai</option>
                            <option value="sebagian" {{ $activity->status=='sebagian' ? 'selected' : '' }}>🔶 Sebagian</option>
                            <option value="tidak"    {{ $activity->status=='tidak'    ? 'selected' : '' }}>❌ Tidak</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold">Realisasi</label>
                        <input type="text" name="activities[{{ $idx }}][realisasi]"
                               class="form-control form-control-sm" placeholder="Apa yang sudah dilakukan"
                               value="{{ $activity->realisasi }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Keterangan</label>
                        <input type="text" name="activities[{{ $idx }}][keterangan]"
                               class="form-control form-control-sm" placeholder="Catatan tambahan"
                               value="{{ $activity->keterangan }}">
                    </div>
                </div>
            </div>
            @endforeach

            <div class="mb-3">
                <label class="form-label fw-semibold">💡 Insight / Catatan Akhir Hari</label>
                <textarea name="insight" rows="3" class="form-control"
                          placeholder="Apa yang bisa ditingkatkan hari ini?">{{ $plan->insight }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">📎 Lampiran (Opsional, maks. 5 file)</label>
                <input type="file" name="attachments[]" class="form-control" multiple
                       accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
                <div class="form-text">Foto bukti, dokumen, atau tangkapan layar. Maks. 5 MB per file.</div>
            </div>

            <button type="submit" class="btn btn-success">
                <i class="bi bi-send me-1"></i>
                {{ $plan->report_submitted_at ? 'Perbarui Report' : 'Kirim Report Sore' }}
            </button>
        </form>
        @else
        {{-- Tampilan read-only report --}}
        @if($plan->activities->count())
            @foreach($plan->activities as $act)
            <div class="mb-2 p-3 border rounded">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-1 mb-1">
                    <span class="fw-semibold">{{ $act->description }}</span>
                    <div>
                        <span class="badge priority-badge-{{ $act->priority }}">{{ $act->priorityLabel() }}</span>
                        @if($act->status)
                        <span class="badge {{ $act->status=='selesai' ? 'bg-success' : ($act->status=='sebagian' ? 'bg-warning text-dark' : 'bg-danger') }} ms-1">
                            {{ $act->statusLabel() }}
                        </span>
                        @endif
                    </div>
                </div>
                @if($act->realisasi) <div class="small text-muted">Realisasi: {{ $act->realisasi }}</div> @endif
                @if($act->keterangan) <div class="small text-muted">Keterangan: {{ $act->keterangan }}</div> @endif
            </div>
            @endforeach
        @endif
        @if($plan->insight)
            <div class="mt-3 p-3 bg-light rounded">
                <span class="fw-semibold">💡 Insight:</span> {{ $plan->insight }}
            </div>
        @endif
        @if(!$plan->report_submitted_at)
            <p class="text-muted">Report belum diisi.</p>
        @endif

        {{-- Lampiran --}}
        @if($plan->attachments && $plan->attachments->count())
        <div class="mt-3">
            <h6 class="text-muted small fw-semibold mb-2">📎 LAMPIRAN</h6>
            <div class="d-flex flex-wrap gap-2">
                @foreach($plan->attachments as $att)
                @if($att->isImage())
                    <a href="{{ Storage::url($att->path) }}" target="_blank">
                        <img src="{{ Storage::url($att->path) }}" alt="{{ $att->original_name }}"
                             class="rounded border" style="height:80px;width:80px;object-fit:cover;">
                    </a>
                @else
                    <a href="{{ Storage::url($att->path) }}" target="_blank"
                       class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-file-earmark me-1"></i>{{ $att->original_name }}
                    </a>
                @endif
                @endforeach
            </div>
        </div>
        @endif
        @endif
    </div>
</div>

{{-- Modal Template Aktivitas --}}
<div class="modal fade" id="modalTemplate" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold"><i class="bi bi-bookmark me-1"></i>Template Aktivitas</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="templateList" class="mb-3">
                    <div class="text-muted small text-center py-3">Memuat...</div>
                </div>
                <hr>
                <div class="fw-semibold small mb-2">Simpan sebagai Template Baru</div>
                <div class="row g-2">
                    <div class="col-8">
                        <input type="text" id="tplDesc" class="form-control form-control-sm" placeholder="Deskripsi aktivitas">
                    </div>
                    <div class="col-3">
                        <select id="tplPriority" class="form-select form-select-sm">
                            <option value="tinggi">🔴 Tinggi</option>
                            <option value="sedang" selected>🟡 Sedang</option>
                            <option value="rendah">🟢 Rendah</option>
                        </select>
                    </div>
                    <div class="col-1">
                        <button type="button" class="btn btn-sm btn-primary w-100" onclick="saveTemplate()">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Feedback atasan --}}
@if($plan->exists && $plan->feedback)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 pt-3 pb-0">
        <h6 class="fw-bold mb-0"><i class="bi bi-chat-dots me-1 text-warning"></i>Feedback Atasan</h6>
    </div>
    <div class="card-body">
        {{-- Feedback utama --}}
        <div class="d-flex align-items-start gap-3 mb-3">
            <div class="n-initials bg-primary" style="width:36px;height:36px;font-size:13px">
                {{ strtoupper(substr($plan->feedback->manager->name, 0, 1)) }}
            </div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="fw-semibold small">{{ $plan->feedback->manager->name }}</span>
                    @if($plan->feedback->rating)
                        <span class="text-warning small">
                            @for($i=1;$i<=5;$i++)
                                <i class="bi bi-star{{ $i <= $plan->feedback->rating ? '-fill' : '' }}"></i>
                            @endfor
                        </span>
                    @endif
                    <span class="text-muted" style="font-size:.7rem">{{ $plan->feedback->created_at->diffForHumans() }}</span>
                </div>
                <p class="mb-0 small">{{ $plan->feedback->comment ?? '—' }}</p>
            </div>
        </div>

        {{-- Balasan --}}
        @foreach($plan->feedback->replies as $reply)
        <div class="d-flex align-items-start gap-3 mb-2 ps-4">
            <div class="n-initials" style="width:28px;height:28px;font-size:11px;background:var(--n-ink-faint)">
                {{ strtoupper(substr($reply->user->name, 0, 1)) }}
            </div>
            <div class="flex-grow-1 rounded p-2" style="background:var(--n-canvas-soft)">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="fw-semibold small">{{ $reply->user->name }}</span>
                    <span class="text-muted" style="font-size:.7rem">{{ $reply->created_at->diffForHumans() }}</span>
                </div>
                <p class="mb-0 small">{{ $reply->body }}</p>
            </div>
        </div>
        @endforeach

        {{-- Form balasan --}}
        <div class="ps-4 mt-3">
            <form action="{{ route('feedback.reply', $plan->feedback) }}" method="POST" class="d-flex gap-2">
                @csrf
                <input type="text" name="body" class="form-control form-control-sm"
                       placeholder="Tulis balasan..." required>
                <button type="submit" class="btn btn-sm btn-outline-primary flex-shrink-0">
                    <i class="bi bi-send"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
let goalIdx = {{ max(($plan->goals?->count() ?? 0) - 1, 0) }};
let actIdx  = {{ max(($plan->activities?->count() ?? 0) - 1, 0) }};

function rebuildIndexes(container, prefix) {
    container.querySelectorAll('[class$="-row"], [class*="-row "]').forEach((row, i) => {
        row.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/\[\d+\]/, `[${i}]`);
        });
    });
}

document.getElementById('btnAddGoal')?.addEventListener('click', () => {
    goalIdx++;
    const container = document.getElementById('goalContainer');
    const div = document.createElement('div');
    div.className = 'goal-row mb-2 p-2 activity-row';
    div.innerHTML = `<div class="row g-2">
        <div class="col-md-7"><input type="text" name="goals[${goalIdx}][description]" class="form-control form-control-sm" placeholder="Deskripsi goal" required></div>
        <div class="col-md-4"><input type="text" name="goals[${goalIdx}][target]" class="form-control form-control-sm" placeholder="Target (opsional)"></div>
        <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-goal w-100"><i class="bi bi-trash"></i></button></div>
    </div>`;
    container.appendChild(div);
    attachRemoveGoal(div.querySelector('.btn-remove-goal'));
});

const availableTags = @json(\App\Models\Activity::TAGS);
const tagOptions = availableTags.map(t => `<option value="${t}">${t}</option>`).join('');

document.getElementById('btnAddActivity')?.addEventListener('click', () => {
    actIdx++;
    const container = document.getElementById('activityContainer');
    const div = document.createElement('div');
    div.className = 'activity-row-item mb-2 p-2 activity-row';
    div.innerHTML = `<div class="row g-2 align-items-center">
        <div class="col-md-6"><input type="text" name="activities[${actIdx}][description]" class="form-control form-control-sm" placeholder="Deskripsi aktivitas" required></div>
        <div class="col-md-2"><select name="activities[${actIdx}][tag]" class="form-select form-select-sm"><option value="">Tag</option>${tagOptions}</select></div>
        <div class="col-md-3"><select name="activities[${actIdx}][priority]" class="form-select form-select-sm">
            <option value="tinggi">🔴 Tinggi</option>
            <option value="sedang" selected>🟡 Sedang</option>
            <option value="rendah">🟢 Rendah</option>
        </select></div>
        <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-activity w-100"><i class="bi bi-trash"></i></button></div>
    </div>`;
    container.appendChild(div);
    attachRemoveActivity(div.querySelector('.btn-remove-activity'));
});

function attachRemoveGoal(btn) {
    btn.addEventListener('click', function() {
        const container = document.getElementById('goalContainer');
        if (container.querySelectorAll('.goal-row').length > 1) {
            this.closest('.goal-row').remove();
        }
    });
}
function attachRemoveActivity(btn) {
    btn.addEventListener('click', function() {
        const container = document.getElementById('activityContainer');
        if (container.querySelectorAll('.activity-row-item').length > 1) {
            this.closest('.activity-row-item').remove();
        }
    });
}

document.querySelectorAll('.btn-remove-goal').forEach(attachRemoveGoal);
document.querySelectorAll('.btn-remove-activity').forEach(attachRemoveActivity);

// Carry over helpers
const carryOverData = @json($carryOverActivities->map(fn($a) => ['description' => $a->description, 'priority' => $a->priority]));

function addCarryOver(desc, priority, tag) {
    actIdx++;
    const container = document.getElementById('activityContainer');
    if (!container) return;
    const div = document.createElement('div');
    div.className = 'activity-row-item mb-2 p-2 activity-row';
    div.innerHTML = `<div class="row g-2 align-items-center">
        <div class="col-md-6"><input type="text" name="activities[${actIdx}][description]" class="form-control form-control-sm" value="${desc.replace(/"/g,'&quot;')}" required></div>
        <div class="col-md-2"><select name="activities[${actIdx}][tag]" class="form-select form-select-sm"><option value="">Tag</option>${tagOptions}</select></div>
        <div class="col-md-3"><select name="activities[${actIdx}][priority]" class="form-select form-select-sm">
            <option value="tinggi" ${priority==='tinggi'?'selected':''}>🔴 Tinggi</option>
            <option value="sedang" ${priority==='sedang'?'selected':''}>🟡 Sedang</option>
            <option value="rendah" ${priority==='rendah'?'selected':''}>🟢 Rendah</option>
        </select></div>
        <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-activity w-100"><i class="bi bi-trash"></i></button></div>
    </div>`;
    container.appendChild(div);
    attachRemoveActivity(div.querySelector('.btn-remove-activity'));
}

function addAllCarryOver() {
    carryOverData.forEach(item => addCarryOver(item.description, item.priority));
}

// Template Aktivitas
const csrf = document.querySelector('meta[name="csrf-token"]').content;

async function loadTemplates() {
    const res = await fetch('{{ route("templates.index") }}');
    const templates = await res.json();
    const list = document.getElementById('templateList');
    if (!templates.length) {
        list.innerHTML = '<div class="text-muted small text-center py-2">Belum ada template.</div>';
        return;
    }
    list.innerHTML = templates.map(t => `
        <div class="d-flex align-items-center justify-content-between py-1 border-bottom">
            <span class="small">${t.description}</span>
            <div class="d-flex gap-1">
                <button type="button" class="btn btn-xs btn-sm btn-outline-primary py-0 px-2"
                        onclick="addCarryOver(${JSON.stringify(t.description)}, ${JSON.stringify(t.priority)})">+ Pakai</button>
                <button type="button" class="btn btn-xs btn-sm btn-outline-danger py-0 px-2"
                        onclick="deleteTemplate(${t.id}, this)"><i class="bi bi-trash"></i></button>
            </div>
        </div>`).join('');
}

async function saveTemplate() {
    const desc = document.getElementById('tplDesc').value.trim();
    const prio = document.getElementById('tplPriority').value;
    if (!desc) return;
    await fetch('{{ route("templates.store") }}', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN': csrf},
        body: JSON.stringify({description: desc, priority: prio})
    });
    document.getElementById('tplDesc').value = '';
    loadTemplates();
}

async function deleteTemplate(id, btn) {
    btn.disabled = true;
    await fetch(`/templates/${id}`, {method:'DELETE', headers:{'X-CSRF-TOKEN':csrf}});
    loadTemplates();
}

document.getElementById('modalTemplate')?.addEventListener('show.bs.modal', loadTemplates);
</script>
@endpush
