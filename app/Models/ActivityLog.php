<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'action', 'description', 'ip_address', 'user_agent', 'metadata',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeRecent(Builder $query, int $limit = 100): Builder
    {
        return $query->with('user')->latest('created_at')->limit($limit);
    }
}
