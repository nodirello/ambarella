@extends('layouts.app')

@section('title', 'Code playground — AMBARELLA')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-14 sm:px-6">
    <x-app.page-header title="💻 Code playground" description="Brauzerda JavaScript sinab ko‘ring (xavfsiz sandbox)." />

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="rounded-3xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900/60">
            <div class="flex items-center justify-between px-1 pb-3">
                <p class="text-sm font-bold">JavaScript</p>
                <button onclick="runCode()" class="rounded-lg bg-blue-600 px-4 py-1.5 text-xs font-bold text-white">▶ Run</button>
            </div>
            <textarea id="code" rows="16" spellcheck="false" class="w-full resize-y rounded-2xl bg-slate-950 p-4 font-mono text-sm leading-6 text-slate-100 outline-none"
placeholder="console.log('Salom, AMBARELLA!');
const a = [1, 2, 3];
console.log(a.map(x => x * 2));"></textarea>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900/60">
            <p class="px-1 pb-3 text-sm font-bold">Natija</p>
            <pre id="output" class="min-h-[320px] overflow-auto rounded-2xl bg-slate-950 p-4 font-mono text-sm leading-6 text-emerald-300">// Run tugmasini bosing…</pre>
        </div>
    </div>
</div>

<script>
function runCode() {
    const out = document.getElementById('output');
    out.textContent = '';
    const logs = [];
    const fake = (level, args) => logs.push(args.map(String).join(' '));

    try {
        const fn = new Function('console', `'use strict';\n${document.getElementById('code').value}`);
        fn({
            log: (...a) => fake('log', a),
            error: (...a) => fake('error', a),
            warn: (...a) => fake('warn', a),
        });
        out.textContent = logs.join('\n') || '✓ Bajarildi (chiqish yo‘q)';
    } catch (e) {
        out.textContent = '❌ ' + (e.message ?? e);
    }
}
</script>
@endsection
