@extends('layouts.app')

@section('title', 'Biz haqimizda — AMBARELLA')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-14 sm:px-6">
    <div class="reveal text-center">
        <h1 class="text-4xl font-black tracking-tight">Bizning maqsadimiz — <span class="bg-gradient-to-r from-blue-500 to-violet-500 bg-clip-text text-transparent">yoshlar imkoniyati</span></h1>
        <p class="mx-auto mt-5 max-w-2xl text-slate-500 dark:text-slate-400">AMBARELLA — bandlik, ekologiya, ta’lim va jamiyatni bir joyga birlashtirgan platforma. Biz har bir yoshga: ish topish, bilim olish, o‘z loyihasini boshlash va jamiyatga hissa qo‘shish uchun teng imkoniyat yaratamiz.</p>
    </div>

    <div class="mt-12 grid gap-4 md:grid-cols-3">
        <x-ui.stat label="Modullar" value="20+" icon="🧩" tone="blue" />
        <x-ui.stat label="Tillar" value="3" icon="🌐" tone="violet" />
        <x-ui.stat label="Mukofot valyutasi" value="GreenCoin" icon="🪙" tone="green" />
    </div>

    <div class="mt-12 rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900/60">
        <h2 class="text-xl font-bold">Qadriyatlarimiz</h2>
        <div class="mt-5 grid gap-6 sm:grid-cols-2">
            @foreach ([['🌱', 'Barqarorlik', 'Ekologik ong va mas’uliyatli iste’mol — platformaning markazida.'], ['🤝', 'Ochiqlik', 'Har bir modul bepul yoki arzon. Yashirin to‘lovlar yo‘q.'], ['🚀', 'Amaliylik', 'Nazariya emas — haqiqiy ish, haqiqiy loyihalar.'], ['🧭', 'Mentorlik', 'Har bir yoshda yo‘l ko‘rsatuvchi bo‘lishi kerak.']] as [$icon, $title, $desc])
                <div class="flex gap-4">
                    <span class="text-2xl">{{ $icon }}</span>
                    <div>
                        <h3 class="font-bold">{{ $title }}</h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $desc }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if ($stories->isNotEmpty())
        <div class="mt-12">
            <h2 class="text-xl font-bold">Muvaffaqiyat hikoyalari</h2>
            <div class="mt-5 grid gap-4 sm:grid-cols-3">
                @foreach ($stories as $story)
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 text-sm dark:border-slate-800 dark:bg-slate-900/60">
                        <p class="font-bold">{{ $story->author_name }} · {{ $story->company }}</p>
                        <p class="mt-2 text-slate-500 dark:text-slate-400">{{ Str::limit($story->content, 140) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
