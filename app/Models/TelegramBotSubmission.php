<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramBotSubmission extends Model
{
    protected $fillable = [
        'user_id', 'telegram_bot_task_id', 'type', 'payload_text', 'photo_path',
        'status', 'telegram_chat_id', 'telegram_message_id', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'telegram_message_id' => 'integer',
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(TelegramBotTask::class, 'telegram_bot_task_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
