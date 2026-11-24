# Personal Web Page: Luan Erazo

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red?style=flat-square&logo=laravel)](https://laravel.com/)
[![Three.js](https://img.shields.io/badge/Three.js-0.167-black?style=flat-square&logo=three.js)](https://threejs.org/)
[![Yuka](https://img.shields.io/badge/Yuka-0.7.8-green?style=flat-square)](https://github.com/Mugen87/yuka)
[![Tailwind CSS](https://img.shields.io/badge/TailwindCSS-3.x-38BDF8?style=flat-square&logo=tailwind-css)](https://tailwindcss.com/)
[![Vite](https://img.shields.io/badge/Vite-5-646CFF?style=flat-square&logo=vite)](https://vitejs.dev/)

This is my personal site, with a **home page**, a **portfolio** and a **blog**. Behind the content runs an interactive **Three.js** scene in which a school of AI-driven "squids" (**Yuka.js** steering behaviours) swims along a path as you scroll.

Live site: <https://luane.online>

> 🚧 This project is in active development. Known issues and planned work are tracked in [journal.md](journal.md) and [docs/plan.md](docs/plan.md).

## Features

- **Portfolio:** projects and skills stored in MySQL (`projects`, `skills`, `project_skill`), shown as cards.
- **Blog:** built on [`binshops/laravel-blog`](https://github.com/binshops/laravel-blog), with an admin panel, categories, search, RSS and a reading progress bar.
- **3D background:** separate scenes for the portfolio and the blog, with steering agents (wander, path following, separation, cohesion) and a camera that follows the scroll.
- **English / Spanish:** `/{locale}/…` routes, detection from session and browser, and a language switcher.
- **Dark mode:** class-based Tailwind theme, saved in `localStorage`, and it follows the system setting by default.
- **Analytics:** Google Analytics and Microsoft Clarity, described on the privacy policy page.

## Quick start

Requirements: PHP 8.1+, Composer, Node 18+, MySQL.

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
# set DB_* in .env
php artisan migrate --seed

npm run dev                      # Vite → 127.0.0.1:8000
php artisan serve --port=8080    # open http://localhost:8080
```

`public/imgs/` isn't tracked in git (`.gitignore` excludes `*.png` and `*.jpg`), so copy those images in by hand. See the [development guide](docs/development.md) for env vars, tests and production builds.

## Tech stack

| Layer | Tools |
|---|---|
| Backend | Laravel 10, MySQL, Breeze auth, Sanctum, `spatie/laravel-sluggable` |
| Blog | `binshops/laravel-blog` |
| Frontend | Blade, Tailwind CSS 3, Alpine.js, iconify-icon, Vite 5 |
| 3D / AI | Three.js 0.167, Yuka 0.7.8 |
| Legacy | Notion API (`fiveam-code/laravel-notion-api`), no longer routed |

## Project structure

```
app/Http/Controllers/     Portfolio, Project (CRUD), Home, CurriculumVitae (legacy Notion)
app/Http/Middleware/      SetLocale
app/Models/               Project, Skill, User
resources/views/          layouts/app, home, portfolio, privacy-policy, components/, vendor/binshopsblog/
resources/lang/{en,es}/   translation strings
resources/js/             app.js (UI), 3dMain.js (3D entry), World/ (scenes, components, systems)
routes/web.php            public + localized routes
docker/                   Apache vhost, cron and entrypoint (laravel-fans/docker)
docs/                     architecture, development, history, roadmap
```

## Documentation

- [docs/architecture.md](docs/architecture.md): routes, data model, i18n, and how the 3D engine works
- [docs/development.md](docs/development.md): setup, common tasks, tests, builds and deployment
- [docs/plan.md](docs/plan.md): roadmap and future features
- [docs/history.md](docs/history.md): how the project evolved, from the git log
- [journal.md](journal.md): per-session log of what works and what doesn't
- [CLAUDE.md](CLAUDE.md): guidance for AI coding assistants

## License

There's no license file yet. `composer.json` still lists the Laravel skeleton's `MIT`, so choose a license and add a `LICENSE` file before reuse.
