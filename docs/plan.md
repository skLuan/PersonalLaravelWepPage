# Roadmap and Future Features

Status: `[ ]` todo · `[~]` in progress · `[x]` done. Each item says what "done" means.
The `#n` numbers point to the problem list in the 2026-11-23 entry of [journal.md](../journal.md).

---

## Phase 0: Stabilize (do first)

The site has open write endpoints and won't boot in a production container. Fix these before adding features.

- [x] **Protect portfolio CRUD** (#1): put `Route::resource('projects', …)` behind `auth` (and ideally an admin gate). *Done when* a guest gets redirected from `/projects/create`, and a `POST /projects` from a guest is rejected. Done 2026-11-25 with `auth` only; with registration closed, every user is an admin. There is no separate admin gate.
- [x] **Close registration** (#2): `/register` removed (404). Users come from `php artisan user:create`. Done 2026-11-25.
- [x] **One auth stack** (#3): keep Breeze (`routes/auth.php`) and remove `Auth::routes()` and the laravel/ui controllers and views. *Done when* `php artisan route:cache` succeeds. Done 2026-11-25: `composer remove laravel/ui`, Breeze login view restored; `route:cache` and `optimize` succeed.
- [x] **Unique route names** (#4): dropped the plain duplicates (`/privacy-policy`, `/home` → redirects). Covered by a test. Done 2026-11-25.
- [x] **Trim the portfolio resource** (#5): `Route::resource('portfolio', …)->only('index')`. Done 2026-11-25.
- [ ] **Config, not `env()`** (#10, #11): add `services.google.tag_id` and `services.notion.*` to `config/services.php`, switch views and controllers to `config()`, and document the keys in `.env.example`.
- [ ] **Guard global JS** (#16): `darkMode_button.js` should return early when `#check-dark-mode` is missing. Do the same for `menu_buton.js`.
- [ ] **Track images** (#13): narrow the `*.png`/`*.jpg` ignore rule so `public/imgs` and the mesh textures get committed (or move them to storage or a CDN).
- [ ] **CI on `main`** (#15): handled by the new `tests.yml` in Phase 0b.
- [x] **Fix the 2 failing auth tests**: login and registration redirect to `/home` (laravel/ui) instead of `/dashboard` (Breeze), and `/home` isn't a route. Done 2026-11-25: login passes; the registration test was replaced by "registration is closed".
- [ ] **Dependency advisories**: `composer` reported "61 security vulnerability advisories affecting 16 packages" (2026-11-25). Run `composer audit`, then update what can be updated within Laravel 10.
- [ ] **`/dashboard` and `/profile` layout**: `<x-app-layout>` renders the site layout, which has `@yield('content')` but no `$slot`, so the Breeze content probably doesn't show. Either give them their own layout or redirect after login to `/projects` (`RouteServiceProvider::HOME`).
- [ ] Remove dead pieces: the `/inWork` route and view (#7), `layouts/projects.blade.php` (#25), and the root `lang/` folder (#12) once its files are moved to `resources/lang/en`.
- [x] **Local Docker environment**: `compose.yml` with app (PHP 8.3), node (Vite), MariaDB 11.4 and Mailpit, the same pattern as thelamb.studio-web. *Done when* `docker compose up -d` serves `http://localhost:8080` and `php artisan test` runs inside the container. Done 2026-11-23; see journal.

### Phase 0b: Deploy with GitHub Actions (Hostinger, modelled on thelamb.studio-web)

Reference: [`skLuan/thelamb.studio-web`](https://github.com/skLuan/thelamb.studio-web) → `.github/workflows/{tests,deploy,hostinger}.yml`, `scripts/hostinger/link-docroot.sh` and `docs/despliegue.md`. Hostinger shared hosting has PHP and MariaDB over SSH but **no Node**, so GitHub Actions builds everything and uploads files that are ready to run.

**Target flow**

```
push to main ─┐                             ┌─▶ staging    (e.g. https://testing.luane.online)
tag v*  ──────┼─▶ tests.yml ─▶ deploy.yml ──┤
Run workflow ─┘                             └─▶ production (https://luane.online)

hostinger.yml: composer install --no-dev · npm ci && npm run build · rsync code → app folder
               check server .env · artisan down · migrate --force · optimize · storage link
               link-docroot.sh (public/ → public_html) · artisan up · curl smoke test
```

**Prerequisites**: these Phase 0 items block the deploy, because `artisan optimize` caches config and routes.
- [x] One auth stack and unique route names (#3, #4). `optimize` runs `route:cache`. Done 2026-11-25.
- [ ] Config, not `env()` (#10). After `optimize`, `GOOGLE_TAG_ID` would be empty.
- [ ] Track images (#13). The deploy uploads from a git checkout, so untracked `public/imgs/*` and mesh textures would be missing on the server.

**Workflows** (copy from the reference and adapt):
- [ ] `tests.yml`: triggers on `pull_request` + `workflow_call`; PHP 8.3, `composer install`, `npm ci && npm run build`, optional `pint --test`, `php artisan test`. Tests need a database: use a MariaDB service container (the binshops fulltext migration may not run on SQLite), or check whether SQLite works. This replaces the Laravel skeleton workflows (`issues.yml`, `pull-requests.yml`, `update-changelog.yml`) and fixes #15.
- [ ] `deploy.yml`: push to `main` → staging; tag `v*` → production; `workflow_dispatch` with an environment choice. Every path runs `tests.yml` first.
- [ ] `hostinger.yml` (reusable, `workflow_call` with `environment`):
  - Validate the environment variables `HOSTINGER_DOCROOT` and `SITE_URL`. They are *variables*, not secrets, or `vars.` reads them as empty.
  - Normalize the SSH key (strip CRLF) and validate it with `ssh-keygen -y` before connecting.
  - Run `rsync -az --delete`, excluding `.git/`, `.github/`, `node_modules/`, `docker/`, `compose.yml`, `tests/`, `docs/`, `*.md`, `.env`, `storage/`, `public/storage`, `public/hot`, `bootstrap/cache/*.php` **and `public/blog_images/`**. Blog uploads live there (`config/binshopsblog.php` → `blog_upload_dir`), and `--delete` would wipe them. A better long-term fix is to move the uploads under `storage/app/public`.
  - Over SSH, run with `set -euo pipefail`:
    - fail if `.env` is missing, `APP_KEY` is empty or `APP_DEBUG=true`;
    - `config:clear` → `down` (with a `trap … up` so the site always comes back) → `migrate --force` → `optimize`;
    - create the `public/storage` link with `ln -s` (Hostinger disables PHP `symlink()`, so `artisan storage:link` fails);
    - run `link-docroot.sh`.
  - Smoke test with `curl -fsS` on `SITE_URL/`, `/portfolio` and `/blog`. Laravel 10 has no `/up` route: add one (`Route::get('/up', fn () => response('ok'))`) or drop that check.
- [ ] `scripts/hostinger/link-docroot.sh`: copy it from the reference and change the marker to `# luane:managed-by-deploy`. It links each `public/` entry into the docroot, keeps `.well-known`, `cgi-bin` and the staging folder, and moves whatever else was there to `storage/app/docroot-backup/<date>/`.

**Hostinger (one-time, per environment)**
- [ ] Confirm where `luane.online` is hosted. If it's the same Hostinger account as thelamb.studio, you can reuse the SSH access, but give this repo its own deploy key so it can be revoked alone.
- [ ] Set PHP 8.3 in hPanel and confirm the `gd`, `intl`, `zip`, `bcmath`, `exif` and `fileinfo` extensions are enabled.
- [ ] Create the staging subdomain (`testing.luane.online`, inside `public_html`) with SSL.
- [ ] Give each environment its own MariaDB database. Never share production's.
- [ ] Put the app folders **outside** `public_html` (`~/domains/luane.online/laravel` and `…/staging`), each with its own `.env`:
  - `APP_ENV`, `APP_DEBUG=false`, `APP_URL`, `APP_KEY` (from `key:generate --force`);
  - `DB_HOST=localhost`;
  - `GOOGLE_TAG_ID`;
  - wrap passwords in single quotes.
- [ ] Add a cron entry every minute: `cd <app> && php artisan schedule:run`. Use `/opt/alt/php83/usr/bin/php` if `php -v` over SSH isn't 8.3.
- [ ] Copy existing production content into the new database: blog posts, projects, `public/blog_images`.
- [ ] Back up the current `public_html` before the first production deploy (`cp -a public_html ~/public_html.backup-$(date +%F)`).

**GitHub (one-time)**
- [ ] Create the environments `staging` and `production`, with the same names and different values.
  - Secrets: `HOSTINGER_SSH_HOST`, `HOSTINGER_SSH_PORT` (usually `65002`), `HOSTINGER_SSH_USER`, `HOSTINGER_SSH_KEY` (private key, no passphrase), `HOSTINGER_APP_PATH`.
  - Variables: `HOSTINGER_DOCROOT`, `SITE_URL`, optional `HOSTINGER_DOCROOT_KEEP` and `HOSTINGER_PHP`.
- [ ] On `production`, allow deployments only from `v*` tags, and require a reviewer.

**Done when:**
- a push to `main` deploys staging and the smoke test passes;
- `git tag v1.0.0 && git push origin v1.0.0` deploys production;
- a failed step leaves the site online (not stuck in maintenance mode);
- `docs/deployment.md` documents the setup and rollback, like the reference's `docs/despliegue.md`;
- the unused `laravel-fans/docker` files (`docker/etc`, `docker/usr`, `docker/var`) are removed.

## Phase 1: Finish what's started

- [ ] **Project detail page** (#6): create `projects/show.blade.php` that renders `body` (Markdown → HTML), the image, skills and site link, with SEO meta from the project. The public URL is `/portfolio/{slug}`.
- [ ] **Portfolio filters**: make the "Web product / Graphic design" buttons work. This needs a `category` (or tag) column on `projects`.
- [ ] **Skills section**: fill the empty `#Skills` section from the `skills` table.
- [ ] **"Let's work" / contact section**: fill `#letsWork` with a contact form (rate-limited, with a honeypot) or a mailto/Calendly link.
- [ ] **Hero section**: the `#hero` section is empty, so decide what goes there or remove it.
- [x] **Blog localization** (#8): `/{locale}/blog/*` index, post, category, search and RSS feed all work, and the language switcher keeps the page. Done 2026-11-25 (fixes #27–#30). The `BlogPost`/`BlogCategories` models from `develop/multilang` were removed, because binshops never reads the `blog_post_model` config key; `URL::defaults` does their job.
- [ ] **Translate blog content**: posts have a single language. Showing an `es` version of a post needs a translation model (binshops has no translation support), or a `locale` column and a filter.
- [ ] **Language switcher everywhere**: show it in the mobile menu too (today it is desktop-only), and keep the current path when switching.
- [ ] **Translate the remaining UI**: project CRUD views (currently hard-coded Spanish), blog chrome and privacy policy.
- [ ] **Real social links** (#24): replace the placeholders in the navbar and footer JSON-LD with config values.
- [ ] **Per-page `<title>` and meta** (#23): the layout should use `$title`, plus meta descriptions, Open Graph, `hreflang` alternates and a sitemap.
- [ ] **Dark-mode toggle in the main navbar**: today it is only on single blog posts.
- [ ] **Admin entry point**: give the project CRUD and `/blog_admin` a proper login and admin menu, once Phase 0 auth is done.

## Phase 2: 3D scene

- [ ] **Clean up debug output** (#17–#19): remove the `cubeGroup` key handler, the double `start()`, grid and axes helpers, the click logger and `console.log`s (or gate them behind `import.meta.env.DEV`).
- [ ] **Path and squids stay in sync** (#20): run `responsivePath()` on load, and push path updates to every vehicle (shared path, or call `updatePath` on resize).
- [ ] **Per-vehicle state** (#21): move `hasSwitched` and `target` into the `VehicleStateController` instance.
- [ ] **Squid model** (#22): load `squid_mini2.glb` through `LoaderManager`, and fix the flicker (likely z-fighting, or `matrixAutoUpdate` on child meshes).
- [ ] **Bundle size**: lazy-load the 3D bundle (dynamic `import()`), import only the Three.js modules in use, and fix `helpers.js` importing from `/node_modules/...`. Target: 3D chunk under 300 kB.
- [ ] **Accessibility and performance**: respect `prefers-reduced-motion`, pause the loop when the tab is hidden or the canvas is off-screen, and cap the pixel ratio on mobile.
- [ ] **Scroll-driven paths**: finish `PathCreator.DOMtoPath()` so waypoints follow page sections (`.to-path` elements).
- [ ] **Per-page worlds**: a distinct scene for home (today it falls back to `PortfolioWorld`) and for blog posts.

## Phase 3: Content and operations

- [ ] **Tests**: feature tests for the public pages (home, portfolio, blog index, privacy) in both locales, project CRUD authorization, and the locale switcher.
- [ ] **Deployment docs**: covered by Phase 0b (`docs/deployment.md`).
- [ ] **Decide on Notion**: either delete `CurriculumVitae`, `NotionService`, `blog.blade.php` and `blogpost.blade.php`, or bring back a Notion-powered CV page on top of config-based IDs.
- [ ] **CV page**: a downloadable or online CV (there is a commented-out link in the legacy `inWork` view).
- [ ] **Analytics consent**: a cookie or consent banner for GA and Clarity, in line with the privacy policy text.
- [ ] **Image handling**: uploads for project images (today they are URLs only), with responsive `srcset` (the `<source>` in `card/project` is empty).
- [ ] **Blog comments**: currently `disabled`. Decide whether to keep them off or use the built-in type with captcha.

## Ideas (not committed)

- RSS link in the footer (`/blog/feed` and `/{locale}/blog/feed` work, served by `BlogFeedController`).
- A "now" or changelog page generated from `journal.md`.
- Interactive squids that react to the cursor (Yuka `SeekBehavior` / `FleeBehavior`).
