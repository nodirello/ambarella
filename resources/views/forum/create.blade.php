@extends('layouts.app')

@section('title', 'Yangi mavzu — Forum — AMBARELLA')

@section('content')
<div class="mx-auto max-w-2xl px-4 py-10 sm:px-6">
    <x-app.page-header title="Yangi mavzu" />

    <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900/60">
        <form method="POST" action="{{ route('forum.store') }}" class="grid gap-5">
            @csrf
            <x-ui.input name="title" label="Sarlavha" required />
            <x-ui.select name="category" label="Kategoriya" :options="['umumiy' => 'Umumiy', 'dasturlash' => 'Dasturlash', 'bandlik' => 'Bandlik', 'ta\'lim' => 'Ta’lim', 'ekologiya' => 'Ekologiya', 'biznes' => 'Biznes', 'psixologiya' => 'Psixologiya']" required />
            <x-ui.textarea name="body" label="Matn" rows="8" required />
            <x-ui.button>Yaratish</x-ui.button>
        </form>
    </div>
</div>
@endsection
