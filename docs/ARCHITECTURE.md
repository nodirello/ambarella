# AMBARELLA — Arxitektura hujjati

> Ushbu hujjat kod arxitekturasini, qatlamlarni va asosiy dizayn qarorlarini tavsiflaydi.

---

## 1. Umumiy ko‘rinish

```
Browser / PWA ──► Laravel 12 (Blade + Tailwind) ──► MySQL 8 / SQLite
      │                       │
      │                       ├── REST API /api/v1 ──► Mobile / 3rd party
      ▼                       │
Telegram Bot ◄──webhook───────┘
Telegram Mini App ◄──initData──┘
```

**Texnologiyalar:**
- Backend: Laravel 12.64, PHP 8.4+
- Frontend: Blade + Tailwind CSS 4 + Vite 7
- DB: MySQL 8 (prod) / SQLite (dev)
- Integratsiya: Telegram Bot API, Telegram Mini App
- PWA: manifest + service worker

---

## 2. Qatlamlar va mas’uliyat

| Qatlam | Papka | Mas’uliyat |
|---|---|---|
| HTTP | `app/Http/Controllers` | So‘rovni qabul qilish, servisni chaqirish, view qaytarish |
| Validatsiya | `app/Http/Requests` | Barcha kirish ma’lumotlarini tekshirish (FormRequest) |
| Biznes mantiq | `app/Services` | GreenCoin, Referral, TOTP, Telegram, Sertifikat, CSV… |
| Ma’lumotlar | `app/Models` | Eloquent modellar, casts, scopes, relations |
| Xavfsizlik | `app/Http/Middleware` | Ban, 2FA, admin, IP bloklash, security headers, til |
| Enum | `app/Enums` | Rol, holat, valyuta turlari (type-safe) |
| UI | `resources/views` | Blade + Tailwind komponentlar (`components/ui/*`) |

**Qoida:** Controller’da faqat so‘rovni bog‘lash va javob qaytarish. Murakkab mantiq (transfer, mukofot, verifikatsiya) — servislarda. Takroriy kod — FormRequest xususiyatlarida.

---

## 3. Muhim dizayn qarorlari

### 3.1 GreenCoin — audit-proof ledger
- Har bir harakat `greencoin_transactions` jadvalida **`balance_after`** bilan yoziladi.
- Balans `SELECT ... FOR UPDATE` (row-lock) ichida yangilanadi — `DB::transaction` garantiyasi.
- `GreenCoinService` — yagona kirish nuqtasi: `credit()`, `debit()`, `transfer()`, `adjust()`.
- Tranzaksiya salbiy balansni qat’iy rad etadi (`RuntimeException`).
- Modul enum bilan tip-safety: `CoinTransactionType::Earned`.

### 3.2 Referal — idempotent bonus
- Bonus **faqat bir marta**, profil to‘ldirilganda (`UserObserver::updated`).
- `referral_rewarded` bayrog‘i + row-lock → ikki marta to‘lov imkonsiz.
- Ikkala tomon ham `+50` oladi.

### 3.3 2FA — TOTP (RFC 6238)
- Dependency-free `TotpService`: SHA1, 30s oyna, ±1 qadam driftga tolerant.
- Secret — base32; zaxira kodlar **hashlangan** holda saqlanadi.
- `RequireTwoFactor` middleware: admin panel va muhim amallar uchun majburiy.

### 3.4 API autentifikatsiya
- Sanctum’siz — `api_tokens` jadvalida **hashlangan** Bearer tokenlar.
- `AuthenticateApiToken` middleware: `Authorization: Bearer <token>` → user resolver.
- Tokenlar muddatli va bekor qilinadigan; `/api/v1/token` orqali beriladi.

### 3.5 Telegram
- Webhook — `X-Telegram-Bot-Api-Secret-Token` header tekshiruvi bilan himoyalangan.
- Mini App — `initData` HMAC-SHA256 (Bot API hujjati bo‘yicha) validatsiya.
- Barcha outbound API chaqiruvlar `TelegramService` orqali; bot ishlamasa sayt ishlashda davom etadi (try/catch).

### 3.6 Xavfsizlik
- CSP, HSTS, X-Frame-Options, Referrer-Policy, Permissions-Policy — global `SecurityHeaders`.
- Parol: BCrypt(12) + mustahkamlik talablari (katta/kichik/raqam).
- Email verifikatsiya (signed URL), ban tizimi, IP bloklash (10 muvaffaqiyatsiz → 30 daq blok).
- Blade `{{ }}` escape + `strip_tags` — XSS’ga qarshi asosiy choralar.
- `Model::preventLazyLoading` (non-prod) — N+1 muammolarini erta ushlaydi.

### 3.7 Moderatsiya
- Blog maqolalar, eko arizalar va izohlar yagona navbatda (`/admin/moderation`).
- Eko arizalar tasdiqlangach GreenCoin avtomatik beriladi.

---

## 4. Ma’lumotlar bazasi sxemasi (asosiy guruhlar)

| Guruh | Jadvallar |
|---|---|
| Auth | users, sessions, api_tokens, password_reset_tokens |
| Bandlik | job_listings, job_applications, business_profiles, business_products |
| Eko/GreenCoin | eco_tasks, eco_task_completions, greencoin_transactions, daily_tasks, habits, habit_logs |
| Ta’lim | courses, course_user, mentors, mentor_requests, mentorships, mentor_tasks |
| Kontent | news, blog_posts, comments, reactions, favorites |
| Jamiyat | platform_events, event_registrations, startups, startup_votes, volunteer_projects, volunteer_applications, challenges, challenge_participants, polls, poll_options, poll_votes, forum_topics, forum_replies, notes |
| Boshqaruv | achievements, user_achievements, activity_logs, announcements, banners, faqs, success_stories, seo_settings, contact_messages |
| Telegram | telegram_users, telegram_bot_tasks, telegram_bot_submissions, telegram_referrals |

Barcha jadvallarda: foreign keys (cascade/nullOnDelete), index’lar, unique constraint’lar (masalan, eko vazifa kuniga bir marta).

---

## 5. Frontend tuzilishi

- `resources/css/app.css` — Tailwind 4 `@theme` (ranglar, animatsiyalar) + reveal effektlar.
- `resources/js/app.js` — tema (dark/light), mobil menyu, reveal-observer, toast, `ambarellaFetch` (CSRF avtomatik), reaksiya tugmalari.
- `resources/views/components/ui/*` — `button`, `card`, `input`, `select`, `textarea`, `badge`, `stat`, `empty`, `pagination`.
- `resources/views/components/app/*` — header navigatsiya, page-header.

---

## 6. Qo‘shimcha tizimlar

| Tizim | Joylashuv |
|---|---|
| Sitemap | `SitemapController` — 14 statik + 3 dinamik manba |
| Health check | `GET /up` — DB + cache holati |
| Live count | `GET /api/live-count` — faol sessiyalar |
| Export | `CsvExporter` — Excel-mos BOM’li CSV |
| Schedule | `routes/console.php` — backup, webhook, yutuqlar, cache, session GC |

---

## 7. Standartlarga rioya

- **PSR-12 / Laravel Pint** uslubi, `declare(strict_types=1)`.
- FormRequest barcha kirish uchun yagona validatsiya manbai.
- Enum’lar string qiymatli — DB saqlanadigan qiymatlar stabil.
- Testlar: `tests/Unit` (TOTP, GreenCoin, Referral), `tests/Feature` (Auth, Job, Eco, API, Telegram webhook).
