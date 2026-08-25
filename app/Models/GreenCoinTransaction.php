<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CoinTransactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class GreenCoinTransaction extends Model
{
    protected $table = 'greencoin_transactions';

    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'amount', 'type', 'description',
        'reference_type', 'reference_id', 'balance_after',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'type' => CoinTransactionType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function isCredit(): bool
    {
        return $this->type->isCredit();
    }
}
