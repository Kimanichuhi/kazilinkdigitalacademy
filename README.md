# Kazilink Digital Academy

Course-enrollment platform for Kazilink Digital Academy — programs, cohorts, bookings, payments, and a full content/marketing admin. Originally a Next.js 13.5 + Supabase app (preserved for reference under [`_legacy-nextjs/`](_legacy-nextjs)), rebuilt here as a Laravel 11 modular monolith so it can run on ordinary cPanel/LiteSpeed shared hosting — no Redis, no websockets, no Node process, no Docker in production.

## Stack

- **Laravel 11** / PHP 8.2+, MariaDB/MySQL
- **nwidart/laravel-modules** — 11 modules under [`Modules/`](Modules): Core, Auth, User, Academy, Booking, Payment, Cms, Marketing, Notification, Audit, Admin
- **Livewire 3** + Alpine.js (bundled by Livewire) for interactivity, Laravel Breeze (Blade) for auth scaffolding
- **spatie/laravel-permission** — 8 roles: `super_admin`, `admin`, `trainer`, `content_manager`, `finance`, `marketing`, `support`, `student`
- Database, session, cache, and queue all run on the `database`/`mysql` driver — nothing needs Redis or a persistent worker
- M-Pesa Daraja STK Push, static bank-transfer details, and a Stripe stub for payments
- ApexCharts for admin analytics
- **Laravel Sanctum** — token auth for the REST API (mobile app / admin app / future integrations), see [API](#api) below

## API

A versioned, token-authenticated REST API lives alongside the Livewire web app, for mobile/admin clients and future integrations. Routes are split per module (`Modules/*/routes/api.php`), same as every other route file in this app — there's no separate central API layer.

| Endpoint | Auth | Notes |
|---|---|---|
| `POST /api/v1/auth/login` | — | Issues a Sanctum token. `POST /api/v1/auth/logout` and `GET /api/v1/auth/user` need the token. |
| `GET /api/v1/programs`, `/programs/{slug}`, `/programs/{slug}/cohorts` | — | Same paginated/filtered data the public `/programs` page uses. |
| `GET /api/v1/resources`, `/resources/{id}` | — | Same as the public `/resources` page. `POST /resources/{id}/purchase` needs a token. |
| `POST /api/v1/bookings`, `GET /bookings`, `GET /bookings/{id}`, `POST /bookings/{id}/pay`, `POST /bookings/{id}/confirm` | token | Mirrors the public booking wizard's three real steps (create → pay → confirm) via the same `BookingCreationService` the wizard itself calls — no separate/divergent booking logic. Unlike the web wizard, the API does not support guest bookings. |
| `GET /api/v1/payments/{checkoutRequestId}/status` | token | Polls an M-Pesa STK push, same as the web `MpesaStatusPoller` component. |

Send the token from `/auth/login` as `Authorization: Bearer <token>`. A booking/purchase/payment endpoint belonging to a different user returns `404`, not `403` — it doesn't confirm whether the resource exists.

Every other module (`Marketing`, `Notification`, `Audit`, `User`, `Admin`, `Cms`'s own generic `CmsController`) still has the original nwidart-scaffolded `routes/api.php` — routes registered, but pointing at empty stub controllers. They're harmless (Sanctum being installed means they no longer error on an undefined guard, they just 404/return nothing meaningful) and out of scope for now; extend them the same way if a client ever needs them.

## Module architecture

Each module under `Modules/<Name>/app/` follows the same shape: `Http/Controllers`, `Livewire`, `Models`, `Enums`, `Services`, `Actions`, `Policies`, `Events`, `Listeners`, `Contracts`, `Providers`. The rule that keeps modules independently disable-able (`Modules/<Name>/module.json` → `modules_statuses.json`): **a module never imports another module's Eloquent model directly.** Cross-module reads go through a `Contract` interface + `Service` implementation bound in that module's own `ServiceProvider`; cross-module writes/side-effects happen via queued event listeners (see `Modules/Payment|Notification|Audit/app/Listeners`). Database foreign keys are still used for referential integrity — that's a schema concern, not a code-coupling one.

## Local setup

Requires PHP 8.2+, Composer, MySQL 8/MariaDB, and Node 18+ (build-time only — see [Deployment](#deployment) for why it's not needed in production). Local dev on this machine runs MySQL 8.4 as a Windows service (`MySQL84`); nothing in the codebase is engine-specific, so MariaDB works identically if you prefer it.

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Create a database matching your `.env` (`DB_DATABASE`/`DB_USERNAME`/`DB_PASSWORD`), then:

```bash
php artisan migrate --seed
php artisan storage:link
```

Seeded demo accounts (password `password` for all): `super_admin@kazilink.academy`, `admin@kazilink.academy`, `trainer@kazilink.academy`, `content_manager@kazilink.academy`, `finance@kazilink.academy`, `marketing@kazilink.academy`, `support@kazilink.academy`, `student@kazilink.academy`.

Run everything (PHP server + queue listener + Vite dev server + log tailer) with:

```bash
composer run dev
```

> **Windows note:** if `php artisan serve` alone 500s on every route, it's a known local quirk — always use `--no-reload` (already baked into the `dev` script above and into `composer.json`).

## Environment variables

All variables live in `.env.example` with inline comments. Groups worth knowing about:

| Group | Vars | Notes |
|---|---|---|
| Core | `APP_*`, `DB_*` | Standard Laravel/DB config |
| Session/Cache/Queue | `SESSION_DRIVER`, `CACHE_STORE`, `QUEUE_CONNECTION` | All set to `database` — shared-hosting safe, no Redis |
| Storage | `FILESYSTEM_DISK=public` | Uploaded images (programs, blog, ads, avatars, etc.) go through the `public` disk; run `php artisan storage:link` after every fresh deploy |
| M-Pesa | `MPESA_*` | Daraja STK Push credentials. Blank by default — the Payment module degrades gracefully (booking stays `awaiting_payment`, nothing 500s) until real sandbox/production credentials are added. `MPESA_CALLBACK_URL` must be a publicly reachable HTTPS URL for Safaricom to call back to (won't work against `localhost`) |
| Bank | `BANK_*` | Static bank-transfer details shown to students — no integration, just config |
| Stripe | `STRIPE_*` | Stub only, mirrors the source app — no real Stripe SDK call exists anywhere in the codebase |
| Clarity | `CLARITY_PROJECT_ID` | Microsoft Clarity session replay. Blank by default — the tracking snippet (`<x-core::clarity />`, included in the public/admin/auth layouts) simply isn't rendered until this is set. Sensitive fields (booking phone/ID number/payment reference, admin PII screens) carry `data-clarity-mask="true"` regardless |

## Payments

- **M-Pesa**: full Daraja STK Push pipeline (`Modules/Payment`) — OAuth token, STK push, callback handler at `POST /payments/mpesa/callback`, and a `payment:reconcile-mpesa` scheduled command that actively queries Daraja for any STK push whose callback never arrived (important since a dev/staging box often isn't reachable by Safaricom at all).
- **Bank transfer**: student enters a reference code manually after paying externally; an admin/finance user confirms it via the Bookings screen.
- **Stripe**: intentionally inert, matching the source.

## Scheduled tasks & queue (shared hosting)

Shared hosting gives you **one cron line** and no daemon/supervisor. Point it at Laravel's scheduler:

```
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

Everything else is defined in [`routes/console.php`](routes/console.php) and runs off that single line:

- `queue:work --stop-when-empty --max-time=50` — every minute, processes whatever's queued (booking-created side effects, M-Pesa STK pushes, notifications, audit logs) and exits cleanly before the next tick
- `payment:reconcile-mpesa` — every 5 minutes, catches any M-Pesa transaction whose callback never arrived

No `queue:work` daemon, no Supervisor config, no Redis — just cron.

## Deployment (cPanel / LiteSpeed shared hosting)

1. **Build assets locally** (or in CI) before deploying — Node is a build-time dependency only:
   ```bash
   npm install
   npm run build
   ```
   This produces `public/build/`. Upload it to the server alongside the rest of the app (it's gitignored on purpose — don't rely on git alone to ship it; upload it via your deploy script/SFTP/zip).
2. **Document root**: cPanel serves from the account's `public_html`, but Laravel's web root is `public/`. Either:
   - Point the domain's document root directly at `<app>/public` (cPanel → Domains → set document root), or
   - If you can't change the document root, copy `public/`'s contents into `public_html` and edit `public_html/index.php`'s two `require`/`__DIR__` paths to point at the real `vendor/autoload.php` and `bootstrap/app.php` locations outside the web root.
3. Upload everything **except** `node_modules` — it's never needed on the server.
4. On the server: `composer install --no-dev --optimize-autoloader`, copy `.env` with production values, `php artisan key:generate` (if not already set), `php artisan migrate --force`, `php artisan storage:link`.
5. Cache for production: `php artisan config:cache && php artisan route:cache && php artisan view:cache`.
6. Add the single cron line above (cPanel → Cron Jobs).
7. Set `APP_ENV=production`, `APP_DEBUG=false`, and a real `MPESA_CALLBACK_URL` pointing at the live HTTPS domain before enabling M-Pesa in production.

### Production PHP checklist (OPcache)

`public/.user.ini` already carries the OPcache settings a shared-hosting account can set per-app (`opcache.validate_timestamps`/`opcache.revalidate_freq`) — see the comments in that file for the one-line change to make once you have a deploy pipeline that always ships a full fresh copy of the app. The rest of OPcache is a `PHP_INI_SYSTEM` setting the *host* controls, not this repo — set it via cPanel's "Select PHP Version" → Options (or the MultiPHP INI Editor), whichever your host exposes:

| Setting | Recommended value |
|---|---|
| `opcache.enable` | `On` |
| `opcache.memory_consumption` | `192` (MB) |
| `opcache.max_accelerated_files` | `10000` |
| `opcache.interned_strings_buffer` | `16` (MB) |

Most cPanel hosts already ship OPcache enabled with sane defaults — this table is only needed if a host check (`phpinfo()` or `php -i | grep opcache.enable`) shows it off.

## Testing a module's isolation

To verify a module can be disabled without breaking the rest of the app (per the modular-monolith design goal), flip it off in `modules_statuses.json` and clear caches:

```bash
php artisan module:disable Marketing
php artisan optimize:clear
```

The homepage, booking flow, and admin dashboard continue to work; only Marketing-owned screens (Advertisements, CTAs, Stats) and the homepage's ad carousel/CTA sections go quiet. Re-enable with `php artisan module:enable Marketing`.
