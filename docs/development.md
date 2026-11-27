# Development Guide

## Option A: Docker (recommended)

Everything runs in containers: PHP 8.4 (the same as the Hostinger target; hPanel allows up to 8.5), Vite, MariaDB 11.4 and Mailpit. You need Docker Desktop and nothing else.

```bash
cp .env.example .env               # already set to the container hosts (DB_HOST=db, MAIL_HOST=mailpit)
docker compose up -d               # builds the image; each start runs composer install + migrate (app) and npm install (node)
docker compose restart app         # after a pull that changes composer.lock or adds migrations
docker compose up -d --build app   # after a Dockerfile change (e.g. the PHP version)
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

| Service | URL / port | Notes |
|---|---|---|
| `app` | http://localhost:8080 | `php artisan serve` inside the container |
| `node` | http://localhost:5174 | Vite dev server + HMR (writes `public/hot`) |
| `db` | `localhost:3308` | MariaDB. User and password `luane`/`luane`, database `luane`, plus `testing` for PHPUnit |
| `mailpit` | http://localhost:8026 | Catches all outgoing mail |

Day-to-day commands:

```bash
docker compose exec app php artisan …        # artisan, tinker, test
docker compose exec app composer require …   # composer always runs INSIDE (vendor/ is a volume)
docker compose run --rm node npm run build   # production assets → public/build
docker compose logs -f app node              # server output
docker compose down                          # stop (data stays in the `db` volume)
docker compose down -v                       # stop and DELETE db, vendor, node_modules volumes
```

Notes:
- `vendor/` and `node_modules/` live in named volumes, not in your working copy (on Windows, bind-mounted `vendor/` is slow). Your IDE won't see the container's `vendor/`. Run `composer install` on the host as well if you want autocompletion.
- The ports are offset from thelamb.studio-web (8000/5173/3307/8025), so both stacks can run at once.
- `docker/db/init/` runs only when the `db` volume is first created. To re-run it, `docker compose down -v`.
- **Page without styles, console shows CORS errors for `127.0.0.1:8000/@vite/client`** (or any address other than `localhost:5174`): `public/hot` is stale. Vite writes it on start and deletes it on a clean stop (`init: true` + `exec` in `compose.yml` make `docker compose stop` clean). It goes stale when Vite is killed, or when you check out a branch with the old pre-Docker `vite.config.js` (port 8000) while `node` runs: Vite reloads with that config. Fix: `docker compose restart node` (or delete `public/hot`). While `node` is stopped, pages use `public/build` (`docker compose run --rm node npm run build` to refresh it).
- The only local stack is the compose project **`luane`** (containers `luane-app`, `luane-node`, `luane-db`, `luane-mailpit`). Containers with other names (e.g. `luane-fil-app`, `luane-wt-app`) were temporary, made by hand for a git worktree; remove them when that worktree is done.

## Option B: Native toolchain

### Prerequisites

- PHP **8.2+** (8.4 recommended) with `pdo_mysql`, `mbstring`, `intl` (Filament), `gd`, `zip`, `curl`
- Composer 2
- Node.js 18+ (verified with Node 22) and npm
- MySQL 8 or MariaDB

### First-time setup

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
php artisan db:seed --class=LocalAdminSeeder   # admin user for /admin (local only)
```

> **Images:** `.gitignore` excludes `*.png`/`*.jpg`, so `public/imgs/` (profile photo, hero) is not in the repo. Copy those files over by hand until this is fixed (see [plan.md](plan.md), Phase 0).

### Running locally

In two terminals:

```bash
npm run dev                      # Vite on http://127.0.0.1:5174
php artisan serve --port=8080    # app on http://localhost:8080
```

| Page | URL |
|---|---|
| Home | `/`, `/es` |
| Portfolio | `/portfolio`, `/es/portfolio` |
| Blog | `/en/blog`, `/es/blog` (`/blog` redirects by browser language) |
| Admin (Filament) | `/admin`: posts, categories, projects, skills. Needs `is_admin`: `php artisan user:create you@example.com --admin` |
| Login | `/admin/login` for the panel, `/login` (Breeze) for the rest. There is no `/register` |
| Privacy | `/privacy-policy` |

## Common tasks

**Add a UI string:** add the key to both `resources/lang/en/<file>.php` and `resources/lang/es/<file>.php`, then use `{{ __('<file>.<key>') }}`. Don't use the root `lang/` folder, because Laravel ignores it here.

**Add a portfolio project:** `/admin/projects` → *New project*. `image_path` must be a full URL. Skills can be created from the skills field.

**Add a blog post:** `/admin/posts` → *New post*. Pick the language; for a translation, create a second post in the other language and set *Translation of*. Empty *Publish at* = draft; admins can preview drafts at their URL.

**Import the old binshops blog** (once, where the `binshops_blog_*` tables have data):
```bash
php artisan blog:import-binshops --dry-run          # shows each post with its guessed language
php artisan blog:import-binshops --set=7:en --pair=3:7
```
Then check `/admin/posts`, rename the categories created for the second language, and link any missing translations.

**Add a page with the 3D background:**
1. Render `<x-three-canvas />` at the top of the section.
2. Make the route name match `portfolio*` or `blog.*`, or extend the `@if` in `layouts/app.blade.php` that includes `3dMain.js`.
3. If the page needs its own scene, add a branch in `resources/js/3dMain.js`.

**Add something animated to a world:** give the object a `tick(delta)` method and push it to `loop.updatables`.

## Tests

```bash
docker compose exec app php artisan test     # Docker: the `testing` DB is created for you
php artisan test                             # native: create a `testing` database yourself
```

`phpunit.xml` uses `DB_DATABASE=testing` on the `.env` connection (a real `DB_DATABASE` env var wins, e.g. `DB_DATABASE=testing_fil php artisan test` for a worktree). Besides the Breeze auth/profile tests: `BlogLocaleTest` (blog per locale, redirects, drafts, search, pagination, RSS, language switcher), `AdminPanelTest` (panel access and pages), `ImportBinshopsBlogTest` (import command) and `AccessControlTest` (closed registration, unique route names, `user:create`). Status on 2026-11-26: **63 passed**. CI runs the same suite on PHP 8.4 + MariaDB 11.4 (`.github/workflows/tests.yml`, see Deployment).

Two checkouts running tests at the same time (for example a git worktree plus the main stack) share the `testing` database, and `RefreshDatabase` in one will break the other. Run them one at a time.

## Building for production

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build          # → public/build (gitignored, so build on deploy)
php artisan migrate --force
php artisan optimize             # config, events, routes, views, blade-icons, Filament
```

## Deployment

Production is **Hostinger shared hosting** (hPanel, PHP 8.4) at <https://luane.online>. Deploys run from GitHub Actions: [`.github/workflows/deploy.yml`](../.github/workflows/deploy.yml), which first runs [`.github/workflows/tests.yml`](../.github/workflows/tests.yml). There is no staging yet (Phase 0b in [plan.md](plan.md)).

**PHP version.** The server, CI (`tests.yml`, `deploy.yml`) and the dev image (`docker/app/Dockerfile`) all use **PHP 8.4**. Change them together. `composer.json` allows `^8.2`; the locked packages (Laravel 12, Filament 5, `nette/utils` 4.1.6, `nette/schema` 1.4.0) support up to 8.6.

**Server layout.** The Laravel app lives **outside** the web root and only `public/` is served:

```
~/domains/luane.online/              Laravel root (.env, app/, vendor/, storage/...)  <- HOSTINGER_DEPLOY_PATH
~/domains/luane.online/public        Laravel public/
~/domains/luane.online/public_html -> public   (relative symlink; the web root)
```

Until 2026-11 the whole app sat inside `public_html/` behind a redirect `.htaccess`. [`scripts/hostinger/migrate-layout.sh`](../scripts/hostinger/migrate-layout.sh) moves it out once (dry run by default, `--apply` to run, backup in `~/backups/`); [`rollback-layout.sh`](../scripts/hostinger/rollback-layout.sh) restores the backup. The deploy workflow refuses to run unless `public_html` is a symlink to `public`.

**Trigger:** every push to `main`, or by hand from *Actions → Deploy to Hostinger → Run workflow*.

**Tests (`tests.yml`).** Runs on pull requests, pushes to `develop/**`, by hand, and as the first job of every deploy. PHP 8.4 + a MariaDB 11.4 service container (not SQLite: the old `laravel_fulltext` migration uses `FULLTEXT`). Steps: `composer install`, `key:generate`, `artisan optimize` + `optimize:clear` (fails on duplicate route names or Blade parse errors), `php artisan test`. If it fails, nothing is deployed.

**What the deploy job does:**
0. Checks the secrets, the layout above (`artisan` in `HOSTINGER_DEPLOY_PATH`, `public_html -> public`, `public/storage` only pointing at `storage/app/public`) and the server's CLI PHP (fails below 8.2, warns if it isn't 8.4).
1. Installs Composer deps with `--no-dev --optimize-autoloader` (this also publishes Filament's gitignored assets; the job fails if they are missing) and runs `npm ci && npm run build` on the runner (the server does not need Node).
2. Connects over SSH, runs `php artisan down`.
3. `rsync --delete` the build to `HOSTINGER_DEPLOY_PATH`. It never deletes top-level entries that are not in the repo (`public_html`, backups, Hostinger files), skips `tests/`, `docker/`, `docs/` and `compose.yml`, **never touches** `.env`, `storage/`, `bootstrap/cache/*.php`, `public/storage`, `public/imgs/`, `public/blog_images/`, any `.htaccess`, `error_log`, `.well-known/`, and never deletes `*.png/*.jpg/*.jpeg/*.webp` on the server (they are gitignored).
4. Deletes `bootstrap/cache/*.php` on the server (a stale package manifest can name providers that were removed, e.g. binshops, and crash artisan), then `migrate --force`, `optimize:clear`, `optimize` (config, events, **routes**, views, blade-icons, Filament), and `ln -s ../storage/app/public public/storage` if missing (Hostinger disables PHP `symlink()`, so `artisan storage:link` fails there).
5. `php artisan up` (always, even if a step failed), then checks that `/`, `/en/portfolio`, `/en/blog`, `/es/blog` and `/admin/login` return 200 and that `/.env`, `/artisan`, `/composer.json`, `/public/index.php`, `/vendor/…` and `/storage/logs/…` do **not**.

**GitHub configuration** (*Settings → Secrets and variables → Actions*):

| Name | Type | Example |
|---|---|---|
| `HOSTINGER_SSH_HOST` | secret | IP shown in hPanel → Advanced → SSH Access |
| `HOSTINGER_SSH_PORT` | secret | `65002` |
| `HOSTINGER_SSH_USER` | secret | `u123456789` |
| `HOSTINGER_SSH_KEY` | secret | private key (whole file, including the `BEGIN`/`END` lines) |
| `HOSTINGER_DEPLOY_PATH` | secret | Laravel root, **not** `public_html`: `/home/u123456789/domains/luane.online` |
| `HOSTINGER_PHP_BIN` | variable (optional) | PHP CLI on the server if `php` over SSH is not 8.4, e.g. `/opt/alt/php84/usr/bin/php` |

The job uses the `production` environment, so you can add a required reviewer there if you want a manual approval step.

**One-time setup:**
```bash
ssh-keygen -t ed25519 -C "github-actions@luane.online" -f hostinger_deploy -N ""
# hostinger_deploy.pub → hPanel → Advanced → SSH Access → SSH keys → Add
# hostinger_deploy     → GitHub secret HOSTINGER_SSH_KEY
# move the app out of public_html (once)
scp -P 65002 scripts/hostinger/*.sh u123456789@HOST:~/
ssh -p 65002 u123456789@HOST 'bash migrate-layout.sh'           # dry run
ssh -p 65002 u123456789@HOST 'bash migrate-layout.sh --apply'   # PHP_BIN=/opt/alt/php84/usr/bin/php if needed
```

The server `.env` must already have `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://luane.online`, the `DB_*` values and `GOOGLE_TAG_ID`.

**First deploy of Laravel 12 + Filament** (the `develop/blog` merge): back up the database and `public_html` first. The deploy runs the new migrations (`posts`, `categories`, `seo_meta`, `users.is_admin`, …); afterwards run `blog:import-binshops` and `user:create --admin` over SSH (plan item D2).

The `docker/etc`, `docker/usr` and `docker/var` files (laravel-fans Apache image) are left over from an earlier plan and aren't used. `docker/app` and `docker/db` belong to the local compose stack.

Production checklist:
- `APP_ENV=production`, `APP_DEBUG=false`, and `APP_URL` set to the real domain.
- `GOOGLE_TAG_ID` set (read through `config('services.google.tag_id')`, so it survives `config:cache`).
- `public/imgs/*` and the mesh textures are present on the server (not in git; the deploy leaves them alone).
- `storage/` and `bootstrap/cache/` are writable by the web user.

## Branches

| Branch | State (2026-11-26) |
|---|---|
| `develop/blog` | Active. Laravel 12 + Filament admin, own blog models, portfolio, i18n. |
| `main` | Behind `develop/blog`; PRs target it. |
| `ci/deploy-hostinger` | 2 commits (GitHub Actions deploy), branched from `main` **before** the Docker setup: its `vite.config.js` is the old one (port 8000). Rebase it on `develop/blog` before working on it, or Vite in Docker breaks (see the stale `public/hot` note above). |
| `feat/3d-cleanup` | Worktree `../PersonalLaravelWepPage-wt/3d-cleanup`, no commits yet. |

`develop/multilang`, `fix/blog-locale-auth`, `blog/larablog`, `blog/notion`, `develop/threejs`, `develop/views` and `portfolio` were merged and deleted on 2026-11-26 (locally and on GitHub).
