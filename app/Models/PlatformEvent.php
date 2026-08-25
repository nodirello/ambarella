<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class PlatformEvent extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'venue', 'city', 'starts_at', 'ends_at',
        'capacity', 'price', 'cover_image', 'is_published', 'created_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'capacity' => 'integer',
        'price' => 'integer',
        'is_published' => 'boolean',
    ];

    public static function booted(): void
    {
        static::creating(function (PlatformEvent $event) {
            $event->slug ??= Str::slug($event->title).'-'.Str::lower(Str::random(6));
        });
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function myRegistration(int $userId): HasOne
    {
        return $this->registrations()->one()->where('user_id', $userId);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('is_published', true)->where('starts_at', '>=', now());
    }
}
