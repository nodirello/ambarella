<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogger
{
    public function __construct(private readonly Request $request)
    {
    }

    public function log(?User $user, string $action, string $description = '', array $metadata = []): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'description' => $description,
            'ip_address' => $this->request->ip(),
            'user_agent' => substr((string) $this->request->userAgent(), 0, 500),
            'metadata' => $metadata ?: null,
        ]);
    }

    public function logAuth(?User $user, string $description = '', array $metadata = []): ActivityLog
    {
        return $this->log($user, 'auth', $description, $metadata);
    }
}
