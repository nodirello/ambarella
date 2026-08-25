<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TelegramUser extends Model
{
    protected $fillable = [
        'chat_id', 'user_id', 'first_name', 'last_name', 'username',
        'phone', 'locale', 'is_joined_channel', 'last_interaction_at',
    ];

    protected $casts = [
        'is_joined_channel' => 'boolean',
        'last_interaction_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(TelegramBotSubmission::class, 'user_id');
    }
}
