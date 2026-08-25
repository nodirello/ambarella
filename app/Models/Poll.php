<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    protected $fillable = [
        'title', 'description', 'allow_multiple', 'is_active', 'is_featured', 'ends_at',
    ];

    protected $casts = [
        'allow_multiple' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'ends_at' => 'datetime',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class)->orderBy('id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn (Builder $q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()));
    }

    public function userVote(int $userId): ?PollVote
    {
        return $this->votes()->where('user_id', $userId)->first();
    }
}
