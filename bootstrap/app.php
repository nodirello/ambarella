<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Reverse-proxy friendly URL generation. In dev/preview the app sits
        // behind a TLS proxy (e2b/Arena), so trust it for scheme + host.
        $middleware->trustProxies(at: env('TRUSTED_PROXIES', '*'));

        // Global middleware (executed for every request).
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->append(\App\Http\Middleware\SetLocale::class);
        $middleware->append(\App\Http\Middleware\BlockSuspiciousIps::class);

        // Telegram webhooks are signed with a secret header — CSRF does not apply.
        $middleware->validateCsrfTokens(except: [
            'webhook/telegram',
        ]);

        $middleware->alias([
            'banned' => \App\Http\Middleware\CheckBanned::class,
            'profile.complete' => \App\Http\Middleware\CheckProfileComplete::class,
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            '2fa' => \App\Http\Middleware\RequireTwoFactor::class,
            'api.auth' => \App\Http\Middleware\AuthenticateApiToken::class,
        ]);

        $middleware->throttleApi('60,1');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (Throwable $e): bool {
            if (app()->isProduction()) {
                \Illuminate\Support\Facades\Log::error('Unhandled exception', [
                    'message' => $e->getMessage(),
                    'exception' => $e::class,
                    'file' => $e->getFile().':'.$e->getLine(),
                    'url' => request()->fullUrl(),
                    'user_id' => auth()->id(),
                ]);
            }

            return false;
        });
    })->create();
