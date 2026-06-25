<?php

namespace App\Console\Commands;

use App\Mail\ReportReminderMail;
use App\Models\DailyPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

#[Signature('reminder:report')]
#[Description('Kirim email reminder report sore ke karyawan yang belum mengisi')]
class SendReportReminder extends Command
{
    public function handle(): void
    {
        $today = Carbon::today('Asia/Jakarta');

        if ($today->isWeekend()) {
            $this->info('Hari libur, tidak ada reminder.');
            return;
        }

        $karyawans = User::where('role', 'karyawan')
            ->where('is_active', true)
            ->get();

        $alreadyFilled = DailyPlan::whereIn('user_id', $karyawans->pluck('id'))
            ->whereDate('plan_date', $today)
            ->whereNotNull('report_submitted_at')
            ->pluck('user_id');

        // Hanya kirim ke yang sudah isi plan tapi belum isi report
        $sudahPlan = DailyPlan::whereIn('user_id', $karyawans->pluck('id'))
            ->whereDate('plan_date', $today)
            ->whereNotNull('plan_submitted_at')
            ->pluck('user_id');

        $belumReport = $karyawans
            ->whereIn('id', $sudahPlan)
            ->whereNotIn('id', $alreadyFilled);

        foreach ($belumReport as $user) {
            Mail::to($user->email)->queue(new ReportReminderMail($user, $today));
            $this->line("Reminder terkirim ke: {$user->email}");
        }

        $this->info("Total: {$belumReport->count()} reminder report sore terkirim.");
    }
}
