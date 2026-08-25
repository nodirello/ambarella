<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Habit extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'frequency', 'days',
        'current_streak', 'best_streak', 'is_active',
    ];

    protected $casts = [
        'days' => 'array',
        'current_streak' => 'integer',
        'best_streak' => 'integer',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(HabitLog::class);
    }

    public function completedToday(): bool
    {
        return $this->logs()->whereDate('completed_on', today())->exists();
    }
}
