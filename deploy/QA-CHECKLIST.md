# Blackroll — Pre-launch QA Checklist (Module 10 + 11 + PATCH-13)

Run `npm run start && npm run seed`, then `npm run qa` (Lighthouse CI) and this manual pass.

## Performance / CWV (per page × ID/EN)
> **Client decision (2026-09):** homepage now ships a video/Three.js/Lottie "premium
> motion" hero (see `themes/blackroll/patterns/home-hero.php`, `assets/js/product-3d.js`,
> `assets/js/motion.js`). Client explicitly accepted a lower Lighthouse/LCP score in
> exchange for a more premium look — the two gates below are no longer hard blockers
> on the homepage/Product page, they are tracked as a known, approved trade-off.
> Everything else in this checklist (a11y, `prefers-reduced-motion`, 2G/3G fallback)
> still applies in full — the trade-off is loading speed, not broken UX for users who
> can't/shouldn't get the motion.
- [ ] LCP < 2.5s (mobile 4G throttled) on every page **except** homepage/Product, where H1 text still paints first even though video/3D/Lottie load after it (accepted trade-off above).
- [ ] CLS < 0.1 (all images have width/height or aspect-ratio; Sebari form container fixed-height; hero video/canvas has a reserved aspect-ratio box so it doesn't shift layout on load).
- [ ] INP < 200ms. Lighthouse mobile perf ≥ 90 on non-motion pages (homepage/Product are exempt per the accepted trade-off above).
- [ ] `npm run build:analyze` — three.js still isolated to the Product bundle only (the gate is about *leaking into other pages*, not about using three.js at all — that stays enforced).
- [ ] Hero video/Lottie: `prefers-reduced-motion` shows the static poster/fallback image (no autoplay); slow/2G-3G connections (`canRun3D()`, `navigator.connection`) get the static fallback too — these are accessibility/UX safety nets, not performance vanity, and are NOT part of the accepted trade-off.
- [ ] All images WebP, `srcset`, lazy below the fold, self-hosted WOFF2 fonts, no render-blocking JS (applies outside the hero motion elements above).

## SEO
- [ ] Unique `<title>` + meta description per page/language.
- [ ] `hreflang` ID/EN emitted for existing pairs; canonical correct.
- [ ] Schema valid (Rich Results): Organization, LocalBusiness, Product, Article, BreadcrumbList.
- [ ] Rank Math sitemap present (core wp-sitemap disabled); robots.txt correct (production allows).
- [ ] Thank-you `/kontak/terima-kasih/` + `/kontak/gagal/` are `noindex`.
- [ ] After `wp rewrite flush`: `/warna/{any-shade-slug}/`, `/seri-warna/*` and `/tipe-proyek/*` all return 404 (shade CPT + these taxonomies are intentionally not public — see `deploy/SITEMAP.md`). If any of these still 200, the rewrite cache is stale or the CPT/taxonomy registration reverted.
- [ ] Sitemap matches `deploy/SITEMAP.md` — no unlisted URL shows up in Rank Math's sitemap or Search Console coverage.

## Accessibility (PATCH-13)
- [ ] Visible focus states site-wide (white on dark, black on light).
- [ ] Skip-to-content link is the first focusable element.
- [ ] Sebari form inputs have real `<label>`s (they do — Nama/WhatsApp/Email).
- [ ] Selector + Portfolio filter controls expose `aria-pressed`.
- [ ] Lightbox: focus trap, `Esc` closes, focus returns to trigger.
- [ ] Contrast: `--brand-grey` only on large/decorative text; `--brand-grey-text` for small on white.
- [ ] Tap targets ≥ 44×44px.
- [ ] Anton headings legible at mobile sizes; no H1/H2 wrapping to 3+ lines at 360px.

## Security (Module 11)
- [ ] HTTPS forced, HSTS present, no mixed content.
- [ ] CSP present; Sebari POST succeeds AND fonts load under it (report-only → verify → enforce).
- [ ] `form-action 'self' https://sebari.co.id` (wp-login/admin/search not broken).
- [ ] XML-RPC off, file editing disabled, uploads PHP-exec blocked, comments off.
- [ ] Backup runs + test restore; content team = Editor role.

## Content
- [ ] `wp blackroll seed` + `wp blackroll content` ran clean; no page still says "(konten contoh)".
- [ ] Tentang Kami, Kebijakan Privasi and all 4 articles are published and render their images.
- [ ] Kebijakan Privasi has client/legal sign-off (WA number + address in it are correct).
- [ ] Internal links in the articles resolve (post permalinks are root-level under `/%postname%/`).
- [ ] Categories Panduan / Produk / Inspirasi each have at least one published article.

## Functional
- [ ] Sebari submit end-to-end → WhatsApp automation; success + failure redirects land on the right pages.
- [ ] Selector filters shades by series + material; preview swaps; no broken images (fallback chain).
- [ ] Portfolio filter + Load More + lightbox work; floating CTA on every page routes to Contact.
- [ ] Language switch keeps page context (once EN pages exist).
