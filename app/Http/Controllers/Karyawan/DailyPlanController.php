<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\DailyPlan;
use App\Models\Goal;
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

        return view('karyawan.kalender', compact(
            'year', 'month', 'firstDay', 'lastDay', 'plans',
            'prevMonth', 'nextMonth', 'now'
        ));
    }

    public function show(Request $request, string $date)
    {
        $user      = Auth::user();
        $parsedDate = Carbon::createFromFormat('Y-m-d', $date, 'Asia/Jakarta');

        // Skip weekend
        if ($parsedDate->isWeekend()) {
            return redirect()->route('karyawan.kalender')->with('error', 'Hari libur tidak ada catatan.');
        }

        $plan = DailyPlan::with(['goals', 'activities', 'feedback.manager'])
            ->firstOrNew(
                ['user_id' => $user->id, 'plan_date' => $date],
                ['user_id' => $user->id, 'plan_date' => $date]
            );

        $isToday     = $parsedDate->isToday();
        $now         = Carbon::now('Asia/Jakarta');
        $isPlanLocked   = !$isToday || ($plan->plan_submitted_at !== null && !$isToday);
        $isReportLocked = !$isToday;

        // Plan bisa diedit hari ini & belum disubmit, atau sudah submit tapi hari ini
        $canEditPlan   = $isToday;
        $canEditReport = $isToday && $plan->plan_submitted_at !== null;

        $planDeadlinePassed   = $now->hour >= DailyPlan::PLAN_DEADLINE_HOUR;
        $reportDeadlinePassed = $now->hour >= DailyPlan::REPORT_DEADLINE_HOUR;

        return view('karyawan.daily', compact(
            'plan', 'parsedDate', 'isToday',
            'canEditPlan', 'canEditReport',
            'planDeadlinePassed', 'reportDeadlinePassed',
        ));
    }

    public function storePlan(Request $request, string $date)
    {
        $user = Auth::user();
        $parsedDate = Carbon::createFromFormat('Y-m-d', $date, 'Asia/Jakarta');

        if (!$parsedDate->isToday()) {
            return back()->with('error', 'Plan hanya bisa disimpan untuk hari ini.');
        }

        $request->validate([
            'goals'              => ['required', 'array', 'min:1'],
            'goals.*.description' => ['required', 'string', 'max:500'],
            'goals.*.target'     => ['nullable', 'string', 'max:255'],
            'activities'         => ['required', 'array', 'min:1'],
            'activities.*.description' => ['required', 'string', 'max:500'],
            'activities.*.priority'    => ['required', 'in:tinggi,sedang,rendah'],
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
            'activities'            => ['required', 'array'],
            'activities.*.id'       => ['required', 'integer'],
            'activities.*.status'   => ['required', 'in:selesai,sebagian,tidak'],
            'activities.*.realisasi' => ['nullable', 'string', 'max:500'],
            'activities.*.keterangan' => ['nullable', 'string', 'max:500'],
            'insight'               => ['nullable', 'string', 'max:1000'],
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

            $plan->insight              = $request->insight;
            $plan->report_submitted_at  = Carbon::now('Asia/Jakarta');
            $plan->save();
        });

        return redirect()->route('karyawan.daily', $date)->with('success', 'Report sore berhasil disimpan!');
    }
}
