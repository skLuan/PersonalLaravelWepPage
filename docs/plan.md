# Roadmap and Future Features

Status: `[ ]` todo · `[~]` in progress · `[x]` done. Each item says what "done" means.
The `#n` numbers point to the problem list in the 2026-11-23 entry of [journal.md](../journal.md).

---

## Phase 0: Stabilize (do first)

The site has open write endpoints and won't boot in a production container. Fix these before adding features.

- [x] **Protect portfolio CRUD** (#1): replaced on 2026-11-26 by the Filament panel, gated by `users.is_admin`; `/projects/*` now redirects to `/admin/projects`. Earlier: put `Route::resource('projects', …)` behind `auth` (and ideally an admin gate). *Done when* a guest gets redirected from `/projects/create`, and a `POST /projects` from a guest is rejected. Done 2026-11-25 with `auth` only; with registration closed, every user is an admin. There is no separate admin gate.
- [x] **Close registration** (#2): `/register` removed (404). Users come from `php artisan user:create`. Done 2026-11-25.
- [x] **One auth stack** (#3): keep Breeze (`routes/auth.php`) and remove `Auth::routes()` and the laravel/ui controllers and views. *Done when* `php artisan route:cache` succeeds. Done 2026-11-25: `composer remove laravel/ui`, Breeze login view restored; `route:cache` and `optimize` succeed.
- [x] **Unique route names** (#4): dropped the plain duplicates (`/privacy-policy`, `/home` → redirects). Covered by a test. Done 2026-11-25.
- [x] **Trim the portfolio resource** (#5): `Route::resource('portfolio', …)->only('index')`. Done 2026-11-25.
- [x] **Config, not `env()`** (#10, #11): add `services.google.tag_id` and `services.notion.*` to `config/services.php`, switch views and controllers to `config()`, and document the keys in `.env.example`. Done 2026-11-25 on `ci/deploy-hostinger` (merged into `main`, PR #9).
- [ ] **Guard global JS** (#16): `darkMode_button.js` should return early when `#check-dark-mode` is missing. Do the same for `menu_buton.js`.
- [ ] **Track images** (#13): narrow the `*.png`/`*.jpg` ignore rule so `public/imgs` and the mesh textures get committed (or move them to storage or a CDN).
- [x] **CI on `main`** (#15): `tests.yml` now runs on pushes to `main` and `develop/**`, on PRs, and from `deploy.yml`. Done 2026-11-26.
- [x] **Fix the 2 failing auth tests**: login and registration redirect to `/home` (laravel/ui) instead of `/dashboard` (Breeze), and `/home` isn't a route. Done 2026-11-25: login passes; the registration test was replaced by "registration is closed".
- [x] **Dependency advisories**: done 2026-11-26 by the Laravel 12 upgrade; `composer` now reports "No security vulnerability advisories found".
- [ ] **`/dashboard` and `/profile` layout**: `<x-app-layout>` renders the site layout, which has `@yield('content')` but no `$slot`, so the Breeze content probably doesn't show. Either give them their own layout or redirect after login to `/projects` (`RouteServiceProvider::HOME`).
- [ ] Remove dead pieces: the `/inWork` route and view (#7) and the root `lang/` folder (#12) once its files are moved to `resources/lang/en`. (`layouts/projects.blade.php` #25 was removed 2026-11-26.)
- [x] **Local Docker environment**: `compose.yml` with app (PHP 8.3), node (Vite), MariaDB 11.4 and Mailpit, the same pattern as thelamb.studio-web. *Done when* `docker compose up -d` serves `http://localhost:8080` and `php artisan test` runs inside the container. Done 2026-11-23; see journal.

### Phase 0b: Deploy with GitHub Actions (Hostinger)

Hostinger shared hosting has PHP and MariaDB over SSH but **no Node**, so GitHub Actions builds everything and uploads files that are ready to run. Reference for the staging/tag extension: [`skLuan/thelamb.studio-web`](https://github.com/skLuan/thelamb.studio-web) (`.github/workflows/{tests,deploy,hostinger}.yml`, `docs/despliegue.md`).

**Current flow** (production only; see `docs/development.md` → Deployment)

```
push to main ─┐
Run workflow ─┴─▶ deploy.yml: tests (tests.yml, PHP 8.4 + MariaDB) ─▶ build (composer --no-dev, npm run build)
                 ─▶ check layout + server PHP ─▶ artisan down ─▶ rsync ─▶ drop bootstrap/cache/*.php
                 ─▶ migrate --force ─▶ optimize ─▶ public/storage (ln) ─▶ artisan up ─▶ smoke test
```

Server layout: `~/domains/luane.online` is the Laravel root and `public_html -> public` (set once with `scripts/hostinger/migrate-layout.sh`).

**Prerequisites**
- [x] One auth stack and unique route names (#3, #4). `optimize` runs `route:cache`. Done 2026-11-25, re-checked 2026-11-26 on Laravel 12 (62 routes, no duplicate names).
- [x] Config, not `env()` (#10). Done 2026-11-25.
- [ ] Track images (#13). The deploy uploads from a git checkout; rsync protects `public/imgs/` and image files already on the server, but a fresh server would miss them.

**Workflows**
- [x] `deploy.yml`: push to `main` + `workflow_dispatch` → production, layout check, rsync, migrate, smoke test and "nothing outside `public/` is reachable" check. Done 2026-11-25 (PR #9).
- [x] PHP 8.4 in CI, the dev image and the server (hPanel shows 8.4.28). `nette/utils` 4.1.6 and `nette/schema` 1.4.0 in the Laravel 12 lock support up to 8.6. Done 2026-11-26.
- [x] Run the tests before every deploy: `tests.yml` is `workflow_call`-able with a MariaDB 11.4 service; `deploy.yml` needs it. Done 2026-11-26.
- [x] `route:cache` back in the deploy (`artisan optimize`). Done 2026-11-26.
- [x] Remove the Laravel skeleton workflows (`issues.yml`, `pull-requests.yml`, `update-changelog.yml`). Done 2026-11-26.
- [ ] Staging: push to `main` → staging, tag `v*` → production, `workflow_dispatch` with an environment choice (move the SSH part into a reusable `hostinger.yml`).
- [ ] Fail the deploy if the server `.env` is missing, `APP_KEY` is empty or `APP_DEBUG=true`.
- [ ] Normalize the SSH key (strip CRLF) and validate it with `ssh-keygen -y` before connecting.
- [ ] Move blog uploads from `public/blog_images` to `storage/app/public`, so rsync doesn't need an exclude to keep them.

**Hostinger (one-time)**
- [x] PHP 8.4 in hPanel (8.4.28 per the response headers, 2026-11-26).
- [ ] Confirm the CLI `php` over SSH is also 8.4 (the deploy prints it and warns otherwise; set the `HOSTINGER_PHP_BIN` variable, e.g. `/opt/alt/php84/usr/bin/php`, if not) and that `intl`, `gd`, `zip`, `bcmath`, `exif` and `fileinfo` are enabled. Filament needs `intl`.
- [ ] Staging subdomain (`testing.luane.online`) with its own database and `.env`.
- [ ] Cron every minute: `cd <app> && php artisan schedule:run`.
- [ ] Back up `public_html` and the database before the first Laravel 12 deploy.

**GitHub (one-time)**
- [ ] Secrets on `production`: `HOSTINGER_SSH_HOST`, `HOSTINGER_SSH_PORT`, `HOSTINGER_SSH_USER`, `HOSTINGER_SSH_KEY`, `HOSTINGER_DEPLOY_PATH`. Optional variable `HOSTINGER_PHP_BIN`. (Set? Not verifiable from here.)
- [ ] With staging: allow `production` only from `v*` tags and require a reviewer.

**Done when:**
- a push to `main` runs the tests, deploys, and the smoke test passes;
- a failed step leaves the site online (not stuck in maintenance mode);
- (staging) `git tag v1.0.0 && git push origin v1.0.0` deploys production;
- the unused `laravel-fans/docker` files (`docker/etc`, `docker/usr`, `docker/var`) are removed.

## Phase 0.5: Blog platform, SEO tool, semantic HTML

Design and reasoning: [design-blog-filament-seo.md](design-blog-filament-seo.md). Answers from Luan (2026-11-25): "RAG" = AI readability (yes); Hostinger can run up to PHP 8.5; posts exist in English too; `/` redirects by browser language.

- [x] **B. Upgrade**: PHP 8.4 (dev image), Laravel 10 → 12.74, Sanctum 4, PHPUnit 11, Collision 8. Done 2026-11-26. Blade 12 treats `@context` as a directive (footer JSON-LD now uses `@@context`).
- [x] **C. Filament panel**: Filament 5.12 (Livewire 4) at `/admin`, `User::canAccessPanel()` = `is_admin`, `user:create --admin`. Project and Skill resources replace the old `/projects` CRUD. Done 2026-11-26.
- [x] **D. Own blog**: `posts` / `categories` / `category_post` / `seo_meta`, row per locale with `translation_group`; Post and Category resources (rich editor, cover on the `blog` disk, SEO section, *Translation of*); `BlogController` + views under `resources/views/blog/`; unprefixed `/blog/*` redirects; `blog:import-binshops`; binshops, its views/config/assets and `laravel-fulltext` removed. Done 2026-11-26.
- [ ] **D2. Run the import in production** after deploying: `php artisan blog:import-binshops --dry-run`, fix languages with `--set`, pair translations with `--pair`, run it, then check `/admin/posts`, rename the second-language categories, and create the admin with `user:create --admin`. Then a later migration drops `binshops_blog_*` and `laravel_fulltext`.
- [ ] **A. SEO + semantics on all pages**: `SeoData` + `<x-seo>` in the layout (title, description, canonical, hreflang, OG, JSON-LD `@graph`), using `seo_meta`; one `<h1>` per page and a real heading outline on home and portfolio (the blog views already have it); `/` and `/portfolio` redirect by browser language; dynamic `robots.txt`; `sitemap.xml`; `llms.txt` and Markdown versions of posts.
- [ ] **A2. SEO audit tool**: `php artisan seo:audit` + a feature test that fails CI on errors.
- [ ] **E. Admin SEO tool**: audit page, SEO settings, redirects resource.
- [ ] **F. (optional)** "Ask my site" chatbot over posts and projects.
- [ ] Responsive images for covers (`srcset`; binshops' medium/thumbnail files are not imported, only the large one).
- [ ] Reading progress bar: it never worked (the layout never loaded `binshops-blog.js`) and was dropped. Rebuild it in `app.js` if wanted.
- [ ] Projects: `image_path` is still a URL; add uploads to the `blog`-style public disk.

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
- [x] **Deployment docs**: Hostinger over SSH via GitHub Actions (`.github/workflows/deploy.yml`), documented in `docs/development.md` → Deployment.
- [x] **Fix `route:cache`** (#3) and add it back to the deploy workflow. Done 2026-11-26 (`artisan optimize`).
- [x] **Run tests before deploy**: `tests.yml` with a MariaDB service, called by `deploy.yml`. Done 2026-11-26.
- [ ] **Decide on Notion**: either delete `CurriculumVitae`, `NotionService`, `blog.blade.php` and `blogpost.blade.php`, or bring back a Notion-powered CV page on top of config-based IDs.
- [ ] **CV page**: a downloadable or online CV (there is a commented-out link in the legacy `inWork` view).
- [ ] **Analytics consent**: a cookie or consent banner for GA and Clarity, in line with the privacy policy text.
- [ ] **Image handling**: uploads for project images (today they are URLs only), with responsive `srcset` (the `<source>` in `card/project` is empty).
- [ ] **Blog comments**: currently `disabled`. Decide whether to keep them off or use the built-in type with captcha.

## Ideas (not committed)

- RSS link in the footer (`/blog/feed` and `/{locale}/blog/feed` work, served by `BlogFeedController`).
- A "now" or changelog page generated from `journal.md`.
- Interactive squids that react to the cursor (Yuka `SeekBehavior` / `FleeBehavior`).
