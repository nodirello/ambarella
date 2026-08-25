<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Achievement extends Model
{
    protected $fillable = [
        'code', 'title', 'description', 'icon', 'reward', 'is_active',
    ];

    protected $casts = [
        'reward' => 'integer',
        'is_active' => 'boolean',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_achievements')->withPivot('achieved_at')->withTimestamps();
    }
}
