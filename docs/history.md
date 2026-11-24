# Project History

A summary of the git log (63 commits, 2024-06-01 → 2025-10-24), for context on why the code looks the way it does. For exact changes, use `git log`.

## 1. Notion-powered CV (June – August 2024)
- `4d9102f` First commit: Laravel 10 config and a Notion provider.
- `0fc9175` Notion communication and the first controller methods (`CurriculumVitae`).
- `bf98189` Blog working from a Notion "journal" database.
- `4721837` Docker files from `laravel-fans/docker`.

## 2. Three.js and Yuka experiments (August – October 2024)
- `aa92d8b` → `721dcd7` Three.js added; World class with a rotating cube.
- `06b7190`, `3825c5b` Yuka added; an agent (cone) moves along a path.
- `73df181` First public version of the site (PRs #3 and #4 from `develop/views`).
- `6661bb1` Google Analytics tag.

## 3. Away from Notion (January – April 2025)
- `a940c49` Deeper Notion API work. This is the last Notion-focused commit.
- `60078ea` **Switch to `binshops/laravel-blog`**, "to continue in a simpler way without Notion".
- `edff943`, `fa52c02` Blog set up and restyled.

## 4. Portfolio and design system (May 2025)
- `10e71e6` iconify and the Tailwind theme (`skl-*` tokens).
- `6082cb8` → `2a2034e` Portfolio page and project card component.
- `0153ea7` 3D canvas on the portfolio.
- `a9b3575`, `fcbdf2a` `projects`, `skills` and `project_skill` tables, and the project CRUD form ("we need to finish edit").
- `d28db4e` 3D limited to the portfolio.

## 5. AI agents: the squid school (May – June 2025)
- `dd9fa0d` → `4050a9b` Yuka entity code split out of `main`.
- `56522c6`, `b090aab` `PathCreator` class; the path becomes a world object.
- `071624b` `VehicleStateController` (steering states).
- `51f840a` Squid school ("cardumen") created.
- `af651ff`, `ecc37cc`, `8cf00a4` Mobile menu, home page, first responsive layout.
- `35c240c` → `81fe650` Footer; blog single-post responsive work.
- `b8d441e` Squid mesh added, but it flickers. (The squids are cones today.)

## 6. Blog world and polish (August – September 2025)
- `cfcd5cc`, `c64d81a` Squid mesh revisited; visual helpers in the portfolio.
- `1d732f6` `BlogWorld`: a separate 3D scene for `/blog`.
- `66edcd5`, `3a3e1be` Smooth scrolling and responsive blog layout; Vite moves from SASS to CSS (PRs #5 and #6).
- `9a02f4c`, `8781d21` Microsoft Clarity.
- `ff34f88` Accessible footer and privacy policy page.
- `445ebb4` Dark mode.

## 7. Internationalization (October 2025)
- `27817bf` English/Spanish: `SetLocale` middleware, language switcher, translated navbar, footer and home.
- `d728263` English auth, validation and pagination files (in root `lang/`, which Laravel ignores here).
- `49db6bf` Blog routes copied into `web.php`, then left commented out (the package still registers them).
- `a47728c` Portfolio page translated; localized route group.
- `develop/multilang` (not merged): localized blog models and routes.

## 8. Documentation (November 2026)
- Project docs, `CLAUDE.md`, `journal.md` and the roadmap were added. See [journal.md](../journal.md).
