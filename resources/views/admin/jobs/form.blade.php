@extends('layouts.admin')

@php($isEdit = isset($job) && $job instanceof \App\Models\Job)

@section('title', $isEdit ? 'Tahrir — Ish' : 'Yangi ish — Admin')
@section('admin-title', $isEdit ? 'Ish e’lonini tahrirlash' : 'Yangi ish e’loni')

@section('content')
<div class="max-w-2xl rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900/60">
    <form method="POST" action="{{ $isEdit ? route('admin.jobs.update', $job) : route('admin.jobs.store') }}" class="grid gap-5">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif
        <div class="grid gap-5 sm:grid-cols-2">
            <x-ui.input name="title" label="Lavozim" :value="$job?->title" required />
            <x-ui.input name="company" label="Kompaniya" :value="$job?->company" required />
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <x-ui.input name="location" label="Joylashuv" :value="$job?->location" />
            <x-ui.select name="type" label="Turi" :options="['full' => 'To‘liq stavka', 'part' => 'Yarim stavka', 'remote' => 'Masofaviy', 'internship' => 'Amaliyot']" :value="$job?->type?->value" required />
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <x-ui.input name="salary_min" label="Min. maosh (so‘m)" type="number" :value="$job?->salary_min" />
            <x-ui.input name="salary_max" label="Max. maosh (so‘m)" type="number" :value="$job?->salary_max" />
        </div>
        <x-ui.textarea name="description" label="Tavsif" :value="$job?->description" rows="6" required />
        <x-ui.textarea name="requirements" label="Talablar" :value="$job?->requirements" rows="3" />
        <x-ui.input name="deadline" label="Muddat" type="date" :value="$job?->deadline?->format('Y-m-d')" />
        <x-ui.button>Saqlash</x-ui.button>
    </form>
</div>
@endsection
