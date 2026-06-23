# Product field guide

For the first public version, fill the required fields first. Optional fields can be completed later.

## Required for public catalog

- `sku`: unique internal product code.
- `product_name_en`: English product title.
- `application`: one of `Food & Bone`, `Wood`, `Metal`.
- `blade_technology`: one of `Hardened`, `Bi-Metal`, `Carbide`.
- `blade_length_mm`: blade length in millimeters.
- `blade_width_mm`: blade width in millimeters.
- `blade_thickness_mm`: blade thickness in millimeters.
- `tooth_pitch`: TPI or pitch, for example `3/4 TPI`, `4/6 TPI`, `2/3 TPI`.
- `image_files`: image folder or filenames. Use SKU-based folders if possible.
- `short_description_en`: one or two plain English sentences.

## Strongly recommended

- `cut_material`: what the buyer cuts, for example `Frozen bone`, `Hardwood`, `Carbon steel`.
- `machine_compatibility`: machine type or common compatible machines.
- `tooth_form`: tooth form or set.
- `backing_material`: backing material.
- `tooth_material`: tooth material.
- `recommended_cutting_range`: workpiece range or use case.
- `selection_rationale_en`: why this blade is recommended.

## Can wait until ecommerce phase

- `price_usd`
- `stock_qty`
- exact shipping rule
- PayPal checkout readiness

## Naming pattern

Recommended English title:

`{Technology} Blade {Length} × {Width} × {Thickness} mm — {TPI}`

Examples:

- `M42 Bi-Metal Blade 4115 × 34 × 1.10 mm — 3/4 TPI`
- `Hardened Meat & Bone Blade 1650 × 16 × 0.56 mm — 4 TPI`
- `Carbide-Tipped Blade 5450 × 41 × 1.30 mm — 2/3 TPI`

## Photo requirements

Minimum for first public version:

- 1 clean product image per product or product family.
- 1 application image per major application if available.

Better:

- product coil / blade overview
- tooth closeup
- weld or joint closeup
- packaging
- cutting scene

