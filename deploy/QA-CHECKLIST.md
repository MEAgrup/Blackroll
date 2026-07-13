# Blackroll — Pre-launch QA Checklist (Module 10 + 11 + PATCH-13)

Run `npm run start && npm run seed`, then `npm run qa` (Lighthouse CI) and this manual pass.

## Performance / CWV (per page × ID/EN)
- [ ] LCP < 2.5s (mobile 4G throttled). Homepage LCP = H1 text, not motion.
- [ ] CLS < 0.1 (all images have width/height or aspect-ratio; Sebari form container fixed-height).
- [ ] INP < 200ms. Lighthouse mobile perf ≥ 90.
- [ ] `npm run build:analyze` — three.js absent from every non-Product bundle (analyzer gate).
- [ ] All images WebP, `srcset`, lazy below the fold, self-hosted WOFF2 fonts, no render-blocking JS.

## SEO
- [ ] Unique `<title>` + meta description per page/language.
- [ ] `hreflang` ID/EN emitted for existing pairs; canonical correct.
- [ ] Schema valid (Rich Results): Organization, LocalBusiness, Product, Article, BreadcrumbList.
- [ ] Rank Math sitemap present (core wp-sitemap disabled); robots.txt correct (production allows).
- [ ] Thank-you `/kontak/terima-kasih/` + `/kontak/gagal/` are `noindex`.

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

## Functional
- [ ] Sebari submit end-to-end → WhatsApp automation; success + failure redirects land on the right pages.
- [ ] Selector filters shades by series + material; preview swaps; no broken images (fallback chain).
- [ ] Portfolio filter + Load More + lightbox work; floating CTA on every page routes to Contact.
- [ ] Language switch keeps page context (once EN pages exist).
