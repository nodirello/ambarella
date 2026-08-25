<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Startup extends Model
{
    protected $fillable = [
        'user_id', 'name', 'description', 'category', 'stage', 'team_size',
        'looking_for', 'website', 'votes_count', 'is_approved',
    ];

    protected $casts = [
        'team_size' => 'integer',
        'votes_count' => 'integer',
        'is_approved' => 'boolean',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(StartupVote::class);
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }
}
