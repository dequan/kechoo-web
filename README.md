# Band Saw Blade Supply Astro site

Static Astro website for **Band Saw Blade Supply**.

The site is focused on B2B bandsaw blade discovery and quotation:

- product discovery without cart or checkout
- application, blade technology and specification pages
- email-based quotation flows with room for buyers to share WhatsApp details
- structured product data for search engines and future content expansion
- reusable industrial visual assets from the previous bandsaw blade project

## Project structure

- `src/pages/`: static routes for home, products, applications, technologies, resources and quotation requests
- `src/data/`: structured site, application, technology and product data
- `src/components/`: shared layout, cards, header/footer and quotation panels
- `src/styles/global.css`: industrial visual system for the Band Saw Blade Supply catalog
- `public/images/`: hero and application imagery
- `DESIGN.md`: current visual direction
- `DEPLOYMENT.md`: production build and launch checklist

## Commands

```bash
npm install
npm run dev
npm run build
npm run preview
```

Local development defaults to:

```text
http://127.0.0.1:4321/
```

## Content model

Products live in `src/data/products.ts`. Public product pages do not expose price, stock, cart or checkout. Each product should include:

- SKU
- application
- blade technology
- length, width, thickness and tooth pitch
- tooth form
- backing and tooth material
- machine type and cutting range
- pack quantity and custom-size policy
- selection rationale

## SEO priorities

The site generates static HTML pages for:

- `/products/`
- `/products/{product-slug}/`
- `/applications/food-bone/`
- `/applications/wood/`
- `/applications/metal/`
- `/technologies/hardened/`
- `/technologies/bi-metal/`
- `/technologies/carbide/`
- `/resources/{guide-slug}/`

Each page has title, description, canonical URL, Open Graph metadata and relevant JSON-LD. The sitemap is generated at build time by `@astrojs/sitemap`.

## Quotation workflow

Quotation forms are static and open a prepared email to `info@bandsawbladesupply.com`. Add an official WhatsApp contact only after the production number is confirmed.

The current business assumption: inquiry quality matters more than instant checkout. Prices, MOQ, lead time, compatibility and shipment details are confirmed by quotation.
