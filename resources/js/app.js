import './bootstrap';

// ── Theme ──────────────────────────────────────────────────────────────────
const THEME_KEY = 'ambarella.theme';

function applyTheme(theme) {
    document.documentElement.classList.toggle('dark', theme === 'dark');
    localStorage.setItem(THEME_KEY, theme);
    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
        btn.textContent = theme === 'dark' ? '☀️' : '🌙';
    });
}

const savedTheme = localStorage.getItem(THEME_KEY) ?? 'dark';
applyTheme(savedTheme);

document.addEventListener('click', (e) => {
    const toggle = e.target.closest('[data-theme-toggle]');
    if (toggle) {
        const next = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
        applyTheme(next);
    }
});

// ── Mobile menu ─────────────────────────────────────────────────────────────
document.addEventListener('click', (e) => {
    const burger = e.target.closest('[data-menu-toggle]');
    const panel = document.getElementById('mobile-menu');
    if (burger && panel) {
        panel.classList.toggle('hidden');
    }
    if (e.target.closest('#mobile-menu a')) {
        panel?.classList.add('hidden');
    }
});

// ── Reveal on scroll ────────────────────────────────────────────────────────
const observer = new IntersectionObserver(
    (entries) => entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        }
    }),
    { threshold: 0.08, rootMargin: '0px 0px -32px 0px' }
);

document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));

// ── Global fetch helper ─────────────────────────────────────────────────────
window.ambarellaFetch = async function ambarellaFetch(url, options = {}) {
    const headers = {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
        Accept: 'application/json',
        ...(options.headers ?? {}),
    };

    const response = await fetch(url, { ...options, headers });

    if (response.status === 419) {
        window.location.reload();
        throw new Error('Session expired');
    }

    return response;
};

// ── Toast helper ────────────────────────────────────────────────────────────
window.showToast = function showToast(message, type = 'success') {
    const colors = { success: 'bg-emerald-600', error: 'bg-rose-600', info: 'bg-sky-600' };
    const toast = document.createElement('div');
    toast.className = `fixed bottom-6 left-1/2 -translate-x-1/2 z-[100] ${colors[type] ?? colors.info} text-white text-sm font-medium px-5 py-3 rounded-xl shadow-2xl animate-fade-up`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.transition = 'opacity .4s';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 400);
    }, 3200);
};

// ── Flash messages → toasts ─────────────────────────────────────────────────
document.querySelectorAll('[data-flash]').forEach((el) => {
    window.showToast(el.dataset.flash, el.dataset.flashType ?? 'success');
});

// ── Reaction buttons ────────────────────────────────────────────────────────
document.querySelectorAll('[data-reaction]').forEach((button) => {
    button.addEventListener('click', async () => {
        const { type, id, reaction } = button.dataset;
        try {
            const res = await window.ambarellaFetch('/reactions/toggle', {
                method: 'POST',
                body: JSON.stringify({ type, id, reaction }),
                headers: { 'Content-Type': 'application/json' },
            });
            if (res.ok) {
                const data = await res.json();
                button.nextElementSibling && (button.nextElementSibling.textContent = data.count);
            }
        } catch {
            /* silently ignore */
        }
    });
});
