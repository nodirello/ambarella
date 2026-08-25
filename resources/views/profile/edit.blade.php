@extends('layouts.app')

@section('title', 'Profil — AMBARELLA')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <x-app.page-header title="👤 Profil" description="Ma’lumotlaringizni yangilang." />

    <div class="mt-8 grid gap-6">
        <x-ui.card title="Asosiy ma’lumotlar">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="grid gap-5">
                @csrf
                <x-ui.input name="name" label="Ism" :value="$user->name" required />
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-ui.input name="birth_date" label="Tug‘ilgan sana" type="date" :value="$user->birth_date?->format('Y-m-d')" />
                    <x-ui.select name="gender" label="Jinsi" :options="['male' => 'Erkak', 'female' => 'Ayol', 'other' => 'Boshqa']" :value="$user->gender" placeholder="Tanlanmagan" />
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-ui.input name="region" label="Hudud" :value="$user->region" />
                    <x-ui.input name="city" label="Shahar" :value="$user->city" />
                </div>
                <x-ui.input name="profession" label="Kasb" :value="$user->profession" />
                <x-ui.textarea name="bio" label="O‘zingiz haqingizda" :value="$user->bio" rows="3" />
                <x-ui.input name="avatar" label="Profil rasmi" type="file" accept="image/*" />
                <x-ui.button>Saqlash</x-ui.button>
            </form>
        </x-ui.card>

        <x-ui.card title="Parolni o‘zgartirish">
            <form method="POST" action="{{ route('profile.password') }}" class="grid gap-5">
                @csrf
                <x-ui.input name="current_password" label="Joriy parol" type="password" required />
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-ui.input name="password" label="Yangi parol" type="password" required />
                    <x-ui.input name="password_confirmation" label="Takrorlang" type="password" required />
                </div>
                <x-ui.button variant="secondary">Parolni yangilash</x-ui.button>
            </form>
        </x-ui.card>
    </div>
</div>
@endsection
