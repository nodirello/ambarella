<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AccountApiController;
use App\Http\Controllers\Api\PublicApiController;
use App\Http\Controllers\Telegram\TelegramWebAppController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| AMBARELLA REST API v1
|--------------------------------------------------------------------------
| Public endpoints are throttled; account endpoints require a Bearer token
| issued via POST /api/v1/token (see AccountApiController).
*/
Route::prefix('v1')->group(function () {
    // Public catalog
    Route::get('/jobs', [PublicApiController::class, 'jobs']);
    Route::get('/courses', [PublicApiController::class, 'courses']);
    Route::get('/news', [PublicApiController::class, 'news']);
    Route::get('/mentors', [PublicApiController::class, 'mentors']);
    Route::get('/stats', [PublicApiController::class, 'stats']);

    // Telegram Web App (initData-signed)
    Route::prefix('tg')->group(function () {
        Route::get('/me', [TelegramWebAppController::class, 'me']);
        Route::get('/eco-tasks', [TelegramWebAppController::class, 'ecoTasks']);
        Route::get('/eco-stats', [TelegramWebAppController::class, 'ecoStats']);
        Route::post('/eco-complete', [TelegramWebAppController::class, 'completeEco']);
    });

    // Account (token based)
    Route::post('/token', [AccountApiController::class, 'issueToken'])->middleware('throttle:5,1');

    Route::middleware('api.auth')->group(function () {
        Route::get('/me', [AccountApiController::class, 'me']);
        Route::get('/greencoin', [AccountApiController::class, 'greencoin']);
        Route::get('/my-applications', [AccountApiController::class, 'myApplications']);
    });
});
