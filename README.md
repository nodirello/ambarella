# AMBARELLA.UZ

**O‘zbekiston yoshlari uchun ko‘p funksiyali platforma** — bandlik, ekologiya, ta’lim, mentorlik, startup, biznes va jamiyat modullari bitta ekotizimda.

Laravel 12 · PHP 8.4+ · MySQL 8 / SQLite · Blade + Tailwind CSS 4 + Vite · Telegram Bot va Mini App · PWA

---

## ✨ Imkoniyatlar

| Modul | Tavsif |
|---|---|
| 💼 Bandlik | Ish e’lonlari, filtrlar, ariza tizimi, biznes paneli, analitika |
| 🌱 Ekologiya | Eko vazifalar, dalil yuborish, **GreenCoin** mukofot (moderatsiya bilan) |
| 🪙 GreenCoin | Audit-proof ledger, transfer, reyting, bonuslar |
| 🎓 Academy | Kurslar, yozilish, taraqqiyot, **sertifikat** (tekshiriladigan) |
| 🧭 Mentorlik | Mentorlar, 1:1 mentorship, vazifalar |
| 🚀 Startuplar | Loyiha joylash, ovoz berish |
| 🤝 Ko‘ngillilik | Loyihalar va ariza |
| 📅 Tadbirlar / 🏆 Musobaqalar | Ro‘yxatdan o‘tish, katnashish |
| 💬 Forum, 📝 Blog, 🗳️ So‘rovnomalar | Jamiyat + **moderatsiya** |
| 🧠 Psixologiya | O‘z-o‘zini baholash testlari |
| 🤖 Telegram | Bot (webhook + secret), Mini App (HMAC initData), kod bilan kirish |
| 🔐 Xavfsizlik | Email verifikatsiya, **TOTP 2FA**, zaxira kodlar, IP bloklash, CSP/HSTS, activity log |
| 📱 PWA | Manifest, service worker, offline |
| 🌐 i18n | UZ / RU / EN |
| 🔌 REST API | `/api/v1` — ommaviy + Bearer token |

---

## 🚀 Ishga tushirish

### Talablar
- PHP **8.4+** (kengaytmalar: `pdo_mysql | pdo_sqlite`, `gd`, `mbstring`, `xml`, `zip`, `curl`, `openssl`)
- Composer 2
- MySQL 8+ (yoki SQLite — local uchun standart)
- Node 20+ va npm

### Local setup
```bash
composer install
cp .env.example .env
php artisan key:generate

# SQLite (standart)
touch database/database.sqlite

# yoki MySQL — .env da DB_* qiymatlarini to‘ldiring

php artisan migrate --seed
npm install && npm run build
php artisan serve
# → http://127.0.0.1:8000
```

**Demo hisoblar:**
| Rol | Email | Parol |
|---|---|---|
| Admin | `admin@ambarella.uz` | `Admin12345` |
| Foydalanuvchi | `demo@ambarella.uz` | `Demo12345` |

### Testlar
```bash
composer install   # dev qo‘shimchalari (phpunit, faker…)
php artisan test
```

---

## 🧱 Arxitektura

```
app/
├── Console/Commands/          # artisan buyruqlar (backup, webhook, yutuqlar)
├── Enums/                     # Role, JobType, ModerationStatus, Locale…
├── Http/
│   ├── Controllers/           # 60+ controller: Auth/, Admin/, Business/, Api/, Telegram/
│   ├── Middleware/            # 7 ta middleware (xavfsizlik, til, API token)
│   └── Requests/              # FormRequest validatsiya
├── Models/                    # 37 model (casts, scopes, relations)
├── Observers/                 # UserObserver (referral kod + bonus)
├── Providers/
├── Services/                  # Biznes mantiq: GreenCoin, Referral, TOTP, Telegram…
└── Traits/                    # TracksViews, LogsActivity

routes/                        # web.php · api.php · admin.php · console.php
database/
├── migrations/                # 14 ta to‘g‘ri sxema (FK, index, unique)
├── factories/                 # deterministik demo ma’lumotlar
└── seeders/
resources/
├── css/app.css                # Tailwind 4 dizayn tizimi
├── js/app.js                  # tema, toast, reveal, fetch helper
└── views/                     # 60+ Blade view, komponentlar bilan
config/ · lang/{uz,ru,en}/ · public/ (PWA) · tests/ (Feature + Unit)
```

**Qatlamlar qoidasi:** Controller → FormRequest (validatsiya) → Service (biznes mantiq) → Model (ma’lumotlar). Controller’larda mantiq minimal, barcha takroriy amallar servisda.

### Muhim dizayn qarorlari
- **GreenCoin** — `greencoin_transactions` da har bir harakat `balance_after` bilan saqlanadi (audit-proof ledger); transfer va mukofotlar row-lock `DB::transaction` ichida.
- **Referal** — bonus faqat bir marta, profil to‘ldirilganda (idempotent).
- **2FA** — dependency-free RFC 6238 TOTP + hashlangan zaxira kodlar.
- **API auth** — hashlangan Bearer token (`api_tokens`), Sanctum’siz, lekin to‘liq testlangan.
- **Moderatsiya** — blog, eko arizalar, izohlar yagona admin navbatida.
- **Telegram** — webhook `X-Telegram-Bot-Api-Secret-Token` bilan himoyalangan; Mini App initData HMAC-SHA256 tekshiruvidan o‘tadi.
- **SQL injection/XSS** — Eloquent + `strip_tags` + Blade `{{ }}` escape; CSP headеr’lar bilan mustahkamlangan.

---

## 📊 Admin panel

`/admin` — auth + admin rol + 2FA talab qilinadi. Imkoniyatlar:
- Foydalanuvchilar (bloklash, rol), **moderatsiya navbati**, ish e’lonlari va arizalar
- GreenCoin balans tuzatish, Telegram bot (vazifalar, arizalar, broadcast, webhook)
- Kontent CRUD (11 tur — bitta umumiy controller), murojaatlar, activity log, CSV export

---

## 🤖 Telegram

```bash
# 1) .env ga token va secret qo‘ying
TELEGRAM_BOT_TOKEN=...
TELEGRAM_WEBHOOK_SECRET=<32+ belgi>

# 2) Webhook o‘rnatish
php artisan telegram:set-webhook
```

Bot buyruqlari: `/start`, `/menu`. `/tg-app` — Telegram Mini App (initData orqali kirish).

---

## 🗂 Hujjatlar

- `docs/ARCHITECTURE.md` — tizim dizayni
- `docs/DEPLOYMENT.md` — production deploy (Nginx + MySQL)
- `docs/LEGACY-ANALYSIS.md` — eski loyiha tahlili (arxiv manba)

## 📄 Litsenziya
MIT — SADAF DEV
