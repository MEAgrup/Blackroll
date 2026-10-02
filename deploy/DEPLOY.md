# Blackroll — Deployment & Ops Runbook (Hostinger)

Deploy is intentionally the **last** step. This is the cutover + ops reference.

> For the copy-paste command sequence over SSH (clone → rsync → activate → seed → verify),
> use **`deploy/HOSTINGER-SSH.md`**. This file covers the decisions and ops around it.

## 0. Prerequisites
- Hostinger Managed WP, PHP ≥ 8.1, HTTPS active, LiteSpeed cache available.
- Staging environment created (Hostinger supports staging).

## 1. Install theme + plugin
- Upload `themes/blackroll` → `wp-content/themes/blackroll`, activate.
- Upload `plugins/blackroll-core` → `wp-content/plugins/blackroll-core`, activate.
- Install the 5 plugins: **Polylang**, **Rank Math** (Lite), **Solid Security Basic**, **LiteSpeed Cache**. (No SCF — D3.)

## 2. WP baseline (PATCH-15)
- Settings → General: timezone **Asia/Jakarta**, site language **ID**.
- Settings → Permalinks: **/%postname%/**.
- Settings → Reading: Front page = **Beranda**, Posts page = **Artikel**.
- Discussion: comments **off** (already enforced by blackroll-core).
- **Polylang: Indonesian must exist and be the default language.** Found on production 2026-09-28: Polylang had only English configured, so every page rendered `<html lang="en-US">` and the floating CTA / Sebari redirect pointed at `/en/contact/…` (404). Since the Fase 0 fix the theme falls back to the ID URLs when Polylang has no `id` language, but the site still announces itself as English to Google until this is fixed: Languages → add **Bahasa Indonesia (id)**, make it default, then "Set all content without language to Indonesian" (and re-assign pages currently tagged EN).
- The header/footer are theme files. If someone customised them in the Site Editor, WordPress keeps serving the DB copy and ignores theme updates: Appearance → Editor → Patterns → Template Parts → Header/Footer → "Reset" after deploying.
- Create the pages/content: run `wp blackroll seed` then `wp blackroll content` (WP-CLI). The first builds the structure (pages, 24 SKUs, projects, articles, media sideload); the second writes the real Bahasa Indonesia copy for Tentang Kami, Kebijakan Privasi and the four articles. See the README content guide for the edit-ownership rules.

## 3. Content ops (MCP optional — letting Claude Code enter content directly)
Native WordPress REST API + Application Password (built into WP core since 5.6 — no extra
plugin). This is what lets an AI session (Claude Code or any MCP content tool) create/edit
Pages, Posts, Shades, Projects and Media directly, without SSH/WP-CLI access:
1. In wp-admin → **Users → Add New**, create one dedicated AI user, role **Editor** (never
   Administrator — least privilege: can manage content, cannot touch themes/plugins/settings).
2. On that user's profile → **Application Passwords** → generate one (name it e.g.
   `claude-code-content`). This is a separate credential from the login password and can be
   revoked independently at any time.
3. Content tools authenticate to `https://blackrollblinds.com/wp-json/wp/v2/...` with HTTP
   Basic Auth (username + application password). Every CPT here (`shade`, `project`, plus
   core `pages`/`posts`/`media`) already has `show_in_rest => true` (`cpt.php`), so they're
   automatically exposed — no extra REST config needed.
4. Order: pages → shades (from SKU sheet) → projects → 3 seed articles → media alt-text pass
   (Bahasa Indonesia).
5. **Revoke the Application Password (and/or deactivate the AI user) once the content phase
   is done** (PATCH-18). Never commit the application password to the repo — keep it in a
   secret/env store, not in code.

(A WordPress.com MCP connector also exists, but it targets sites managed via WordPress.com/
Jetpack — not applicable to this self-hosted Hostinger install unless Jetpack is added. The
REST + Application Password route above is the one that works here as-is.)

## 4. Sebari lead form
- Confirm the embed IDs in `blackroll-core/inc/sebari.php` (`971` / `5260` / `139954`).
- ✅ **Open Dependency #7 resolved (2026-09):** Sebari team confirmed `redirect_url` is supported — Mode R ships as the default, no code change needed.
- In the **Sebari dashboard**, set the form's redirect URLs:
  - Success → `https://blackrollblinds.com/kontak/terima-kasih/`
  - Failure → `https://blackrollblinds.com/kontak/gagal/`
- (Contingency only, not expected to be needed) If Sebari's redirect stops working: set `define('BLACKROLL_SEBARI_SUBMIT_MODE','fetch');` in `wp-config.php` (Mode F: inline success, needs the CSP `connect-src` which is added automatically for fetch mode).
- Test an end-to-end submit into the WhatsApp automation.

## 5. SEO (Rank Math)
- Run the setup wizard; verify per-page title/meta, `Organization`/`LocalBusiness`, `Product`, `Article`, `BreadcrumbList` schema.
- Confirm **core wp-sitemap is disabled** (blackroll-core does this) and Rank Math's per-language sitemap is submitted to Google Search Console.
- Verify `hreflang` for ID/EN pairs (Polylang). EN is progressive (D6) — hreflang only emits for existing pairs.
- Cross-check the submitted sitemap against **`deploy/SITEMAP.md`** — it should contain exactly the "Halaman publik" list there, nothing from the "sengaja TIDAK publik" table (shade singles, `color_series`/`project_type` archives).

## 6. Security (Module 11)
- Copy `deploy/uploads.htaccess` → `wp-content/uploads/.htaccess` (blocks PHP execution).
- Optionally add `deploy/security-headers.htaccess` rules to the site root `.htaccess` (HSTS + headers) — CSP is already sent by blackroll-core.
- CSP rollout: it ships **report-only**. After verifying the Sebari POST + map + fonts work, set `define('BLACKROLL_CSP_REPORT_ONLY', false);` in `wp-config.php` to enforce.
- `define('DISALLOW_FILE_EDIT', true);` (also set by the plugin), XML-RPC off (set by the plugin).
- Solid Security: enable 2FA for admins, limit login attempts, obscure wp-login.
- Add `define('WP_ENVIRONMENT_TYPE','production');` on production so `robots.txt` stops disallowing (staging returns `Disallow: /`).

## 7. Cutover (staging → production)
1. Final content check on staging.
2. Search-replace staging URL → `https://blackrollblinds.com` (WP-CLI `wp search-replace`).
3. Purge LiteSpeed cache.
4. **Flip `robots.txt`**: ensure production env type so it allows crawling (classic launch-killer if forgotten).
5. Resubmit Rank Math sitemap to GSC.
6. Flip CSP report-only → enforce (step 6) after a final submit test.
7. **`wp rewrite flush`** — required after this codebase's `shade`/`color_series`/`project_type` registration changes (now non-public). Without a flush, old rewrite rules stay cached and `/warna/*`, `/seri-warna/*`, `/tipe-proyek/*` keep resolving instead of 404ing. Verify per `deploy/QA-CHECKLIST.md`.

## 8. Ops runbook
- **Admin access:** MEA + client admin accounts (least-privilege; content team = Editor).
- **Backups:** Hostinger daily managed backup, retain ≥ 30 days; test a restore once.
- **Monthly update check — OWNER: MEA ops** (calendar reminder). Core/theme/plugin updates on staging first.
- **Incident:** where backups live + restore steps documented here; who to contact.

## Open dependencies still to close
- ✅ ~~#7 Sebari `redirect_url` support~~ — **resolved 2026-09**, confirmed supported.
- Official logo — ✅ **traced from the client's official white PNG (2026-10-01)** into `logo-horizontal.svg` / `logo-stacked.svg` / `logo-full.svg` / `favicon.svg`. The client confirmed no SVG master exists; the PNG is kept in `themes/blackroll/assets/images/brand/`.
- **SKU mapping for the Sept 2026 photo shoot** — the client's 19 zip folders are named `blind 2…19`; only `A004` and `S004` are identifiable. Once the client maps the rest, add them to `blackroll_shade_photo_keys()` (`plugins/blackroll-core/inc/selector.php`) and every mapped shade gets its swatch + preview photo. See `themes/blackroll/assets/images/collection/README.md`.
- Motor / remote photos for the Motorized page — not in the Sept 2026 shoot.
- ✅ ~~Kebijakan Privasi sign-off~~ — **legal sign-off done 2026-09**; final copy still to be uploaded and swapped into `inc/seed-content.php` (current draft is a placeholder pending that upload).
- EN translations (progressive, D6) — ongoing, not a launch blocker.
