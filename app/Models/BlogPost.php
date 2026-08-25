<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ModerationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    protected $fillable = [
        'user_id', 'title', 'slug', 'content', 'cover_image', 'category',
        'tags', 'status', 'views_count', 'is_featured', 'published_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'views_count' => 'integer',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'status' => ModerationStatus::class,
    ];

    public static function booted(): void
    {
        static::creating(function (BlogPost $post) {
            $post->slug ??= Str::slug($post->title).'-'.Str::lower(Str::random(6));
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', ModerationStatus::Approved);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ModerationStatus::Pending);
    }
}
