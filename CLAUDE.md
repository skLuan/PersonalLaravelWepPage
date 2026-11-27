# CLAUDE.md

Guidance for Claude Code when working in this repository.

## Session workflow (do this every session)

1. **Start:** read [journal.md](journal.md), at least the latest entry, and the open items in [docs/plan.md](docs/plan.md).
2. **End:** add a new entry at the top of `journal.md`, using the template in that file. List what works, what doesn't, and what you could not verify. Record only what you ran or read yourself; label assumptions.
3. When a feature from `docs/plan.md` is finished, tick it there. When you find new work, add it there.
4. If you change architecture, routes, env vars or commands, update the matching file in [docs/](docs/) in the same change.

## What this project is

Luan Erazo's personal site: a home page, a portfolio and a blog, with an animated Three.js + Yuka.js background (a school of "squid" agents that follow a path).

- **Backend:** Laravel 12 (PHP ^8.2; the dev image and Hostinger target are PHP 8.4), MariaDB, Blade views.
- **Admin:** Filament 5 panel at `/admin` (posts, categories, projects, skills). Only users with `is_admin` get in.
- **Blog:** own models `Post` / `Category` (one row per language, linked by `translation_group`) + `SeoMeta`. Public pages at `/{locale}/blog/*` (`BlogController`). The old `binshops/laravel-blog` package is removed; `php artisan blog:import-binshops` copies its tables.
- **Portfolio:** `Project` and `Skill` models (many-to-many via `project_skill`), managed in the admin panel.
- **Frontend:** Vite 5, Tailwind 3 (`darkMode: 'class'`), Alpine, iconify-icon, Three.js 0.167, Yuka 0.7.8.
- **i18n:** English and Spanish, using URL prefixes `/{locale}/...` and the `SetLocale` middleware.
- **Legacy:** Notion API (`CurriculumVitae` controller, `NotionService`). No route uses it any more; it is kept for reference.

See [docs/architecture.md](docs/architecture.md) for the full map.

## Commands

The local environment is Docker (`compose.yml`): app at http://localhost:8080, Vite at :5174, MariaDB at :3308 (`luane`/`luane`), Mailpit at :8026.

```bash
docker compose up -d                                   # start everything; app runs composer install + migrate on every start
docker compose restart app                             # after pulling new packages/migrations (e.g. a 500 "Class … not found")
docker compose up -d --build app                       # after a Dockerfile change (PHP version)
docker compose exec app php artisan migrate --seed     # seeds the skills table only
docker compose exec app php artisan test               # uses the `testing` DB (docker/db/init)
docker compose exec app php artisan route:list --except-vendor
docker compose exec app composer …                     # composer runs INSIDE: vendor/ is a named volume
docker compose run --rm node npm run build             # production assets -> public/build (gitignored)
docker compose logs -f app node
```

`.env` uses container hosts (`DB_HOST=db`, `MAIL_HOST=mailpit`). The pre-Docker `.env` is saved as `.env.backup`. Running natively also works (see docs/development.md, Option B), with Vite on 127.0.0.1:5174.

Deployment isn't built yet. The plan is GitHub Actions → Hostinger over SSH, copied from `skLuan/thelamb.studio-web` (local checkout at `../thelamb.studio`); see Phase 0b in `docs/plan.md`.

## Code map

| Area | Location |
|---|---|
| Routes | `routes/web.php` (public + localized), `routes/auth.php` (Breeze) |
| Controllers | `app/Http/Controllers/` (`PortfolioController`, `BlogController`, `BlogFeedController`, Breeze `Auth/*`, legacy `CurriculumVitae`) |
| Admin | `app/Filament/Resources/{Posts,Categories,Projects,Skills}`, panel in `app/Providers/Filament/AdminPanelProvider.php` |
| Blog models | `app/Models/{Post,Category,SeoMeta}.php`, traits `app/Models/Concerns/{HasTranslations,HasSeo}.php` |
| Commands | `php artisan user:create <email> [--admin]` (registration is closed), `php artisan blog:import-binshops [--dry-run] [--pair=a:b] [--set=id:locale]` |
| Locale | `app/Http/Middleware/SetLocale.php` (alias `setlocale`), `config/app.php` → `available_locales` |
| Translations | `resources/lang/{en,es}/messages.php`, `portfolio.php`, `blog.php`. **Not** the root `lang/` folder (see gotchas) |
| Layout | `resources/views/layouts/app.blade.php`. Every page extends it. It loads GA + Clarity |
| Components | Classes in `app/View/Components/`, Blade in `resources/views/components/` |
| 3D entry | `resources/js/3dMain.js`. Picks `PortfolioWorld` or `BlogWorld` from the URL path |
| 3D engine | `resources/js/World/` → `components/` (meshes, `PathCreator`, `squid`) and `systems/` (`Loop`, `Resizer`, `VehicleStateController`, …) |
| Styles | `resources/css/app.css`, theme tokens in `tailwind.config.js` |

## Conventions

- **Colors:** use the `skl-*` Tailwind tokens (`skl-black`, `skl-grey`, `skl-white`, `skl-pink` `#DC105F`, `skl-yellow` `#FFC030`, `skl-purple` `#301770`). Fonts are `font-skl-titles` (Chakra Petch) and `font-skl-nunito`.
- **Dark mode:** toggle the `dark` class on `<html>`. The choice is saved in `localStorage.theme`, and an inline script in the layout applies it before paint.
- **Strings:** never hard-code UI text. Add it to **both** `resources/lang/en/*.php` and `resources/lang/es/*.php`. Files use flat dotted keys (`'nav.home' => ...`), read with `__('messages.nav.home')`.
- **3D loop:** anything animated goes into `loop.updatables` and must expose `tick(delta)`. Yuka-driven meshes set `matrixAutoUpdate = false` and sync from `entity.worldMatrix`.
- **3D bundle:** `3dMain.js` is only included on routes named `portfolio*` or `blog.*` (see the layout). Pages that want the canvas also render `<x-three-canvas />`.
- **Commits:** conventional commits (`feat(i18n): …`, `fix(...)`, `docs: …`). The active branch is `develop/blog`; PRs target `main`.
- Existing code comments are often in Spanish. Either language is fine; keep new comments short.

## Gotchas (verified 2026-11-26)

- **Route names must stay unique**, or `route:cache` / `optimize` (which the deploy runs) fail. `AccessControlTest::test_route_names_are_unique` catches this. Localized routes use the plain name (`home`, `portfolio`, `blog.index`); the unprefixed copies end in `.fallback`.
- `SetLocale` removes `{locale}` from the route parameters, so controllers inside the `{locale}` group **can't** take a `$locale` argument. Use `app()->getLocale()` instead. `route()` fills in `locale` from `URL::defaults`.
- Never call `env()` outside `config/`. Production runs `optimize` (`config:cache`), so `env()` returns `null` there. `GOOGLE_TAG_ID` and `NOTION_*` are read via `config('services.google.tag_id')` / `config('services.notion.*')`.
- **Deploy:** every push to `main` runs the tests and then deploys to Hostinger (`.github/workflows/deploy.yml`, which calls `tests.yml`). CI and the dev image use PHP 8.4, like the server. See `docs/development.md` → Deployment.
- Root `lang/` is ignored, because Laravel prefers `resources/lang/` when it exists.
- `darkMode_button.js` runs on every page but the toggle only exists on single blog posts. Every other page throws a `TypeError` and never reaches `Alpine.start()`.
- `.gitignore` excludes `*.png` and `*.jpg`, so `public/imgs/*` (profile photo, hero) is not in git.
- `/dashboard` and `/profile` use `<x-app-layout>`, which renders `layouts/app.blade.php`. That layout uses `@yield('content')`, not `$slot`, so the Breeze page content is probably not shown (not checked in a browser).
- Blog uploads go to `public/blog_images/` (the `blog` disk), which is untracked. Any deploy that syncs `public/` with `--delete` must exclude it.
- Unstyled pages + CORS errors for `127.0.0.1:8000/@vite/client` = stale `public/hot`. It happens when Vite is killed or reloads with an old `vite.config.js` (e.g. on `ci/deploy-hostinger`). `docker compose restart node` fixes it. The only local stack is compose project `luane`.
- Filament's assets (`public/{js,css,fonts}/filament`) are gitignored. `composer install` regenerates them (`filament:upgrade` in `post-autoload-dump`).
- Blade 12 treats `@context` as a directive: write `@@context` in inline JSON-LD.
- Local admin for `/admin`: `php artisan db:seed --class=LocalAdminSeeder` (local/testing only; credentials in that file).

The full list, with status, is in `journal.md`.
