@extends('layouts.admin')

@section('title', 'Foydalanuvchilar — Admin')
@section('admin-title', 'Foydalanuvchilar')

@section('content')
<form method="GET" class="mb-6 flex flex-wrap gap-3">
    <x-ui.input name="search" placeholder="Ism yoki email…" class="max-w-xs" />
    <x-ui.select name="role" :options="['user' => 'Foydalanuvchi', 'business' => 'Biznes', 'moderator' => 'Moderator', 'admin' => 'Admin']" placeholder="Barcha rollar" />
    <x-ui.button variant="secondary">Filtr</x-ui.button>
    <div class="ml-auto flex gap-2">
        <x-ui.button :href="route('admin.export.users')" variant="secondary">CSV</x-ui.button>
    </div>
</form>

<div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900/60">
    <table class="w-full min-w-[720px] text-sm">
        <thead class="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-400 dark:border-slate-800">
            <tr><th class="px-4 py-3">Foydalanuvchi</th><th class="px-4 py-3">Rol</th><th class="px-4 py-3">🪙 Balans</th><th class="px-4 py-3">Ro‘yxatdan o‘tgan</th><th class="px-4 py-3 text-right">Amallar</th></tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr class="border-b border-slate-50 dark:border-slate-800/60">
                    <td class="px-4 py-3">
                        <p class="font-semibold">{{ $user->name }}</p>
                        <p class="text-xs text-slate-400">{{ $user->email }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <x-ui.badge :tone="match ($user->role->value) { 'admin' => 'violet', 'business' => 'blue', 'moderator' => 'amber', default => 'slate' }">{{ $user->role->label() }}</x-ui.badge>
                        @if ($user->is_banned)<x-ui.badge tone="rose">Blok</x-ui.badge>@endif
                    </td>
                    <td class="px-4 py-3 font-semibold">{{ number_format($user->greencoin_balance) }}</td>
                    <td class="px-4 py-3 text-xs text-slate-400">{{ $user->created_at->format('d.m.Y') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-2 text-xs font-semibold">
                            <form method="POST" action="{{ route('admin.users.role', $user) }}">
                                @csrf
                                <button class="text-blue-600 dark:text-blue-400">{{ $user->isAdmin() ? 'Moderator' : 'Admin qilish' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.users.ban', $user) }}">
                                @csrf
                                <button class="{{ $user->is_banned ? 'text-emerald-500' : 'text-rose-500' }}">{{ $user->is_banned ? 'Faollashtirish' : 'Bloklash' }}</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<x-ui.pagination :paginator="$users" />
@endsection
