<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\DailyPlan;
use App\Models\Feedback;
use App\Models\FeedbackReply;
use App\Models\InAppNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringController extends Controller
{
    public function tim(Request $request)
    {
        $user = Auth::user();

        $now    = Carbon::now('Asia/Jakarta');
        $date   = $request->get('date', $now->toDateString());
        $parsed = Carbon::parse($date, 'Asia/Jakarta');

        // Admin/Direksi: semua karyawan; lainnya: direct reports saja
        $subordinatesQuery = ($user->isAdmin() || $user->isDireksi())
            ? User::where('role', 'karyawan')->where('is_active', true)
            : User::where('reports_to', $user->id)->where('is_active', true);

        $subordinates = $subordinatesQuery->with('division')->orderBy('name')->get();
        $subordinateIds = $subordinates->pluck('id');

        $plans = DailyPlan::with(['goals', 'activities', 'feedback'])
            ->whereIn('user_id', $subordinateIds)
            ->whereDate('plan_date', $parsed)
            ->get()
            ->keyBy('user_id');

        $total     = $subordinates->count();
        $sudahPlan = $plans->whereNotNull('plan_submitted_at')->count();
        $lengkap   = $plans->whereNotNull('report_submitted_at')->count();

        // Alias untuk view (view masih pakai $karyawans)
        $karyawans = $subordinates;

        return view('manager.tim', compact(
            'karyawans', 'plans', 'date', 'parsed',
            'total', 'sudahPlan', 'lengkap'
        ));
    }

    public function detail(User $user, string $date)
    {
        $supervisor = Auth::user();

        // Otorisasi: admin/direksi bisa lihat siapapun; lainnya hanya direct reports
        if (!$supervisor->isAdmin() && !$supervisor->isDireksi() && (int) $user->reports_to !== $supervisor->id) {
            abort(403);
        }

        $parsedDate = Carbon::createFromFormat('Y-m-d', $date, 'Asia/Jakarta');
        $plan = DailyPlan::with(['goals', 'activities', 'feedback.manager', 'feedback.replies.user'])
            ->where('user_id', $user->id)
            ->where('plan_date', $date)
            ->firstOrFail();

        $feedback = $plan->feedback;

        return view('manager.detail', compact('user', 'plan', 'parsedDate', 'feedback'));
    }

    public function saveFeedback(Request $request, DailyPlan $dailyPlan)
    {
        $supervisor = Auth::user();

        // Otorisasi: admin/direksi bisa beri feedback ke siapapun; lainnya hanya direct reports
        if (!$supervisor->isAdmin() && !$supervisor->isDireksi() && (int) $dailyPlan->user->reports_to !== $supervisor->id) {
            abort(403);
        }

        $request->validate([
            'comment' => ['nullable', 'string', 'max:1000'],
            'rating'  => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        Feedback::updateOrCreate(
            ['daily_plan_id' => $dailyPlan->id],
            [
                'manager_id' => $supervisor->id,
                'comment'    => $request->comment,
                'rating'     => $request->rating,
            ]
        );

        InAppNotification::create([
            'user_id' => $dailyPlan->user_id,
            'type'    => 'feedback',
            'title'   => 'Feedback baru dari ' . $supervisor->name,
            'body'    => $request->comment ? \Illuminate\Support\Str::limit($request->comment, 80) : null,
            'url'     => route('karyawan.daily', $dailyPlan->plan_date->format('Y-m-d')),
        ]);

        return back()->with('success', 'Feedback berhasil disimpan.');
    }

    public function replyFeedback(Request $request, Feedback $feedback)
    {
        $request->validate(['body' => ['required', 'string', 'max:1000']]);

        FeedbackReply::create([
            'feedback_id' => $feedback->id,
            'user_id'     => Auth::id(),
            'body'        => $request->body,
        ]);

        return back()->with('success', 'Balasan berhasil dikirim.');
    }
}
