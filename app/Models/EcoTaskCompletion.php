<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ModerationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EcoTaskCompletion extends Model
{
    protected $fillable = [
        'eco_task_id', 'user_id', 'proof_text', 'photo_path',
        'status', 'completed_on', 'reviewed_by',
    ];

    protected $casts = [
        'completed_on' => 'date',
        'status' => ModerationStatus::class,
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(EcoTask::class, 'eco_task_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ModerationStatus::Pending);
    }
}
