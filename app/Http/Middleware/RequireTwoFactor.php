<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->two_factor_secret && ! $request->session()->get('2fa.verified')) {
            if (! $request->routeIs('2fa.*', 'logout')) {
                return redirect()->route('2fa.challenge');
            }
        }

        return $next($request);
    }
}
