<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['user_id', 'plan_date', 'plan_submitted_at', 'report_submitted_at', 'insight'])]
class DailyPlan extends Model
{
    // Deadline plan pagi & report sore (WIB)
    public const PLAN_DEADLINE_HOUR = 9;   // 09:00
    public const REPORT_DEADLINE_HOUR = 20; // 20:00

    protected function casts(): array
    {
        return [
            'plan_date' => 'date',
            'plan_submitted_at' => 'datetime',
            'report_submitted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(Feedback::class);
    }

    // Status kalender: belum_isi | plan_telat | plan_ok | laporan_telat | lengkap
    public function getCalendarStatus(): string
    {
        $today = Carbon::today('Asia/Jakarta');
        $date = Carbon::instance($this->plan_date)->startOfDay();
        $isPast = $date->lt($today);

        if (!$this->plan_submitted_at) {
            if ($isPast || ($date->isToday() && Carbon::now('Asia/Jakarta')->hour >= self::PLAN_DEADLINE_HOUR)) {
                return 'plan_telat';
            }
            return 'belum_isi';
        }

        $planLate = Carbon::instance($this->plan_submitted_at)->setTimezone('Asia/Jakarta')->hour >= self::PLAN_DEADLINE_HOUR;

        if (!$this->report_submitted_at) {
            // Hari ini sebelum deadline sore = ok; lewat deadline atau hari lampau = laporan_telat
            if ($isPast || ($date->isToday() && Carbon::now('Asia/Jakarta')->hour >= self::REPORT_DEADLINE_HOUR)) {
                return 'laporan_telat';
            }
            return 'plan_ok';
        }

        $reportLate = Carbon::instance($this->report_submitted_at)->setTimezone('Asia/Jakarta')->hour >= self::REPORT_DEADLINE_HOUR;

        if ($planLate || $reportLate) {
            return 'laporan_telat'; // submitted tapi telat
        }

        return 'lengkap';
    }

    // Apakah plan sudah boleh diedit (hari ini & belum ganti hari)
    public function isPlanLocked(): bool
    {
        return !Carbon::instance($this->plan_date)->isToday();
    }

    // Apakah report sudah boleh diedit
    public function isReportLocked(): bool
    {
        return $this->isPlanLocked();
    }
}
