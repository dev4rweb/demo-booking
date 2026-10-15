# Bookly — booking & subscription SaaS demo

Portfolio demo by **Vladislav**, full-stack Laravel + Vue developer.

Live: https://demo-booking.dev4rweb.com  
Repo: https://github.com/dev4rweb/demo-booking

Multi-tenant scheduling for salons, studios, clinics and tutors: public booking, staff calendars, Stripe plans (with demo-mode fallback) and Telegram alerts.

## Stack

Laravel 12 (PHP 8.3), Vue 3, Inertia, Tailwind CSS, MySQL/MariaDB (SQLite for tests), Pest, Playwright, Laravel Cashier (TEST).

## Demo logins

| Role | Email | Password |
| --- | --- | --- |
| Owner (Luna Beauty Studio) | owner@demo.local | demo123456 |
| Staff | staff@demo.local | demo123456 |
| Platform admin | admin@demo.local | demo123456 |

Public studios: `/b/luna-beauty`, `/b/peak-fitness`

## Local setup

```bash
cp .env.example .env
# sqlite is fine locally:
# DB_CONNECTION=sqlite
php artisan key:generate
touch database/database.sqlite
php artisan migrate --force
php artisan db:seed --class=DemoSeeder --force
php artisan storage:link
npm ci --ignore-scripts
npm run build
php artisan serve
```

`DemoSeeder` is idempotent and is **not** called from `DatabaseSeeder`.

## Hostinger deploy

Document root is the **project root**. Root `.htaccess` routes into `public/`. `public/build` is committed (the host cannot run Vite).

PHP 8.3 binary: `/opt/alt/php83/usr/bin/php`

```bash
cd /home/USER/domains/demo-booking.dev4rweb.com
/opt/alt/php83/usr/bin/php artisan key:generate --force
/opt/alt/php83/usr/bin/php artisan migrate --force
/opt/alt/php83/usr/bin/php artisan db:seed --class=DemoSeeder --force
/opt/alt/php83/usr/bin/php artisan storage:link
/opt/alt/php83/usr/bin/php artisan config:cache
/opt/alt/php83/usr/bin/php artisan route:cache
/opt/alt/php83/usr/bin/php artisan view:cache
```

Cron (once per minute):

```
* * * * * /opt/alt/php83/usr/bin/php /home/USER/domains/demo-booking.dev4rweb.com/artisan schedule:run >> /dev/null 2>&1
```

Queue is `sync` by default. If you switch to `database`, the scheduler runs `queue:work --stop-when-empty`.

Stripe and Telegram are optional: empty `STRIPE_KEY` / `STRIPE_SECRET` shows demo mode; empty `TELEGRAM_BOT_TOKEN` is a no-op.

## Tests

```bash
php artisan test
npx playwright test
```
