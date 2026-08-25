<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function check(): JsonResponse
    {
        $healthy = true;
        $database = 'connected';
        $cache = 'working';

        try {
            DB::connection()->getPdo();
        } catch (\Throwable) {
            $database = 'error';
            $healthy = false;
        }

        try {
            Cache::put('health_check', true, 10);
            $cache = Cache::get('health_check') ? 'working' : 'error';
        } catch (\Throwable) {
            $cache = 'error';
            $healthy = false;
        }

        return response()->json([
            'status' => $healthy ? 'ok' : 'degraded',
            'timestamp' => now()->toIso8601String(),
            'database' => $database,
            'cache' => $cache,
        ], $healthy ? 200 : 503);
    }
}
