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
				<li><a href="<?php echo esc_url( kechoo_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php printf( esc_html__( 'WhatsApp %s', 'kechoo' ), esc_html( kechoo_whatsapp_manager() ) ); ?></a></li>
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
<a class="kechoo-whatsapp-fab" href="<?php echo esc_url( kechoo_whatsapp_url() ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( sprintf( __( 'Chat with manager %s on WhatsApp', 'kechoo' ), kechoo_whatsapp_manager() ) ); ?>">
	<svg aria-hidden="true" viewBox="0 0 24 24" width="26" height="26"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
	<span class="kechoo-whatsapp-fab__label"><?php esc_html_e( 'WhatsApp', 'kechoo' ); ?></span>
</a>
<?php wp_footer(); ?>
</body>
</html>
