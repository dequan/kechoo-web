# Kechoo WordPress Agent Rules

## Project Architecture

- `wp-content/themes/kechoo` contains presentation code, templates, theme assets, and WooCommerce display overrides.
- `wp-content/plugins/kechoo-core` contains business logic, product metadata, taxonomies, selector behavior, setup routines, and RFQ workflow.
- `data-templates` contains source templates for preparing catalog and business data. Do not deploy it into the public WordPress web root.
- `test-site` contains local WordPress Playground fixtures. Do not deploy it into the public WordPress web root.
- Never modify WordPress core, WooCommerce core, GeneratePress core, or third-party plugin/theme code.
- Never commit secrets, database dumps, uploads, backups, customer files, payment data, or logs.

## WordPress Rules

- Use WordPress hooks and filters where practical.
- Escape all output.
- Sanitize all user input.
- Use nonces for state-changing requests.
- Use prepared SQL statements for custom queries.
- Keep strings translatable.
- Do not add plugin or frontend dependencies without approval.

## WooCommerce Rules

- Prefer hooks before template overrides.
- Use template overrides only when needed for the KECHOO theme.
- Preserve HPOS compatibility.
- Do not alter checkout, payment, or order behavior without explicit approval.
- Public-lite mode should not expose test prices or Add to cart flows unless explicitly requested.

## Deployment Rules

- Production deployment copies only:
  - `wp-content/themes/kechoo`
  - `wp-content/plugins/kechoo-core`
- Do not copy repository docs, `data-templates`, `test-site`, Node dependencies, Git metadata, local logs, backups, or database exports to the public web root.
- Use SSH keys and a limited deploy user where possible. Avoid root for routine deployments.
- Restrict `rsync --delete` to the two custom deployment directories above.

## Workflow

1. Read relevant files before editing.
2. Describe the intended change before file edits.
3. Make the smallest reasonable patch.
4. Run syntax, build, or lint checks appropriate to the change.
5. Report modified files.
6. Report database, WooCommerce, SEO URL, cache, performance, and security impact.
7. Report rollback steps.
