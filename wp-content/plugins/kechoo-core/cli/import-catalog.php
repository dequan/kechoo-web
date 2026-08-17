<?php
/**
 * KECHOO catalog importer (WP-CLI only).
 *
 * Imports the public-lite product catalog from a JSON file, creating taxonomy
 * terms, WooCommerce products, technical meta, and category assignments.
 * Idempotent: products are matched by SKU and updated on repeat runs.
 *
 * Usage:
 *
 *   wp eval-file import-catalog.php /tmp/products-public-lite.json
 *   wp eval-file import-catalog.php /tmp/products-public-lite.json --dry-run
 *
 * Expected JSON item shape (same as test-site/hot-selling-products.json):
 *
 *   {
 *     "sku": "KB-M42-2375-19-081-10-14",
 *     "name": "M42 Bi-Metal Blade 2375 × 19 × 0.81 mm — 10/14 TPI",
 *     "application": "metal",
 *     "technology": "bi-metal",
 *     "category": "Metal",
 *     "cut_material": "steel-tube-profile",
 *     "cut_material_name": "Steel Tube & Profile",
 *     "machine": "vertical-metal-band-saws",
 *     "machine_name": "Vertical Metal Band Saws",
 *     "length": "2375 mm",
 *     "width": "19 mm",
 *     "thickness": "0.81 mm",
 *     "tooth_pitch": "10/14 variable TPI",
 *     "tooth_form": "Variable tooth, raker set",
 *     "backing_material": "Alloy spring-steel backing",
 *     "tooth_material": "M42 high-speed steel, 8% cobalt edge",
 *     "recommended_range": "Thin, medium, and thick gauge metal tube and profile",
 *     "pack_quantity": "1 welded loop",
 *     "dispatch_estimate": "5–7 business days from China",
 *     "moq": "3 loops for custom production",
 *     "custom_size": "Welded-to-length service available",
 *     "selection_rationale": "...",
 *     "price": "19.99",
 *     "stock": 32
 *   }
 *
 * @package KechooCore
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "This script must be run through WP-CLI:\n  wp eval-file import-catalog.php /path/to/catalog.json\n" );
	exit( 1 );
}

if ( empty( $args[0] ) ) {
	WP_CLI::error( 'Missing catalog JSON path. Usage: wp eval-file import-catalog.php /path/to/catalog.json' );
}

$catalog_file = $args[0];
$dry_run      = in_array( '--dry-run', $args, true ) || in_array( 'dry-run', $args, true );

if ( ! is_readable( $catalog_file ) ) {
	WP_CLI::error( "Catalog file is not readable: {$catalog_file}" );
}

if ( ! class_exists( 'WooCommerce' ) || ! class_exists( 'WC_Product_Simple' ) ) {
	WP_CLI::error( 'WooCommerce must be active before the KECHOO catalog can be imported.' );
}

$catalog = json_decode( file_get_contents( $catalog_file ), true, 512, JSON_THROW_ON_ERROR );
if ( ! is_array( $catalog ) ) {
	WP_CLI::error( 'Catalog JSON must be an array of products.' );
}

if ( class_exists( 'Kechoo_Taxonomies' ) ) {
	Kechoo_Taxonomies::register();
	Kechoo_Taxonomies::seed_terms();
}

/**
 * Return or create a taxonomy term ID.
 */
function kechoo_import_term_id( $taxonomy, $slug, $name ) {
	$slug = sanitize_title( $slug );
	$existing = term_exists( $slug, $taxonomy );
	if ( $existing ) {
		return (int) ( is_array( $existing ) ? $existing['term_id'] : $existing );
	}

	$created = wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) );
	if ( is_wp_error( $created ) ) {
		WP_CLI::warning( "Could not create term {$taxonomy}:{$slug} — {$created->get_error_message()}" );
		return 0;
	}

	return (int) $created['term_id'];
}

$tech_labels = array(
	'hardened' => 'Hardened',
	'bi-metal' => 'Bi-Metal',
	'carbide'  => 'Carbide',
);

$created = 0;
$updated = 0;
$errors  = 0;

foreach ( $catalog as $item ) {
	$sku = isset( $item['sku'] ) ? sanitize_text_field( $item['sku'] ) : '';
	if ( '' === $sku ) {
		WP_CLI::warning( 'Skipping item without a SKU.' );
		$errors++;
		continue;
	}

	$technology = isset( $item['technology'] ) ? sanitize_title( $item['technology'] ) : '';
	$tech_label = isset( $tech_labels[ $technology ] ) ? $tech_labels[ $technology ] : ucwords( str_replace( '-', ' ', $technology ) );

	if ( $dry_run ) {
		WP_CLI::log( "DRY RUN — would import: {$sku}" );
		continue;
	}

	$existing_id = wc_get_product_id_by_sku( $sku );
	$is_new      = ! $existing_id;
	$product     = $existing_id ? wc_get_product( $existing_id ) : new WC_Product_Simple();

	$product->set_name( $item['name'] );
	$product->set_slug( sanitize_title( $item['name'] ) );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'visible' );
	$product->set_sku( $sku );

	$product->set_description(
		'<p><strong>' . esc_html( $tech_label ) . ' bandsaw blade</strong> for ' . esc_html( $item['category'] ) . ' cutting.</p>' .
		'<p><strong>Specification:</strong> ' . esc_html( $item['length'] ) . ' × ' . esc_html( $item['width'] ) . ' × ' . esc_html( $item['thickness'] ) . ' — ' . esc_html( $item['tooth_pitch'] ) . '.</p>' .
		'<p>' . esc_html( $item['selection_rationale'] ) . '</p>'
	);
	$product->set_short_description( $item['selection_rationale'] );

	if ( isset( $item['price'] ) && '' !== $item['price'] ) {
		$product->set_regular_price( $item['price'] );
	}

	$stock = isset( $item['stock'] ) ? (int) $item['stock'] : 0;
	$product->set_manage_stock( true );
	$product->set_stock_quantity( $stock );
	$product->set_stock_status( $stock > 0 ? 'instock' : 'outofstock' );
	$product->set_weight( '0.8' );

	$product_id = $product->save();
	if ( ! $product_id ) {
		WP_CLI::warning( "Could not save product {$sku}." );
		$errors++;
		continue;
	}

	if ( $is_new ) {
		$created++;
	} else {
		$updated++;
	}

	$term_map = array(
		'product_cat'            => array( sanitize_title( $item['category'] ), $item['category'] ),
		'kechoo_application'     => array( $item['application'], $item['category'] ),
		'kechoo_blade_technology' => array( $technology, $tech_label ),
		'kechoo_cut_material'    => array( $item['cut_material'], $item['cut_material_name'] ),
		'kechoo_machine'         => array( $item['machine'], $item['machine_name'] ),
	);

	foreach ( $term_map as $taxonomy => $term ) {
		$term_id = kechoo_import_term_id( $taxonomy, $term[0], $term[1] );
		if ( $term_id ) {
			wp_set_object_terms( $product_id, array( $term_id ), $taxonomy );
		}
	}

	$meta = array(
		'blade_length'        => $item['length'],
		'blade_width'         => $item['width'],
		'blade_thickness'     => $item['thickness'],
		'tooth_pitch'         => $item['tooth_pitch'],
		'tooth_form'          => $item['tooth_form'],
		'backing_material'    => $item['backing_material'],
		'tooth_material'      => $item['tooth_material'],
		'recommended_range'   => $item['recommended_range'],
		'pack_quantity'       => $item['pack_quantity'],
		'dispatch_estimate'   => $item['dispatch_estimate'],
		'moq'                 => $item['moq'],
		'custom_size'         => $item['custom_size'],
		'selection_rationale' => $item['selection_rationale'],
	);

	foreach ( $meta as $key => $value ) {
		if ( isset( $value ) && '' !== $value ) {
			update_post_meta( $product_id, '_kechoo_' . $key, sanitize_text_field( $value ) );
		}
	}
}

if ( $dry_run ) {
	WP_CLI::success( sprintf( 'Dry run complete. %d items would be imported.', count( $catalog ) ) );
} else {
	WP_CLI::success( sprintf( 'Import complete: %d created, %d updated, %d errors.', $created, $updated, $errors ) );
}
