<?php

namespace App\Http\Controllers;

use App\Models\DailyPlan;
use App\Models\Holiday;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $today = Carbon::today('Asia/Jakarta');

        // Karyawan tidak punya dashboard — langsung ke kalender
        if ($user->isKaryawan()) {
            return redirect()->route('karyawan.kalender');
        }

        // Admin/Direksi: semua karyawan; lainnya: direct reports saja
        if ($user->isAdmin() || $user->isDireksi()) {
            $usersQuery = User::where('role', 'karyawan')
                ->where('is_active', true)
                ->with('division');
        } else {
            $usersQuery = User::where('reports_to', $user->id)
                ->where('is_active', true)
                ->with('division');
        }

        $totalBawahan = $usersQuery->count();

        $sudahIsiBulan = DailyPlan::whereIn('user_id', $usersQuery->pluck('id'))
            ->where('plan_date', '>=', $today->copy()->startOfMonth())
            ->where('plan_date', '<=', $today->copy()->endOfMonth())
            ->whereNotNull('plan_submitted_at')
            ->count();

        $hariKerjaBulan = $this->countWorkdays($today->copy()->startOfMonth(), $today);

        $sudahIsiHariIni = DailyPlan::whereIn('user_id', $usersQuery->pluck('id'))
            ->whereDate('plan_date', $today)
            ->whereNotNull('plan_submitted_at')
            ->count();

        $belumIsiHariIni = $totalBawahan - $sudahIsiHariIni;

        // Rekap per divisi — hanya untuk admin & direksi (lintas divisi)
        $perDivisi = collect();
        if ($user->isAdmin() || $user->isDireksi()) {
            $scope = User::where('role', 'karyawan')->where('is_active', true)->with('division')->get();

            $perDivisi = $scope
                ->groupBy('division_id')
                ->map(function ($members) use ($today) {
                    $ids = $members->pluck('id');
                    return [
                        'divisi'  => $members->first()->division?->name ?? 'Tanpa Divisi',
                        'total'   => $members->count(),
                        'isi'     => DailyPlan::whereIn('user_id', $ids)->whereDate('plan_date', $today)->whereNotNull('plan_submitted_at')->count(),
                        'lengkap' => DailyPlan::whereIn('user_id', $ids)->whereDate('plan_date', $today)->whereNotNull('report_submitted_at')->count(),
                    ];
                })->values();
        }

        // Tren kepatuhan 4 minggu terakhir
        $trendData = $this->getComplianceTrend($usersQuery->pluck('id')->toArray());

        $totalKaryawan = $totalBawahan;

        return view('dashboard', compact(
            'totalKaryawan', 'sudahIsiHariIni', 'belumIsiHariIni',
            'sudahIsiBulan', 'hariKerjaBulan', 'perDivisi', 'today',
            'trendData'
        ));
    }

    private function countWorkdays(Carbon $start, Carbon $end): int
    {
        $count = 0;
        $current = $start->copy();
        while ($current->lte($end)) {
            if (!$current->isWeekend() && !Holiday::isHoliday($current->toDateString())) {
                $count++;
            }
            $current->addDay();
        }
        return $count;
    }

    private function getComplianceTrend(array $userIds): array
    {
        if (empty($userIds)) return ['labels' => [], 'plan' => [], 'report' => []];

        $end   = Carbon::today('Asia/Jakarta');
        $start = $end->copy()->subDays(27);
        $labels = $plan = $report = [];

        $current = $start->copy();
        while ($current->lte($end)) {
            if (!$current->isWeekend() && !Holiday::isHoliday($current->toDateString())) {
                $dateStr = $current->toDateString();
                $total = count($userIds);
                if ($total === 0) { $current->addDay(); continue; }

                $planCount   = DailyPlan::whereIn('user_id', $userIds)->whereDate('plan_date', $dateStr)->whereNotNull('plan_submitted_at')->count();
                $reportCount = DailyPlan::whereIn('user_id', $userIds)->whereDate('plan_date', $dateStr)->whereNotNull('report_submitted_at')->count();

                $labels[]  = $current->format('d/m');
                $plan[]    = round($planCount / $total * 100);
                $report[]  = round($reportCount / $total * 100);
            }
            $current->addDay();
        }

        return compact('labels', 'plan', 'report');
    }
}
