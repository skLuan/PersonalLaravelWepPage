# Development Journal

One entry per working session, **newest first**. Each entry records what was done, what works, what is broken, and what could not be checked. Write down only what was run or read during the session, and label guesses as guesses.

<details>
<summary>Entry template</summary>

```markdown
## YYYY-MM-DD: <short session title>

**Branch:** <branch> · **Goal:** <one line>

### Done
- ...

### ✅ Works (verified)
- ...

### ❌ Broken / not working
- ...

### ❔ Not verified
- ...

### Next
- ...
```
</details>

---

## 2026-11-25: Blog localization fixes and auth cleanup

**Branch:** `fix/blog-locale-auth` (from `develop/blog` at `1764524`, git worktree at `../PersonalLaravelWepPage-wt/blog-locale-auth`) · **Goal:** fix #27–#30 and do the Phase 0 auth cleanup (#1–#5, #26).

### Done
- **#27 `/es/blog` 404**: confirmed the cause. `BinshopsBlogReaderController@index($category_slug)` received `es`. `SetLocale` now calls `$request->route()->forgetParameter('locale')` and sets `URL::defaults(['locale' => …])`.
- **#28 search 500**: fixed by the same `URL::defaults`, because `route('binshopsblog.search')` now gets the locale. Before the fix, `/blog/search` (unprefixed) also returned 500.
- **#29 feed 500**: new `BlogFeedController` + `resources/views/feed/rss.blade.php` (RSS 2.0, published posts only, `<language>` = current locale). It replaces binshops' controller, which needs `laravelium/feed`; that package is archived and its latest version requires PHP ^8.3.
- **#30 language switcher**: it only adds the prefix when the localized path matches a route. Otherwise (`/projects`, `/login`) it redirects to the same path. It also keeps the query string, and it no longer treats `/essay` as starting with the `es` prefix.
- New bug found and fixed: `partials/index_loop.blade.php` built post links as the relative `blog/<slug>`, which breaks on category pages (`/es/blog/category/x` → `/es/blog/category/blog/<slug>`). It now uses `$post->url()`.
- Removed `App\Models\BlogPost` and `BlogCategories` and their config keys. I grepped `vendor/binshops`: nothing reads `blog_post_model` or `blog_category_model`, so these models were never used.
- **Auth (#1–#3, #26)**: ran `composer remove laravel/ui`, deleted `Auth::routes()`, the 6 laravel/ui controllers, `auth/verify`, `auth/passwords/*` and `HomeController`. `auth/login.blade.php` was laravel/ui's Bootstrap view and has been replaced by Breeze's stub. Removed `/register` and `RegisteredUserController`. Added `php artisan user:create <email>`. Put `/projects` behind `auth`.
- **Routes (#4, #5)**: `/home` and `/{locale}/home` now redirect to `/` and `/{locale}`. Removed the duplicate plain `/privacy-policy`, because the fallback group serves it. The portfolio resource is now `->only('index')`.
- Tests: added `BlogLocaleTest` (11) and `AccessControlTest` (7), and removed `RegistrationTest`.
- Docs: `plan.md` (ticked items, added new ones), `architecture.md`, `development.md`, `CLAUDE.md`.

### ✅ Works (verified)
- `php artisan test`: **40 passed** (116 assertions), in the worktree container.
- `php artisan route:cache` and `php artisan optimize` succeed, with 78 routes and no duplicate names.
- Over HTTP on the worktree server (:8090), with routes cached:
  - 200: `/`, `/es`, `/blog`, `/es/blog`, `/en/blog`, `/es/blog/search?s=a`, `/blog/feed`, `/es/blog/feed`, `/portfolio`, `/es/portfolio`, `/privacy-policy`, `/es/privacy-policy`, `/login`, `/forgot-password`.
  - 302: `/home` → `/`, `/es/home` → `/es`. `/projects`, `/projects/create` and `/dashboard` → `/login`.
  - 404: `/register`, `/es/portfolio/1`.
  - 401: `/blog_admin` (guest).
- The feed output starts with `<?xml` (no leading whitespace) and links to `/es/blog`.

### ❌ Broken / not working
- `composer` reports **61 security advisories in 16 packages** (it was already like this; added to the plan).
- `/dashboard` and `/profile` use `<x-app-layout>` → `layouts/app.blade.php`, which has `@yield('content')` and no `$slot`. Reading the code, the Breeze content likely doesn't render. I did not check this in a browser.

### ❔ Not verified
- Nothing has been checked in a real browser: login form styling (Breeze view on the guest layout), the 3D canvas on blog pages, the language switcher click.
- The dev DB is still empty, so posts, categories and the feed were only tested with test data in the `testing` DB.
- The blog admin login: no user exists yet. `canManageBinshopsBlogPosts()` requires `id === 1` and a fixed email. Running `php artisan user:create <that email>` on an empty `users` table should satisfy that check (not tried).

### Next
- Merge `fix/blog-locale-auth` into `develop/blog`, then run `git worktree remove` and remove the `luane-wt-app` container and `luane_wt_vendor` volume.
- On the main stack after the merge: `docker compose exec app composer install` (laravel/ui is gone from the lock file).
- Fix the dashboard and profile layout, or set `RouteServiceProvider::HOME = '/projects'`.

---

## 2026-11-24: Docker local environment and deployment plan

**Branch:** `develop/blog` (after merge `58ddd15`) · **Goal:** add `compose.yml`, start the local stack, and plan GitHub Actions deployment based on `skLuan/thelamb.studio-web`.

### Done
- Added `compose.yml`, `docker/app/Dockerfile` (PHP 8.3 CLI + `pdo_mysql`, `intl`, `gd`, `zip`, `bcmath`, `exif`, `opcache`, `pcntl`), `docker/app/php.ini`, and `docker/db/init/01-testing-database.sql`. These copy the thelamb.studio-web layout. Services: `app` (:8080), `node` (Vite :5174), `db` (MariaDB 11.4 :3308), `mailpit` (:8026). vendor, node_modules and db each live in a named volume.
- `vite.config.js`: Docker-aware (`VITE_IN_DOCKER`), port **5174** with polling. This resolves #14: Vite no longer takes port 8000.
- `.env`: backed up to `.env.backup`, then pointed at the containers (`APP_URL=http://localhost:8080`, `DB_HOST=db`, user and password `luane`/`luane`). `.env.example` now has the same defaults plus `GOOGLE_TAG_ID` and `NOTION_*` (partly resolves #11).
- `package.json`: added `"name": "luane-personal-site"`. Without it, npm wrote the folder name into `package-lock.json`, which is `www` inside the container.
- `docs/plan.md`: added **Phase 0b**, deployment through GitHub Actions to Hostinger. Updated `docs/development.md`, `CLAUDE.md` and `README.md` for Docker.

### ✅ Works (verified)
- `docker compose up -d`: all 4 containers come up and `db` is healthy. On first start, `composer install` runs in the `vendor` volume and `npm install` in the `node_modules` volume.
- `php artisan migrate --seed` on MariaDB: all 14 migrations run, including the binshops and fulltext ones, and the seeder runs.
- HTTP 200: `/`, `/es`, `/portfolio`, `/es/portfolio`, `/en/portfolio`, `/blog`, `/privacy-policy`, `/es/privacy-policy`, `/login`. Spanish pages render `lang="es"` and Spanish copy.
- Vite: the pages load `localhost:5174/@vite/client`, `app.css` and `app.js`. `app.js` and `3dMain.js` return 200 from 5174. Mailpit UI returns 200.
- **Test suite runs for the first time: 23 passed, 2 failed.**

### ❌ Broken / not working
- 26. **2 auth tests fail**: `users can authenticate…` and `new users can register` expect `/dashboard` but get `/home`. The laravel/ui `LoginController` and `RegisterController` (`$redirectTo = '/home'`) override Breeze's (#3), and `/home` is only defined as `/{locale}/home`, so after logging in a real user lands on a 404.
- 27. **`/es/blog` and `/en/blog` return 404**, even though `route:list` shows `{locale}/blog`. Likely cause (unconfirmed): binshops' `BinshopsBlogReaderController@index($category_slug = null)` receives `es` as the category slug, because the route parameters are passed by position.
- 28. **`/es/blog/search` returns 500**: `search_form.blade.php` calls `route('binshopsblog.search')` without the `locale` parameter.
- 29. **`/blog/feed` returns 500**: `Class "Laravelium\Feed\Feed" does not exist`. The RSS dependency isn't installed.
- 30. The language switcher on `/blog` redirects to `/es/blog`, which is the 404 above. On `/projects` it redirects to `/es/projects`, which is also a 404 (#8).
- Re-confirmed over HTTP: `/projects/create` and `/register` return 200 to guests (#1, #2), `/inWork` returns 500 (#7), and `/es/home` redirects to login (#9).
- `/blog_admin` returns **401** to guests. That is correct, but there is no admin user to log in with yet.

### ❔ Not verified
- Anything in a real browser: the 3D canvas, HMR, dark mode, mobile layout.
- Single blog posts and categories: the new MariaDB database is **empty**, with no posts and no projects. The old local MySQL data, which `.env.backup` points at, was not imported.
- Anything about the deployment (Phase 0b is a plan only). It's also unconfirmed that `luane.online` is on the same Hostinger account as thelamb.studio.

### Next
- Fix #27–#29 to finish "Blog localization", then tick it.
- Phase 0 auth cleanup (it fixes #3, #26 and `route:cache`, and unblocks Phase 0b).
- Optionally, import the old DB: `mysqldump luane | docker compose exec -T db mariadb -uluane -pluane luane`.

---

## 2026-11-23: Resolve `develop/multilang` merge conflict in routes

**Branch:** `develop/blog` · **Goal:** resolve the `routes/web.php` conflict from merging `develop/multilang`.

### Done
- Resolved all conflict hunks in `routes/web.php` and staged it. The merge commit is not made yet.
- Kept multilang's structure: manual blog routes inside `{locale}`, plus the unprefixed fallback group (`*.fallback` names, `/blog_admin`). This is required because multilang sets `include_default_routes => false` and `BlogPost`/`BlogCategories` build URLs from the `.fallback` names.
- Kept HEAD's `App` and `HomeController` imports, and used `HomeController::class` in both groups.
- Dropped HEAD's unprefixed `/` and `/portfolio` routes. The fallback group serves the same URLs, and no view calls `route('home')` or `route('portfolio')` without a locale (checked with grep).

### ✅ Works (verified)
- `php -l routes/web.php` passes, and no conflict markers are left.
- `php artisan route:list` shows 98 routes. It includes the 6 public blog routes under `/{locale}/blog` and 6 under `/blog`, the 20 `binshopsblog.admin.*` routes, and `portfolio.fallback` / `home.fallback`.

### ❌ Broken / not working
- `home.fallback` is the name of both `/` and `/home`, and `home` is the name of both `{locale}` and `{locale}/home`. This was already the case on both branches, and `route:cache` was already failing (#3, #4).

### ❔ Not verified
- I did not test any page in a browser. The blog index, single post, category links and language switcher on `/blog` all still need checking.

### Next
- Commit the merge, then check `/blog`, `/es/blog` and the language switcher in a browser. If they work, tick "Blog localization" in `docs/plan.md`.

---

## 2026-11-23: Full project read-through and documentation

**Branch:** `develop/blog` · **Goal:** read the whole project and its git history, then write the project docs.

### Done
- Read all application code (routes, controllers, models, middleware, views, 3D engine, config, Docker, CI) and the full git log (63 commits, June 2024 → October 2025).
- Added `CLAUDE.md`, this journal, `docs/plan.md`, `docs/architecture.md`, `docs/development.md` and `docs/history.md`, and rewrote `README.md`.
- No application code was changed.

### ✅ Works (verified)
- `php artisan` boots on PHP 8.1.10 (Laravel 10.48.12). `route:list` shows 55 routes, plus the binshops blog routes.
- `vite build` succeeds: `app.js` 69 kB, `app.css` 49 kB, `3dMain.js` **630 kB** (one chunk; Vite warns it is over 500 kB).
- Translations resolve: `__('messages.nav.home')` returns "Home", and validation messages fall back to the framework's English files.
- Locale detection order in `SetLocale` (URL prefix → session → `Accept-Language` → default) matches its code.

### ❌ Broken / not working
**Security**
1. `/projects` CRUD (create, update, delete) has **no auth**, so anyone can change portfolio content.
2. `/register` is open. Anyone can create a user account.

**Routing**
3. `php artisan route:cache` throws `LogicException: Another route has already been assigned name [password.request]`. Breeze `routes/auth.php` and laravel/ui `Auth::routes()` are both loaded. The production entrypoint runs `route:cache`, so a production container would fail on start.
4. Route names `home`, `portfolio` and `privacy-policy` are defined twice (plain and `{locale}`). `route('portfolio')` with no arguments resolves to the localized version and needs a `locale` parameter.
5. `Route::resource('portfolio', …)` registers create/store/show/edit/update/destroy actions, but `PortfolioController` only has `index()`. Those URLs return 500 errors.
6. `projects.show` returns `view('projects.show')`, which doesn't exist (500). The "Ver más" link on `/projects` points there.
7. `/inWork` calls `route('inWorkPortfolio')`, but that route is commented out, so the page errors.
8. The language switcher sends `/blog/...` and `/projects` to `/{locale}/blog/...` and `/{locale}/projects`, and neither route exists (404). Blog localization is on the unmerged `develop/multilang` branch.
9. `/{locale}/home` uses `HomeController`, whose constructor requires `auth`. Guests get sent to the login page.

**Configuration**
10. `env('GOOGLE_TAG_ID')` and `env('NOTION_*')` are called directly in views and controllers. After `config:cache` (which runs in the production entrypoint) they return `null`, so Google Analytics gets an empty ID.
11. `.env.example` is missing `NOTION_API_TOKEN`, `NOTION_CURRICULUM_VITAE_ID` and `GOOGLE_TAG_ID`.
12. The root `lang/en/*.php` files (added in d728263) are ignored, because Laravel 10 uses `resources/lang/` when that folder exists.
13. `.gitignore` excludes `*.png` and `*.jpg`, so `public/imgs/Luan_square.png`, `hero1.jpg` and the squid textures are not tracked. A fresh clone shows broken images.
14. Vite's dev server is pinned to `127.0.0.1:8000`, the same port `php artisan serve` uses by default.
15. The CI workflow `tests.yml` triggers on `master` and `*.x` pushes, but the repo uses `main`. It only runs on PRs.

**Frontend and 3D**
16. `darkMode_button.js` looks for `#check-dark-mode`, which only exists on single blog posts. Every other page throws `TypeError` (null `addEventListener`), which stops the rest of `app.js` from running, including `Alpine.start()`.
17. Pressing space on any 3D page throws: `keyBoardKeys()` toggles `cubeGroup`, which is never assigned.
18. `world.start()` runs twice: `init()` calls `this.start()`, then `main()` calls it again. This registers duplicate keypress listeners.
19. Debug output ships to production: grid and axes helpers are added to the scene, every click logs the world position, and there are many `console.log`s.
20. `PathCreator.responsivePath()` only runs on `resize`, and it only moves the drawn line. Squids hold a *cloned* path (`VehicleStateController.updatePath`), so after a resize the line and the squids' route no longer match.
21. `VehicleStateController` keeps `hasSwitched` and `target` at module level, so all squids share them, and only the first squid reacts to the scroll threshold. This looks unintended.
22. The squid `.glb` model is commented out; squids are rendered as cones. The model "flickered" when last tried (commit b8d441e).
23. The layout `<title>` is always `config('app.name')`. The `$title` passed by the portfolio, privacy and blog views is ignored.
24. The social links in the mobile navbar and the footer JSON-LD are placeholders (`twitter.com/yourprofile`, `youremail@example.com`).
25. `layouts/projects.blade.php` loads `resources/sass/app.scss`, which isn't a Vite input. The layout is unused, but would error if used.

### ❔ Not verified
- **Test suite:** `phpunit` hangs or fails, because no MySQL is reachable on `localhost:3306` and no containers are running. The failures could not be attributed to code.
- Page rendering in a browser, dark mode visuals, and mobile layout. Nothing was served this session.
- Blog admin (`/blog_admin`) permissions: binshops requires `canManageBinshopsBlogPosts()` on the `User` model, and this wasn't checked.
- The deployment target and process. There is no Dockerfile or compose file in the repo, only `docker/` entrypoint and config files from `laravel-fans/docker`.

### Next
- Start with **Phase 0** in [docs/plan.md](docs/plan.md): lock down `/projects` and `/register`, remove the duplicate auth stack, fix `route:cache`, and guard `darkMode_button.js`.
