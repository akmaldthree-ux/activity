<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DivisionTarget extends Model
{
    protected $fillable = ['division_id', 'set_by', 'year', 'month', 'plan_target_pct', 'report_target_pct', 'note'];

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function setter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'set_by');
    }
}
