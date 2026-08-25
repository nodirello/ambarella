<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ForumTopic extends Model
{
    protected $fillable = [
        'user_id', 'title', 'slug', 'body', 'category',
        'views_count', 'is_pinned', 'is_locked', 'is_solved',
    ];

    protected $casts = [
        'views_count' => 'integer',
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'is_solved' => 'boolean',
    ];

    public static function booted(): void
    {
        static::creating(function (ForumTopic $topic) {
            $topic->slug ??= Str::slug($topic->title).'-'.Str::lower(Str::random(6));
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ForumReply::class)->latest();
    }

    public function bestReply(): HasMany
    {
        return $this->hasMany(ForumReply::class)->where('is_best', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('is_pinned')->latest();
    }
}
