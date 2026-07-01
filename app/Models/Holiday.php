<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = ['date', 'name'];

    protected function casts(): array
    {
        return ['date' => 'date'];
    }

    public static function isHoliday(string $date): bool
    {
        return static::where('date', $date)->exists();
    }

    public static function allDatesForMonth(int $year, int $month): array
    {
        return static::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get()
            ->keyBy(fn($h) => $h->date->format('Y-m-d'))
            ->map(fn($h) => $h->name)
            ->all();
    }
}
