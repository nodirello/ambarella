<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TelegramBotTask extends Model
{
    protected $fillable = [
        'title', 'description', 'reward', 'type', 'channel_url', 'is_active',
    ];

    protected $casts = [
        'reward' => 'integer',
        'is_active' => 'boolean',
    ];

    public function submissions(): HasMany
    {
        return $this->hasMany(TelegramBotSubmission::class, 'telegram_bot_task_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
