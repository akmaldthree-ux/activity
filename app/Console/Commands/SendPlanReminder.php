<?php

namespace App\Console\Commands;

use App\Mail\PlanReminderMail;
use App\Models\DailyPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

#[Signature('reminder:plan')]
#[Description('Kirim email reminder plan pagi ke karyawan yang belum mengisi')]
class SendPlanReminder extends Command
{
    public function handle(): void
    {
        $today = Carbon::today('Asia/Jakarta');

        // Skip weekend
        if ($today->isWeekend()) {
            $this->info('Hari libur, tidak ada reminder.');
            return;
        }

        $karyawans = User::where('role', 'karyawan')
            ->where('is_active', true)
            ->get();

        $alreadyFilled = DailyPlan::whereIn('user_id', $karyawans->pluck('id'))
            ->whereDate('plan_date', $today)
            ->whereNotNull('plan_submitted_at')
            ->pluck('user_id');

        $belumIsi = $karyawans->whereNotIn('id', $alreadyFilled);

        foreach ($belumIsi as $user) {
            Mail::to($user->email)->queue(new PlanReminderMail($user, $today));
            $this->line("Reminder terkirim ke: {$user->email}");
        }

        $this->info("Total: {$belumIsi->count()} reminder plan pagi terkirim.");
    }
}
