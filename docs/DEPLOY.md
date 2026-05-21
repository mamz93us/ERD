# Deployment Guide — Adly Group Agency CRMS

> **Status: SKELETON.** This document gets filled in during Phase 14. Until then, only Phase 0 sanity items are documented here.

---

## Target Environment

- **Host**: WHM/cPanel AlmaLinux server (same infrastructure as owner's prior Laravel project at `t.samirgroup.net`)
- **PHP**: 8.2+ (selectable in cPanel)
- **Database**: MySQL 8 or MariaDB 10.4+ (whichever is offered by the production cPanel — to be confirmed before Phase 14)
- **Web server**: Apache with PHP-FPM (cPanel default)
- **Mail**: Amazon SES SMTP (already configured on WHM)
- **WhatsApp**: Green API REST (owner's account)
- **HTTPS**: AutoSSL via cPanel
- **Production domain**: TBD (likely `app.adlygroup.<tld>` or similar — owner to confirm)

## Required PHP Extensions (enable in cPanel selectable PHP)

`bcmath`, `intl`, `mbstring`, `gd`, `exif`, `fileinfo`, `mysqli`, `pdo_mysql`, `zip`, `curl`, `openssl`, `sodium`.

## Required Cron Jobs (configure via cPanel UI in Phase 14)

```
* * * * * cd /home/USER/app && php artisan schedule:run >> /dev/null 2>&1
* * * * * cd /home/USER/app && php artisan queue:work --stop-when-empty --max-time=55 >> /dev/null 2>&1
```

## Hard Constraints (from CLAUDE.md §4)

1. No long-running queue workers — cron-based only.
2. No root access — only what cPanel exposes.
3. Public folder pattern — app lives outside `public_html/`, contents of `public/` symlinked/copied in.
4. Hybrid local-build deploys — run `composer install --no-dev` and `npm run build` locally, upload `vendor/` and `public/build/`.
5. `storage/` and `bootstrap/cache/` → `0775`, owner = cPanel user.
6. Storage symlink: `php artisan storage:link` once.
7. Force HTTPS in `.htaccess` and `AppServiceProvider`.

## Local Development (Phase 0)

- Stack: XAMPP on Windows
  - PHP `C:\xampp\php\php.exe` (version 8.2.12)
  - MariaDB 10.4.32 (`C:\xampp\mysql\bin\`)
  - Apache from XAMPP (optional — `php artisan serve` works for the admin)
- Sodium extension enabled by uncommenting `extension=sodium` in `C:\xampp\php\php.ini` (done 2026-05-20).
- Composer 2.9.5 globally available.
- Project root: `c:\Users\MohamedZahran\Downloads\ERD\`.

## Production Build Checklist (Phase 14 — fill in)

- [ ] `composer install --no-dev --optimize-autoloader`
- [ ] `npm ci && npm run build`
- [ ] `.env.production` configured (no secrets in git)
- [ ] Upload `vendor/`, `public/build/`, app source to cPanel
- [ ] Configure document root → public folder pattern
- [ ] `php artisan key:generate` (production key, separate from dev)
- [ ] `php artisan migrate --force`
- [ ] `php artisan storage:link`
- [ ] `php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan event:cache`
- [ ] Seed initial branch + super_admin user
- [ ] Translator seeds final ar/en copy edits into `translations` table via admin
- [ ] Configure cron jobs (above)
- [ ] AutoSSL verified, force HTTPS
- [ ] Backup script scheduled (Phase 13 deliverable)
- [ ] Smoke: log in, create test booking, send test WhatsApp + email

---

*End of skeleton. Phase 14 will expand each section with exact cPanel UI steps and any owner-specific gotchas discovered during the dry-run deploy.*
