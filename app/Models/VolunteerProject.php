<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VolunteerProject extends Model
{
    protected $fillable = [
        'title', 'description', 'organization', 'city', 'starts_at', 'ends_at',
        'hours_expected', 'capacity', 'is_active', 'created_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'hours_expected' => 'integer',
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(VolunteerApplication::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
