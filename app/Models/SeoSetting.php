<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SeoSetting extends Model
{
    protected $fillable = [
        'page', 'title', 'description', 'keywords', 'og_image',
    ];

    protected $casts = [
        'keywords' => 'array',
    ];

    public static function forPage(string $page): ?self
    {
        return Cache::remember("seo:{$page}", 3600, fn () => self::where('page', $page)->first());
    }
}
