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
