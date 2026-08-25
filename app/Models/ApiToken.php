<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiToken extends Model
{
    protected $fillable = [
        'user_id', 'name', 'token_hash', 'abilities', 'last_used_at', 'expires_at',
    ];

    protected $casts = [
        'abilities' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isValid(): bool
    {
        return $this->expires_at === null || $this->expires_at->isFuture();
    }

    public function can(string $ability): bool
    {
        return in_array('*', $this->abilities ?? [], true)
            || in_array($ability, $this->abilities ?? [], true);
    }

    public static function hashToken(string $plainToken): string
    {
        return hash('sha256', $plainToken);
    }
}
