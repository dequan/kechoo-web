<?php
/**
 * Site header.
 *
 * @package Kechoo
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main-content"><?php esc_html_e( 'Skip to main content', 'kechoo' ); ?></a>
<header class="kechoo-header" data-site-header>
	<div class="kechoo-header__inner">
		<a class="kechoo-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'KECHOO home', 'kechoo' ); ?>">
			<span class="kechoo-brand__name">KECHOO</span>
			<span class="kechoo-brand__cut" aria-hidden="true"></span>
		</a>
		<button class="kechoo-menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation">
			<span class="kechoo-menu-toggle__label"><?php esc_html_e( 'Menu', 'kechoo' ); ?></span>
			<span aria-hidden="true"></span>
		</button>
		<nav id="primary-navigation" class="kechoo-nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'kechoo' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'kechoo-nav__list',
					'fallback_cb'    => 'kechoo_menu_fallback',
				)
			);
			?>
		</nav>
		<div class="kechoo-header__tools">
			<a class="kechoo-icon-link" href="<?php echo esc_url( home_url( '/?s=' ) ); ?>" aria-label="<?php esc_attr_e( 'Search KECHOO', 'kechoo' ); ?>">
				<svg aria-hidden="true" viewBox="0 0 24 24" width="20" height="20"><path d="m21 21-4.4-4.4m2.4-5.6a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
			</a>
			<span class="kechoo-language" aria-label="<?php esc_attr_e( 'Current language: English', 'kechoo' ); ?>">EN</span>
			<?php if ( function_exists( 'wc_get_cart_url' ) ) : ?>
				<a class="kechoo-icon-link kechoo-cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'View cart', 'kechoo' ); ?>">
					<svg aria-hidden="true" viewBox="0 0 24 24" width="21" height="21"><path d="M3 4h2l2.1 10.1a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L20 7H6M10 20h.01M17 20h.01" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
					<span class="kechoo-cart-count"><?php echo esc_html( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?></span>
				</a>
			<?php endif; ?>
			<a class="kechoo-button kechoo-button--header" href="<?php echo esc_url( home_url( '/request-a-quote/' ) ); ?>"><?php esc_html_e( 'Request a Quote', 'kechoo' ); ?></a>
		</div>
	</div>
</header>

