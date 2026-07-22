# Band Saw Blade Supply Astro Deployment

## Build

```bash
npm install
npm run build
```

The production output is generated in:

```text
dist/
```

## Environment

Set the production site URL before building:

```bash
PUBLIC_SITE_URL=https://bandsawbladesupply.com npm run build
```

## Static Hosting

This Astro site can be deployed to static hosting such as Cloudflare Pages, Netlify, Vercel, GitHub Pages or an ordinary static web server.

Recommended production settings:

- build command: `npm run build`
- output directory: `dist`
- Node.js: current LTS
- redirects: confirm whether `www` redirects to root or root redirects to `www`
- cache images and built assets aggressively

## Launch Checklist

- Add an official WhatsApp contact after the production number is confirmed.
- Confirm final domain and set `PUBLIC_SITE_URL`.
- Add verified company identity to About, Contact, Privacy and Terms.
- Replace placeholder product details with verified launch catalog data.
- Add real product family photos where available.
- Run `npm run build` and inspect generated sitemap.
- Submit sitemap in Google Search Console after DNS launch.
- Configure analytics only after privacy wording is updated.

## Not In Current Deployment

The current site intentionally does not deploy WordPress, WooCommerce, PayPal checkout, customer accounts or a database. Those systems can be reintroduced later only if direct online ordering becomes a validated business requirement.
