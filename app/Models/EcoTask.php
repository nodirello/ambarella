<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ModerationStatus;
use Database\Factories\EcoTaskFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EcoTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'reward', 'category', 'type', 'requires_proof', 'is_active',
    ];

    protected $casts = [
        'reward' => 'integer',
        'requires_proof' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function completions(): HasMany
    {
        return $this->hasMany(EcoTaskCompletion::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    protected static function newFactory(): EcoTaskFactory
    {
        return EcoTaskFactory::new();
    }
}
