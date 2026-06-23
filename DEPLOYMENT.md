# KECHOO Deployment Plan

This document captures the deployment decisions for `kechoo.com` and separates what is ready for the v0.1 deploy-prep milestone from what should wait for real operating data.

## Recommended stage decision

Seal a stage version after the current deploy-prep changes. Do not wait for final product photos, final pricing, exact stock, PayPal credentials, or precise shipping rules. Those belong in the next data and commerce iteration.

Suggested next tag after these changes: `v0.2-public-lite`.

## Domain and DNS

Domain: `kechoo.com`

Recommended DNS manager: Cloudflare.

Initial DNS shape:

- `A` or `CNAME` for `kechoo.com` to the production WordPress host.
- `CNAME` for `www` to `kechoo.com` or to the host-provided target.
- Use Cloudflare proxy only after the host SSL certificate is issued and WordPress is loading correctly.
- Keep mail-related records unproxied. MX, SPF, DKIM, and DMARC records must stay DNS-only.

## SSL and CDN

Recommended Cloudflare SSL mode after the origin certificate is valid: Full (strict).

Initial Cloudflare settings:

- SSL/TLS mode: Full (strict)
- Always Use HTTPS: on after the site works over HTTPS
- Automatic HTTPS Rewrites: on
- Minimum TLS: TLS 1.2 or higher
- Brotli: on
- HTTP/3: on
- Cache: standard Cloudflare cache for static assets, not full-page cache at first

Do not enable aggressive HTML caching before checkout, cart, account, and RFQ flows are tested.

## Hosting recommendation

Best first deployment path: managed WordPress hosting or managed VPS panel, not a raw self-managed VPS.

Why:

- WooCommerce needs reliable PHP, database, cron, backups, object cache, SMTP, SSL, and security updates.
- KECHOO’s immediate business value is inquiries, product pages, SEO, and stable checkout, not server administration.

Shortlist direction:

- Managed WordPress host for lowest maintenance.
- Cloudways-style managed VPS if more control is needed.
- Raw VPS only if someone will actively maintain Linux, PHP, MySQL, Redis, SSL, firewall, backups, monitoring, and updates.

## Email

Cloudflare Email Routing is enough for receiving mail such as `info@kechoo.com`, `sales@kechoo.com`, or `support@kechoo.com` and forwarding it to an existing mailbox.

It is not a full mailbox by itself. It does not give a normal inbox, sent folder, IMAP mailbox, or team collaboration flow.

Recommended free/low-cost path:

1. Start with Cloudflare Email Routing for inbound aliases.
2. Use a real mailbox for the destination, for example Gmail, Outlook, Zoho Mail, or another business mailbox.
3. If sending from `@kechoo.com` is required, use a mailbox provider or SMTP provider that supports domain authentication.

For a serious B2B site, use a real mailbox once inquiries begin. Free forwarding is fine for launch testing; proper business email is better for buyer trust and reply deliverability.

## SMTP

WordPress should use authenticated SMTP or a transactional email service in production.

SMTP is necessary because WordPress/PHP mail often lands in spam or fails silently on many hosts.

Recommended SMTP path:

- Stage 1: host-provided SMTP or mailbox SMTP for quote notifications and order emails.
- Stage 2: transactional provider if volume grows or deliverability becomes important.

Required sender addresses:

- `sales@kechoo.com` for RFQ and commercial replies
- `orders@kechoo.com` for WooCommerce order notifications
- Optional: `support@kechoo.com` for after-sales support

DNS records to configure when the mail provider is selected:

- SPF
- DKIM
- DMARC

## Google Search Console and GA4

Recommended sequence:

1. Create a Google Search Console domain property for `kechoo.com`.
2. Verify ownership with a DNS TXT record in Cloudflare.
3. Submit the WordPress XML sitemap after production launch.
4. Create a GA4 property and web data stream for `https://kechoo.com`.
5. Add the GA4 measurement tag through a small plugin, Google Site Kit, or a theme integration.

Do not spend time analyzing traffic before the site has real pages, real products, and Search Console indexing data.

## Backup and security

Minimum production baseline:

- Daily database backup.
- Daily files backup.
- Keep at least 14 to 30 days of restore points.
- Off-site backup storage, not only on the same server.
- Test a restore once after launch.
- Enable WordPress automatic minor updates.
- Update plugins and themes on a schedule, not randomly during business hours.
- Use strong admin passwords and two-factor authentication.
- Disable unused admin accounts.
- Limit login attempts.
- Add anti-spam protection to RFQ forms.
- Keep a staging site or local copy for testing updates.

Recommended first tools depend on hosting:

- If managed host includes backups and WAF, use host features first.
- If not, add a reputable backup plugin and a lightweight security plugin.
- Avoid stacking multiple security plugins that overlap.

## RFQ spam protection

Current local RFQ form has nonce, honeypot, and basic rate limiting.

Production options:

- Keep honeypot and rate limiting.
- Add Cloudflare Turnstile if spam appears.
- Add server-side email/domain filtering only after real spam patterns are known.

## Shipping and duties

Current position:

- Stock products ship from China.
- Online flat rates can be used for early testing.
- Bulk, OEM, and distributor orders should use manual quotation.
- Import duties, taxes, customs brokerage, and destination fees are normally buyer responsibility unless a quotation states otherwise.

Recommended first production approach:

1. Keep simple flat-rate zones for small online stock orders.
2. Add “request freight quote” language for countries or large orders that cannot be priced reliably online.
3. For B2B distributor and OEM orders, quote shipping manually.
4. Confirm Incoterms before using them publicly.

## PayPal

Do not block the public-lite launch on PayPal.

When ready:

1. Register PayPal business account.
2. Create Sandbox credentials.
3. Test WooCommerce checkout in Sandbox.
4. Switch to live credentials only after small internal test orders pass.

Until then, local development uses a no-payment test gateway.

## Public-lite product data

For the first public version, use `data-templates/products-public-lite.csv`.

Required product data:

- SKU
- English product title
- application: Food & Bone, Wood, or Metal
- blade technology: Hardened, Bi-Metal, or Carbide
- length, width, and thickness
- tooth pitch / TPI
- short English product description
- at least one product or product-family image

Recommended but not launch-blocking:

- cut material
- machine compatibility
- tooth form
- backing material
- tooth material
- recommended cutting range
- selection rationale
- dispatch estimate
- MOQ for custom sizes

Can wait for ecommerce phase:

- public USD price
- exact stock quantity
- online checkout
- PayPal credentials
- exact automatic shipping rates

## Legal pages

The site includes draft pages for:

- Privacy Policy
- Terms and Conditions
- Returns and Refunds
- Customs and Duties
- Shipping from China

These are practical B2B ecommerce drafts and should be reviewed before production launch.

## v0.1 deploy-prep acceptance checklist

- Favicon present.
- Domain plan documented for `kechoo.com`.
- Cloudflare SSL/CDN plan documented.
- Email, SMTP, backup, security, GSC, GA4, PayPal, and shipping decisions documented.
- Legal draft pages present.
- Test site routes return 200.
- Product test data remains clearly marked as non-production.
