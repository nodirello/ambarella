<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChallengeParticipant extends Model
{
    protected $fillable = [
        'challenge_id', 'user_id', 'status', 'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];
}
