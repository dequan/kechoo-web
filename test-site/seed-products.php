<?php
/**
 * Seed the local Playground site with an explicitly non-production catalog.
 *
 * This file is mounted outside wp-content and only runs from test-site/blueprint.json.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WooCommerce' ) || ! class_exists( 'WC_Product_Simple' ) ) {
	throw new RuntimeException( 'WooCommerce must be active before the KECHOO test catalog can be seeded.' );
}

if ( class_exists( 'Kechoo_Taxonomies' ) ) {
	Kechoo_Taxonomies::register();
	Kechoo_Taxonomies::seed_terms();
}

update_option( 'blogname', 'KECHOO — Choose Better Cutting' );
update_option( 'blogdescription', 'Industrial bandsaw blade test site' );
update_option( 'timezone_string', 'Asia/Shanghai' );
update_option( 'permalink_structure', '/%postname%/' );
update_option( 'woocommerce_currency', 'USD' );
update_option( 'woocommerce_default_country', 'CN' );
update_option( 'woocommerce_store_address', 'China — local test configuration' );
update_option( 'woocommerce_store_city', 'Test city' );
update_option( 'woocommerce_store_postcode', '000000' );
update_option( 'woocommerce_enable_guest_checkout', 'yes' );
update_option( 'woocommerce_enable_signup_and_login_from_checkout', 'yes' );
update_option( 'woocommerce_calc_taxes', 'no' );
update_option( 'woocommerce_coming_soon', 'no' );
update_option( 'woocommerce_store_pages_only', 'no' );
update_option( 'woocommerce_onboarding_profile', array( 'completed' => true, 'skipped' => true ) );
update_option( 'kechoo_test_catalog_notice', 'Catalog specifications are based on public competitor reference data; verify SKU dimensions and pricing before production launch.' );

$test_admin = get_user_by( 'login', 'kechoo-admin' );
if ( $test_admin ) {
	wp_set_password( 'kechoo-test', $test_admin->ID );
	$test_admin_id = $test_admin->ID;
} else {
	$test_admin_id = wp_insert_user(
		array(
			'user_login'   => 'kechoo-admin',
			'user_pass'    => 'kechoo-test',
			'user_email'   => 'kechoo-admin@example.test',
			'display_name' => 'KECHOO Test Admin',
			'role'         => 'administrator',
		)
	);
	if ( is_wp_error( $test_admin_id ) ) {
		throw new RuntimeException( 'Could not create the local KECHOO test administrator: ' . $test_admin_id->get_error_message() );
	}
}

update_user_meta( $test_admin_id, 'locale', 'zh_CN' );

try {
	require_once ABSPATH . 'wp-admin/includes/translation-install.php';
	$language_installed = wp_download_language_pack( 'zh_CN' );
	if ( $language_installed ) {
		require_once ABSPATH . 'wp-admin/includes/update.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/class-language-pack-upgrader.php';
		add_filter(
			'plugins_update_check_locales',
			static function ( $locales ) {
				$locales[] = 'zh_CN';
				return array_unique( $locales );
			}
		);
		delete_site_transient( 'update_plugins' );
		wp_update_plugins();
		$translation_updates = array_filter(
			wp_get_translation_updates(),
			static function ( $update ) {
				return isset( $update->language ) && 'zh_CN' === $update->language;
			}
		);
		if ( $translation_updates ) {
			$language_upgrader = new Language_Pack_Upgrader( new Automatic_Upgrader_Skin() );
			$language_upgrader->bulk_upgrade( array_values( $translation_updates ) );
		}
		update_option( 'kechoo_test_chinese_admin', 'installed', false );
	}
} catch ( Throwable $error ) {
	update_option( 'kechoo_test_chinese_admin', 'failed: ' . $error->getMessage(), false );
}

$privacy_page = get_page_by_path( 'privacy-policy', OBJECT, 'page' );
if ( $privacy_page ) {
	if ( 'publish' !== $privacy_page->post_status ) {
		wp_update_post( array( 'ID' => $privacy_page->ID, 'post_status' => 'publish' ) );
	}
	update_option( 'wp_page_for_privacy_policy', $privacy_page->ID );
}

$cart_page_id = wc_get_page_id( 'cart' );
if ( $cart_page_id > 0 ) {
	wp_update_post( array( 'ID' => $cart_page_id, 'post_content' => '[woocommerce_cart]' ) );
}
$checkout_page_id = wc_get_page_id( 'checkout' );
if ( $checkout_page_id > 0 ) {
	wp_update_post( array( 'ID' => $checkout_page_id, 'post_content' => '[woocommerce_checkout]' ) );
}

update_option(
	'woocommerce_cod_settings',
	array(
		'enabled'            => 'yes',
		'title'              => 'Test checkout — no payment collected',
		'description'        => 'Local Playground checkout only. No payment will be collected.',
		'instructions'       => 'This order exists only in the local KECHOO test site.',
		'enable_for_methods' => array(),
		'enable_for_virtual' => 'yes',
	)
);

/**
 * Return or create a taxonomy term ID.
 */
function kechoo_test_term_id( $taxonomy, $slug, $name ) {
	$existing = term_exists( $slug, $taxonomy );
	if ( $existing ) {
		return (int) ( is_array( $existing ) ? $existing['term_id'] : $existing );
	}

	$created = wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) );
	if ( is_wp_error( $created ) ) {
		throw new RuntimeException( 'Could not create term ' . $taxonomy . ':' . $slug . ' — ' . $created->get_error_message() );
	}

	return (int) $created['term_id'];
}

/**
 * Import one temporary visual into the media library for catalog layout testing.
 */
function kechoo_test_catalog_image() {
	$existing = (int) get_option( 'kechoo_test_catalog_image_id', 0 );
	if ( $existing && get_post( $existing ) ) {
		return $existing;
	}

	$source = get_theme_file_path( 'assets/images/hero-bandsaw-blade.png' );
	if ( ! is_readable( $source ) ) {
		return 0;
	}

	$contents = file_get_contents( $source );
	$upload   = wp_upload_bits( 'kechoo-test-bandsaw-blade.png', null, $contents );
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	$filetype      = wp_check_filetype( $upload['file'] );
	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => $filetype['type'],
			'post_title'     => 'KECHOO temporary blade visual',
			'post_content'   => 'Generated test-site visual. Replace with verified photography for every production SKU.',
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	if ( is_wp_error( $attachment_id ) ) {
		return 0;
	}

	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', 'Temporary KECHOO bandsaw blade visual for local catalog testing' );
	update_option( 'kechoo_test_catalog_image_id', $attachment_id, false );

	return (int) $attachment_id;
}

/**
 * Create a test flat-rate shipping zone without duplicating it on repeated seeds.
 */
function kechoo_test_shipping_zone( $name, array $countries, $cost ) {
	$existing_zones = WC_Shipping_Zones::get_zones();
	foreach ( $existing_zones as $zone_data ) {
		if ( $name === $zone_data['zone_name'] ) {
			return;
		}
	}

	$zone = new WC_Shipping_Zone();
	$zone->set_zone_name( $name );
	$zone->set_zone_order( count( $existing_zones ) + 1 );
	foreach ( $countries as $country ) {
		$zone->add_location( $country, 'country' );
	}
	$zone->save();

	$instance_id = $zone->add_shipping_method( 'flat_rate' );
	update_option(
		'woocommerce_flat_rate_' . $instance_id . '_settings',
		array(
			'title'      => 'Test flat rate from China',
			'tax_status' => 'none',
			'cost'       => (string) $cost,
		)
	);
}

kechoo_test_shipping_zone( 'TEST — United States & Canada', array( 'US', 'CA' ), '35' );
kechoo_test_shipping_zone( 'TEST — Europe', array( 'GB', 'DE', 'FR', 'IT', 'ES', 'NL', 'BE', 'PL', 'SE', 'DK', 'FI', 'NO', 'AT', 'IE', 'PT', 'CZ' ), '39' );
kechoo_test_shipping_zone( 'TEST — Southeast Asia', array( 'SG', 'MY', 'TH', 'VN', 'ID', 'PH' ), '25' );

$catalog_file = '/wordpress/kechoo-test-site/hot-selling-products.json';
$catalog      = json_decode( file_get_contents( $catalog_file ), true, 512, JSON_THROW_ON_ERROR );
$image_id     = kechoo_test_catalog_image();
$sales_rank   = 90;

$tech_labels = array(
	'hardened' => 'Hardened',
	'bi-metal' => 'Bi-Metal',
	'carbide'  => 'Carbide-Tipped',
);

foreach ( $catalog as $item ) {
	$product_id = wc_get_product_id_by_sku( $item['sku'] );
	$product    = $product_id ? wc_get_product( $product_id ) : new WC_Product_Simple();
	$tech_label = isset( $tech_labels[ $item['technology'] ] ) ? $tech_labels[ $item['technology'] ] : ucwords( str_replace( '-', ' ', $item['technology'] ) );

	$product->set_name( $item['name'] );
	$product->set_slug( sanitize_title( $item['name'] ) );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'visible' );
	$product->set_description(
		'<p><strong>' . esc_html( $tech_label ) . ' bandsaw blade</strong> for ' . esc_html( $item['category'] ) . ' cutting.</p>' .
		'<p><strong>Specification:</strong> ' . esc_html( $item['length'] ) . ' × ' . esc_html( $item['width'] ) . ' × ' . esc_html( $item['thickness'] ) . ' — ' . esc_html( $item['tooth_pitch'] ) . '.</p>' .
		'<p>' . esc_html( $item['selection_rationale'] ) . '</p>'
	);
	$product->set_short_description( $item['selection_rationale'] );
	$product->set_sku( $item['sku'] );
	$product->set_regular_price( $item['price'] );
	$product->set_manage_stock( true );
	$product->set_stock_quantity( (int) $item['stock'] );
	$product->set_stock_status( $item['stock'] > 0 ? 'instock' : 'outofstock' );
	$product->set_weight( '0.8' );
	$product->set_virtual( false );
	if ( $image_id ) {
		$product->set_image_id( $image_id );
	}
	$product_id = $product->save();

	$category_id = kechoo_test_term_id( 'product_cat', sanitize_title( $item['category'] ), $item['category'] );
	wp_set_object_terms( $product_id, array( $category_id ), 'product_cat' );
	wp_set_object_terms( $product_id, $item['application'], 'kechoo_application' );
	wp_set_object_terms( $product_id, $item['technology'], 'kechoo_blade_technology' );
	kechoo_test_term_id( 'kechoo_cut_material', $item['cut_material'], $item['cut_material_name'] );
	wp_set_object_terms( $product_id, $item['cut_material'], 'kechoo_cut_material' );
	kechoo_test_term_id( 'kechoo_machine', $item['machine'], $item['machine_name'] );
	wp_set_object_terms( $product_id, $item['machine'], 'kechoo_machine' );

	$meta = array(
		'blade_length'       => $item['length'],
		'blade_width'        => $item['width'],
		'blade_thickness'    => $item['thickness'],
		'tooth_pitch'        => $item['tooth_pitch'],
		'tooth_form'         => $item['tooth_form'],
		'backing_material'   => $item['backing_material'],
		'tooth_material'     => $item['tooth_material'],
		'recommended_range'  => $item['recommended_range'],
		'pack_quantity'      => $item['pack_quantity'],
		'dispatch_estimate'  => $item['dispatch_estimate'],
		'moq'                => $item['moq'],
		'custom_size'        => $item['custom_size'],
		'selection_rationale'=> $item['selection_rationale'],
	);
	foreach ( $meta as $key => $value ) {
		update_post_meta( $product_id, '_kechoo_' . $key, sanitize_text_field( $value ) );
	}
	update_post_meta( $product_id, '_kechoo_catalog_source', 'foxbc-reference' );
	update_post_meta( $product_id, '_kechoo_image_status', 'Placeholder visual — replace with verified SKU photography.' );
	update_post_meta( $product_id, 'total_sales', $sales_rank );
	$sales_rank -= 10;
}

update_option( 'kechoo_test_catalog_seeded_at', gmdate( 'c' ), false );
flush_rewrite_rules();
