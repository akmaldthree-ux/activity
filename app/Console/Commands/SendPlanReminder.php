<?php

namespace App\Console\Commands;

use App\Mail\PlanReminderMail;
use App\Models\DailyPlan;
use App\Models\Holiday;
use App\Models\User;
use App\Services\WhatsAppService;
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

        // Skip weekend & holiday
        if ($today->isWeekend() || Holiday::isHoliday($today->toDateString())) {
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

        $wa = app(WhatsAppService::class);

        foreach ($belumIsi as $user) {
            Mail::to($user->email)->queue(new PlanReminderMail($user, $today));
            $this->line("Email reminder: {$user->email}");

            if ($user->no_hp) {
                $msg = "Hai {$user->name}, jangan lupa isi *Plan Pagi* hari ini sebelum pukul 09:00 WIB.\n\nAkses: " . config('app.url') . '/kalender/' . $today->toDateString();
                $wa->send($user->no_hp, $msg);
                $this->line("WA reminder: {$user->no_hp}");
            }
        }

        $this->info("Total: {$belumIsi->count()} reminder plan pagi terkirim.");
    }
}
