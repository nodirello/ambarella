<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    public function notice(Request $request): View|RedirectResponse
    {
        return $request->user()?->hasVerifiedEmail()
            ? redirect()->route('dashboard.index')
            : view('auth.verify-email');
    }

    public function verify(Request $request, string $id, string $hash): RedirectResponse
    {
        $user = $request->user();

        abort_if(! $user || ! hash_equals((string) $user->getKey(), $id), 403);
        abort_if(! hash_equals(sha1($user->getEmailForVerification()), $hash), 403);

        if ($user->markEmailAsVerified()) {
            \Illuminate\Support\Facades\Event::dispatch(new \Illuminate\Auth\Events\Verified($user));
        }

        return redirect()->route('dashboard.index')->with('success', 'Email tasdiqlandi.');
    }

    public function resend(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard.index');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Tasdiqlash havolasi qayta yuborildi.');
    }
}
