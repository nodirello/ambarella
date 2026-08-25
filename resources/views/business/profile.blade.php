@extends('layouts.app')

@section('title', 'Biznes profili — AMBARELLA')

@section('content')
<div class="mx-auto max-w-2xl px-4 py-10 sm:px-6">
    <x-app.page-header title="Biznes profili" />

    <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900/60">
        <form method="POST" action="{{ route('business.profile.update') }}" enctype="multipart/form-data" class="grid gap-5">
            @csrf
            <x-ui.input name="company_name" label="Kompaniya nomi" :value="$profile->company_name" required />
            <x-ui.textarea name="description" label="Tavsif" :value="$profile->description" rows="4" />
            <div class="grid gap-5 sm:grid-cols-2">
                <x-ui.input name="industry" label="Soha" :value="$profile->industry" />
                <x-ui.input name="city" label="Shahar" :value="$profile->city" />
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-ui.input name="website" label="Veb-sayt" :value="$profile->website" />
                <x-ui.input name="phone" label="Telefon" :value="$profile->phone" />
            </div>
            <x-ui.input name="address" label="Manzil" :value="$profile->address" />
            <x-ui.button>Saqlash</x-ui.button>
        </form>
    </div>
</div>
@endsection
