<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    protected $fillable = [
        'job_listing_id', 'user_id', 'cover_letter', 'resume_path', 'status',
    ];

    protected $casts = [
        'status' => ApplicationStatus::class,
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_listing_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ApplicationStatus::Pending);
    }
}
