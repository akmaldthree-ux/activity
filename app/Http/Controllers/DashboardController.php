<?php

namespace App\Http\Controllers;

use App\Models\DailyPlan;
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

        if ($user->isKaryawan()) {
            return redirect()->route('karyawan.kalender');
        }

        // Manager / Admin: dashboard monitoring
        $divisionId = $user->isAdmin() ? null : $user->division_id;

        $usersQuery = User::where('role', 'karyawan')
            ->where('is_active', true)
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->with('division');

        $totalKaryawan = $usersQuery->count();

        $sudahIsiBulan = DailyPlan::whereIn('user_id', $usersQuery->pluck('id'))
            ->where('plan_date', '>=', $today->copy()->startOfMonth())
            ->where('plan_date', '<=', $today->copy()->endOfMonth())
            ->count();

        $hariKerjaBulan = $this->countWorkdays($today->copy()->startOfMonth(), $today);

        $sudahIsiHariIni = DailyPlan::whereIn('user_id', $usersQuery->pluck('id'))
            ->whereDate('plan_date', $today)
            ->whereNotNull('plan_submitted_at')
            ->count();

        $belumIsiHariIni = $totalKaryawan - $sudahIsiHariIni;

        // Per divisi (admin only)
        $perDivisi = [];
        if ($user->isAdmin()) {
            $perDivisi = User::where('role', 'karyawan')
                ->where('is_active', true)
                ->with('division')
                ->get()
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

        return view('dashboard', compact(
            'totalKaryawan', 'sudahIsiHariIni', 'belumIsiHariIni',
            'sudahIsiBulan', 'hariKerjaBulan', 'perDivisi', 'today'
        ));
    }

    private function countWorkdays(Carbon $start, Carbon $end): int
    {
        $count = 0;
        $current = $start->copy();
        while ($current->lte($end)) {
            if (!$current->isWeekend()) {
                $count++;
            }
            $current->addDay();
        }
        return $count;
    }
}
