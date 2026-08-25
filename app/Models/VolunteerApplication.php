<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolunteerApplication extends Model
{
    protected $fillable = [
        'volunteer_project_id', 'user_id', 'message', 'status', 'hours',
    ];

    protected $casts = [
        'hours' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(VolunteerProject::class, 'volunteer_project_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
