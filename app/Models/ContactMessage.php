<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name', 'email', 'subject', 'message', 'status',
    ];

    public function scopeNew(Builder $query): Builder
    {
        return $query->where('status', 'new');
    }
}
