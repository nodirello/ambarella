<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\Locale;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = Locale::fromAny(
            $request->cookie('locale')
            ?? $request->header('Accept-Language')
        );

        app()->setLocale($locale->value);

        return $next($request);
    }
}
