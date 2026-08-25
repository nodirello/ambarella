@extends('layouts.app')

@section('title', 'Aloqa — AMBARELLA')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-14 sm:px-6">
    <div class="grid gap-10 lg:grid-cols-2">
        <div class="reveal">
            <h1 class="text-3xl font-extrabold tracking-tight">Biz bilan bog‘laning</h1>
            <p class="mt-3 text-slate-500 dark:text-slate-400">Savol, taklif yoki hamkorlik — 24 soat ichida javob beramiz.</p>
            <div class="mt-8 grid gap-4">
                @foreach ([['📞', '+998 90 074 10 70'], ['✉️', 'info@ambarella.uz'], ['📱', '@AmbarellaBot'], ['📍', 'Toshkent, O‘zbekiston']] as [$icon, $value])
                    <div class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900/60">
                        <span class="text-xl">{{ $icon }}</span><span class="text-sm font-medium">{{ $value }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="reveal rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900/60">
            <form method="POST" action="{{ route('contact.store') }}" class="grid gap-4">
                @csrf
                <x-ui.input name="name" label="Ismingiz" required />
                <x-ui.input name="email" label="Email" type="email" required />
                <x-ui.input name="subject" label="Mavzu" required />
                <x-ui.textarea name="message" label="Xabar" rows="5" required />
                <x-ui.button>Yuborish</x-ui.button>
            </form>
        </div>
    </div>
</div>
@endsection
