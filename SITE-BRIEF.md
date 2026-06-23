# KECHOO WordPress Independent Site Design Brief

## 1. Feature Summary

Build an English-first, production-ready WordPress and WooCommerce site for KECHOO bandsaw blades. The site serves overseas industrial end users and distributors across Europe, North America, Southeast Asia, and other core markets. It must combine a memorable industrial brand presentation with fast product selection, online purchase of selected in-stock specifications, and structured RFQ handling for custom, technical, or wholesale requirements.

The launch catalog will contain only hot-selling specifications. The architecture must scale from a few dozen launch SKUs to hundreds of configurations without turning the site into a crowded marketplace.

## 2. Primary User Action

The primary action is to identify the correct bandsaw blade for a cutting application, then complete one of two outcomes:

1. Purchase a verified in-stock specification in USD through PayPal.
2. Submit a technically complete RFQ for a custom size, complex application, or distributor order.

Visitors must never need to understand blade manufacturing terminology before they can choose Food & Bone, Wood, or Metal.

## 3. Design Direction

**Color strategy:** Committed. Deep oxide red carries the KECHOO brand across the hero and decisive actions; inspection white, machine graphite, cool gray, and pale steel keep product information exact and readable.

**Scene sentence:** An overseas production manager or distributor reviews blade options on a bright workshop-office screen beside operating machinery, focused on fit, cutting result, stock, and delivery rather than browsing for inspiration.

**Anchor references:**

- Visual Probe B for brand impact, deep-red ownership, blade drama, and the strongest expression of “Choose Better Cutting.”
- Visual Probe C for immediate Food & Bone, Wood, and Metal classification, purchasing clarity, and technical density.
- Hilti and Starrett for disciplined industrial confidence and evidence-led product communication.
- FOXBC only for functional lessons around purchasable specifications, reviews, compatibility-led organization, and regional commerce. Its crowded homepage and weak English are explicit anti-references.

**Winning direction:** Use B as the hero and brand layer, then place C's three-application gateway directly beneath it. The first viewport creates desire and trust; the next viewport immediately answers what KECHOO sells, what it cuts, and where the visitor should go.

## 4. Scope

**Fidelity:** Production-ready planning.

**Breadth:** Whole public website and purchasing flow, including:

- Home
- Product catalog
- Food & Bone cutting
- Wood cutting
- Metal cutting
- Hardened bandsaw blades
- Bi-metal bandsaw blades
- Carbide bandsaw blades
- Product detail
- Find Your Blade selector
- Solutions and application pages
- Technology and quality
- Resources and technical articles
- Distributor and OEM page
- About KECHOO
- Contact and RFQ
- Search
- Cart and checkout
- Customer account and order status
- Shipping, returns, privacy, terms, and warranty policies

**Interactivity:** Shipped-quality responsive interactions, filters, selector flow, cart, PayPal checkout, RFQ upload, validation, stock state, and configured shipping fees.

**Time intent:** Build the durable foundation first. Launch with selected hot-selling specifications, then expand products, technical content, regional shipping rules, and languages without redesigning the content model.

**Technical constraints:**

- WordPress with a custom lightweight block theme
- Gutenberg for controlled editorial composition
- WooCommerce for USD catalog, stock, cart, checkout, customer account, orders, and PayPal
- China as the initial dispatch origin
- Configurable shipping zones, methods, rates, and unavailable-destination handling
- Structured custom fields and taxonomies for blade data
- English-first content, prepared for future multilingual use
- WCAG 2.2 AA baseline
- Performance-first implementation without a heavy visual page builder

## 5. Layout Strategy

### Homepage flow

1. **Brand hero:** Probe B composition. Deep oxide-red field, dramatic bandsaw blade photography, “KECHOO — Choose Better Cutting.”, and two actions: Shop In-Stock and Request a Quote.
2. **Application gateway:** Probe C logic immediately below the hero. Three large, image-led entries for Food & Bone, Wood, and Metal, each explaining what is cut and leading to a guided catalog.
3. **Find Your Blade:** A compact selector entry that asks about application, machine or blade size, workpiece material, and cutting goal.
4. **Blade technology:** Hardened, bi-metal, and carbide blade families explained through application fit rather than metallurgy alone.
5. **Hot-selling stock:** A focused set of verified purchasable products, never an endless product wall.
6. **Proof of process:** Tooth forming, heat treatment, welding, inspection, material control, and cutting tests shown with real evidence.
7. **Solutions and technical guidance:** Application cases, selection guidance, troubleshooting, and blade-life content.
8. **Distributor path:** Bulk supply, OEM packaging, commercial support, and partnership RFQ.
9. **Closing action:** Find a Blade, Shop In-Stock, or Request a Quote.

### Global navigation

Primary navigation should remain concise:

- Products
- Applications
- Find Your Blade
- Technology
- Resources
- Distributors
- About

Utility actions contain search, language readiness, account, cart, and Request a Quote. Mobile navigation preserves the three commercial actions without reproducing a desktop mega-menu at full density.

### Catalog organization

Allow two complementary entry systems:

- **By application:** Food & Bone, Wood, Metal
- **By blade technology:** Hardened, Bi-metal, Carbide

Filters narrow within an already chosen context. They must not ask users to process every attribute at once. Desktop uses a clear filter rail or toolbar; mobile uses a dedicated filter sheet with applied-filter summary.

### Product template

The product page prioritizes:

1. Product identity and compatible cutting use
2. Product imagery and tooth/detail inspection
3. Stock configuration selector
4. Price, stock, pack quantity, dispatch and shipping cues
5. Add to Cart for verified stock
6. Request Custom Size or Ask for Selection Help
7. Structured specification table
8. Compatibility and recommended cutting range
9. Technical downloads, use guidance, reviews, and related products

## 6. Key States

- **Catalog default:** Hot-selling products appear first with meaningful application and stock labels.
- **No filter results:** Explain which filters conflict and offer Clear Filters, Request a Custom Blade, and Ask for Selection Help.
- **Product data pending:** Draft products remain unpublished. Missing technical fields never appear as empty rows on the public site.
- **In stock:** Show purchasable configuration, available quantity or truthful availability wording, dispatch estimate, and shipping calculation path.
- **Low stock:** Communicate limited availability without artificial urgency.
- **Out of stock:** Disable purchase, offer stock notification where appropriate, and route to RFQ or an alternative specification.
- **Custom only:** Hide ordinary Add to Cart and make Request a Quote the primary action.
- **Unknown machine or size:** Let users enter machine brand/model, upload a blade label or photo, and request selection help.
- **Selector loading/error:** Preserve previous answers and provide retry plus direct RFQ fallback.
- **RFQ success:** Confirm receipt, show a reference number, summarize submitted specifications, and state the expected response window.
- **RFQ validation error:** Keep all entered data and identify the exact missing or invalid field.
- **Shipping unavailable:** Explain that the current destination has no configured online method and offer a freight quotation instead of a dead checkout.
- **Payment failure/cancelled:** Preserve the cart and shipping selection, explain the next action, and allow PayPal retry.
- **Order success:** Confirm payment, delivery destination, order number, order status path, and support contact.
- **Search empty:** Offer application categories, selector entry, spelling guidance, and RFQ.

## 7. Interaction Model

### Find Your Blade

Use progressive disclosure rather than a long technical form:

1. Choose Food & Bone, Wood, or Metal.
2. Identify machine model or known blade dimensions.
3. Choose workpiece material and approximate size.
4. Choose priority such as finish, speed, blade life, or general purpose.
5. Show compatible stock products first and a technical RFQ fallback when no verified match exists.

Every result must explain why it was recommended. The selector never claims compatibility when required data is missing.

### Purchase path

Select verified specification, review price and stock, add to cart, enter destination, calculate configured shipping, and pay in USD through PayPal. Feedback must be immediate and preserve the selected blade attributes throughout cart, checkout, order email, and account history.

### RFQ path

RFQ is available globally and prefilled when launched from a product, filter result, or selector. It collects contact/company details, buyer type, application, material, machine, blade dimensions, estimated quantity, target market, and optional files. Distributor RFQs add annual volume, sales territory, OEM/private-label needs, and desired cooperation type.

### Motion

Use responsive feedback and controlled transitions only. The hero may use one composed load sequence, but content must remain visible without animation. Filters, selector steps, cart feedback, validation, and menus require clear state changes and reduced-motion alternatives.

## 8. Content Requirements

### Structured product data

Each product or purchasable configuration needs fields for:

- Product family and blade technology
- Application world and compatible materials
- SKU
- Length, width, thickness, and unit
- TPI or tooth pitch
- Tooth form or set where relevant
- Blade/backing/tooth material
- Compatible machine brands/models where verified
- Pack quantity
- USD price
- Stock state and quantity policy
- Dispatch estimate
- Shipping class
- MOQ for non-stock or wholesale orders
- Custom-size availability
- Technical summary and selection rationale
- Usage, break-in, maintenance, and safety guidance
- Main image, blade detail, tooth macro, weld detail, packaging, and application image
- Datasheet or downloadable technical document

Variation architecture must avoid creating an unmanageable matrix. Only verified hot-selling combinations become purchasable WooCommerce variations. Other combinations route to RFQ.

### Image roles

Launch requires real product assets, not generic factory placeholders:

- Brand hero bandsaw blade coil or tooth macro
- Food & Bone application image
- Wood application image
- Metal application image
- Product pack shot on controlled neutral background
- Tooth profile macro
- Weld detail
- Blade marking and packaging
- Manufacturing and inspection evidence
- Cutting result or workpiece surface

Temporary generated imagery may guide composition, but final product claims and technical detail must use verified KECHOO photography.

### Copy and trust content

- Professionally edited English throughout
- Clear distinction between stock purchase, custom order, and distributor inquiry
- Honest China dispatch origin and shipping expectations
- Manufacturing process and quality-control evidence
- Payment, shipping, returns, warranty, privacy, and terms
- Company identity and contact information
- No certification, performance, stock, delivery, or compatibility claim without supporting data

### Realistic scale

- Launch: selected hot-selling specifications across the three application worlds
- Typical growth: dozens of product families and purchasable configurations
- Long-term: hundreds of configurations, application articles, machine compatibility records, and regional shipping rules

## 9. Recommended References

Implementation should load these Impeccable references as needed:

- `layout.md` for the B-plus-C homepage rhythm, catalog density, product pages, and mobile restructuring
- `typeset.md` for the single-family technical hierarchy and specification readability
- `colorize.md` for disciplined oxide-red ownership without promotional red leakage
- `clarify.md` for selection questions, technical labels, checkout copy, RFQ guidance, and English microcopy
- `harden.md` for stock, shipping, payment, form, upload, empty, error, and multilingual edge cases
- `adapt.md` for mobile filters, tables, selection flow, navigation, and checkout
- `audit.md` before launch for accessibility, performance, responsive behavior, and technical quality

## 10. Open Questions

These do not block design-brief confirmation, but must be resolved before the affected implementation or launch step:

- Final KECHOO logo artwork and approved file formats
- Initial hot-selling SKU list and verified product taxonomy values
- Product photos, technical documents, English product copy, prices, and stock data
- PayPal merchant account and production credentials
- Initial shipping zones, calculation method, rates, exclusions, and delivery estimates from China
- Return, warranty, privacy, tax, customs-duty, and shipping policy wording
- RFQ recipient workflow, response-time commitment, and future CRM destination

## Confirmation Gate

**Status: Confirmed by the project owner on 2026-06-21.**

This brief is the implementation boundary. The B-plus-C direction, full-site scope, dual purchase/RFQ model, and proposed information architecture are approved for implementation.
