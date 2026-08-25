<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\NewsFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'content', 'image', 'category', 'author_name',
        'views_count', 'is_published', 'published_at',
    ];

    protected $casts = [
        'views_count' => 'integer',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public static function booted(): void
    {
        static::creating(function (News $news) {
            $news->slug ??= Str::slug($news->title).'-'.Str::lower(Str::random(6));
            $news->published_at ??= now();
        });
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    protected static function newFactory(): NewsFactory
    {
        return NewsFactory::new();
    }
}
