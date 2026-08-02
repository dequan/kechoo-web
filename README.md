# KECHOO WordPress Site

Custom WordPress and WooCommerce implementation for **KECHOO — Choose Better Cutting.**

## Project structure

- `wp-content/themes/kechoo`: custom responsive theme and WooCommerce presentation
- `wp-content/plugins/kechoo-core`: product taxonomy, technical product fields, blade selector, and RFQ workflow
- `PRODUCT.md`, `DESIGN.md`, `SITE-BRIEF.md`: approved product and design direction
- `DEPLOYMENT.md`: deployment, Cloudflare, email, SMTP, backup, analytics, PayPal, shipping, and launch decisions
- `docs/github-vps-deployment-runbook.md`: repeatable GitHub-to-aaPanel VPS deployment and migration runbook
- `data-templates/`: CSV and fill-in templates for first public product and launch information

## WordPress installation

1. Install WordPress 6.5+ and WooCommerce on PHP 8.1+.
2. Copy this repository's `wp-content/themes/kechoo` and `wp-content/plugins/kechoo-core` directories into the WordPress installation.
3. Activate **KECHOO Core**, then activate the **KECHOO** theme.
4. Configure WooCommerce for USD, PayPal, stock management, China as the store origin, and the required shipping zones/rates.
5. Assign the primary and footer menus, set a static front page, and add verified hot-selling product data.

The KECHOO Core activation routine creates the Find Your Blade, Request a Quote, and Distributors pages when those slugs do not already exist.

## Local WordPress test site

1. Run `npm install` once.
2. Run `npm run wp:start`.
3. Open `http://127.0.0.1:9400/`. WordPress admin is available at `/wp-admin/` with username `kechoo-admin` and password `kechoo-test`. This test administrator uses Simplified Chinese; the public site remains English.

The first start installs WooCommerce, activates the KECHOO theme and core plugin, creates the RFQ/selector pages, and imports nine clearly marked test products from `test-site/hot-selling-products.json`. USD, China store origin, guest checkout, and test flat-rate zones for North America, Europe, and Southeast Asia are configured. A local-only “Test checkout — no payment collected” gateway keeps checkout testable before PayPal Sandbox credentials are connected.

Useful test paths:

- `/shop/`: WooCommerce catalog with the KECHOO blade filter panel and compact specification summaries on product cards.
- `/find-your-blade/`: guided selector page.
- `/request-a-quote/`: RFQ form for custom sizes, complex applications, and distributors.
- `/wp-admin/edit.php?post_type=product&page=kechoo-product-guide`: Chinese KECHOO product-upload guide for operators.
- `/returns-refunds/`: draft returns and refunds policy.
- `/customs-duties/`: draft customs and duties policy.

Run `npm run wp:reset` when you need a clean test database. Resetting deletes the locally persisted Playground site and imports the test catalog again. Test prices, stock, shipping, compatibility, dispatch times, product text, and generated imagery must never be treated as production data.

PayPal should be added back when Sandbox or merchant credentials are ready. Until then the local checkout uses the no-payment test gateway to keep ordering flows simple and deterministic.

For deployment planning, see `DEPLOYMENT.md`. For the exact GitHub-to-VPS deployment and migration flow, see `docs/github-vps-deployment-runbook.md`. The current recommended milestone is `v0.1-deploy-prep`.

## Public-lite launch mode

The current site is configured as an RFQ-first public catalog. Products can be browsed, filtered, and used to start quote requests, but the frontend does not expose test prices, Add to cart buttons, or checkout as the primary buyer path.

Use `data-templates/products-public-lite.csv` to prepare the first real product batch. Price, stock, PayPal, and exact shipping rules can wait until the ecommerce phase.
