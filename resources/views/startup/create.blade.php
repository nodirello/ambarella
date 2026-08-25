@extends('layouts.app')

@section('title', 'Loyiha joylash — AMBARELLA')

@section('content')
<div class="mx-auto max-w-2xl px-4 py-10 sm:px-6">
    <x-app.page-header title="🚀 Loyiha joylash" description="Startupingizni jamiyatga taqdim eting." />

    <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900/60">
        <form method="POST" action="{{ route('startups.store') }}" class="grid gap-5">
            @csrf
            <x-ui.input name="name" label="Loyiha nomi" required />
            <x-ui.textarea name="description" label="Tavsif" rows="5" required />
            <div class="grid gap-5 sm:grid-cols-2">
                <x-ui.input name="category" label="Kategoriya" required />
                <x-ui.select name="stage" label="Bosqich" :options="['idea' => 'G‘oya', 'mvp' => 'MVP', 'growth' => 'O‘sish', 'scale' => 'Kengayish']" required />
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-ui.input name="team_size" label="Jamoa (kishi)" type="number" value="1" required />
                <x-ui.input name="website" label="Veb-sayt" placeholder="https://…" />
            </div>
            <x-ui.input name="looking_for" label="Nima kerak?" placeholder="Jamoa, investor, mentor…" />
            <x-ui.button>Joylash</x-ui.button>
        </form>
    </div>
</div>
@endsection
