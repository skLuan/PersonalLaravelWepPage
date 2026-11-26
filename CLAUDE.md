# CLAUDE.md

Guidance for Claude Code when working in this repository.

## Session workflow (do this every session)

1. **Start:** read [journal.md](journal.md), at least the latest entry, and the open items in [docs/plan.md](docs/plan.md).
2. **End:** add a new entry at the top of `journal.md`, using the template in that file. List what works, what doesn't, and what you could not verify. Record only what you ran or read yourself; label assumptions.
3. When a feature from `docs/plan.md` is finished, tick it there. When you find new work, add it there.
4. If you change architecture, routes, env vars or commands, update the matching file in [docs/](docs/) in the same change.

## What this project is

Luan Erazo's personal site: a home page, a portfolio and a blog, with an animated Three.js + Yuka.js background (a school of "squid" agents that follow a path).

- **Backend:** Laravel 10 (PHP ^8.1), MySQL, Blade views.
- **Blog:** the `binshops/laravel-blog` package (routes `/blog/*`, admin at `/blog_admin/*`). Views are overridden in `resources/views/vendor/binshopsblog/`.
- **Portfolio:** `Project` and `Skill` models (many-to-many via `project_skill`), CRUD at `/projects`.
- **Frontend:** Vite 5, Tailwind 3 (`darkMode: 'class'`), Alpine, iconify-icon, Three.js 0.167, Yuka 0.7.8.
- **i18n:** English and Spanish, using URL prefixes `/{locale}/...` and the `SetLocale` middleware.
- **Legacy:** Notion API (`CurriculumVitae` controller, `NotionService`). No route uses it any more; it is kept for reference.

See [docs/architecture.md](docs/architecture.md) for the full map.

## Commands

The local environment is Docker (`compose.yml`): app at http://localhost:8080, Vite at :5174, MariaDB at :3308 (`luane`/`luane`), Mailpit at :8026.

```bash
docker compose up -d                                   # start everything (first run installs vendor + node_modules into volumes)
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
| Controllers | `app/Http/Controllers/` (`PortfolioController`, `ProjectController`, `HomeController`, legacy `CurriculumVitae`) |
| Locale | `app/Http/Middleware/SetLocale.php` (alias `setlocale`), `config/app.php` → `available_locales` |
| Translations | `resources/lang/{en,es}/messages.php`, `portfolio.php`. **Not** the root `lang/` folder (see gotchas) |
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
- **3D bundle:** `3dMain.js` is only included on routes named `portfolio*` or `binshopsblog*` (see the layout). Pages that want the canvas also render `<x-three-canvas />`.
- **Commits:** conventional commits (`feat(i18n): …`, `fix(...)`, `docs: …`). The active branch is `develop/blog`; PRs target `main`.
- Existing code comments are often in Spanish. Either language is fine; keep new comments short.

## Gotchas (verified 2026-11-23)

- `php artisan route:cache` **fails**: `password.request` and other route names are defined twice, because both Breeze (`routes/auth.php`) and laravel/ui (`Auth::routes()`) are registered. The production Docker entrypoint runs `route:cache`, so it will crash.
- `/projects` CRUD and `/register` are **public** (no auth middleware).
- `env()` is called directly in Blade views and controllers (`GOOGLE_TAG_ID`, `NOTION_*`). After `config:cache` these return `null`. Read them through `config()` instead.
- Root `lang/` is ignored, because Laravel 10 prefers `resources/lang/` when it exists.
- `darkMode_button.js` runs on every page but the toggle only exists on single blog posts. Every other page throws a `TypeError` and never reaches `Alpine.start()`.
- `.gitignore` excludes `*.png` and `*.jpg`, so `public/imgs/*` (profile photo, hero) is not in git.
- 2 Breeze tests fail (login and registration redirect to `/home`) because laravel/ui's controllers override Breeze's. `/home` only exists as `/{locale}/home`.
- Blog uploads go to `public/blog_images/`, which is untracked. Any deploy that syncs `public/` with `--delete` must exclude it.

The full list, with status, is in `journal.md`.
