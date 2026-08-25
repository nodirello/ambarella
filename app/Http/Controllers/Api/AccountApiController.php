<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountApiController extends Controller
{
    /**
     * Exchange credentials for a personal access token.
     */
    public function issueToken(Request $request, ActivityLogger $activity): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = \App\Models\User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password) || $user->is_banned) {
            return response()->json(['message' => 'Email yoki parol noto‘g‘ri.'], 401);
        }

        $plain = Str::random(48);

        ApiToken::create([
            'user_id' => $user->id,
            'name' => 'api-client',
            'token_hash' => ApiToken::hashToken($plain),
            'abilities' => ['*'],
            'expires_at' => now()->addYear(),
        ]);

        $activity->log($user, 'api_token', 'API token yaratildi');

        return response()->json([
            'token' => $plain,
            'token_type' => 'Bearer',
            'expires_at' => now()->addYear()->toIso8601String(),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role->value,
            'greencoin_balance' => $user->greencoin_balance,
            'login_streak' => $user->login_streak,
        ]);
    }

    public function greencoin(Request $request): JsonResponse
    {
        return response()->json([
            'balance' => $request->user()->greencoin_balance,
            'transactions' => $request->user()
                ->transactions()
                ->latest()
                ->paginate(20),
        ]);
    }

    public function myApplications(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $request->user()->applications()->with('job')->latest()->get(),
        ]);
    }
}
