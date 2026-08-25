@extends('layouts.app')

@section('title', 'So‘rovnomalar — AMBARELLA')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <x-app.page-header title="🗳️ So‘rovnomalar" description="Fikringiz platformani yaxshilaydi." />

    <div class="mt-8 grid gap-4">
        @foreach ($polls as $poll)
            <a href="{{ route('polls.show', $poll) }}" class="reveal rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900/60">
                <h2 class="font-bold">{{ $poll->title }}</h2>
                <p class="mt-1 text-xs text-slate-400">{{ $poll->options->count() }} variant · {{ $poll->votes->count() }} ovoz</p>
            </a>
        @endforeach
    </div>

    <x-ui.pagination :paginator="$polls" />
</div>
@endsection
