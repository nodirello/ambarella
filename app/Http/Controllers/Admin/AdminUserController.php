<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\TelegramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::with('businessProfile')
            ->when($request->search, fn ($q, $s) => $q
                ->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%"))
            ->when($request->role, fn ($q, $role) => $q->where('role', $role))
            ->latest()
            ->paginate(20);

        return view('admin.users.index', ['users' => $users]);
    }

    public function toggleBan(Request $request, User $user, ActivityLogger $activity, TelegramService $telegram): RedirectResponse
    {
        abort_if($user->is($request->user()), 422, 'O‘zingizni bloklay olmaysiz.');

        $user->update(['is_banned' => ! $user->is_banned]);
        $activity->log($request->user(), 'user_ban', "Foydalanuvchi ".($user->is_banned ? 'bloklandi' : 'blokdan chiqarildi').": {$user->email}");

        if ($user->is_banned && $tg = $user->telegramUsers()->first()) {
            $telegram->sendMessage($tg->chat_id, '⛔ Hisobingiz bloklangan. Administrator bilan bog‘laning.');
        }

        return back()->with('success', $user->is_banned ? 'Foydalanuvchi bloklandi.' : 'Foydalanuvchi faollashtirildi.');
    }

    public function toggleAdmin(Request $request, User $user, ActivityLogger $activity): RedirectResponse
    {
        abort_if($user->is($request->user()), 422, 'O‘z rolingizni o‘zgartira olmaysiz.');

        $user->update(['role' => $user->isAdmin() ? Role::User : Role::Admin]);

        $activity->log($request->user(), 'user_role', "Rol o‘zgardi: {$user->email} → {$user->role->value}");

        return back()->with('success', 'Rol yangilandi.');
    }
}
