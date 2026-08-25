<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BusinessProfile extends Model
{
    protected $fillable = [
        'user_id', 'company_name', 'slug', 'description', 'industry', 'website',
        'logo_path', 'phone', 'address', 'city', 'verification_status', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function booted(): void
    {
        static::creating(function (BusinessProfile $profile) {
            $profile->slug ??= Str::slug($profile->company_name).'-'.Str::lower(Str::random(6));
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(BusinessProduct::class);
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified' && $this->is_active;
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('verification_status', 'verified')->where('is_active', true);
    }
}
