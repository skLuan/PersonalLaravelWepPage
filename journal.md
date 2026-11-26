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

## 2026-11-26: Main stack 500 after the merge (`Filament\PanelProvider` not found), branch cleanup

**Branch:** `claude/blog-filament-seo-migration-6c5c1d` (commit `8dff4ef`) · **Goal:** fix the main Docker stack after `develop/blog` was fast-forwarded to `de62ac1`, then delete merged branches.

### Done
- Cause: `compose.yml` ran `composer install` only when `vendor/autoload.php` was missing. The `luane_vendor` volume still held Laravel 10.48 without Filament, and the image was still PHP 8.3.
- Rebuilt `luane-app:dev` (PHP 8.4.28). Ran `composer install` + `migrate --force` in a one-off `docker compose run --rm app`, which applied the 2 new migrations to the `luane` DB.
- `compose.yml` (on this branch, **not yet in `develop/blog`**): every app start now runs `git config safe.directory` → `composer install` → `migrate --force` → `serve`. Documented in `CLAUDE.md` and `docs/development.md`.
- Removed the `fix/blog-locale-auth` worktree, the `luane-wt-app` container and the `luane_wt_vendor` volume. Deleted the merged local branches `fix/blog-locale-auth`, `develop/multilang`, `develop/threejs`, `develop/views`, `blog/larablog`, `blog/notion` and `portfolio` (`git branch -d`).

### ✅ Works (verified)
- `:8080` after the fix: `/`, `/es`, `/en/blog`, `/en/portfolio` and `/admin/login` return 200; `/blog` → 302 `/en/blog`; `/admin` → login.
- The new start command (without `serve`) exits 0 in a throwaway container on the main stack's volumes, reporting Laravel 12.74.0.

### ❔ Not verified
- The edited `compose.yml` has not been used by the real `luane-app` container yet: it starts from `develop/blog`, which still has the old command.

### Kept on purpose
- `feat/3d-cleanup` (worktree `../PersonalLaravelWepPage-wt/3d-cleanup`, no commits of its own yet, probably in use), `ci/deploy-hostinger` (2 unmerged commits), `main`, `develop/blog`, and this branch (1 commit ahead of `develop/blog`).
- Remote branches `origin/develop/threejs`, `origin/develop/views` and `origin/portfolio` were merged into `develop/blog` and had no open PRs. Deleted on GitHub with Luan's OK (`git push origin --delete`). Remaining on GitHub: `main`, `develop/blog`, `ci/deploy-hostinger`.

---

## 2026-11-26: Laravel 12 + Filament 5, own blog models, binshops removed

**Branch:** `claude/blog-filament-seo-migration-6c5c1d` (worktree `.claude/worktrees/blog-filament-seo-migration-6c5c1d`, with `fix/blog-locale-auth` merged in at `1b99869`) · **Goal:** phases B, C and D of [docs/design-blog-filament-seo.md](docs/design-blog-filament-seo.md), so the SEO work can start.

### Done
- Merged `fix/blog-locale-auth` (security and locale fixes) into this branch. It was a real merge, because `main` has 2 PR merge commits that the fix branch lacks. There were no conflicts.
- **PHP 8.4** dev image (`docker/app/Dockerfile`). **Laravel 10.48 → 12.74**, Sanctum 4.5, PHPUnit 11.5, Collision 8.9, Breeze 2.4, Sail 1.71. `composer` resolved this with no conflicts. Fixed one break: Blade 12 compiles `@context` in the footer JSON-LD as a directive (ParseError), so it is now `@@context`.
- **Filament 5.12 + Livewire 4.4**: `/admin` panel with skl-pink as the primary color, `users.is_admin` + `User::canAccessPanel()`, `user:create --admin`, `LocalAdminSeeder`. Resources: Post, Category, Project, Skill.
- **Own blog**: migrations `posts`, `categories`, `category_post` and `seo_meta` (one row per language via `translation_group`). Models `Post`, `Category` and `SeoMeta`, traits `HasTranslations` and `HasSeo`. `blog` disk = `public/blog_images`. `BlogController` and views `resources/views/blog/{index,show,search}` + partials, with one `<h1>` per page, posts as `<article>`, `<time datetime>`, breadcrumb `<nav>`, restored pagination and `hreflang` links to translations. `resources/lang/{en,es}/blog.php`. New RSS feed on `Post`.
- Routes: `/{locale}/blog/*` → `BlogController`. Unprefixed `/blog/{slug}` and `/blog/category/{slug}` 301 to their own language; `/blog`, `/blog/search` and `/blog/feed` 302 to the visitor's language. `/projects/*` and `/blog_admin/*` 301 to the panel.
- `php artisan blog:import-binshops`: guesses each post's language, takes `--set`, `--pair`, `--locale` and `--dry-run`, can run again (`legacy_id`), copies `seo_title`/`meta_desc` to `seo_meta`, and creates one category per language.
- Removed: `binshops/laravel-blog` (and with it intervention/image 2, swisnl/laravel-fulltext, cviebrock sluggable, laravel/helpers), `config/binshopsblog.php`, `config/laravel-fulltext.php`, `resources/views/vendor/binshopsblog`, `public/binshops-*`, `ProjectController` + `resources/views/projects`, and `layouts/projects.blade.php` (#25).
- Tests: rewrote `BlogLocaleTest` (13) and added `AdminPanelTest` (17) and `ImportBinshopsBlogTest` (4). Updated `AccessControlTest`.
- Docs: `CLAUDE.md`, `docs/architecture.md`, `docs/development.md`, `docs/plan.md` (Phase 0.5) and `README.md`.

### ✅ Works (verified)
- `php artisan test` in container `luane-fil-app` (PHP 8.4.28) against DB `testing_fil`: **63 passed** (208 assertions). `route:cache` and `optimize` succeed. `composer` reports no security advisories.
- `npm run build` succeeds; the 3D chunk is still 630 kB.
- HTTP on :8091 (built assets, DB `luane_fil` with 2 sample binshops posts imported via `--pair=1:2`): 200 for `/`, `/es`, `/en/blog`, `/es/blog`, both posts, search, feed, `/en/portfolio` and `/admin/login`. `/blog` → 302 `/es/blog` (with `Accept-Language: es`). `/blog/existir-como-humano` → 301. `/projects` and `/blog_admin` → 301 to the panel. `/admin` → login.
- In the built-in browser: the Spanish post page renders (breadcrumb, h1, Spanish date, body h2), the blog index renders with the 3D canvas, admin login works, the posts table shows locale, linked translation and category, and the post edit form loads the imported SEO description. **Saving the form updated `seo_meta` through the relationship.**
- The import's language guess was right for both sample posts (es and en).

### ❌ Broken / not working
- The English category created by the import is named "Reflexiones": binshops categories have a single name. Rename it in the admin after importing.
- The reading progress bar never worked (the layout never loaded `binshops-blog.js`). It was dropped; added to the plan.
- Imported posts keep only binshops' *large* image as the cover. The medium and thumbnail files are not used, so there is no `srcset` yet.

### ❔ Not verified
- The import against **real** data: the dev DB had 0 posts. The real posts are in production (Hostinger) or the old local MySQL (`.env.backup`).
- Rich-editor image attachments (upload into `blog_images/content`) and cover upload in the browser. Only a text field was saved.
- Hostinger: PHP 8.4 availability (Luan says up to 8.5), and whether `composer install` there runs `filament:upgrade` correctly.
- Dark mode and mobile layout of the new blog views. The admin panel on mobile.
- The JS errors from #16 (`darkMode_button.js`) were not rechecked.

### Next
- Review, then merge this branch (it includes `fix/blog-locale-auth`).
- Phase 0.5-A: `SeoData` + `<x-seo>`, semantic home/portfolio, `/` redirect by language, sitemap, robots, `llms.txt`.
- After deploying: run `blog:import-binshops` in production (plan item D2).
- Clean up: `docker rm -f luane-fil-app`, plus the `luane_fil_vendor` and `luane_fil_node_modules` volumes and the `luane_fil`/`testing_fil` DBs when this worktree is done.

---

## 2026-11-25: Design: Filament vs binshops, SEO tool, semantic HTML

**Branch:** `claude/blog-filament-seo-migration-6c5c1d` · **Goal:** decide whether to move the blog to Filament, and plan the SEO/AI-visibility and HTML-structure fixes.

### Done
- Wrote [docs/design-blog-filament-seo.md](docs/design-blog-filament-seo.md). The recommendation is own blog models + a Filament admin, after a Laravel upgrade; SEO and semantic fixes come next.

### ✅ Works (verified)
- Packagist (2026-11-25): binshops v10.0.0 (2025-04-16) is the latest; Filament 5.12.0 requires PHP ^8.2.

### ❌ Broken / not working (from reading the views)
- No `<h1>` on home, portfolio or blog index. Blog pagination was commented out (only 10 posts linked). The post body was wrapped in `<p>`. `/` vs `/en` serve duplicate content with no canonical. No sitemap. The mobile menu element has `id` twice. (The blog items were fixed on 2026-11-26.)

### ❔ Not verified
- None beyond what the next entry covers.

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
- Tests: added `BlogLocaleTest` (11) and `AccessControlTest` (7), and removed `RegistrationTest`. `tests/TestCase.php` now calls `withoutVite()`: without `public/hot` or `public/build`, 15 tests failed with `ViteManifestNotFoundException`.
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
