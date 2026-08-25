<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>AMBARELLA</title>
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <style>
        :root { --bg: var(--tg-theme-bg-color, #0f172a); --card: var(--tg-theme-secondary-bg-color, #1e293b); --text: var(--tg-theme-text-color, #e2e8f0); --hint: var(--tg-theme-hint-color, #64748b); --accent: var(--tg-theme-button-color, #2563eb); }
        * { box-sizing: border-box; margin: 0; -webkit-tap-highlight-color: transparent; }
        body { font: 15px/1.5 -apple-system, Inter, sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }
        .page { padding: 16px 16px 92px; max-width: 560px; margin: 0 auto; }
        .card { background: var(--card); border-radius: 16px; padding: 18px; margin-bottom: 12px; }
        .balance { font-size: 32px; font-weight: 900; color: #4ade80; }
        .btn { display: block; width: 100%; padding: 14px; border: 0; border-radius: 14px; background: var(--accent); color: #fff; font-weight: 700; font-size: 15px; }
        .tabbar { position: fixed; bottom: 0; left: 0; right: 0; display: flex; background: var(--card); border-top: 1px solid rgba(255,255,255,.06); padding: 6px 0 calc(6px + env(safe-area-inset-bottom)); }
        .tab { flex: 1; text-align: center; font-size: 10px; color: var(--hint); cursor: pointer; }
        .tab.active { color: var(--accent); font-weight: 700; }
        .task { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,.06); }
    </style>
</head>
<body>
<div class="page">
    <div class="card">
        <div style="font-size:13px;color:var(--hint)">Salom, <span id="name">—</span></div>
        <div class="balance" id="balance">0 🪙</div>
        <div style="font-size:12px;color:var(--hint)">🔥 <span id="streak">0</span> kun seriya</div>
    </div>

    <div id="tasks" class="card">
        <h3 style="margin-bottom:8px">🌱 Bugungi eko vazifalar</h3>
        <div id="task-list"><div style="color:var(--hint);padding:12px 0">Yuklanmoqda…</div></div>
    </div>

    <button class="btn" onclick="completeEco()" id="complete-btn" style="margin-top:8px">✓ Eng oson vazifani bajarish</button>
</div>

<div class="tabbar">
    <div class="tab active" data-tab="home">🏠<br>Bosh</div>
    <div class="tab" data-tab="eco">🌱<br>Eko</div>
    <div class="tab" data-tab="jobs">💼<br>Ish</div>
</div>

<script>
const tg = window.Telegram?.WebApp;
tg?.ready();
tg?.expand();

const BASE = '/api/v1/tg';
let currentTask = null;

async function api(path, options = {}) {
    const res = await fetch(BASE + path, {
        ...options,
        headers: {
            'Content-Type': 'application/json',
            'X-Telegram-Init-Data': tg?.initData ?? '',
            ...(options.headers ?? {}),
        },
    });
    return res.json();
}

async function loadHome() {
    const me = await api('/me');
    document.getElementById('name').textContent = me.user?.name ?? 'mehmon';
    document.getElementById('balance').textContent = (me.user?.greencoin_balance ?? 0) + ' 🪙';
    document.getElementById('streak').textContent = me.user?.login_streak ?? 0;

    const data = await api('/eco-tasks');
    const list = document.getElementById('task-list');
    list.innerHTML = '';
    (data.tasks ?? []).slice(0, 5).forEach((task) => {
        const row = document.createElement('div');
        row.className = 'task';
        row.innerHTML = `<span>${task.title}</span><b style="color:#4ade80">+${task.reward}</b>`;
        list.appendChild(row);
        if (!currentTask) currentTask = task;
    });
}

async function completeEco() {
    if (!currentTask) return;
    await api('/eco-complete', {
        method: 'POST',
        body: JSON.stringify({ eco_task_id: currentTask.id, proof_text: 'Telegram Mini App orqali bajarildi' }),
    });
    alert('Arizangiz yuborildi!');
    loadHome();
}

document.querySelectorAll('.tab').forEach((tab) => tab.addEventListener('click', () => {
    document.querySelectorAll('.tab').forEach((t) => t.classList.remove('active'));
    tab.classList.add('active');
    if (tab.dataset.tab === 'jobs') document.getElementById('tasks').innerHTML = '<h3>💼 Ish e’lonlari</h3><p style="color:var(--hint);font-size:13px">To‘liq ro‘yxat saytda: ambarella.uz/jobs</p>';
    if (tab.dataset.tab === 'eco') loadHome();
}));

loadHome();
</script>
</body>
</html>
