<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mentorship extends Model
{
    protected $fillable = [
        'mentor_id', 'student_id', 'status', 'goal', 'started_at', 'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(MentorTask::class);
    }

    public function involvedUserIds(): array
    {
        return [
            $this->student_id,
            $this->mentor?->user_id,
        ];
    }
}
