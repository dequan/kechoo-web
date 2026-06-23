<?php
/**
 * WooCommerce wrapper.
 *
 * @package Kechoo
 */

get_header();
?>
<main id="main-content" class="kechoo-commerce-shell">
	<?php woocommerce_content(); ?>
</main>
<?php get_footer(); ?>

