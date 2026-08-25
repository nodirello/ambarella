<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorTask extends Model
{
    protected $fillable = [
        'mentorship_id', 'title', 'description', 'due_date', 'status', 'completed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function mentorship(): BelongsTo
    {
        return $this->belongsTo(Mentorship::class);
    }
}
