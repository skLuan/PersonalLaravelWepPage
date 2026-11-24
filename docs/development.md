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

> The deployment target and process are **not recorded in the repo**. The `docker/` folder suggests a `laravel-fans/docker` Apache image, but there is no Dockerfile or compose file. Document the real process here (host, how assets are built, how `.env` is provided, how migrations run).

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
