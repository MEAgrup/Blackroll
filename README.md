# Blackroll — WordPress Website

Custom **block theme** (`themes/blackroll`) + **companion plugin** (`plugins/blackroll-core`) for **Blackroll** — "Affordable Premium Blinds for Modern Living". Monochrome, bilingual (ID primary / EN secondary via Polylang), performance-first (LCP < 2.5s on mobile 4G), no page builder.

Built to `Blackroll_MASTER_PRD.md` (11 locked modules) as amended by **Amendment 01** (18 patches, decisions D1–D6). Where a decision was locked, it is implemented as a **single swappable config point** — never hardcoded in many places.

---

## Locked decisions (Amendment 01, D1–D6)

| # | Decision | Value | Where it lives |
|---|---|---|---|
| D1 | Post-submit | **Mode R (redirect)** → `/kontak/terima-kasih/` | `BLACKROLL_SEBARI_SUBMIT_MODE` constant; Mode F retained in `contact.js` as contingency |
| D2 | Analytics | **GSC-only** (no GA4) | `BLACKROLL_ANALYTICS_MODE = none` |
| D3 | Field UI | **Native** `register_post_meta` + hand-built panels (no SCF) | `plugins/blackroll-core/inc/fields.php` |
| D4 | Primary WA | **+62813-3838-8500** | Blackroll → Settings (option `blackroll_primary_wa`) |
| D5 | Heading font | **Anton** (weight 400 only, all-caps) + body **Inter** | `themes/blackroll/theme.json` |
| D6 | EN content | **Progressive** (ID first, "Coming soon" EN) | Polylang; EN skeletons created as translated |

> ✅ **Open Dependency #7 — RESOLVED (2026-09):** Sebari team confirmed `redirect_url` is supported. Mode R (redirect) ships as the default and needs no change. Mode F stays in the codebase as a dormant contingency only.

---

## Repository layout

```
themes/blackroll/            Custom block theme (presentation, performance, motion)
  theme.json                 Design tokens: Anton/Inter, monochrome palette, spacing
  style.css                  Theme header only
  functions.php  inc/        Setup, enqueue (preloaded self-hosted fonts), perf, hooks, helpers
  templates/  parts/         Block templates (front-page, product, thank-you, 404, search…) + header/footer
  patterns/                  Home + CTA block patterns
  assets/fonts/              Self-hosted Anton + Inter WOFF2 (Latin + Latin-ext)
  assets/css/  assets/js/    fonts.css, app.css; motion/selector/portfolio/contact + product-3d (code-split stub)

plugins/blackroll-core/      Data + config + security (survives theme swaps)
  inc/config.php             Options + accessors (WA number, page roles, config flags)
  inc/cpt.php  taxonomies.php CPT shade/project + color_series/project_type (all show_in_rest)
  inc/meta.php  fields.php    Native meta + admin panels with wp.media image picker (D3)
  inc/rewrites.php           Localized archive base (/en/portfolio/), comments off, core sitemap off
  inc/query.php              "featured" projects for the homepage highlights query
  inc/schema.php             LocalBusiness + BreadcrumbList
  inc/security.php           CSP (report-only→enforce), headers, XML-RPC off, DISALLOW_FILE_EDIT
  inc/sebari.php             Lead-form shortcode, honeypot, redirect_url wiring (PLACEHOLDER config)
  inc/settings-page.php      Blackroll admin settings

scripts/                     build.mjs (esbuild + analyzer gate), mu-plugins/seed.php (wp blackroll seed)
.wp-env.json  lighthouserc.json  package.json
```

---

## Local development

Requires Docker (for `@wordpress/env`) and Node 18+.

```bash
npm install
npm run start          # boots WordPress at http://localhost:8888  (admin: http://localhost:8888/wp-admin, admin/password)
npm run seed           # pages, 24 shades, projects, 4 published articles + real photos
npm run build          # minify + code-split JS into assets/js/dist
npm run build:analyze  # prints bundle analysis; FAILS if three.js leaks off the Product chunk
npm run qa             # Lighthouse CI against wp-env (mobile-throttled, asserts Module 10 gates)
npm run stop           # stop containers
```

After `npm run start` the theme + plugin are auto-activated, permalinks set to `/%postname%/`, timezone `Asia/Jakarta` (see `.wp-env.json` lifecycle script).

The theme also runs un-bundled in dev — `assets/js/*.js` are enqueued directly, so `npm run build` is only needed for production minification and the analyzer gate.

---

## Content-team guide (D3 = native fields)

Editing UX is native (rawer than SCF) — here is the step-by-step.

### Add a shade (colour)
1. **Shades → Add New**. Title = the display name (e.g. *Cream*). Use Polylang to add the EN title.
2. In **Shade Details**: enter **SKU Code** verbatim from the client sheet (e.g. `ML002B`); pick **Swatch Image** and **Preview Image** (and per-material previews if the shade differs between Blackout/Solar); tick which materials it's available in.
3. In the sidebar, set **Color Series** = Black Series or White Series.
4. **Publish.** It appears in the Material & Color selector automatically.

### Add a project (portfolio)
1. **Portfolio → Add New**. Title = project name.
2. Set the **Featured** checkbox to surface it in the Homepage highlights.
3. Set **Project Type** (Residensial/Kantor/Apartemen) in the sidebar — drives the filter.
4. Featured image = the card image; extra photos via **Gallery Image IDs** (comma-separated attachment IDs).

### Add an article
Standard **Posts → Add New** with a category (Panduan / Inspirasi / Produk). Comments are off site-wide. Author shows as "Tim Blackroll".

### Where the shipped copy lives

The launch copy — **Tentang Kami**, **Kebijakan Privasi** and the four articles — is
authored in `plugins/blackroll-core/inc/seed-content.php`, not typed into the DB. It is
versioned, reviewable in a PR, and re-appliable to any environment:

```bash
wp blackroll content              # write copy into pages/articles still holding placeholders
wp blackroll content --dry-run    # show what would change
wp blackroll content --force      # overwrite even editor-modified content
```

Without `--force` the command never clobbers a page an editor has already touched — it
compares against the placeholder markers the old seed wrote and skips anything else.

**Two ways to edit copy, pick one per page:**
- *Copy owned by the repo* — edit `seed-content.php`, commit, `git pull` on the server, re-run `wp blackroll content --force`.
- *Copy owned by the content team* — edit in wp-admin and never run `--force` on that page again.

Articles are written from Blackroll's own brand material in `seed-assets/` (the 5-layer
blackout spec sheet, the installation guide, the product spec grid). No pricing, warranty
terms or founding dates are asserted anywhere — those need client confirmation first.

---

## Placeholders to swap (open dependencies)

| Placeholder | Status | Where | Replace with |
|---|---|---|---|
| Sebari embed (form_id 971, group 5260, user 139954, +email) | ✅ **wired (real)** | `inc/sebari.php` → `blackroll_sebari_config()` | — (confirm redirect_url support, Open Dep #7) |
| SKU/shade data | ✅ **real list seeded** (24 SKUs, 4 materials) | `scripts/mu-plugins/seed.php` | — |
| Product/portfolio photos | ✅ **real photos** (WebP, `seed-assets/`) | `plugins/blackroll-core/seed-assets/` | more via Drive folder as needed |
| Article bodies | ✅ **written** (4 published articles, ID) | `inc/seed-content.php` | — |
| Tentang Kami copy | ✅ **written** (ID) | `inc/seed-content.php` | — |
| Kebijakan Privasi copy | ✅ **legal sign-off done** (2026-09) — final copy pending upload from client | `inc/seed-content.php` (current draft) | Swap in final signed-off copy when uploaded |
| Logo + favicon | ✅ **traced from the client's official PNG** (Oct 2026; no SVG exists) | `assets/images/logo-horizontal.svg`, `logo-stacked.svg`, `logo-full.svg`, `favicon.svg`, `logo-mark.svg`; source PNG in `assets/images/brand/` | — |
| Collection photos (Sept 2026 shoot) | ✅ **80 curated WebP** — text-free crops, white-balanced | `assets/images/collection/` (see its README) | SKU mapping for the `blind N` folders |
| EN translations | placeholder | Polylang | Progressive per D6 |
| Domain | default | `BLACKROLL_SITE_URL` (`https://blackrollblinds.com`) | Final production domain |

> **Real assets bundled:** product photos for the ML-series shades + D-series, plus lifestyle (ruang tamu/dapur/kantor/office) and tech (5-lapis blackout, tahan air, panduan pasang) images live in `plugins/blackroll-core/seed-assets/` as optimized WebP. `wp blackroll seed` sideloads them into the Media Library and attaches them to shades, projects and articles.
>
> **Catalogue note (scope flag):** the client SKU sheet lists **four** materials — Blackout, Solar Screen, **Dimout, Zebra Blinds**. Module 6's selector is locked to Blackout + Solar Screen only. All four are stored on each shade via the additive `material_type` meta so no data is lost; expanding the selector to 4 materials is a scope decision.

---

## Plugins to install (5, minimal — Module 11 Rule 4)

Polylang · Rank Math Lite (disable core `wp-sitemap`) · Solid Security Basic · LiteSpeed Cache (Hostinger). `blackroll-core` is the 6th, first-party. **No SCF** (D3).

---

## Deployment (Hostinger) & ops — see Steps 11–13

SEO layer (Rank Math config, hreflang, schema), security enforce flip (CSP report-only → enforce), and the cutover runbook (`robots.txt` staging-disallow → go-live flip, cache purge, GSC sitemap resubmit) are wired in later build steps. A one-page ops runbook (admin access, backups, monthly update owner) ships with the security step.

---

## Build progress

- [x] **Step 1** — Theme + plugin scaffold, design system, fonts, base templates, data model, config, security headers, dev/QA tooling
- [x] **Step 2** — Global components: header nav (real sitemap + Produk submenu), language switcher, footer
- [x] **Step 3** — IA/routing: pages nested under /produk/, blog posts page /artikel/, localized /en/portfolio/ rewrite
- [x] **Step 4** — Motion loaders (lazy Lottie, reduced-motion, code-split 3D stub)
- [x] **Step 5** — Homepage (hero LCP + trust + product preview + real room showcase + material teaser + featured portfolio)
- [x] **Step 6** — Product template (shared) + Manual & Motorized patterns; `/produk/blinds-manual/` + `/produk/blinds-motorized/`
- [x] **Step 7** — Material & Color selector ([blackroll_selector], real shades, 4 catalogue materials, series+material filter, preview fallback chain)
- [x] **Step 8** — Portfolio ([blackroll_portfolio], project CPT archive `/portofolio/`, filter + Load More + a11y lightbox)
- [x] **Step 9** — Blog: posts page `/artikel/` (home.html), categories, 3 seed article stubs with real featured images, comments off
- [x] **Step 10** — Contact — real Sebari embed + fallback + static map; success `/kontak/terima-kasih/` + failure `/kontak/gagal/` pages (noindex) for Sebari redirect config
- [x] **Step 11** — SEO layer: OG/Twitter/canonical fallback (defers to Rank Math), robots.txt env-aware, default OG image, hreflang fallback
- [x] **Step 12** — Security: CSP + headers, XML-RPC off, DISALLOW_FILE_EDIT, attachment/author hygiene, uploads PHP-exec block (deploy/)
- [x] **Step 13** — A11y + QA: checklist (deploy/QA-CHECKLIST.md), Lighthouse CI config, analyzer gate
- [x] **Step 14** — Editorial content: real ID copy for Tentang Kami, Kebijakan Privasi and 4 published articles (`inc/seed-content.php`, `wp blackroll content`)
- [ ] Deploy — `deploy/HOSTINGER-SSH.md` (command runbook) / `deploy/DEPLOY.md` (decisions + ops)
