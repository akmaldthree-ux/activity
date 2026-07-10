<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\DailyPlan;
use App\Models\Division;
use App\Models\Feedback;
use App\Models\Holiday;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function kinerja(Request $request)
    {
        $manager    = Auth::user();

        $year  = $request->integer('year', now()->year);
        $month = $request->integer('month', now()->month);

        $start = Carbon::create($year, $month, 1, 0, 0, 0, 'Asia/Jakarta');
        $end   = $start->copy()->endOfMonth();

        $karyawans = $this->getSubordinates($manager)->with('division')->orderBy('name')->get();

        $workdays = $this->countWorkdays($start, $end);

        $rekap = $karyawans->map(function ($user) use ($start, $end, $workdays) {
            $plans = DailyPlan::where('user_id', $user->id)
                ->whereBetween('plan_date', [$start->toDateString(), $end->toDateString()])
                ->with('feedback')
                ->get();

            $planCount   = $plans->whereNotNull('plan_submitted_at')->count();
            $reportCount = $plans->whereNotNull('report_submitted_at')->count();

            $ratings = $plans->pluck('feedback.rating')->filter();
            $avgRating = $ratings->count() ? round($ratings->avg(), 1) : null;

            return [
                'user'        => $user,
                'plan_pct'    => $workdays > 0 ? round($planCount / $workdays * 100) : 0,
                'report_pct'  => $workdays > 0 ? round($reportCount / $workdays * 100) : 0,
                'plan_count'  => $planCount,
                'report_count'=> $reportCount,
                'workdays'    => $workdays,
                'avg_rating'  => $avgRating,
            ];
        });

        $months = collect(range(1, 12))->mapWithKeys(fn($m) => [$m => Carbon::create($year, $m)->translatedFormat('F')]);

        return view('manager.kinerja', compact('rekap', 'year', 'month', 'months'));
    }

    public function leaderboard(Request $request)
    {
        $manager    = Auth::user();
        $divisionId = ($manager->isAdmin() || $manager->isDireksi()) ? null : $manager->division_id;

        $year  = $request->integer('year', now()->year);
        $month = $request->integer('month', now()->month);

        $start = Carbon::create($year, $month, 1, 0, 0, 0, 'Asia/Jakarta');
        $end   = $start->copy()->endOfMonth();
        $workdays = $this->countWorkdays($start, $end);

        $divisions = Division::withCount(['users as karyawan_count' => fn($q) => $q->whereIn('role', User::MONITORED_ROLES)->where('is_active', true)])
            ->when($divisionId, fn($q) => $q->where('id', $divisionId))
            ->having('karyawan_count', '>', 0)
            ->get()
            ->map(function ($div) use ($start, $end, $workdays) {
                $ids = User::where('division_id', $div->id)->whereIn('role', User::MONITORED_ROLES)->where('is_active', true)->pluck('id');
                $total = $ids->count();

                $plans   = DailyPlan::whereIn('user_id', $ids)->whereBetween('plan_date', [$start->toDateString(), $end->toDateString()]);
                $planOk  = (clone $plans)->whereNotNull('plan_submitted_at')->count();
                $repOk   = (clone $plans)->whereNotNull('report_submitted_at')->count();

                $maxScore = $total * $workdays * 2; // plan + report each count
                $score    = $planOk + $repOk;
                $pct      = $maxScore > 0 ? round($score / $maxScore * 100) : 0;

                return [
                    'division'    => $div,
                    'total'       => $total,
                    'plan_ok'     => $planOk,
                    'report_ok'   => $repOk,
                    'score_pct'   => $pct,
                ];
            })->sortByDesc('score_pct')->values();

        $months = collect(range(1, 12))->mapWithKeys(fn($m) => [$m => Carbon::create($year, $m)->translatedFormat('F')]);

        return view('manager.leaderboard', compact('divisions', 'year', 'month', 'months'));
    }

    public function weekly(Request $request)
    {
        $manager    = Auth::user();

        // Default to current week
        $weekInput = $request->input('week', now()->format('Y-\WW'));
        // Parse: "2026-W27" format
        $parts    = explode('-W', $weekInput);
        $weekYear = (int) ($parts[0] ?? now()->year);
        $weekNum  = (int) ($parts[1] ?? now()->isoWeek());

        $weekStart = Carbon::now()->setISODate($weekYear, $weekNum)->startOfDay()->timezone('Asia/Jakarta');
        $weekEnd   = $weekStart->copy()->endOfWeek(Carbon::SATURDAY); // Mon-Sat

        // All weekdays in range
        $weekDays = [];
        $current = $weekStart->copy();
        while ($current->lte($weekEnd)) {
            if (!$current->isSunday()) {
                $weekDays[] = $current->copy();
            }
            $current->addDay();
        }

        $allHolidays = Holiday::whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->get()
            ->keyBy(fn($h) => $h->date->format('Y-m-d'))
            ->map(fn($h) => $h->name)
            ->all();

        $karyawans = $this->getSubordinates($manager)->with('division')->orderBy('name')->get();

        $workdays = count(array_filter($weekDays, fn($d) => !isset($allHolidays[$d->format('Y-m-d')])));

        $rekap = $karyawans->map(function ($user) use ($weekStart, $weekEnd, $weekDays, $allHolidays, $workdays) {
            $plans = DailyPlan::where('user_id', $user->id)
                ->whereBetween('plan_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                ->get()
                ->keyBy(fn($p) => $p->plan_date->format('Y-m-d'));

            $planCount   = $plans->whereNotNull('plan_submitted_at')->count();
            $reportCount = $plans->whereNotNull('report_submitted_at')->count();

            return [
                'user'        => $user,
                'plans'       => $plans,
                'holidays'    => $allHolidays,
                'plan_pct'    => $workdays > 0 ? round($planCount / $workdays * 100) : 0,
                'report_pct'  => $workdays > 0 ? round($reportCount / $workdays * 100) : 0,
            ];
        });

        $totalKaryawan = $karyawans->count();

        return view('manager.weekly-report', compact(
            'rekap', 'weekStart', 'weekEnd', 'weekDays', 'weekInput', 'totalKaryawan'
        ));
    }

    private function getSubordinates(User $manager): \Illuminate\Database\Eloquent\Builder
    {
        if ($manager->isAdmin() || $manager->isDireksi()) {
            return User::whereIn('role', User::MONITORED_ROLES)->where('is_active', true);
        }
        return User::where('reports_to', $manager->id)->where('is_active', true);
    }

    private function countWorkdays(Carbon $start, Carbon $end): int
    {
        $count   = 0;
        $current = $start->copy();
        while ($current->lte($end)) {
            if (!$current->isSunday() && !Holiday::isHoliday($current->toDateString())) {
                $count++;
            }
            $current->addDay();
        }
        return $count;
    }
}
