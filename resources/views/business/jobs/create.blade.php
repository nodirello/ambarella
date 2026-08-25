@extends('layouts.app')

@section('title', 'Yangi ish e’loni — AMBARELLA')

@section('content')
<div class="mx-auto max-w-2xl px-4 py-10 sm:px-6">
    <x-app.page-header title="Yangi ish e’loni" description="Faqat tasdiqlangan bizneslar e’lon bera oladi." />

    <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900/60">
        <form method="POST" action="{{ route('business.jobs.store') }}" class="grid gap-5">
            @csrf
            <x-ui.input name="title" label="Lavozim" required />
            <x-ui.input name="location" label="Joylashuv" placeholder="Toshkent / Masofaviy" />
            <x-ui.select name="type" label="Turi" :options="['full' => 'To‘liq stavka', 'part' => 'Yarim stavka', 'remote' => 'Masofaviy', 'internship' => 'Amaliyot']" required />
            <div class="grid gap-5 sm:grid-cols-2">
                <x-ui.input name="salary_min" label="Min. maosh" type="number" />
                <x-ui.input name="salary_max" label="Max. maosh" type="number" />
            </div>
            <x-ui.textarea name="description" label="Tavsif" rows="6" required />
            <x-ui.textarea name="requirements" label="Talablar" rows="3" />
            <x-ui.button>Joylash</x-ui.button>
        </form>
    </div>
</div>
@endsection
