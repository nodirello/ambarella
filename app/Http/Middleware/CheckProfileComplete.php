<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProfileComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && ! $request->user()->profile_completed) {
            if (! $request->routeIs('onboarding.*') && ! $request->routeIs('logout')) {
                return redirect()->route('onboarding.index');
            }
        }

        return $next($request);
    }
}
