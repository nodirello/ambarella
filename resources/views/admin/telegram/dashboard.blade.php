@extends('layouts.admin')

@section('title', 'Telegram — Admin')
@section('admin-title', 'Telegram bot')

@section('content')
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
    <x-ui.stat label="Bot foydalanuvchilari" :value="$stats['users']" icon="🤖" tone="blue" />
    <x-ui.stat label="Ulangan" :value="$stats['linked']" icon="🔗" tone="violet" />
    <x-ui.stat label="Vazifalar" :value="$stats['tasks']" icon="✅" tone="green" />
    <x-ui.stat label="Kutilayotgan" :value="$stats['pending']" icon="⏳" tone="amber" />
    <x-ui.stat label="Referallar" :value="$stats['referrals']" icon="🎁" tone="rose" />
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <x-ui.card title="Sozlash">
        <div class="grid gap-3 text-sm">
            <form method="POST" action="{{ route('admin.telegram.set-webhook') }}" class="flex items-center justify-between rounded-xl border border-slate-200 p-4 dark:border-slate-700">
                <span>Webhook o‘rnatish<br><span class="text-xs text-slate-400">{{ config('app.url') }}{{ config('services.telegram.webhook_url') }}</span></span>
                @csrf
                <x-ui.button variant="secondary">O‘rnatish</x-ui.button>
            </form>

            <form method="POST" action="{{ route('admin.telegram.broadcast') }}" class="grid gap-2 rounded-xl border border-slate-200 p-4 dark:border-slate-700">
                @csrf
                <x-ui.textarea name="message" label="Broadcast xabar (HTML ruxsat etiladi)" rows="3" />
                <x-ui.button variant="secondary">Yuborish</x-ui.button>
            </form>

            <form method="POST" action="{{ route('admin.telegram.tasks.store') }}" class="grid gap-2 rounded-xl border border-slate-200 p-4 dark:border-slate-700">
                @csrf
                <p class="font-semibold">Yangi bot vazifasi</p>
                <div class="grid gap-2 sm:grid-cols-2">
                    <x-ui.input name="title" placeholder="Vazifa nomi" required />
                    <x-ui.input name="reward" type="number" placeholder="🪙 Mukofot" required />
                </div>
                <x-ui.select name="type" :options="['subscribe' => 'Obuna', 'referral' => 'Referal', 'eco' => 'Eko', 'photo' => 'Rasm', 'text' => 'Matn']" />
                <x-ui.input name="channel_url" placeholder="https://t.me/…" />
                <x-ui.button variant="secondary">Qo‘shish</x-ui.button>
            </form>
        </div>
    </x-ui.card>

    <x-ui.card title="Bot vazifalari">
        <div class="grid gap-3">
            @forelse (\App\Models\TelegramBotTask::latest()->get() as $task)
                <div class="flex items-center justify-between rounded-xl border border-slate-200 p-4 text-sm dark:border-slate-700">
                    <div>
                        <p class="font-semibold">{{ $task->title }}</p>
                        <p class="text-xs text-slate-400">{{ $task->type }} · +{{ $task->reward }} 🪙</p>
                    </div>
                    <form method="POST" action="{{ route('admin.telegram.tasks.toggle', $task) }}">
                        @csrf
                        <x-ui.badge :tone="$task->is_active ? 'green' : 'rose'">{{ $task->is_active ? 'Faol' : 'Yopiq' }}</x-ui.badge>
                    </form>
                </div>
            @empty
                <p class="text-sm text-slate-400">Vazifalar yo‘q.</p>
            @endforelse
        </div>
    </x-ui.card>
</div>
@endsection
