<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\DailyPlan;
use App\Models\Feedback;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringController extends Controller
{
    public function tim(Request $request)
    {
        $user = Auth::user();
        $divisionId = $user->isAdmin() ? null : $user->division_id;

        $now    = Carbon::now('Asia/Jakarta');
        $date   = $request->get('date', $now->toDateString());
        $parsed = Carbon::parse($date, 'Asia/Jakarta');

        $karyawans = User::where('role', 'karyawan')
            ->where('is_active', true)
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->with('division')
            ->orderBy('name')
            ->get();

        $karyawanIds = $karyawans->pluck('id');

        $plans = DailyPlan::with(['goals', 'activities', 'feedback'])
            ->whereIn('user_id', $karyawanIds)
            ->whereDate('plan_date', $parsed)
            ->get()
            ->keyBy('user_id');

        // Statistik ringkas
        $total    = $karyawans->count();
        $sudahPlan = $plans->whereNotNull('plan_submitted_at')->count();
        $lengkap   = $plans->whereNotNull('report_submitted_at')->count();

        return view('manager.tim', compact(
            'karyawans', 'plans', 'date', 'parsed',
            'total', 'sudahPlan', 'lengkap'
        ));
    }

    public function detail(User $user, string $date)
    {
        $manager = Auth::user();

        // Manager hanya bisa lihat divisinya sendiri
        if ($manager->isManager() && $user->division_id !== $manager->division_id) {
            abort(403);
        }

        $parsedDate = Carbon::createFromFormat('Y-m-d', $date, 'Asia/Jakarta');
        $plan = DailyPlan::with(['goals', 'activities', 'feedback.manager'])
            ->where('user_id', $user->id)
            ->where('plan_date', $date)
            ->firstOrFail();

        $feedback = $plan->feedback;

        return view('manager.detail', compact('user', 'plan', 'parsedDate', 'feedback'));
    }

    public function saveFeedback(Request $request, DailyPlan $dailyPlan)
    {
        $manager = Auth::user();

        // Pastikan karyawan dalam divisi manager
        if ($manager->isManager() && $dailyPlan->user->division_id !== $manager->division_id) {
            abort(403);
        }

        $request->validate([
            'comment' => ['nullable', 'string', 'max:1000'],
            'rating'  => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        Feedback::updateOrCreate(
            ['daily_plan_id' => $dailyPlan->id],
            [
                'manager_id' => $manager->id,
                'comment'    => $request->comment,
                'rating'     => $request->rating,
            ]
        );

        return back()->with('success', 'Feedback berhasil disimpan.');
    }
}
