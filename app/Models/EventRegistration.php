<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EventRegistration extends Model
{
    protected $fillable = [
        'platform_event_id', 'user_id', 'status', 'ticket_code',
    ];

    public static function booted(): void
    {
        static::creating(function (EventRegistration $registration) {
            $registration->ticket_code ??= 'EVT-'.Str::upper(Str::random(10));
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(PlatformEvent::class, 'platform_event_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
