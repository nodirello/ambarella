@extends('layouts.admin')

@section('title', 'Moderatsiya — Admin')
@section('admin-title', 'Moderatsiya')

@section('content')
<div class="grid gap-6 xl:grid-cols-2">
    <x-ui.card title="📝 Blog maqolalari ({{ $blogPosts->count() }})">
        <div class="grid gap-3">
            @forelse ($blogPosts as $post)
                <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-700">
                    <p class="font-semibold">{{ $post->title }}</p>
                    <p class="mt-1 line-clamp-2 text-xs text-slate-400">{{ $post->content }}</p>
                    <p class="mt-1 text-xs text-slate-400">{{ $post->author->name }}</p>
                    <div class="mt-3 flex gap-2">
                        <form method="POST" action="{{ route('admin.moderation.blog.approve', $post) }}">
                            @csrf
                            <x-ui.button variant="success">Tasdiqlash</x-ui.button>
                        </form>
                        <form method="POST" action="{{ route('admin.moderation.blog.reject', $post) }}">
                            @csrf
                            <x-ui.button variant="danger">Rad etish</x-ui.button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-400">Navbat bo‘sh. ✅</p>
            @endforelse
        </div>
    </x-ui.card>

    <x-ui.card title="🌱 Eko arizalar ({{ $ecoCompletions->count() }})">
        <div class="grid gap-3">
            @forelse ($ecoCompletions as $completion)
                <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-700">
                    <p class="font-semibold">{{ $completion->task->title }} <span class="text-emerald-500">+{{ $completion->task->reward }} 🪙</span></p>
                    <p class="mt-1 text-xs text-slate-400">{{ $completion->user->name }} · {{ $completion->completed_on }}</p>
                    <p class="mt-1 text-xs">{{ $completion->proof_text }}</p>
                    <div class="mt-3 flex gap-2">
                        <form method="POST" action="{{ route('admin.moderation.eco.approve', $completion) }}">
                            @csrf
                            <x-ui.button variant="success">Tasdiqlash</x-ui.button>
                        </form>
                        <form method="POST" action="{{ route('admin.moderation.eco.reject', $completion) }}">
                            @csrf
                            <x-ui.button variant="danger">Rad etish</x-ui.button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-400">Navbat bo‘sh. ✅</p>
            @endforelse
        </div>
    </x-ui.card>
</div>
@endsection
