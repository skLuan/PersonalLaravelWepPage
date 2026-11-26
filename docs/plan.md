# Roadmap and Future Features

Status: `[ ]` todo · `[~]` in progress · `[x]` done. Each item says what "done" means.
The `#n` numbers point to the problem list in the 2026-11-23 entry of [journal.md](../journal.md).

---

## Phase 0: Stabilize (do first)

The site has open write endpoints and won't boot in a production container. Fix these before adding features.

- [ ] **Protect portfolio CRUD** (#1): put `Route::resource('projects', …)` behind `auth` (and ideally an admin gate). *Done when* a guest gets redirected from `/projects/create`, and a `POST /projects` from a guest is rejected.
- [ ] **Close registration** (#2): disable `/register` or limit it to an allow-list.
- [ ] **One auth stack** (#3): keep Breeze (`routes/auth.php`) and remove `Auth::routes()` and the laravel/ui controllers and views. *Done when* `php artisan route:cache` succeeds.
- [ ] **Unique route names** (#4): rename the localized routes (`localized.home`, `localized.portfolio`, …) or drop the plain duplicates.
- [ ] **Trim the portfolio resource** (#5): `Route::resource('portfolio', …)->only('index')`.
- [x] **Config, not `env()`** (#10, #11): add `services.google.tag_id` and `services.notion.*` to `config/services.php`, switch views and controllers to `config()`, and document the keys in `.env.example`.
- [ ] **Guard global JS** (#16): `darkMode_button.js` should return early when `#check-dark-mode` is missing. Do the same for `menu_buton.js`.
- [ ] **Track images** (#13): narrow the `*.png`/`*.jpg` ignore rule so `public/imgs` and the mesh textures get committed (or move them to storage or a CDN).
- [ ] **CI on `main`** (#15): change the `tests.yml` branches, and use SQLite in-memory for tests so CI doesn't need MySQL.
- [ ] Remove dead pieces: the `/inWork` route and view (#7), `layouts/projects.blade.php` (#25), and the root `lang/` folder (#12) once its files are moved to `resources/lang/en`.

## Phase 1: Finish what's started

- [ ] **Project detail page** (#6): create `projects/show.blade.php` that renders `body` (Markdown → HTML), the image, skills and site link, with SEO meta from the project. The public URL is `/portfolio/{slug}`.
- [ ] **Portfolio filters**: make the "Web product / Graphic design" buttons work. This needs a `category` (or tag) column on `projects`.
- [ ] **Skills section**: fill the empty `#Skills` section from the `skills` table.
- [ ] **"Let's work" / contact section**: fill `#letsWork` with a contact form (rate-limited, with a honeypot) or a mailto/Calendly link.
- [ ] **Hero section**: the `#hero` section is empty, so decide what goes there or remove it.
- [ ] **Blog localization** (#8): merge `develop/multilang` (2 commits: `BlogPost`/`BlogCategories` models, localized blog routes). Then the language switcher works on `/blog`.
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
- [x] **Deployment docs**: Hostinger over SSH via GitHub Actions (`.github/workflows/deploy.yml`), documented in `docs/development.md`.
- [ ] **Fix `route:cache`** (#3) and add it back to the deploy workflow.
- [ ] **Run tests before deploy**: give `tests.yml` a MySQL service and make the deploy depend on it.
- [ ] **Decide on Notion**: either delete `CurriculumVitae`, `NotionService`, `blog.blade.php` and `blogpost.blade.php`, or bring back a Notion-powered CV page on top of config-based IDs.
- [ ] **CV page**: a downloadable or online CV (there is a commented-out link in the legacy `inWork` view).
- [ ] **Analytics consent**: a cookie or consent banner for GA and Clarity, in line with the privacy policy text.
- [ ] **Image handling**: uploads for project images (today they are URLs only), with responsive `srcset` (the `<source>` in `card/project` is empty).
- [ ] **Blog comments**: currently `disabled`. Decide whether to keep them off or use the built-in type with captcha.

## Ideas (not committed)

- RSS link in the footer (binshops already serves `/blog/feed`).
- A "now" or changelog page generated from `journal.md`.
- Interactive squids that react to the cursor (Yuka `SeekBehavior` / `FleeBehavior`).
