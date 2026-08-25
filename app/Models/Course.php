<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CourseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'description', 'instructor', 'instructor_role', 'duration',
        'price', 'level', 'rating', 'category', 'tags', 'cover_image',
        'is_published', 'is_featured',
    ];

    protected $casts = [
        'tags' => 'array',
        'price' => 'integer',
        'rating' => 'float',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public static function booted(): void
    {
        static::creating(function (Course $course) {
            $course->slug ??= Str::slug($course->title).'-'.Str::lower(Str::random(6));
        });
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot(['progress', 'completed_at', 'certificate_code'])->withTimestamps();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function isFree(): bool
    {
        return $this->price === 0;
    }

    protected static function newFactory(): CourseFactory
    {
        return CourseFactory::new();
    }
}
