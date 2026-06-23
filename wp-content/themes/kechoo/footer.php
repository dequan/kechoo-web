<?php
/**
 * Site footer.
 *
 * @package Kechoo
 */
?>
<footer class="kechoo-footer">
	<div class="kechoo-footer__grid">
		<div class="kechoo-footer__brand">
			<a class="kechoo-brand kechoo-brand--footer" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<span class="kechoo-brand__name">KECHOO</span>
				<span class="kechoo-brand__cut" aria-hidden="true"></span>
			</a>
			<p><?php esc_html_e( 'Choose Better Cutting.', 'kechoo' ); ?></p>
			<p class="kechoo-footer__note"><?php esc_html_e( 'Bandsaw blades for food processing, woodworking, and metal cutting.', 'kechoo' ); ?></p>
		</div>
		<div>
			<h2><?php esc_html_e( 'Explore', 'kechoo' ); ?></h2>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'fallback_cb'    => 'kechoo_menu_fallback',
				)
			);
			?>
		</div>
		<div>
			<h2><?php esc_html_e( 'Buying support', 'kechoo' ); ?></h2>
			<ul>
				<li><a href="<?php echo esc_url( home_url( '/find-your-blade/' ) ); ?>"><?php esc_html_e( 'Find Your Blade', 'kechoo' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/request-a-quote/' ) ); ?>"><?php esc_html_e( 'Request a Quote', 'kechoo' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/shipping/' ) ); ?>"><?php esc_html_e( 'Shipping from China', 'kechoo' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact technical support', 'kechoo' ); ?></a></li>
			</ul>
		</div>
		<div>
			<h2><?php esc_html_e( 'For distributors', 'kechoo' ); ?></h2>
			<p><?php esc_html_e( 'Talk with us about volume supply, OEM packaging, and regional cooperation.', 'kechoo' ); ?></p>
			<a class="kechoo-text-link" href="<?php echo esc_url( home_url( '/distributors/' ) ); ?>"><?php esc_html_e( 'Discuss distribution', 'kechoo' ); ?> <span aria-hidden="true">→</span></a>
		</div>
	</div>
	<div class="kechoo-footer__legal">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> KECHOO. <?php esc_html_e( 'All rights reserved.', 'kechoo' ); ?></p>
		<div>
			<a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy', 'kechoo' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>"><?php esc_html_e( 'Terms', 'kechoo' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/returns-refunds/' ) ); ?>"><?php esc_html_e( 'Returns', 'kechoo' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/customs-duties/' ) ); ?>"><?php esc_html_e( 'Customs', 'kechoo' ); ?></a>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
