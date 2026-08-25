@extends('layouts.app')

@section('title', 'Analitika — Biznes — AMBARELLA')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
    <x-app.page-header title="📊 Analitika" />

    <div class="mt-8 grid gap-4 sm:grid-cols-3">
        <x-ui.stat label="Ko‘rishlar" :value="number_format($totals['views'])" icon="👁" tone="blue" />
        <x-ui.stat label="Arizalar" :value="$totals['applications']" icon="📄" tone="violet" />
        <x-ui.stat label="Konversiya" :value="$totals['conversion'].'%'" icon="🎯" tone="green" />
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <x-ui.card title="Holatlar bo‘yicha">
            <div class="grid gap-2">
                @foreach (['pending' => 'Yuborilgan', 'viewed' => 'Ko‘rildi', 'shortlisted' => 'Qisqa ro‘yxat', 'accepted' => 'Qabul', 'rejected' => 'Rad'] as $status => $label)
                    <div class="flex justify-between rounded-xl border border-slate-200 px-4 py-2.5 text-sm dark:border-slate-700">
                        <span>{{ $label }}</span><strong>{{ $byStatus[$status] ?? 0 }}</strong>
                    </div>
                @endforeach
            </div>
        </x-ui.card>

        <x-ui.card title="Oxirgi 14 kun">
            <div class="flex h-48 items-end gap-1.5">
                @for ($i = 13; $i >= 0; $i--)
                    @php $day = now()->subDays($i)->format('Y-m-d'); $count = $last14Days[$day] ?? 0; @endphp
                    <div class="flex h-full flex-1 flex-col items-center justify-end gap-1">
                        <div class="w-full rounded-t bg-blue-500/80" style="height: {{ max(4, $count * 24) }}%"></div>
                        <span class="text-[9px] text-slate-400">{{ \Carbon\Carbon::parse($day)->format('d') }}</span>
                    </div>
                @endfor
            </div>
        </x-ui.card>
    </div>
</div>
@endsection
