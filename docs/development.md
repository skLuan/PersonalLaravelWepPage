# Development Guide

## Option A: Docker (recommended)

Everything runs in containers: PHP 8.3 (the same as the Hostinger target), Vite, MariaDB 11.4 and Mailpit. You need Docker Desktop and nothing else.

```bash
cp .env.example .env               # already set to the container hosts (DB_HOST=db, MAIL_HOST=mailpit)
docker compose up -d               # first run: builds the image, composer install + npm install into volumes
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
- If the `node` container is killed without a clean shutdown, `public/hot` can be left behind and pages will try to load assets from a dead Vite server. Delete `public/hot` in that case.

## Option B: Native toolchain

### Prerequisites

- PHP **8.1+** with `pdo_mysql`, `mbstring`, `gd` (blog image resizing), `curl`
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
php artisan storage:link       # blog image uploads
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
docker compose exec app php artisan test     # Docker: the `testing` DB is created for you
php artisan test                             # native: create a `testing` database yourself
```

`phpunit.xml` uses `DB_DATABASE=testing` on the `.env` connection. Only the Breeze auth/profile tests and the skeleton example tests exist; there is no coverage of the site's own pages yet. Status on 2026-11-23: **23 passed, 2 failed**. Both failures are the laravel/ui vs Breeze redirect conflict (journal #3).

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

> **Not set up yet.** The planned process is GitHub Actions → Hostinger over SSH (push to `main` → staging, tag `v*` → production), copied from thelamb.studio-web. See **Phase 0b** in [plan.md](plan.md). When it's built, the full guide goes in `docs/deployment.md`.
>
> The `docker/etc`, `docker/usr` and `docker/var` files (laravel-fans Apache image) are left over from an earlier plan and aren't used. `docker/app` and `docker/db` belong to the local compose stack.

Production checklist:
- `APP_ENV=production`, `APP_DEBUG=false`, and `APP_URL` set to the real domain.
- `GOOGLE_TAG_ID` set. After moving it to `config/services.php`, it survives `config:cache`; today it doesn't (journal #10).
- `public/imgs/*` and the mesh textures are present on the server.
- `storage/` and `bootstrap/cache/` are writable by the web user.

## Branches

| Branch | State (2026-11-23) |
|---|---|
| `develop/blog` | Active. Blog, portfolio, i18n, dark mode. |
| `main` | 32 commits behind `develop/blog`. |
| `develop/multilang` | 2 commits not merged: localized blog models and routes. |
| `blog/larablog`, `blog/notion`, `develop/threejs`, `develop/views`, `portfolio` | Fully merged; can be deleted. |
