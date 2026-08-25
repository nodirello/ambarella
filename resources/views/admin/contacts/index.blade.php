@extends('layouts.admin')

@section('title', 'Murojaatlar — Admin')
@section('admin-title', 'Murojaatlar')

@section('content')
<div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900/60">
    <table class="w-full min-w-[720px] text-sm">
        <thead class="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-400 dark:border-slate-800">
            <tr><th class="px-4 py-3">Kimdan</th><th class="px-4 py-3">Mavzu</th><th class="px-4 py-3">Holat</th><th class="px-4 py-3">Sana</th><th class="px-4 py-3 text-right">Amal</th></tr>
        </thead>
        <tbody>
            @foreach ($messages as $message)
                <tr class="border-b border-slate-50 dark:border-slate-800/60">
                    <td class="px-4 py-3"><p class="font-semibold">{{ $message->name }}</p><p class="text-xs text-slate-400">{{ $message->email }}</p></td>
                    <td class="px-4 py-3">{{ $message->subject }}</td>
                    <td class="px-4 py-3"><x-ui.badge :tone="$message->status === 'new' ? 'amber' : 'slate'">{{ $message->status }}</x-ui.badge></td>
                    <td class="px-4 py-3 text-xs text-slate-400">{{ $message->created_at->format('d.m.Y') }}</td>
                    <td class="px-4 py-3 text-right">
                        <form method="POST" action="{{ route('admin.contacts.destroy', $message) }}">
                            @csrf @method('DELETE')
                            <button class="text-xs font-semibold text-rose-500">O‘chirish</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<x-ui.pagination :paginator="$messages" />
@endsection
