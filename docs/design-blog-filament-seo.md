# Design: blog platform, SEO/AI-visibility tool, semantic HTML

Status: **phases B, C and D built** (2026-11-26, see journal). Phases A, A2, E and F are open.
Decision asked: migrate the blog to Filament, or keep `binshops/laravel-blog`? And how to fix SEO, "RAG" (AI readability) and the HTML structure, as a tool inside the project.

**Short answer:** yes to Filament, but as the **admin panel for blog models we own**, not as a "Filament blog plugin". Do the SEO and semantic-HTML fixes **first**: they live in the public Blade views and layout, and are ~80% independent of the admin choice.

---

## 1. Requirements

### Functional
1. Write and publish posts in **ES and EN**, each with its own slug, title, description and image.
2. One admin for everything: posts, categories/tags, projects, skills, SEO settings, audit results.
3. Every public page outputs correct `<title>`, meta description, canonical, `hreflang`, Open Graph/Twitter and JSON-LD.
4. `sitemap.xml`, a `robots.txt` that points to it, RSS (exists), `llms.txt` and a Markdown version of each post for AI crawlers.
5. **SEO audit tool**: crawl own pages, report heading hierarchy, missing meta, missing `alt`, broken internal links, invalid JSON-LD, missing `hreflang` pairs. Runs as an Artisan command, a Filament page and a CI test.
6. Old URLs (`/blog/{slug}`) keep working through 301 redirects.

### Non-functional
- Personal site: low traffic, tens of posts. Scale is not a driver; **maintainability and solo-dev time are**.
- Pages stay server-rendered Blade (best for SEO). The 3D canvas must not hurt LCP or content parsing.
- No new external services needed for the core (AI answers are optional, Phase F).

### Constraints (verified this session unless marked)
| Fact | Source |
|---|---|
| Laravel 10.48, PHP 8.1.10 local | `php -v`, journal 2026-11-23 |
| Laravel 10 security support ended Feb 2025 | general knowledge, *not re-checked* |
| `binshops/laravel-blog` v10.0.0 (2025-04-16), the latest tag. No PHP/Laravel constraint in composer; README claims Laravel 11/12 | packagist |
| binshops v10 is the single-language line ("9.x = old multi lang") | packagist README |
| Filament v5.12.0 (2026-11-21) requires PHP `^8.2`. v4 and v3 lines also exist | packagist |
| Filament v4/v5 need Laravel 11+ and Livewire 3/4 | Filament docs from memory, *not verified* |
| `ralphjsmit/laravel-seo` 1.8.3 needs PHP 8.2, Laravel 11–13 | packagist |
| `spatie/laravel-sitemap` 8.2 needs PHP 8.4, Laravel 12–13 | packagist |
| Deploy target and its PHP version | **unknown**. Must be ≥ 8.2 (ideally 8.3/8.4) |

---

## 2. Current state (what is wrong today)

### The blog package is already being fought
- `develop/multilang` (835833b) extends `BinshopsBlogPost`/`BinshopsBlogCategory`, **disables the package routes** and re-declares every reader and admin route by hand in `routes/web.php`, just to get `/{locale}/blog`.
- Admin is Bootstrap, separate from everything else, gated by a hard-coded id + email in `User::canManageBinshopsBlogPosts()`.
- Depends on `intervention/image 2.*` (old major) and `swisnl/laravel-fulltext`.
- Projects/Skills CRUD has no admin at all (and no auth, journal #1).

### SEO (read from the views)
| # | Problem | Where |
|---|---|---|
| S1 | `<title>` is always `config('app.name')`; `$title` passed by views is ignored. No meta description, canonical, OG, `hreflang` | `layouts/app.blade.php:9` |
| S2 | **Duplicate content**: `/` and `/en`, `/portfolio` and `/en/portfolio` render the same page with no canonical | `routes/web.php` |
| S3 | `/blog` is outside the locale group, so `<html lang>` is always `en`, while the blog hero text is Spanish (hard-coded) | `vendor/binshopsblog/index.blade.php:15` |
| S4 | **Pagination is commented out**: only the 10 newest posts are linked from the index; older posts are orphaned for crawlers | `index.blade.php:82-84` |
| S5 | No `sitemap.xml`; `robots.txt` has no `Sitemap:` line | `public/robots.txt` |
| S6 | JSON-LD is one `WebSite` block in the footer, with placeholder `sameAs` links. No `Person`, `BlogPosting`, `BreadcrumbList` | `footer.blade.php:352` |
| S7 | Post dates only as `diffForHumans()`; no `<time datetime>`, no `dateModified` | `full_post_details.blade.php` |
| S8 | GA ID read with `env()` in the view (empty after `config:cache`) | layout, journal #10 |

### Semantic HTML and heading hierarchy
| # | Problem | Where |
|---|---|---|
| H1 | **No `<h1>` on home, portfolio or blog index.** Home starts at `<H2>`, portfolio at `<H2>`, blog index at `<h3>` then `<h2>Blog` | `home.blade.php:380`, `portfolio.blade.php:461`, `index.blade.php:15,25` |
| H2 | Heading levels chosen for size: section titles are `<h3>` (`x-title-web`), groups `<h4>`, card labels `<h5>`; post list titles `<h4>`, subtitles `<h5>` | `title-web`, `card/project`, `index_loop` |
| H3 | Portfolio prints the name twice (`portfolio.hero.name` in a `<span>` and an `<H2>`) | `portfolio.blade.php:459-461` |
| H4 | Post body (`HTML`) is wrapped in `<p>`; block content inside `<p>` is invalid and the browser closes it early. Excerpts cut HTML with `mb_strimwidth`, which can leave open tags | `full_post_details.blade.php`, `index_loop.blade.php:112` |
| H5 | `<article>` used as a layout wrapper (grids, hero), while real articles (post, project card) are `<div>`s, and the post `<h1>` sits outside the `<article>` | portfolio, card, single post |
| H6 | Label/value pairs as `<h5>` ("Url", "Skills") instead of `<dl>` | `card/project.blade.php` |
| H7 | Two `<nav>`s without `aria-label`; one element has `id` twice, so `aria-controls` points nowhere; "Social" is an `<h5>` inside a list | `navbar/main.blade.php:147-149` |
| H8 | Empty sections (`#hero`, `#Skills`, `#letsWork`), filter `<button>`s that do nothing, `alt=""` on the profile photo, empty `<source media="(min-width: )">` | portfolio, card |
| H9 | Bootstrap class names (`row`, `col-md-9`) without Bootstrap; hard-coded strings ("No posts!", "Posted", "Edit Post") | blog views |
| H10 | No skip link; 3D container is not `aria-hidden` | layout, `three-canvas` |

---

## 3. Options for the blog

| | A. Keep binshops | B. Own models + Filament admin (**recommended**) | C. Third-party Filament blog plugin |
|---|---|---|---|
| ES/EN posts with own slugs | Fight the package (already started, routes duplicated by hand) | Native: one row per locale | Depends on plugin; most use JSON-translatable, one slug |
| One admin for blog + projects + SEO | No: Bootstrap admin + nothing for projects | Yes | Partly (plugin's resources + your own) |
| SEO tool place to live | Nowhere natural | Filament pages/resources | Filament, but SEO fields fixed by plugin |
| Laravel upgrade | Possible (README says 11/12), but each upgrade waits on a small package | Only on Filament (large, active) | Waits on plugin author |
| Up-front work | Lowest | Highest (~2 sessions for blog core + data copy) | Medium, then lock-in |
| Long-term work | Grows: every feature = override or fork | Lowest: plain Eloquent + Blade | Medium |
| Risk | Package goes quiet; intervention v2 | Upgrade 10 → 12 first; host must run PHP ≥ 8.2 | Plugin abandoned |

**Why B.** The blog is small (models: post, category, tag, translations). Everything binshops gives (WYSIWYG, uploads, categories, RSS, search) is either a Filament form field, ~30 lines of code, or not needed. What binshops does *not* give (per-locale slugs, SEO fields, hreflang, one admin, auth for projects) is exactly the roadmap. The work in `develop/multilang` shows the cost of option A is already being paid.

**Why not migrate the admin alone and keep binshops models?** Possible as a stepping stone (Filament resources over `binshops_blog_posts`), but the single-language schema is the core problem, so it only moves the pain.

**Prerequisite either way for B:** Laravel 10 → 11 → 12 and PHP ≥ 8.2 (Phase B). This is due anyway: Laravel 10 no longer gets security fixes. Upgrading also lets us drop laravel/ui (journal #3).

---

## 4. Target architecture

```
                       ┌──────────────────── Filament panel  /admin  (auth + gate) ───────────────────┐
                       │ PostResource  CategoryResource  ProjectResource  SkillResource              │
                       │ SeoSettings page   Redirects resource   SeoAudit page (runs + issues list)   │
                       └───────────────┬──────────────────────────────────────────────┬──────────────┘
                                       │ Eloquent                                     │ dispatch job
                                       ▼                                              ▼
 MySQL: posts · post_category · categories · projects · skills · seo_meta · redirects · seo_audit_runs/issues
                                       ▲                                              │
                                       │                                              │ HTTP GET own URLs
 Public (Blade, /{locale}/…)           │                                              ▼
  BlogController ─┐                    │                                   SeoAuditor (DomCrawler rules)
  PortfolioCtrl ──┼──► SeoData (DTO) ──┴──► <x-seo :data/> in <head>             ▲
  PageController ─┘        │                  title · description · canonical    │ same rules
                           │                  hreflang · OG · JSON-LD @graph     │
                           ▼                                                     │
  SitemapController  /sitemap.xml        LlmsController  /llms.txt, /llms-full.txt, /{locale}/blog/{slug}.md
  robots.txt (dynamic, Sitemap: line)    RSS /{locale}/blog/feed            tests/Feature/SeoAuditTest (CI)
```

### Data model (new tables)

```
posts            id, translation_group (uuid), locale (en|es), slug, title, subtitle,
                 excerpt, body (HTML), cover_image, cover_alt, author_id → users,
                 status (draft|published), published_at, updated_at
                 UNIQUE(locale, slug), UNIQUE(translation_group, locale)
categories       id, translation_group, locale, slug, name, description
category_post    category_id, post_id
seo_meta         id, seoable_type, seoable_id, title, description, og_image, canonical_override,
                 robots (index,follow|noindex…), schema_type, focus_keyword
                 (one row per post/project/page row; posts are already per-locale)
static_pages     key (home|portfolio|privacy), locale, + seo_meta  (or config file; see §6)
redirects        from_path (unique), to_path, status (301|410), hits
seo_audit_runs   id, started_at, finished_at, pages, errors, warnings
seo_audit_issues run_id, url, rule, severity, message, context(json)
```

- **Row per locale, linked by `translation_group`** (not JSON-translatable columns): each language gets its own slug and URL, which is what `hreflang` and search engines want. A post may exist in one language only; then no alternate is emitted.
- `projects` gets the same `locale` + `translation_group` later (portfolio i18n), or stays single-language with translated UI only. Decide in Phase E.

### URL scheme
- `/{locale}/blog`, `/{locale}/blog/{slug}`, `/{locale}/blog/category/{slug}`, `/{locale}/blog/feed`.
- `/` → redirect 302 to `/{preferred locale}` (Accept-Language) **or** serve the default locale with `canonical` = `/en`. Pick one; recommendation: redirect, so every page has exactly one URL.
- Non-prefixed `/blog/*`, `/portfolio` → 301 to the prefixed URL. Old binshops slugs go into `redirects` during the data copy.

### SEO module (`app/Seo/`)
- `SeoData` DTO: title, description, canonical, alternates (`locale → url`), image, type, `schema` (array of JSON-LD nodes), robots.
- `HasSeo` trait on Post/Project: `toSeoData()` uses `seo_meta` overrides, else falls back to model fields (title, excerpt, cover).
- `<x-seo :data="$seo" />` in the layout `<head>`. Layout gets `@stack('head')` and requires every page to pass `$seo` (a default built from config if missing, so nothing breaks).
- JSON-LD: one `@graph` per page with `WebSite` + `Person` (Luan, real `sameAs` from `config('site.social')`) + the page node (`BlogPosting`, `CollectionPage`, `ProfilePage`/`CreativeWork` for projects) + `BreadcrumbList`.
- Build our own (~5 small classes) instead of `ralphjsmit/laravel-seo`: we need per-locale alternates, the `@graph`, and the same data for sitemap/llms.txt/audit. Revisit if it grows.
- Sitemap: own controller (tiny) or `spatie/laravel-sitemap` (8.x needs PHP 8.4 + Laravel 12). Own controller avoids pinning PHP 8.4. Include `xhtml:link` alternates.

### "RAG" / AI readability (assumption: you mean being read and cited correctly by ChatGPT/Claude/Perplexity, not a chatbot)
1. Semantic HTML + JSON-LD (above). This is most of it: LLM crawlers parse the DOM, not the canvas.
2. `/llms.txt`: site summary, who Luan is, and a list of posts/projects with one-line descriptions, generated from the DB.
3. `/llms-full.txt` and `/{locale}/blog/{slug}.md`: Markdown of each post (HTML → Markdown via `league/html-to-markdown`), linked with `<link rel="alternate" type="text/markdown">`.
4. `robots.txt`: explicit choices for `GPTBot`, `ClaudeBot`, `PerplexityBot`, `Google-Extended` (allow, I assume, since the goal is visibility).
5. Content chunks the audit can check: one topic per `<h2>` section, a summary paragraph near the top (`excerpt`), dates in `<time>`.

Optional **Phase F, "Ask my site" chatbot** (real RAG): chunk posts/projects by `<h2>`, embed, store vectors (a JSON column and cosine in PHP is enough for < 2,000 chunks), answer with a small Claude model (e.g. Haiku 5.5) citing post URLs. Adds an API key, cost and a rate-limited public endpoint. Only worth it once there are enough posts.

### SEO audit tool (`app/Seo/Audit/`)
- `SeoAuditor` collects URLs from the sitemap, fetches each through the app (`Http::get(config('app.url').$path)` or the HTTP kernel in tests), parses with `symfony/dom-crawler`, runs rules, stores a run.
- Rules (each a small class, `severity` error|warning):
  - exactly one `<h1>`; no skipped levels (h2 → h4); heading text not empty
  - `<title>` 30–60 chars, unique across site; description 70–160, unique
  - canonical present, absolute, self-referencing unless overridden
  - `hreflang` pairs reciprocal, `x-default` present, `<html lang>` matches URL locale
  - every `<img>` has `alt` (empty allowed only with `role="presentation"`), width/height set
  - JSON-LD parses; `BlogPosting` has `headline`, `datePublished`, `author`, `image`
  - internal links return 200 (or a known redirect); no links to `yourprofile` placeholders
  - landmarks: one `<main>`, `<nav>`s labelled, no duplicate `id`s
  - no block elements inside `<p>`
- Surfaces: `php artisan seo:audit [--url=] [--fail-on=error]`, Filament "SEO audit" page (run button, issues table filterable by rule/URL, trend of errors per run), and `tests/Feature/SeoAuditTest` that fails CI on any error.
- In the Post form: a live "SEO" panel (title/description length counters, Google snippet preview, focus keyword present in title/h1/first paragraph). Same rule classes, run on the draft HTML.

---

## 5. Semantic HTML target (per page)

```
<body>
  <a href="#content" class="sr-only focus:not-sr-only">Skip to content</a>
  <header> <nav aria-label="Main">…</nav> </header>          ← desktop + mobile = one nav, CSS decides layout
  <div id="scene-container" aria-hidden="true"></div>
  <main id="content">
    Home:      <h1>Luan Erazo</h1> <p>tagline</p> <nav aria-label="Sections">Blog · Portfolio</nav>
    Portfolio: <h1>Luan Erazo, UX/UI + web developer</h1>
               <section aria-labelledby=…><h2>About me</h2>…</section>
               <section><h2>What I do</h2> <h3>Design</h3> <ul><li><article><h4>card</h4>…
               <section><h2>Projects</h2> <ul><li><article><h3>title</h3><dl><dt>Site</dt><dd>…</dd>…
    Blog list: <h1>Blog</h1><p>Reflections…</p> <ol><li><article><h2><a>title</a></h2><p>excerpt</p><time>…
               <nav aria-label="Pagination">…</nav>
    Post:      <article> <header><h1>title</h1><p class="subtitle">…</p><time datetime>…</time></header>
               <figure><img alt width height><figcaption/></figure>
               <div class="prose">body (h2/h3 only; editor blocks h1)</div>
               <footer>author · categories</footer> </article>
               <nav aria-label="Breadcrumb">…</nav>
  </main>
  <footer> <nav aria-label="Footer">…</nav> <p>© …</p> </footer>
</body>
```

Rules: heading level = document outline, never size (use Tailwind classes for size). `x-title-web` takes a `level` prop. Editor toolbar allows h2–h4 only.

---

## 6. Phasing

Each phase ships on its own and leaves the site working.

| Phase | Content | Depends on | Size (assumed sessions) |
|---|---|---|---|
| **0** Stabilize | Existing Phase 0 in `plan.md` (auth on `/projects`, close `/register`, one auth stack, `route:cache`, `config()` not `env()`) | none | 1–2 |
| **A** SEO + semantics on current views | `SeoData` + `<x-seo>`, fix headings/landmarks (§5) in layout, home, portfolio, binshops overrides; restore pagination; `<time>`; real social links in config; canonical + `/` redirect; dynamic `robots.txt`; `sitemap.xml`; `llms.txt` | 0 | 2 |
| **A2** Audit tool (CLI + CI) | `SeoAuditor`, rules, `seo:audit`, feature test | A | 1–2 |
| **B** Upgrade | PHP ≥ 8.2 locally and on host; Laravel 10 → 11 → 12; remove laravel/ui | 0 | 1–2 |
| **C** Filament panel | Filament v5 (v4 if v5 needs something we lack) at `/admin`; `FilamentUser::canAccessPanel` replaces hard-coded email; ProjectResource + SkillResource (closes journal #1 for good) | B | 1 |
| **D** Own blog | `posts`/`categories`/`seo_meta`/`redirects` migrations; PostResource (RichEditor, uploads, SEO tab with live checks); public controllers + views (reuse Phase A views); copy command `blog:import-binshops` (posts → `es`/`en` rows, images kept, old slugs → `redirects`); remove binshops | C | 2–3 |
| **E** Admin SEO tool | Filament SEO audit page + settings page (site name, social, default OG image, AI-bot policy) + redirects resource; `seo_meta` on projects; portfolio i18n decision | A2, D | 1–2 |
| **F** (optional) "Ask my site" | Embeddings + Claude answers with citations | D, enough content | 2 |

Phase A is done on binshops views on purpose: the SEO data flows through `SeoData`, so in Phase D only the model → `SeoData` mapping changes, not the layout.

`develop/multilang` does not need to be merged if Phase D is accepted: its custom models and hand-written routes are replaced. Keep only its translation strings.

---

## 7. Risks and trade-offs

- **Host PHP version** is the biggest unknown. If the host cannot run PHP 8.2+, Phases B–E are blocked and option A (keep binshops) becomes the fallback; Phase A and A2 still apply.
- **Laravel upgrade** may surface breakage in binshops (only README claims 11/12). Mitigation: do B before D, test the blog after each major; if binshops breaks, accelerate D.
- **Tailwind**: Filament v4+ ships its own compiled panel CSS (Tailwind 4). The public site can stay on Tailwind 3; only a custom panel theme would force Tailwind 4. *Assumption, verify in Phase C.*
- **Data copy**: binshops stores HTML with `auto_nl2br`; the import must reproduce the rendered HTML (`post_body_output()`), not the raw column. Check image paths in `public/blog_images` (gitignored images, journal #13).
- **Own SEO code vs package**: more code to own (~5 classes + rules), but one source of truth for head, sitemap, llms.txt and audit.
- **Audit by HTTP** needs the app reachable at `APP_URL`; in CI use the kernel (`$this->get()`) instead.

## 8. Revisit later
- More than a few hundred posts: cache `SeoData` and sitemap, queue the audit, paginate llms-full.txt.
- If a second author appears: roles/policies in Filament, editorial workflow (review status).
- If search matters: Laravel Scout (database driver) replaces binshops' fulltext search.
- Core Web Vitals: the 630 kB 3D bundle (plan Phase 2) affects ranking more than any meta tag. Lazy-load it after content.

## Open questions for Luan
1. "RAG": AI readability (llms.txt, structured data), or an actual chatbot on the site? This doc assumes the first, with the chatbot as optional Phase F.
2. Where is the site deployed, and which PHP version can it run?
3. Are existing posts Spanish only? Do you want every post in both languages, or "write once, translate when worth it"?
4. `/`: redirect to `/es` or `/en` by browser language, or serve one default language?
