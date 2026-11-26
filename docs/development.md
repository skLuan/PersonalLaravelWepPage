# Development Guide

## Prerequisites

- PHP **8.1+** with `pdo_mysql`, `mbstring`, `gd` (blog image resizing), `curl`
- Composer 2
- Node.js 18+ (verified with Node 22) and npm
- MySQL 8 (or MariaDB)

## First-time setup

```bash
git clone git@github.com:skLuan/PersonalLaravelWepPage.git
cd PersonalLaravelWepPage

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```dotenv
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=personal_web
DB_USERNAME=...
DB_PASSWORD=...

# Optional
GOOGLE_TAG_ID=G-XXXXXXX            # Google Analytics; leave empty locally
NOTION_API_TOKEN=                  # legacy Notion integration only
NOTION_CURRICULUM_VITAE_ID=
```

Then:

```bash
php artisan migrate --seed     # creates app + blog tables, seeds 4 skills
php artisan storage:link       # blog image uploads
```

> **Images:** `.gitignore` excludes `*.png`/`*.jpg`, so `public/imgs/` (profile photo, hero) is not in the repo. Copy those files over by hand until this is fixed (see [plan.md](plan.md), Phase 0).

## Running locally

In two terminals:

```bash
npm run dev                      # Vite on http://127.0.0.1:8000 (pinned in vite.config.js)
php artisan serve --port=8080    # app on http://localhost:8080
```

Use a port other than 8000 for `artisan serve`, because Vite already holds 8000.

| Page | URL |
|---|---|
| Home | `/`, `/es` |
| Portfolio | `/portfolio`, `/es/portfolio` |
| Blog | `/blog` |
| Blog admin | `/blog_admin` (logged-in user who passes `canManageBinshopsBlogPosts()`) |
| Project CRUD | `/projects` |
| Privacy | `/privacy-policy` |

## Common tasks

**Add a UI string:** add the key to both `resources/lang/en/<file>.php` and `resources/lang/es/<file>.php`, then use `{{ __('<file>.<key>') }}`. Don't use the root `lang/` folder, because Laravel ignores it here.

**Add a portfolio project:** go to `/projects/create`. `image_path` must be a full URL. Pick at least one skill. To add new skills, insert rows into `skills` (or extend `DatabaseSeeder`).

**Add a blog post:** use `/blog_admin` → *Add post*.

**Add a page with the 3D background:**
1. Render `<x-three-canvas />` at the top of the section.
2. Make the route name match `portfolio*` or `binshopsblog*`, or extend the `@if` in `layouts/app.blade.php` that includes `3dMain.js`.
3. If the page needs its own scene, add a branch in `resources/js/3dMain.js`.

**Add something animated to a world:** give the object a `tick(delta)` method and push it to `loop.updatables`.

## Tests

```bash
php artisan test
```

`phpunit.xml` uses `DB_DATABASE=testing` on the `.env` connection, so a MySQL database called `testing` must exist and be reachable. Only the Breeze auth/profile tests and the skeleton example tests exist. There is no coverage of the site's own pages yet.

## Building for production

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build          # → public/build (gitignored, so build on deploy)
php artisan migrate --force
php artisan config:cache
php artisan route:cache          # ⚠ currently fails: duplicate auth route names (journal #3)
php artisan view:cache
```

## Deployment

Production is **Hostinger shared hosting** (hPanel, PHP 8.2) at <https://luane.online>. Deploys run from GitHub Actions: [`.github/workflows/deploy.yml`](../.github/workflows/deploy.yml).

**Server layout.** The Laravel app lives **outside** the web root and only `public/` is served:

```
~/domains/luane.online/              Laravel root (.env, app/, vendor/, storage/...)  <- HOSTINGER_DEPLOY_PATH
~/domains/luane.online/public        Laravel public/
~/domains/luane.online/public_html -> public   (relative symlink; the web root)
```

Until 2026-11 the whole app sat inside `public_html/` behind a redirect `.htaccess`. [`scripts/hostinger/migrate-layout.sh`](../scripts/hostinger/migrate-layout.sh) moves it out once (dry run by default, `--apply` to run, backup in `~/backups/`); [`rollback-layout.sh`](../scripts/hostinger/rollback-layout.sh) restores the backup. The deploy workflow refuses to run unless `public_html` is a symlink to `public`.

**Trigger:** every push to `main`, or by hand from *Actions → Deploy to Hostinger → Run workflow*.

**What the workflow does:**
0. Checks the layout above (`artisan` in `HOSTINGER_DEPLOY_PATH`, `public_html -> public`).
1. Installs Composer deps with `--no-dev --optimize-autoloader` and runs `npm ci && npm run build` on the runner (the server does not need Node).
2. Connects over SSH, runs `php artisan down`.
3. `rsync --delete` the build to `HOSTINGER_DEPLOY_PATH`. It never deletes top-level entries that are not in the repo (`public_html`, backups, Hostinger files) and **never touches** `.env`, `storage/`, `bootstrap/cache/*.php`, `public/storage`, `public/imgs/`, `public/blog_images/`, any `.htaccess`, `error_log`, `.well-known/`, and it never deletes `*.png/*.jpg/*.jpeg/*.webp` on the server (they are gitignored).
4. `migrate --force`, `optimize:clear`, `config:cache`, `view:cache`, and `ln -s ../storage/app/public public/storage` if missing (Hostinger disables PHP `symlink()`, so `artisan storage:link` fails there). The layout check also fails if `public/storage` points anywhere else. `route:cache` is skipped until journal #3 is fixed.
5. `php artisan up` (always, even if a step failed), then checks that `https://luane.online/` returns 200 and that `/.env`, `/artisan`, `/composer.json`, `/public/index.php`, `/vendor/…` and `/storage/logs/…` do **not**.

**GitHub configuration** (*Settings → Secrets and variables → Actions*):

| Name | Type | Example |
|---|---|---|
| `HOSTINGER_SSH_HOST` | secret | IP shown in hPanel → Advanced → SSH Access |
| `HOSTINGER_SSH_PORT` | secret | `65002` |
| `HOSTINGER_SSH_USER` | secret | `u123456789` |
| `HOSTINGER_SSH_KEY` | secret | private key (whole file, including the `BEGIN`/`END` lines) |
| `HOSTINGER_DEPLOY_PATH` | secret | Laravel root, **not** `public_html`: `/home/u123456789/domains/luane.online` |
| `HOSTINGER_PHP_BIN` | variable (optional) | PHP CLI on the server if `php` is not 8.2, e.g. `/opt/alt/php82/usr/bin/php` |

The job uses the `production` environment, so you can add a required reviewer there if you want a manual approval step.

**One-time setup:**
```bash
ssh-keygen -t ed25519 -C "github-actions@luane.online" -f hostinger_deploy -N ""
# hostinger_deploy.pub → hPanel → Advanced → SSH Access → SSH keys → Add
# hostinger_deploy     → GitHub secret HOSTINGER_SSH_KEY
# move the app out of public_html (once)
scp -P 65002 scripts/hostinger/*.sh u123456789@HOST:~/
ssh -p 65002 u123456789@HOST 'bash migrate-layout.sh'           # dry run
ssh -p 65002 u123456789@HOST 'bash migrate-layout.sh --apply'   # PHP_BIN=/opt/alt/php82/usr/bin/php if needed
``` The server `.env` must already have `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://luane.online`, the `DB_*` values and `GOOGLE_TAG_ID`.

Production checklist:
- `APP_ENV=production`, `APP_DEBUG=false`, and `APP_URL` set to the real domain.
- `GOOGLE_TAG_ID` set (read through `config('services.google.tag_id')`, so it survives `config:cache`).
- `public/imgs/*` and the mesh textures are present on the server (not in git; the deploy leaves them alone).
- `storage/` and `bootstrap/cache/` are writable by the web user.

## Branches

| Branch | State (2026-11-23) |
|---|---|
| `develop/blog` | Active. Blog, portfolio, i18n, dark mode. |
| `main` | 32 commits behind `develop/blog`. |
| `develop/multilang` | 2 commits not merged: localized blog models and routes. |
| `blog/larablog`, `blog/notion`, `develop/threejs`, `develop/views`, `portfolio` | Fully merged; can be deleted. |
