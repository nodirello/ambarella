<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Notification;

/**
 * Human-readable fan-out for in-app + email notifications.
 */
class PushBroadcastService
{
    /**
     * @param  class-string<\Illuminate\Notifications\Notification>  $notificationClass
     */
    public function toAllUsers(string $notificationClass, array $params = [], ?int $limit = null): int
    {
        $query = User::where('is_banned', false);

        if ($limit) {
            $query->limit($limit);
        }

        $count = 0;
        $query->chunkById(200, function ($users) use ($notificationClass, $params, &$count): void {
            foreach ($users as $user) {
                $user->notify(new $notificationClass(...$params));
                $count++;
            }
        });

        return $count;
    }
}
