<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\JobType;
use Database\Factories\JobFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;

    protected $table = 'job_listings';

    protected $fillable = [
        'user_id', 'business_profile_id', 'title', 'company', 'location', 'type',
        'salary_min', 'salary_max', 'currency', 'tags', 'description', 'requirements',
        'benefits', 'contact_email', 'deadline', 'views_count', 'is_active', 'is_featured',
    ];

    protected $casts = [
        'tags' => 'array',
        'deadline' => 'date',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
        'salary_min' => 'integer',
        'salary_max' => 'integer',
        'type' => JobType::class,
    ];

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function businessProfile(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'job_listing_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, fn (Builder $q, $term) => $q
                ->where(fn (Builder $w) => $w
                    ->where('title', 'like', "%{$term}%")
                    ->orWhere('company', 'like', "%{$term}%")
                    ->orWhere('location', 'like', "%{$term}%")))
            ->when($filters['type'] ?? null, fn (Builder $q, $type) => $q->where('type', $type))
            ->when($filters['city'] ?? null, fn (Builder $q, $city) => $q->where('location', 'like', "%{$city}%"))
            ->when($filters['tag'] ?? null, fn (Builder $q, $tag) => $q->whereJsonContains('tags', $tag))
            ->when($filters['min_salary'] ?? null, fn (Builder $q, $salary) => $q->where('salary_max', '>=', $salary));
    }

    public function salaryRange(): string
    {
        if (! $this->salary_min && ! $this->salary_max) {
            return 'Kelishilgan';
        }

        return number_format((int) $this->salary_min).' – '.number_format((int) $this->salary_max).' '.$this->currency;
    }

    protected static function newFactory(): JobFactory
    {
        return JobFactory::new();
    }
}
