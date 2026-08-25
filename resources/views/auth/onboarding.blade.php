@extends('layouts.app')

@section('title', 'Profilni to‘ldirish — AMBARELLA')

@section('content')
<div class="mx-auto max-w-2xl px-4 py-14 sm:px-6">
    <div class="reveal">
        <div class="text-4xl">👋</div>
        <h1 class="mt-4 text-3xl font-extrabold tracking-tight">Xush kelibsiz! Profilingizni to‘ldiring</h1>
        <p class="mt-2 text-slate-500 dark:text-slate-400">Bu 1 daqiqa vaqt oladi. To‘ldirilgan profil bilan barcha modullar ochiladi.</p>

        <form method="POST" action="{{ route('onboarding.store') }}" enctype="multipart/form-data" class="mt-8 grid gap-5 rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900/60">
            @csrf
            <x-ui.input name="birth_date" label="Tug‘ilgan sana" type="date" />
            <div class="grid gap-5 sm:grid-cols-2">
                <x-ui.select name="gender" label="Jinsi" :options="['male' => 'Erkak', 'female' => 'Ayol', 'other' => 'Boshqa']" placeholder="Tanlang" />
                <x-ui.select name="region" label="Hudud" :options="array_combine($regions, $regions)" placeholder="Tanlang" />
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-ui.input name="city" label="Shahar" />
                <x-ui.input name="profession" label="Kasb / yo‘nalish" placeholder="Dasturchi, talaba…" />
            </div>
            <x-ui.textarea name="bio" label="O‘zingiz haqingizda" placeholder="Qisqacha — qiziqishlaringiz va maqsadlaringiz" rows="3" />
            <x-ui.input name="interests[]" label="Qiziqishlar (vergul bilan)" placeholder="IT, ekologiya, kitob" />
            <x-ui.input name="avatar" label="Profil rasmi" type="file" accept="image/*" />
            <x-ui.button class="w-full">Saqlash va davom etish →</x-ui.button>
        </form>
    </div>
</div>
@endsection
