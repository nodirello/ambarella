<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LiveCountController extends Controller
{
    /**
     * Approximate number of active users in the last 2 minutes.
     */
    public function count(): JsonResponse
    {
        $online = Cache::remember('live.online', 60, function (): int {
            try {
                return (int) DB::table('sessions')
                    ->where('last_activity', '>', now()->subMinutes(2)->timestamp)
                    ->count();
            } catch (\Throwable) {
                return $this->countSessionFiles();
            }
        });

        return response()->json(['online' => $online]);
    }

    private function countSessionFiles(): int
    {
        try {
            $count = 0;
            foreach (glob(storage_path('framework/sessions/*')) ?: [] as $file) {
                if (is_file($file) && filemtime($file) > now()->subMinutes(2)->timestamp) {
                    $count++;
                }
            }

            return $count;
        } catch (\Throwable) {
            return 0;
        }
    }
}
