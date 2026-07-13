# Blackroll — Deployment & Ops Runbook (Hostinger)

Deploy is intentionally the **last** step. This is the cutover + ops reference.

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
- Create the pages/content: run `wp blackroll seed` (WP-CLI) OR create pages manually and enter shades/projects/articles (see main README content guide). Enter real shades from the SKU sheet.

## 3. Content ops (MCP optional)
- Create a **limited-role (Editor) AI user**, generate an Application Password for MCP content-entry, **revoke after** the content phase (PATCH-18).
- Order: pages → shades (from SKU sheet) → projects → 3 seed articles → media alt-text pass (Bahasa Indonesia).

## 4. Sebari lead form
- Confirm the embed IDs in `blackroll-core/inc/sebari.php` (`971` / `5260` / `139954`).
- In the **Sebari dashboard**, set the form's redirect URLs:
  - Success → `https://blackrollblinds.com/kontak/terima-kasih/`
  - Failure → `https://blackrollblinds.com/kontak/gagal/`
- If Sebari cannot redirect: set `define('BLACKROLL_SEBARI_SUBMIT_MODE','fetch');` in `wp-config.php` (Mode F: inline success, needs the CSP `connect-src` which is added automatically for fetch mode).
- Test an end-to-end submit into the WhatsApp automation.

## 5. SEO (Rank Math)
- Run the setup wizard; verify per-page title/meta, `Organization`/`LocalBusiness`, `Product`, `Article`, `BreadcrumbList` schema.
- Confirm **core wp-sitemap is disabled** (blackroll-core does this) and Rank Math's per-language sitemap is submitted to Google Search Console.
- Verify `hreflang` for ID/EN pairs (Polylang). EN is progressive (D6) — hreflang only emits for existing pairs.

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

## 8. Ops runbook
- **Admin access:** MEA + client admin accounts (least-privilege; content team = Editor).
- **Backups:** Hostinger daily managed backup, retain ≥ 30 days; test a restore once.
- **Monthly update check — OWNER: MEA ops** (calendar reminder). Core/theme/plugin updates on staging first.
- **Incident:** where backups live + restore steps documented here; who to contact.

## Open dependencies still to close
- #7 Sebari `redirect_url` support (drives step 4 choice).
- Official logo vector (currently a reconstructed placeholder mark).
- Kebijakan Privasi copy (MEA).
- EN translations (progressive, D6).
