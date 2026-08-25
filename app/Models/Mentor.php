<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mentor extends Model
{
    protected $fillable = [
        'user_id', 'name', 'role', 'company', 'experience_years',
        'skills', 'rating', 'bio', 'avatar', 'hourly_rate', 'is_available',
    ];

    protected $casts = [
        'skills' => 'array',
        'experience_years' => 'integer',
        'rating' => 'float',
        'hourly_rate' => 'integer',
        'is_available' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(MentorRequest::class);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_available', true);
    }
}
