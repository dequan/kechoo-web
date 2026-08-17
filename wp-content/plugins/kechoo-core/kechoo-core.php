<?php
/**
 * Plugin Name: KECHOO Core
 * Description: Product taxonomy, blade selection, technical specifications, and RFQ workflow for KECHOO.
 * Version: 1.4.2
 * Requires at least: 6.5
 * Requires PHP: 8.1
 * Author: KECHOO
 * Text Domain: kechoo-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KECHOO_CORE_VERSION', '1.4.2' );
define( 'KECHOO_CORE_PATH', plugin_dir_path( __FILE__ ) );

function kechoo_is_public_lite_mode() {
	$enabled = get_option( 'kechoo_public_lite_mode', 'yes' );
	return (bool) apply_filters( 'kechoo_public_lite_mode', 'yes' === $enabled );
}

require_once KECHOO_CORE_PATH . 'includes/class-kechoo-taxonomies.php';
require_once KECHOO_CORE_PATH . 'includes/class-kechoo-product-meta.php';
require_once KECHOO_CORE_PATH . 'includes/class-kechoo-selector.php';
require_once KECHOO_CORE_PATH . 'includes/class-kechoo-rfq.php';
require_once KECHOO_CORE_PATH . 'includes/class-kechoo-site-setup.php';

function kechoo_core_boot() {
	Kechoo_Taxonomies::init();
	Kechoo_Product_Meta::init();
	Kechoo_Selector::init();
	Kechoo_RFQ::init();
	Kechoo_Site_Setup::init();
}
add_action( 'plugins_loaded', 'kechoo_core_boot' );

function kechoo_core_activate() {
	Kechoo_Taxonomies::register();
	Kechoo_RFQ::register_post_type();
	Kechoo_Site_Setup::ensure_pages();
	add_option( 'kechoo_public_lite_mode', 'yes', '', false );
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'kechoo_core_activate' );

function kechoo_core_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'kechoo_core_deactivate' );
