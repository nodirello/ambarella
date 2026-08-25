@extends('layouts.app')

@section('title', 'Sozlamalar — AMBARELLA')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <x-app.page-header title="⚙️ Sozlamalar" description="Xavfsizlik, API va hisob boshqaruvi." />

    <div class="mt-8 grid gap-6">
        {{-- 2FA --}}
        <x-ui.card title="Ikki bosqichli autentifikatsiya (TOTP)">
            @if ($user->two_factor_secret)
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-emerald-500">✅ 2FA yoqilgan — authenticator ilovangizdagi kodlar talab qilinadi.</p>
                    <form method="POST" action="{{ route('settings.2fa.disable') }}">
                        @csrf
                        <x-ui.button variant="danger">O‘chirish</x-ui.button>
                    </form>
                </div>
                @if (session('otpauth_uri'))
                    <div class="mt-4 rounded-2xl border border-blue-500/30 p-4 text-sm">
                        <p class="font-semibold">Authenticator’ga qo‘shish:</p>
                        <p class="mt-2 break-all text-xs text-slate-400">{{ session('otpauth_uri') }}</p>
                    </div>
                @endif
            @else
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-slate-500 dark:text-slate-400">TOTP autentifikatsiya — Google Authenticator, Aegis va boshqalar bilan mos.</p>
                    <form method="POST" action="{{ route('settings.2fa.enable') }}">
                        @csrf
                        <x-ui.button>Yoqish</x-ui.button>
                    </form>
                </div>
            @endif
        </x-ui.card>

        {{-- API tokens --}}
        <x-ui.card title="API tokenlar">
            <form id="token-form" class="flex gap-3">
                <input id="token-name" placeholder="Token nomi (masalan: mobil-ilova)" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900">
                <x-ui.button type="button" onclick="createToken()">Yaratish</x-ui.button>
            </form>
            <div id="token-result" class="mt-3 hidden rounded-xl border border-emerald-500/30 bg-emerald-500/5 p-4 text-sm">
                <p class="font-semibold text-emerald-500">Yangi token (faqat bir marta ko‘rinadi):</p>
                <p id="token-value" class="mt-1 break-all font-mono text-xs"></p>
            </div>
            <div class="mt-5 grid gap-2">
                @forelse ($tokens as $token)
                    <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm dark:border-slate-700">
                        <div>
                            <p class="font-medium">{{ $token->name }}</p>
                            <p class="text-xs text-slate-400">Yaratilgan: {{ $token->created_at->format('d.m.Y') }} · Oxirgi foydalanish: {{ $token->last_used_at?->diffForHumans() ?? '—' }}</p>
                        </div>
                        <form method="POST" action="{{ route('settings.tokens.revoke', $token) }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-xs font-semibold text-rose-500">Bekor qilish</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Hozircha tokenlar yo‘q.</p>
                @endforelse
            </div>
        </x-ui.card>

        {{-- Data export --}}
        <x-ui.card title="Ma’lumotlar">
            <div class="flex flex-wrap gap-3">
                <x-ui.button :href="route('settings.export')" variant="secondary">📥 CSV export</x-ui.button>
                <form method="POST" onsubmit="return confirm('Hisobingiz butunlay o‘chiriladi. Davom etasizmi?')" action="{{ route('settings.account.delete') }}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="confirm" value="DELETE">
                    <x-ui.button variant="danger">Hisobni o‘chirish</x-ui.button>
                </form>
            </div>
        </x-ui.card>
    </div>
</div>

<script>
async function createToken() {
    const name = document.getElementById('token-name').value.trim();
    if (!name) return alert('Token nomini kiriting');
    const res = await window.ambarellaFetch('{{ route('settings.tokens.create') }}', {
        method: 'POST', body: JSON.stringify({ name }), headers: { 'Content-Type': 'application/json' },
    });
    if (res.ok) {
        const data = await res.json();
        document.getElementById('token-value').textContent = data.token;
        document.getElementById('token-result').classList.remove('hidden');
        document.getElementById('token-name').value = '';
        window.showToast('Token yaratildi');
    }
}
</script>
@endsection
