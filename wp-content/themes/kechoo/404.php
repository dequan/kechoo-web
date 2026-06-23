<?php
/**
 * Not found template.
 *
 * @package Kechoo
 */

get_header();
?>
<main id="main-content" class="kechoo-error-page">
	<p class="kechoo-kicker kechoo-kicker--dark">404</p>
	<h1><?php esc_html_e( 'This page is not in the cutting plan.', 'kechoo' ); ?></h1>
	<p><?php esc_html_e( 'The link may have changed. Start with your application or search the product catalog.', 'kechoo' ); ?></p>
	<div class="kechoo-actions"><a class="kechoo-button" href="<?php echo esc_url( home_url( '/find-your-blade/' ) ); ?>"><?php esc_html_e( 'Find Your Blade', 'kechoo' ); ?></a><a class="kechoo-button kechoo-button--outline" href="<?php echo esc_url( kechoo_shop_url() ); ?>"><?php esc_html_e( 'Browse Products', 'kechoo' ); ?></a></div>
</main>
<?php get_footer(); ?>

