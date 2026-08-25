@extends('layouts.app')

@section('title', 'Maqola yozish — AMBARELLA')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <x-app.page-header title="Yangi maqola" description="Maqolangiz moderatsiyadan o‘tgach e’lon qilinadi." />

    <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900/60">
        <form method="POST" action="{{ route('blog.store') }}" enctype="multipart/form-data" class="grid gap-5">
            @csrf
            <x-ui.input name="title" label="Sarlavha" required />
            <x-ui.input name="category" label="Kategoriya" placeholder="Dasturlash, ekologiya…" />
            <x-ui.textarea name="content" label="Matn" rows="12" required />
            <x-ui.input name="cover_image" label="Cover rasm" type="file" accept="image/*" />
            <x-ui.button>Moderatsiyaga yuborish</x-ui.button>
        </form>
    </div>
</div>
@endsection
