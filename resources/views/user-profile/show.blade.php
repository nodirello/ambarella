@extends('layouts.app')

@section('title', $profile->name.' — AMBARELLA')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
    <div class="reveal rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900/60">
        <div class="flex items-center gap-5">
            <span class="grid h-20 w-20 place-items-center rounded-3xl bg-gradient-to-br from-blue-600/20 to-violet-600/20 text-3xl font-extrabold">{{ $profile->initials() }}</span>
            <div>
                <h1 class="text-2xl font-extrabold">{{ $profile->name }}</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $profile->profession ?? 'A‘zo' }} · {{ $profile->region ?? 'O‘zbekiston' }}</p>
            </div>
        </div>
        @if ($profile->bio)
            <p class="mt-6 text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $profile->bio }}</p>
        @endif
        <div class="mt-8 grid grid-cols-3 gap-3">
            <x-ui.stat label="Yutuqlar" :value="$stats['achievements']" icon="🏅" tone="violet" />
            <x-ui.stat label="Referallar" :value="$stats['referrals']" icon="👥" tone="blue" />
            <x-ui.stat label="Seriya" :value="$stats['streak']" icon="🔥" tone="amber" />
        </div>
    </div>
</div>
@endsection
