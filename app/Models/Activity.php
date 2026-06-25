<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['daily_plan_id', 'description', 'priority', 'status', 'realisasi', 'keterangan'])]
class Activity extends Model
{
    public const PRIORITY_TINGGI = 'tinggi';
    public const PRIORITY_SEDANG = 'sedang';
    public const PRIORITY_RENDAH = 'rendah';

    public const STATUS_SELESAI  = 'selesai';
    public const STATUS_SEBAGIAN = 'sebagian';
    public const STATUS_TIDAK    = 'tidak';

    public function dailyPlan(): BelongsTo
    {
        return $this->belongsTo(DailyPlan::class);
    }

    public function priorityLabel(): string
    {
        return match ($this->priority) {
            self::PRIORITY_TINGGI => 'Tinggi',
            self::PRIORITY_SEDANG => 'Sedang',
            self::PRIORITY_RENDAH => 'Rendah',
            default => '-',
        };
    }

    public function priorityBadgeClass(): string
    {
        return match ($this->priority) {
            self::PRIORITY_TINGGI => 'bg-danger',
            self::PRIORITY_SEDANG => 'bg-warning text-dark',
            self::PRIORITY_RENDAH => 'bg-success',
            default => 'bg-secondary',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_SELESAI  => 'Selesai',
            self::STATUS_SEBAGIAN => 'Sebagian',
            self::STATUS_TIDAK    => 'Tidak',
            default => '-',
        };
    }
}
