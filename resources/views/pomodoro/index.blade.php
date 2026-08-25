@extends('layouts.app')

@section('title', 'Pomodoro taymer — AMBARELLA')

@section('content')
<div class="mx-auto max-w-xl px-4 py-14 sm:px-6">
    <x-app.page-header title="🍅 Pomodoro taymer" description="25 daqiqa ish — 5 daqiqa tanaffus. Diqqatni jamlang." />

    <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-10 text-center dark:border-slate-800 dark:bg-slate-900/60">
        <p id="timer" class="text-7xl font-black tabular-nums tracking-tight">25:00</p>
        <p id="phase" class="mt-3 text-sm font-semibold uppercase tracking-widest text-slate-400">Ish vaqti</p>

        <div class="mt-8 flex justify-center gap-3">
            <button id="start-btn" onclick="toggleTimer()" class="rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow hover:bg-blue-700">Boshlash</button>
            <button onclick="resetTimer()" class="rounded-xl border border-slate-200 px-6 py-2.5 text-sm font-semibold dark:border-slate-700">Tugatish</button>
        </div>

        <div class="mt-8 grid grid-cols-4 gap-3 text-center text-xs text-slate-400">
            <div class="rounded-xl border border-slate-200 py-3 dark:border-slate-700"><p class="text-lg font-black text-slate-700 dark:text-slate-200" id="done-count">0</p>sessiya</div>
            <div class="rounded-xl border border-slate-200 py-3 dark:border-slate-700"><p class="text-lg font-black text-slate-700 dark:text-slate-200" id="min-today">0</p>daq. bugun</div>
            <div class="rounded-xl border border-slate-200 py-3 dark:border-slate-700"><p class="text-lg font-black text-slate-700 dark:text-slate-200">🍅</p>🍅</div>
            <div class="rounded-xl border border-slate-200 py-3 dark:border-slate-700"><p class="text-lg font-black text-slate-700 dark:text-slate-200">25/5</p>daqiqa</div>
        </div>
    </div>
</div>

<script>
const WORK = 25 * 60, BREAK = 5 * 60;
let total = WORK, left = WORK, timerId = null, phase = 'work';
let done = Number(localStorage.getItem('pomodoro.done') ?? 0);
let minutesToday = Number(localStorage.getItem('pomodoro.minutes') ?? 0);

function render() {
    const m = String(Math.floor(left / 60)).padStart(2, '0');
    const s = String(left % 60).padStart(2, '0');
    document.getElementById('timer').textContent = `${m}:${s}`;
    document.getElementById('phase').textContent = phase === 'work' ? 'Ish vaqti' : 'Tanaffus';
    document.getElementById('done-count').textContent = done;
    document.getElementById('min-today').textContent = minutesToday;
}

function tick() {
    left--;
    if (left <= 0) {
        if (phase === 'work') {
            done++; minutesToday += 25;
            localStorage.setItem('pomodoro.done', done);
            localStorage.setItem('pomodoro.minutes', minutesToday);
            phase = 'break'; total = BREAK; left = BREAK;
            if (Notification.permission === 'granted') new Notification('Tanaffus vaqti! 🍵', { body: '5 daqiqa dam oling.' });
        } else {
            phase = 'work'; total = WORK; left = WORK;
            if (Notification.permission === 'granted') new Notification('Ish vaqti! 💪', { body: 'Yana 25 daqiqa diqqat.' });
        }
    }
    render();
}

function toggleTimer() {
    const btn = document.getElementById('start-btn');
    if (timerId) { clearInterval(timerId); timerId = null; btn.textContent = 'Davom etish'; return; }
    btn.textContent = 'Pauza';
    timerId = setInterval(tick, 1000);
}

function resetTimer() { clearInterval(timerId); timerId = null; phase = 'work'; total = left = WORK; render(); document.getElementById('start-btn').textContent = 'Boshlash'; }

render();
</script>
@endsection
