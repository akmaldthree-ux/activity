<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\DailyPlan;
use App\Models\Goal;
use App\Models\Holiday;
use App\Models\ReportAttachment;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DailyPlanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $now  = Carbon::now('Asia/Jakarta');

        $year  = (int) $request->get('year', $now->year);
        $month = (int) $request->get('month', $now->month);

        $firstDay = Carbon::create($year, $month, 1, 0, 0, 0, 'Asia/Jakarta');
        $lastDay  = $firstDay->copy()->endOfMonth();

        // Ambil semua plan bulan ini milik user
        $plans = DailyPlan::where('user_id', $user->id)
            ->whereBetween('plan_date', [$firstDay->toDateString(), $lastDay->toDateString()])
            ->get()
            ->keyBy(fn($p) => $p->plan_date->format('Y-m-d'));

        // Navigasi bulan
        $prevMonth = $firstDay->copy()->subMonth();
        $nextMonth = $firstDay->copy()->addMonth();

        // Hari libur nasional bulan ini
        $holidays = Holiday::allDatesForMonth($year, $month);

        return view('karyawan.kalender', compact(
            'year', 'month', 'firstDay', 'lastDay', 'plans',
            'prevMonth', 'nextMonth', 'now', 'holidays'
        ));
    }

    public function show(Request $request, string $date)
    {
        $user      = Auth::user();
        $parsedDate = Carbon::createFromFormat('Y-m-d', $date, 'Asia/Jakarta');

        // Skip weekend & holiday
        if ($parsedDate->isWeekend() || Holiday::isHoliday($date)) {
            return redirect()->route('karyawan.kalender')->with('error', 'Hari libur tidak ada catatan.');
        }

        $plan = DailyPlan::with(['goals', 'activities', 'feedback.manager', 'feedback.replies.user', 'attachments'])
            ->firstOrNew(
                ['user_id' => $user->id, 'plan_date' => $date],
                ['user_id' => $user->id, 'plan_date' => $date]
            );

        $isToday     = $parsedDate->isToday();
        $now         = Carbon::now('Asia/Jakarta');

        // Plan bisa diedit hari ini & belum disubmit, atau sudah submit tapi hari ini
        $canEditPlan   = $isToday;
        $canEditReport = $isToday && $plan->plan_submitted_at !== null;

        $planDeadlinePassed   = $now->hour >= DailyPlan::PLAN_DEADLINE_HOUR;
        $reportDeadlinePassed = $now->hour >= DailyPlan::REPORT_DEADLINE_HOUR;

        // Carry over: aktivitas belum selesai dari hari kerja sebelumnya (jika hari ini & plan belum diisi)
        $carryOverActivities = collect();
        if ($isToday && !$plan->plan_submitted_at) {
            $prevWorkDay = $parsedDate->copy()->subDay();
            while ($prevWorkDay->isWeekend() || Holiday::isHoliday($prevWorkDay->toDateString())) {
                $prevWorkDay->subDay();
            }
            $prevPlan = DailyPlan::where('user_id', $user->id)
                ->where('plan_date', $prevWorkDay->toDateString())
                ->first();
            if ($prevPlan) {
                $carryOverActivities = $prevPlan->activities()
                    ->whereIn('status', ['sebagian', 'tidak'])
                    ->get();
            }
        }

        return view('karyawan.daily', compact(
            'plan', 'parsedDate', 'isToday',
            'canEditPlan', 'canEditReport',
            'planDeadlinePassed', 'reportDeadlinePassed',
            'carryOverActivities',
        ));
    }

    public function storePlan(Request $request, string $date)
    {
        $user = Auth::user();
        $parsedDate = Carbon::createFromFormat('Y-m-d', $date, 'Asia/Jakarta');

        if (!$parsedDate->isToday() || $parsedDate->isWeekend() || Holiday::isHoliday($date)) {
            return back()->with('error', 'Plan hanya bisa disimpan untuk hari kerja hari ini.');
        }

        $request->validate([
            'goals'              => ['required', 'array', 'min:1'],
            'goals.*.description' => ['required', 'string', 'max:500'],
            'goals.*.target'     => ['nullable', 'string', 'max:255'],
            'activities'               => ['required', 'array', 'min:1'],
            'activities.*.description' => ['required', 'string', 'max:500'],
            'activities.*.priority'    => ['required', 'in:tinggi,sedang,rendah'],
            'activities.*.tag'         => ['nullable', 'string', 'max:50'],
        ]);

        DB::transaction(function () use ($request, $user, $date) {
            $plan = DailyPlan::firstOrCreate(
                ['user_id' => $user->id, 'plan_date' => $date],
                ['user_id' => $user->id, 'plan_date' => $date]
            );

            $plan->plan_submitted_at = Carbon::now('Asia/Jakarta');
            $plan->save();

            // Hapus & recreate goals
            $plan->goals()->delete();
            foreach ($request->goals as $g) {
                $plan->goals()->create([
                    'description' => $g['description'],
                    'target'      => $g['target'] ?? null,
                ]);
            }

            // Hapus & recreate activities (jaga report data jika sudah ada)
            $existingActivities = $plan->activities()->get()->keyBy('id');
            $plan->activities()->delete();
            foreach ($request->activities as $a) {
                $plan->activities()->create([
                    'description' => $a['description'],
                    'priority'    => $a['priority'],
                    'tag'         => $a['tag'] ?? null,
                ]);
            }
        });

        return redirect()->route('karyawan.daily', $date)->with('success', 'Plan pagi berhasil disimpan!');
    }

    public function storeReport(Request $request, string $date)
    {
        $user = Auth::user();
        $parsedDate = Carbon::createFromFormat('Y-m-d', $date, 'Asia/Jakarta');

        if (!$parsedDate->isToday()) {
            return back()->with('error', 'Report hanya bisa disimpan untuk hari ini.');
        }

        $plan = DailyPlan::where('user_id', $user->id)->where('plan_date', $date)->firstOrFail();

        if (!$plan->plan_submitted_at) {
            return back()->with('error', 'Isi plan pagi terlebih dahulu.');
        }

        $request->validate([
            'activities'               => ['required', 'array'],
            'activities.*.id'          => ['required', 'integer'],
            'activities.*.status'      => ['required', 'in:selesai,sebagian,tidak'],
            'activities.*.realisasi'   => ['nullable', 'string', 'max:500'],
            'activities.*.keterangan'  => ['nullable', 'string', 'max:500'],
            'insight'                  => ['nullable', 'string', 'max:1000'],
            'attachments'              => ['nullable', 'array', 'max:5'],
            'attachments.*'            => ['file', 'max:5120', 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx'],
        ]);

        DB::transaction(function () use ($request, $plan) {
            foreach ($request->activities as $a) {
                Activity::where('id', $a['id'])
                    ->where('daily_plan_id', $plan->id)
                    ->update([
                        'status'     => $a['status'],
                        'realisasi'  => $a['realisasi'] ?? null,
                        'keterangan' => $a['keterangan'] ?? null,
                    ]);
            }

            $plan->insight             = $request->insight;
            $plan->report_submitted_at = Carbon::now('Asia/Jakarta');
            $plan->save();

            // Simpan lampiran
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('report-attachments', 'public');
                    ReportAttachment::create([
                        'daily_plan_id' => $plan->id,
                        'path'          => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type'     => $file->getMimeType(),
                    ]);
                }
            }
        });

        return redirect()->route('karyawan.daily', $date)->with('success', 'Report sore berhasil disimpan!');
    }
}
