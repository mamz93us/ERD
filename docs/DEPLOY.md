# Deployment Guide — Adly Group Agency CRMS

> **Phase 14, staging dry-run.** Target: `erd.aladly-group.com` (admin + CRMS).
> The customer portal at the apex `aladly-group.com` ships in Phase 11 and is documented separately when that phase lands.

This is a **dry-run staging deploy** with only Phases 0–7 in the build (foundation → master data → fleet → pricing → trips → maintenance → compliance). Phases 8–13 (accounting, notifications, driver/customer portals, dashboards, hardening) build locally first, then redeploy.

---

## 0. Target Environment

| Concern | Value |
|---|---|
| Host | WHM/cPanel AlmaLinux (host TBD — owner to share) |
| Admin URL | `https://erd.aladly-group.com` |
| Customer URL (Phase 11+) | `https://aladly-group.com` |
| PHP | 8.2+ (selectable PHP in cPanel) |
| Database | MySQL 8 *or* MariaDB 10.4+ (whichever cPanel offers) |
| Web server | Apache + PHP-FPM (cPanel default) |
| Mail | Amazon SES SMTP (configured later in Phase 9) |
| WhatsApp | Green API REST (configured later in Phase 9) |
| HTTPS | AutoSSL via cPanel |
| Locale | `ar` default (RTL), `en` fallback |
| Timezone | UTC stored, `Africa/Cairo` displayed |

---

## 1. cPanel Setup (do these BEFORE upload)

### 1.1 Create the subdomain

cPanel → **Domains** → **Create a New Domain** (or **Subdomains** on older skins):

- Domain: `erd.aladly-group.com`
- Document Root: `/home/<cpaneluser>/erd.aladly-group.com` *(leave the default — we'll override the actual public folder below)*
- **Uncheck** "Share document root with main domain" if asked.

cPanel will auto-create the folder and an AutoSSL cert request will be queued.

### 1.2 Point DNS

If aladly-group.com nameservers point to this WHM:
- cPanel auto-creates an A record for `erd` — nothing to do.

If DNS is managed elsewhere (Cloudflare, registrar):
- Add an **A record**: `erd.aladly-group.com` → `<cPanel server IP>`
- Wait for DNS propagation (5–60 min).

### 1.3 Create the MySQL database

cPanel → **MySQL Databases**:

1. **Create New Database**: `<cpaneluser>_adly_crms`
2. **Create New User**: `<cpaneluser>_adly_app` (generate a strong password, save it)
3. **Add User to Database**: grant **ALL PRIVILEGES** (the deploy needs `CREATE`, `ALTER`, `DROP`, `TRIGGER`, plus normal CRUD)

> **Important:** the Phase 5 booking-overlap triggers require `TRIGGER` privilege. If cPanel doesn't expose it (some shared hosts hide it), open a ticket with the host before deploying — the migrations will fail without it.

Note the exact strings cPanel hands out (they always have your username prefix):
- DB name: `_____________________`
- DB user: `_____________________`
- DB pass: `_____________________`

### 1.4 Confirm required PHP extensions

cPanel → **Select PHP Version** → choose **8.2** (or 8.3 if available) → **Extensions** tab. Enable:

`bcmath`, `intl`, `mbstring`, `gd`, `exif`, `fileinfo`, `mysqli`, `pdo_mysql`, `zip`, `curl`, `openssl`, `sodium`

Click **Save** at the bottom.

---

## 2. Local Build (on your Windows dev machine)

These steps build a deployment-ready bundle from the current branch. Run from a **fresh clone** in a temp folder so they don't disturb your dev install.

```powershell
# In a fresh shell, anywhere outside the working ERD/ dir
cd $env:USERPROFILE\Downloads
git clone https://github.com/mamz93us/ERD.git ERD-deploy
cd ERD-deploy
git checkout phase-07-compliance     # or the branch you want to ship

# Composer prod install (no dev deps, optimized autoload)
composer install --no-dev --optimize-autoloader --prefer-dist

# Frontend assets
npm ci
npm run build

# Publish Filament v5 assets (writes into public/css/filament, public/js/filament, public/fonts/filament — these are gitignored)
php artisan filament:assets
```

After these complete, the `ERD-deploy/` folder is ready to upload.

---

## 3. The "public folder pattern" on cPanel

cPanel serves files from `~/public_html/` (or the subdomain's document root). Laravel's `public/` folder is where requests should land, but the rest of the framework (`app/`, `bootstrap/`, etc.) **must not be web-accessible**.

We solve this by uploading the app **outside** the document root, then symlinking the `public/` contents into the document root.

### Folder layout on the server

```
/home/<cpaneluser>/
├── apps/
│   └── adly-crms/              ← the entire Laravel app
│       ├── app/
│       ├── bootstrap/
│       ├── config/
│       ├── database/
│       ├── public/             ← Laravel's public folder
│       ├── resources/
│       ├── routes/
│       ├── storage/
│       ├── vendor/             ← uploaded, NOT installed on server
│       ├── .env                ← created on server (NOT uploaded)
│       └── artisan
└── erd.aladly-group.com/       ← cPanel's auto-created docroot (subdomain root)
    └── (we'll point this at apps/adly-crms/public)
```

### Two ways to do the docroot rewrite

**Option A — symlink (preferred, faster).** From cPanel **Terminal**:

```bash
cd /home/<cpaneluser>
rm -rf erd.aladly-group.com
ln -s apps/adly-crms/public erd.aladly-group.com
```

**Option B — modify `public/index.php` paths (fallback for hosts that block symlinks).** Copy `apps/adly-crms/public/*` into `erd.aladly-group.com/`, then edit the two `require __DIR__.'/../...'` lines in `erd.aladly-group.com/index.php` to point at `/home/<cpaneluser>/apps/adly-crms/...`. Document A is cleaner; only use B if symlinks are denied.

---

## 4. Upload the build

From your Windows dev machine:

### Easiest: zip + File Manager

```powershell
cd $env:USERPROFILE\Downloads\ERD-deploy
# Exclude .git, node_modules, .env from the zip
Compress-Archive -Path * -DestinationPath ..\ERD-deploy.zip -Force
```

Then in cPanel **File Manager**:
1. Navigate to `/home/<cpaneluser>/apps/` (create the `apps` folder if missing)
2. Upload `ERD-deploy.zip`
3. Extract into `/home/<cpaneluser>/apps/adly-crms/`

### Alternative: FTP (FileZilla)

Sync the entire `ERD-deploy/` directory to `/home/<cpaneluser>/apps/adly-crms/`. **Skip** `node_modules/` and `.git/` to save time — they aren't needed.

---

## 5. Configure `.env` on the server

cPanel **Terminal** (or File Manager → create new file):

```bash
cd /home/<cpaneluser>/apps/adly-crms
cp .env.production.example .env
nano .env
```

Fill in the values you saved earlier (DB, APP_URL, etc.) — see `.env.production.example` for the full template.

Then generate the production APP_KEY:

```bash
php artisan key:generate
```

This writes the key into `.env`. **Never commit this key to git** — staging and prod each have their own.

---

## 6. Run migrations + seed initial data

```bash
cd /home/<cpaneluser>/apps/adly-crms

# Storage symlink (creates public/storage → ../storage/app/public)
php artisan storage:link

# Migrations — runs schema + 4 booking-overlap triggers
php artisan migrate --force

# Seed translations, roles, branches, categories, rate cards
php artisan db:seed --force
```

**If migrate fails with `TRIGGER privilege required`:** the DB user wasn't granted TRIGGER. Go back to step 1.3 and grant ALL PRIVILEGES (or specifically TRIGGER), then re-run.

**Create the super_admin user** (interactive — promotes an email to super_admin):

```bash
php artisan tinker
```
```php
$user = \App\Models\User::factory()->create([
    'email' => 'owner@aladly-group.com',
    'full_name' => 'Mohamed Zahran',
    'full_name_ar' => 'محمد زهران',
    'password' => bcrypt('CHANGE_ME_TO_A_STRONG_PASSWORD'),
    'branch_id' => \App\Models\Branch::where('code', 'CAI')->first()->id,
]);
$user->assignRole('super_admin');
exit
```

---

## 7. Set file permissions

```bash
cd /home/<cpaneluser>/apps/adly-crms
chmod -R 775 storage bootstrap/cache
# Owner should already be your cPanel user; if not:
# chown -R <cpaneluser>:<cpaneluser> storage bootstrap/cache
```

---

## 8. Cache for production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

If you ever change `.env`, run `php artisan config:clear` then `config:cache` again — cached config is frozen at cache time.

---

## 9. Cron jobs

cPanel → **Cron Jobs** → **Add New Cron Job**. Add both:

```
Minute: *   Hour: *   Day: *   Month: *   Weekday: *
Command: cd /home/<cpaneluser>/apps/adly-crms && /usr/local/bin/php artisan schedule:run >> /dev/null 2>&1

Minute: *   Hour: *   Day: *   Month: *   Weekday: *
Command: cd /home/<cpaneluser>/apps/adly-crms && /usr/local/bin/php artisan queue:work --stop-when-empty --max-time=55 >> /dev/null 2>&1
```

Confirm the PHP binary path with `which php` in cPanel Terminal — some hosts use `/opt/cpanel/ea-php82/root/usr/bin/php` instead of `/usr/local/bin/php`.

---

## 10. Force HTTPS

cPanel → **SSL/TLS Status** → confirm AutoSSL issued a cert for `erd.aladly-group.com`. Then in **Domains** → enable **Force HTTPS Redirect**.

(`AppServiceProvider::boot()` also calls `URL::forceScheme('https')` when `APP_ENV !== 'local'`, so generated URLs match.)

---

## 11. Smoke test

1. Open `https://erd.aladly-group.com/admin/login` — Filament login should render in Arabic (RTL).
2. Log in with the owner email + password from step 6.
3. Confirm:
   - [ ] Dashboard renders (no errors)
   - [ ] `/admin/branches` shows ABH + CAI
   - [ ] `/admin/cars` empty list renders
   - [ ] `/admin/trip-schedule` renders (the Cars × Days grid — should NOT 60s-timeout like local; production PHP-FPM has multiple workers)
   - [ ] `/admin/translations` — edit one label, save, refresh, see the change
   - [ ] Language switch ar → en in the header — URL flips to `/en/admin/...`, labels update

If any of these break, check `storage/logs/laravel.log` via cPanel File Manager.

---

## 12. Troubleshooting (common cPanel gotchas)

| Symptom | Likely cause | Fix |
|---|---|---|
| `500 Internal Server Error` on every page | `.env` missing / `APP_KEY` empty / `storage/` not writable | Check `storage/logs/laravel.log`. Re-run `chmod -R 775 storage bootstrap/cache` and `php artisan key:generate`. |
| Filament login shows but assets 404 | `filament:assets` wasn't run, OR symlink didn't include the `public/css/filament` folder | SSH/Terminal: `cd apps/adly-crms && php artisan filament:assets`, confirm files exist under `public/css/filament/`. |
| Migrations fail at trigger creation | DB user missing TRIGGER privilege | cPanel MySQL → **Manage User Privileges** → grant ALL on the DB. |
| `php artisan` says `command not found` | Wrong PHP CLI path on this host | `which php` to find it. Some hosts: `/opt/cpanel/ea-php82/root/usr/bin/php artisan ...`. |
| Storage uploads (FileUpload fields) fail | `public/storage` symlink missing | `php artisan storage:link` (delete and recreate if it points to a stale path). |
| RTL layout broken / English labels showing | `APP_LOCALE=ar` not set, OR `mcamara/laravel-localization` not seeing prefix | Confirm `.env` has `APP_LOCALE=ar`, clear caches, visit `/ar/admin`. |
| Slow first request after deploy | OPcache cold | Normal — warms after a few requests. To preempt: hit each main route once. |

---

## 13. Updating the deploy (after a phase merge)

When Phase 8+ merges to `main`:

```powershell
# Local rebuild
cd $env:USERPROFILE\Downloads\ERD-deploy
git pull
composer install --no-dev --optimize-autoloader --prefer-dist
npm ci && npm run build
php artisan filament:assets
```

Then upload only the changed folders to `/home/<cpaneluser>/apps/adly-crms/` — typically `app/`, `database/migrations/`, `resources/`, `vendor/`, `public/build/`, `public/css/filament/`, `public/js/filament/`.

On the server:

```bash
cd /home/<cpaneluser>/apps/adly-crms
php artisan migrate --force
php artisan config:clear && php artisan config:cache
php artisan view:clear && php artisan view:cache
php artisan route:clear && php artisan route:cache
```

---

## 14. Backups (Phase 13 deliverable, scheduled here)

Once Phase 13 lands, the `backup.sh` script will be added under `storage/scripts/`. For now, set up cPanel's built-in backup:

cPanel → **Backup Wizard** → schedule a daily full backup retained for 7 days. Configure off-site destination (S3 / Google Drive via cPanel's remote backup) — owner decision pending.

---

*End of deployment guide. Decisions about prod cron retention, off-site backup target, and any host-specific quirks discovered during this dry-run get appended to `docs/DECISIONS.md`.*
