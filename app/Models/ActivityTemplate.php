<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityTemplate extends Model
{
    protected $fillable = ['user_id', 'description', 'priority'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
