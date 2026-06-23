<?php
/**
 * KECHOO theme setup.
 *
 * @package Kechoo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KECHOO_THEME_VERSION', '1.2.0' );

function kechoo_theme_setup() {
	load_theme_textdomain( 'kechoo', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
	add_editor_style( 'assets/css/main.css' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary navigation', 'kechoo' ),
			'footer'  => __( 'Footer navigation', 'kechoo' ),
		)
	);

	add_image_size( 'kechoo-application', 1200, 800, true );
	add_image_size( 'kechoo-product', 900, 900, true );
}
add_action( 'after_setup_theme', 'kechoo_theme_setup' );

function kechoo_enqueue_assets() {
	wp_enqueue_style(
		'kechoo-fonts',
		'https://fonts.googleapis.com/css2?family=Barlow:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900&display=swap',
		array(),
		null
	);
	wp_enqueue_style(
		'kechoo-main',
		get_theme_file_uri( 'assets/css/main.css' ),
		array(),
		KECHOO_THEME_VERSION
	);
	wp_enqueue_script(
		'kechoo-main',
		get_theme_file_uri( 'assets/js/main.js' ),
		array(),
		KECHOO_THEME_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'kechoo_enqueue_assets' );

function kechoo_site_icon_links() {
	if ( has_site_icon() ) {
		return;
	}
	?>
	<link rel="icon" href="<?php echo esc_url( get_theme_file_uri( 'assets/images/favicon.svg' ) ); ?>" type="image/svg+xml">
	<link rel="shortcut icon" href="<?php echo esc_url( get_theme_file_uri( 'assets/images/favicon.svg' ) ); ?>" type="image/svg+xml">
	<?php
}
add_action( 'wp_head', 'kechoo_site_icon_links' );
add_action( 'admin_head', 'kechoo_site_icon_links' );

function kechoo_menu_fallback() {
	$items = array(
		__( 'Products', 'kechoo' )        => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/products/' ),
		__( 'Applications', 'kechoo' )    => home_url( '/applications/' ),
		__( 'Find Your Blade', 'kechoo' ) => home_url( '/find-your-blade/' ),
		__( 'Technology', 'kechoo' )      => home_url( '/technology/' ),
		__( 'Resources', 'kechoo' )       => home_url( '/resources/' ),
		__( 'Distributors', 'kechoo' )    => home_url( '/distributors/' ),
		__( 'About', 'kechoo' )           => home_url( '/about/' ),
	);

	echo '<ul class="kechoo-nav__list">';
	foreach ( $items as $label => $url ) {
		printf(
			'<li><a href="%1$s">%2$s</a></li>',
			esc_url( $url ),
			esc_html( $label )
		);
	}
	echo '</ul>';
}

function kechoo_cart_count_fragment( $fragments ) {
	ob_start();
	?>
	<span class="kechoo-cart-count" aria-label="<?php esc_attr_e( 'Items in cart', 'kechoo' ); ?>">
		<?php echo esc_html( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?>
	</span>
	<?php
	$fragments['.kechoo-cart-count'] = ob_get_clean();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'kechoo_cart_count_fragment' );

function kechoo_shop_url() {
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/products/' );
}
