<?php

declare(strict_types=1);

namespace App\Traits;

use App\Services\ActivityLogger;

trait LogsActivity
{
    protected function logActivity(string $action, string $description = '', array $metadata = [])
    {
        return app(ActivityLogger::class)->log(auth()->user(), $action, $description, $metadata);
    }
}
