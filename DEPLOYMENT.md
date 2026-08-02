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

## aaPanel VPS deployment runbook

Use this runbook when deploying the public-lite version to a VPS with aaPanel installed.

For the exact repeatable GitHub-to-VPS deployment flow, deploy-key setup, routine updates, rollback, and new-server migration checklist, see:

```text
docs/github-vps-deployment-runbook.md
```

The standard deployment model is:

```text
Local commit and push
→ GitHub private repository
→ VPS pulls with a read-only deploy key
→ rsync only wp-content/themes/kechoo and wp-content/plugins/kechoo-core
→ WordPress database keeps pages, products, RFQs, settings, uploads, and live state
```

Do not use local direct file upload as the normal deployment path.

### 1. aaPanel environment

Install or confirm these aaPanel App Store components:

- Nginx
- MySQL 8.0 or MariaDB 10.6+
- PHP 8.3
- phpMyAdmin, optional
- Redis, optional for later optimization

Enable these PHP 8.3 extensions:

- `mysqli`
- `pdo_mysql`
- `curl`
- `mbstring`
- `xml`
- `zip`
- `gd` or `imagick`
- `intl`
- `fileinfo`
- `exif`
- `opcache`

Basic server safety:

- Change the default aaPanel port.
- Use a strong aaPanel password.
- Open only required ports: SSH, aaPanel, 80, 443.
- Do not expose MySQL port 3306 to the public internet.
- Prefer SSH keys over password login.

### 2. Cloudflare DNS before deployment

In Cloudflare DNS, create:

```txt
A      @      <your-vps-ip>
CNAME  www    kechoo.com
```

Recommended order:

1. Start with DNS-only records while installing and issuing SSL.
2. Confirm `http://kechoo.com` and `https://kechoo.com` work on the origin.
3. Then enable Cloudflare proxy.

Keep email records DNS-only:

- MX
- SPF TXT
- DKIM TXT/CNAME
- DMARC TXT

### 3. Create the site in aaPanel

In aaPanel:

1. Go to `Website`.
2. Add site:

```txt
Domain: kechoo.com
Alias: www.kechoo.com
PHP: 8.3
Root: /www/wwwroot/kechoo.com
```

3. Create a database:

```txt
Database: kechoo_wp
User: kechoo_wp
Password: strong generated password
```

Save the database name, user, and password. You will need them for WordPress.

### 4. Install WordPress

Use either aaPanel's WordPress installer or a manual WordPress install.

For manual install:

```bash
cd /www/wwwroot/kechoo.com
wget https://wordpress.org/latest.tar.gz
tar -xzf latest.tar.gz
mv wordpress/* .
rm -rf wordpress latest.tar.gz
chown -R www:www /www/wwwroot/kechoo.com
```

Then open:

```txt
http://kechoo.com
```

Complete the WordPress installer with the database credentials from aaPanel.

Recommended initial admin account:

- Do not use `admin`.
- Use a strong password.
- Store the password in a password manager.

### 5. Install WooCommerce

In WordPress admin:

1. Plugins → Add New.
2. Install WooCommerce.
3. Activate WooCommerce.
4. Configure minimally:
   - Currency: USD
   - Store country: China
   - Taxes: off for public-lite unless you have a tax workflow
   - Payments: skip for now
   - Shipping: keep quote-first or temporary manual rules

The public-lite version does not need PayPal or online checkout to launch.

### 6. Deploy KECHOO theme and plugin

SSH into the VPS:

```bash
REPO_DIR=/opt/kechoo/deploy/kechoo-web
BRANCH=codex/wordpress-deploy-prep
SITE_ROOT=/www/wwwroot/kechoo.com
export GIT_SSH_COMMAND='ssh -i /root/.ssh/kechoo_github_deploy -o StrictHostKeyChecking=accept-new'

mkdir -p "$(dirname "$REPO_DIR")"
git clone --branch "$BRANCH" --single-branch git@github.com:dequan/kechoo-web.git "$REPO_DIR"
```

Copy only the KECHOO theme and KECHOO plugin:

```bash
find "$REPO_DIR/wp-content/themes/kechoo" \
     "$REPO_DIR/wp-content/plugins/kechoo-core" \
     -name "*.php" -print0 |
xargs -0 -n1 php -l

rsync -az --delete "$REPO_DIR/wp-content/themes/kechoo/" "$SITE_ROOT/wp-content/themes/kechoo/"
rsync -az --delete "$REPO_DIR/wp-content/plugins/kechoo-core/" "$SITE_ROOT/wp-content/plugins/kechoo-core/"
chown -R www:www "$SITE_ROOT/wp-content/themes/kechoo"
chown -R www:www "$SITE_ROOT/wp-content/plugins/kechoo-core"
```

Do not copy these development-only paths into the production web root:

- `node_modules/`
- `test-site/`
- `package.json`
- `package-lock.json`
- `.wordpress-playground/`
- `data-templates/`

The `data-templates/` folder is for preparing catalog data locally, not for public web serving.

### 7. Activate KECHOO in WordPress

In WordPress admin:

1. Plugins → Activate `KECHOO Core`.
2. Appearance → Themes → Activate `KECHOO`.
3. Settings → Permalinks → choose `Post name` → Save.

KECHOO Core should create these pages:

- `/find-your-blade/`
- `/request-a-quote/`
- `/distributors/`
- `/applications/`
- `/technology/`
- `/resources/`
- `/about/`
- `/shipping/`
- `/privacy-policy/`
- `/terms/`
- `/returns-refunds/`
- `/customs-duties/`

If a page is missing, deactivate and reactivate KECHOO Core once, then save permalinks again.

### 8. Production WordPress settings

Settings → General:

```txt
Site Title: KECHOO
Tagline: Choose Better Cutting
WordPress Address: https://kechoo.com
Site Address: https://kechoo.com
Timezone: Asia/Shanghai or the preferred business timezone
```

Settings → Reading:

- Homepage should show the KECHOO front page.
- Search engine visibility should be unchecked when you are ready for Google indexing.
- If you are still testing publicly, keep it noindex until final review.

### 9. SSL in aaPanel

In aaPanel:

1. Website → `kechoo.com` → SSL.
2. Apply for Let's Encrypt SSL.
3. Include both:
   - `kechoo.com`
   - `www.kechoo.com`
4. Enable force HTTPS only after HTTPS works.

Test:

```txt
https://kechoo.com
https://www.kechoo.com
```

After the origin certificate works, switch Cloudflare SSL/TLS mode to:

```txt
Full (strict)
```

Then enable:

- Always Use HTTPS
- Automatic HTTPS Rewrites
- Brotli
- HTTP/3

Avoid full-page HTML caching at this stage.

### 10. Cloudflare cache rules

Use standard caching first.

Do not cache these paths:

```txt
/wp-admin/*
/wp-login.php
/cart/*
/checkout/*
/my-account/*
/request-a-quote/*
```

For public-lite, cart and checkout are not primary paths, but keeping these exclusions avoids future WooCommerce problems.

### 11. Email receiving

Cloudflare Email Routing can be used for early receiving:

```txt
sales@kechoo.com   -> your existing mailbox
info@kechoo.com    -> your existing mailbox
support@kechoo.com -> your existing mailbox
orders@kechoo.com  -> your existing mailbox
```

This is enough to receive inquiries at launch, but it is not a full mailbox. For sending replies as `@kechoo.com`, use a real mailbox provider such as Zoho Mail, Google Workspace, Microsoft 365, or another SMTP-capable provider.

### 12. SMTP for WordPress

Install one SMTP plugin:

- FluentSMTP, or
- WP Mail SMTP

Configure:

```txt
From Email: sales@kechoo.com
From Name: KECHOO
```

Use authenticated SMTP from your mailbox provider. Then test:

1. Submit `/request-a-quote/`.
2. Confirm the email arrives.
3. Confirm it does not go to spam.

If SMTP is not configured, WordPress/PHP mail may fail silently or land in spam.

### 13. Backups in aaPanel

Create aaPanel scheduled tasks:

Daily database backup:

```txt
Database: kechoo_wp
Retention: 14-30 days
```

Daily website file backup:

```txt
Path: /www/wwwroot/kechoo.com
Retention: 14-30 days
```

Strong recommendation:

- Store a copy outside the VPS.
- Use S3, FTP, Google Drive, another server, or host-level snapshot.
- Test one restore after launch.

### 14. Security baseline

WordPress:

- Do not use `admin` as the admin username.
- Use strong passwords.
- Create separate accounts for operators.
- Use Shop Manager or a limited role for product operators.
- Delete unused themes.
- Delete unused plugins.
- Update WordPress, WooCommerce, theme, and plugins on a schedule.
- Install a login limit plugin.

aaPanel:

- Change the panel port.
- Use strong panel password.
- Keep panel and stack updated.
- Do not expose MySQL publicly.
- Keep SSH restricted.

Cloudflare:

- Enable basic WAF protections.
- Add Turnstile to RFQ later if spam appears.
- Do not over-tune security rules before the site is stable.

### 15. Public-lite acceptance test

After deployment, check these URLs:

```txt
https://kechoo.com/
https://kechoo.com/shop/
https://kechoo.com/products/
https://kechoo.com/applications/
https://kechoo.com/technology/
https://kechoo.com/resources/
https://kechoo.com/about/
https://kechoo.com/shipping/
https://kechoo.com/contact/
https://kechoo.com/find-your-blade/
https://kechoo.com/request-a-quote/
https://kechoo.com/distributors/
https://kechoo.com/privacy-policy/
https://kechoo.com/terms/
https://kechoo.com/returns-refunds/
https://kechoo.com/customs-duties/
```

Expected public-lite behavior:

- Product catalog loads.
- Product filters work.
- Product cards show `Quote on request`.
- Product pages show `Request price and availability`.
- No public test price is visible.
- No `Add to cart` button is visible.
- RFQ form submits.
- RFQ email arrives.
- Mobile menu works.
- Footer legal links work.
- SSL lock is valid.
- No Cloudflare redirect loop.

### 16. Search Console and GA4 after deployment

After the site is stable:

1. Create Google Search Console domain property for `kechoo.com`.
2. Add Google's TXT verification record in Cloudflare DNS.
3. Verify the domain.
4. Submit sitemap:

```txt
https://kechoo.com/wp-sitemap.xml
```

For GA4:

1. Create GA4 property.
2. Create Web data stream for `https://kechoo.com`.
3. Add the measurement ID with Site Kit or a lightweight header/footer plugin.

Do not over-focus on analytics before the site has real content and Search Console indexing data.
