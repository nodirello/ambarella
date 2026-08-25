<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Global guard: after N failed login attempts from one IP the whole IP is
 * blocked for a cooldown window. Applied to every request.
 */
class BlockSuspiciousIps
{
    private const FAIL_LIMIT = 10;
    private const FAIL_TTL = 300; // 5 min
    private const BLOCK_TTL = 1800; // 30 min

    public function handle(Request $request, Closure $next): Response
    {
        if (Cache::has($this->blockKey($request->ip()))) {
            abort(429, 'Juda ko‘p urinish. Iltimos, 30 daqiqadan so‘ng qayta urinib ko‘ring.');
        }

        return $next($request);
    }

    public static function recordFailedLogin(string $ip): void
    {
        $key = self::failKey($ip);
        $attempts = (int) Cache::get($key, 0) + 1;

        Cache::put($key, $attempts, self::FAIL_TTL);

        if ($attempts >= self::FAIL_LIMIT) {
            Cache::put(self::blockKey($ip), true, self::BLOCK_TTL);
            Cache::forget($key);
        }
    }

    private static function failKey(string $ip): string
    {
        return "security:failed_login:{$ip}";
    }

    private static function blockKey(string $ip): string
    {
        return "security:blocked_ip:{$ip}";
    }
}
