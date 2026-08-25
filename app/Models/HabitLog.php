<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HabitLog extends Model
{
    protected $fillable = [
        'habit_id', 'user_id', 'completed_on',
    ];

    protected $casts = [
        'completed_on' => 'date',
    ];

    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }
}
