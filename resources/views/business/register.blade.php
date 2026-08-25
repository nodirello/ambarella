@extends('layouts.app')

@section('title', 'Biznes ro‘yxati — AMBARELLA')

@section('content')
<div class="mx-auto max-w-2xl px-4 py-10 sm:px-6">
    <x-app.page-header title="🏢 Biznesni ro‘yxatdan o‘tkazish" description="Tasdiqlangach ish e’lonlari va mahsulot joylash imkoniyati ochiladi." />

    <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900/60">
        <form method="POST" action="{{ route('business.register.store') }}" enctype="multipart/form-data" class="grid gap-5">
            @csrf
            <x-ui.input name="company_name" label="Kompaniya nomi" required />
            <x-ui.textarea name="description" label="Tavsif" rows="4" />
            <div class="grid gap-5 sm:grid-cols-2">
                <x-ui.input name="industry" label="Soha" placeholder="IT, qishloq xo‘jaligi…" />
                <x-ui.input name="city" label="Shahar" />
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-ui.input name="website" label="Veb-sayt" placeholder="https://…" />
                <x-ui.input name="phone" label="Telefon" />
            </div>
            <x-ui.input name="address" label="Manzil" />
            <x-ui.input name="logo" label="Logotip" type="file" accept="image/*" />
            <x-ui.button>Yuborish</x-ui.button>
        </form>
    </div>
</div>
@endsection
