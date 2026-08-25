<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Stateless API authentication with hashed personal access tokens.
 * Header: Authorization: Bearer {plainToken}
 */
class AuthenticateApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = $request->bearerToken();

        if (! $plainToken) {
            return response()->json(['message' => 'Bearer token kiritilmagan.'], 401);
        }

        $token = ApiToken::where('token_hash', ApiToken::hashToken($plainToken))->first();

        if (! $token || ! $token->isValid()) {
            return response()->json(['message' => 'Token yaroqsiz yoki muddati o‘tgan.'], 401);
        }

        $token->update(['last_used_at' => now()]);

        $request->setUserResolver(fn () => $token->user);
        $request->attributes->set('api_token', $token);

        return $next($request);
    }
}
