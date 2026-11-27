# Architecture

This is a server-rendered Laravel 12 site (PHP 8.4 in development and on Hostinger). Blade builds every page; a Filament 5 panel at `/admin` manages the content. A Vite bundle adds small interactions (menu, dark mode, language dropdown), and on some pages a full-screen Three.js canvas with Yuka-driven agents sits behind the content.

```
Browser
  │  GET /es/portfolio
  ▼
Laravel (web middleware) ──► SetLocale (URL prefix → session → Accept-Language → 'en')
  │
  ├── PortfolioController@index ──► Project::with('skills') ──► MySQL
  ├── BlogController / BlogFeedController (/{locale}/blog/*) ──► Post, Category ──► MariaDB
  ├── Filament panel /admin (Livewire) ──► Post, Category, Project, Skill
  └── closures (home, privacy-policy)
  │
  ▼
Blade: layouts/app ─ navbar ─ @yield('content') ─ footer
  │      ├─ @vite app.css + app.js            (every page)
  │      └─ @vite 3dMain.js                    (routes portfolio* / blog.*)
  ▼
Browser: <x-three-canvas/> #scene-container ◄── 3dMain.js → PortfolioWorld | BlogWorld
```

## Routes

Source: `routes/web.php`, `routes/auth.php`, plus routes registered by packages.

| URL | Handler | Notes |
|---|---|---|
| `/` (`home.fallback`), `/{locale}` (`home`) | closure → `home` view | `locale` ∈ `config('app.available_locales')` = `en`, `es`. |
| `/home`, `/{locale}/home` | redirect | To `/` and `/{locale}`. |
| `/portfolio`, `/{locale}/portfolio` | `PortfolioController@index` | Lists projects with skills. `resource(...)->only('index')`. |
| `/projects/*`, `/blog_admin/*` | redirects | 301 to `/admin/projects` and `/admin/posts` (the old CRUDs are gone). |
| `/{locale}/blog`, `/search?s=`, `/feed`, `/category/{slug}`, `/{slug}` | `BlogController`, `BlogFeedController` | Lookups are scoped to the URL locale. A slug from the other language redirects to its translation (or to the post in its own language). Drafts/scheduled posts: 404, except for admins (preview). 10 per page. |
| `/blog`, `/blog/search`, `/blog/feed` | closures | 302 to the visitor's locale (session → `Accept-Language` → `en`). |
| `/blog/{slug}`, `/blog/category/{slug}` | closures | 301 to the post/category in its own language (keeps old binshops links working). |
| `/admin/*` | Filament | Login at `/admin/login`. `User::canAccessPanel()` = `is_admin`. Resources: posts, categories, projects (route key `slug`), skills. |
| `/privacy-policy`, `/{locale}/privacy-policy` | closure | Plus redirects from `/privacy`, `/pp`, `/policy`, and several typo URLs. |
| `/locale/{locale}` | closure | Saves the locale in the session and redirects to the previous path with the new prefix. If that path has no localized route (`/projects`, `/login`), it redirects to the path unchanged. |
| `/dashboard`, `/profile` | Breeze | Auth only. |
| `/login`, `/forgot-password`, `/reset-password/*`, `/verify-email/*`, … | Breeze (`routes/auth.php`) | The only auth stack (laravel/ui was removed 2026-11-25). **No `/register`**: create users with `php artisan user:create`. |
| `/inWork` | closure | Legacy landing page; currently errors. |
| `/api/user` | Sanctum | Laravel default. |

## Data model

```
users                     Laravel default (+ Breeze profile)
projects                  id, title, slug (unique), short_description, image_path (URL),
                          site_url, body (HTML/Markdown), timestamps
skills                    id, name (unique), timestamps         ← seeded: Ux/Ui, Wordpress, Frontend, Laravel
project_skill             id, project_id → projects (cascade), skill_id → skills (cascade), timestamps
posts                     id, translation_group (uuid), locale, slug, title, subtitle, excerpt, body (HTML),
                          cover_image (path on the `blog` disk), cover_alt, author_id → users, published_at
                          (null = draft, future = scheduled), legacy_id (binshops id)
                          UNIQUE(locale, slug), UNIQUE(translation_group, locale)
categories                id, translation_group, locale, slug, name, description, legacy_id
category_post             category_id, post_id
seo_meta                  morph seoable (posts today), title, description, og_image, canonical_url,
                          noindex, focus_keyword
users.is_admin            gate for /admin
binshops_*                old blog tables. Only read by `blog:import-binshops`; drop after the import
                          runs in production
laravel_fulltext          old binshops search index, unused; drop with binshops_*
```

`Project` uses `spatie/laravel-sluggable` (the slug comes from `title` unless one is typed in the admin) and binds routes by `slug`, including in Filament (`/admin/projects/{slug}/edit`).

## Blog

History: Notion → `binshops/laravel-blog` (April 2025) → own models + Filament (November 2026).

- **One row per language.** `Post` and `Category` use `HasTranslations`: rows sharing `translation_group` are translations, so each language has its own slug and URL. In the admin, *Translation of* sets the group.
- **SEO overrides** live in `seo_meta` (`HasSeo` → `$post->seo`), edited in the post form's SEO section. Empty fields fall back to the title and excerpt.
- **Images** use the `blog` disk = `public/blog_images` (URL `/blog_images`), so the old binshops files keep their URLs and no `storage:link` is needed. Covers go to `covers/`, editor attachments to `content/`.
- **Body** is HTML from Filament's rich editor (H2/H3 only; the title is the page H1), printed unescaped: only admins can write it.
- **Import:** `php artisan blog:import-binshops` copies posts, categories and SEO fields, guesses each post's language from common words (`--set=id:locale` overrides, `--locale=` forces all), links translations with `--pair=a:b`, and can run again (matches on `legacy_id`). `--dry-run` prints the plan.
- **Search** is a `LIKE` query on title, subtitle, excerpt and body in the current language.

## Internationalization

- `config/app.php`: `locale = en`, `fallback_locale = en`, `available_locales = ['en', 'es']`.
- The `SetLocale` middleware (alias `setlocale`) runs on the `{locale}`-prefixed group and the unprefixed fallback group. It sets `App::setLocale()`, saves the locale in the session, and sets `URL::defaults(['locale' => …])`, so `route('blog.show', ['post' => $slug])` works without passing a locale. It also **removes `locale` from the route parameters** (`forgetParameter`), because Laravel passes route parameters to controllers by position: `BlogController::show(string $slug)` gets the slug, not `es`.
- Strings live in `resources/lang/{en,es}/messages.php` (nav, home, footer, language switcher), `portfolio.php` and `blog.php`. The keys are flat with dots (`'hero.name'`).
- `<x-language-switcher />` lists the other locales and links to `route('locale.switch', $locale)`.
- The layout sets `<html lang dir>`, with RTL support ready for `ar`, `he`, `fa` and `ur`.
- Blog content is localized per row (see Blog). `develop/multilang` is superseded and should not be merged.

## Frontend

| File | Role |
|---|---|
| `resources/js/app.js` | Loaded on every page: iconify-icon, Alpine, `menu_buton.js` (mobile menu and back-to-top), `darkMode_button.js`. |
| `resources/js/3dMain.js` | Separate Vite entry. Finds `#scene-container` and picks `PortfolioWorld` for `/portfolio`, `BlogWorld` for `/blog`, or `PortfolioWorld` as the default. Then it runs `await world.init()` and `world.start()`. |
| `resources/css/app.css` | Tailwind layers and global typography. (`resources/sass/` is legacy and is no longer a Vite input.) |
| `tailwind.config.js` | `skl-*` colors, `skl-titles` / `skl-nunito` fonts, `darkMode: 'class'`. |

**Dark mode:** an inline `<head>` script applies `localStorage.theme`, falling back to `prefers-color-scheme`. The toggle component `<x-darkmode-button />` is currently used only on single blog posts.

**Analytics:** the layout loads Google Analytics (`GOOGLE_TAG_ID`) and Microsoft Clarity (project ID hard-coded). The privacy policy page and the footer text describe both.

## 3D engine (`resources/js/World/`)

```
World/
├── PortfolioWorld.js / BlogWorld.js   scene setup per page (near-identical; BlogWorld passes its own path + colors)
├── World.js                           original prototype (single cone on a path), not used by 3dMain
├── components/
│   ├── camera.js scene.js lights.js material.js meshGroup.js cube/cone/sphere/cylinder.js
│   ├── PathCreator.js                 YUKA.Path + THREE.LineLoop visual, responsive waypoints
│   └── squid.js                       builds a squid (currently a scaled cone) + its Yuka vehicle
└── systems/
    ├── Loop.js                        renderer.setAnimationLoop → tick(delta) on every updatable
    ├── Resizer.js                     keeps camera aspect / renderer size in sync with the container
    ├── renderer.js                    WebGLRenderer { antialias, alpha }
    ├── entityManager.js               YUKA.EntityManager wrapped with tick()
    ├── vehicleCreator.js              YUKA.Vehicle + render sync + VehicleStateController
    ├── VehicleStateController.js      steering "states": wander, follow path, on-path, separation/cohesion
    ├── LoaderManager.js               singleton THREE.LoadingManager
    ├── coordenatesSettler.js          DOM element → Yuka world coords (for DOM-driven paths)
    └── controls.js helpers.js mathUtils.js aiVehicle.js
```

**How a frame works:** `Loop` calls `tick(delta)` on each entry in `updatables` (camera, the squid `EntityManager`, `PathCreator`). The entity manager advances Yuka. Each vehicle's steering behaviours update its `worldMatrix`, and the `sync` callback copies that into the Three.js mesh (`matrixAutoUpdate = false`). Then the renderer draws the scene.

**Camera:** the camera sits top-down at `(0, 120, 0)`. On scroll, its `z` moves by `scrollDelta × 0.03`, so the scene "travels" with the page.

**Squids:** `init()` creates 5 squids at random positions. Each one gets a copy of the `PathCreator` path, wander + separation + cohesion behaviours, and a neighbourhood radius of 10.

## Legacy: Notion integration

`CurriculumVitae` (controller) and `NotionService` (a plain class that extends `ServiceProvider` but isn't registered) read pages, blocks and databases from Notion through `fiveam-code/laravel-notion-api`, using `NOTION_API_TOKEN` and `NOTION_CURRICULUM_VITAE_ID`. They powered the first versions of the home page and blog (`blog.blade.php`, `blogpost.blade.php`). No route points to them now. [plan.md](plan.md) lists the decision on whether to keep them.

## Infrastructure

- `docker/` contains the files generated by `laravel-fans/docker`: an Apache vhost (`DocumentRoot public`, HTTPS and no-www redirects), a cron entry for `schedule:run`, and `docker-laravel-entrypoint`. The entrypoint takes `CONTAINER_ROLE` = `app` | `scheduler` | `queue`, generates `APP_KEY` locally, runs `config:cache` and `route:cache` outside local, and runs `migrate --force` on every start.
- There is **no Dockerfile or docker-compose** in the repo.
- `.github/workflows/`: `tests.yml` (PHP 8.4 + MariaDB, on PRs, `develop/**` pushes and before each deploy) and `deploy.yml` (push to `main` → tests → build → rsync to Hostinger). See `docs/development.md` → Deployment.
