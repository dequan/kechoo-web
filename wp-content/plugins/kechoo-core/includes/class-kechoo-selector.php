<?php
/**
 * Blade selector.
 *
 * @package KechooCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Kechoo_Selector {
	private static $taxonomies = array(
		'kechoo_application'      => 'Application',
		'kechoo_cut_material'     => 'Material',
		'kechoo_machine'          => 'Machine',
		'kechoo_blade_technology' => 'Blade technology',
	);

	public static function init() {
		add_shortcode( 'kechoo_blade_selector', array( __CLASS__, 'render' ) );
		add_action( 'woocommerce_before_shop_loop', array( __CLASS__, 'render_shop_filter_panel' ), 4 );
		add_action( 'pre_get_posts', array( __CLASS__, 'filter_product_query' ) );
		add_filter( 'woocommerce_product_query_tax_query', array( __CLASS__, 'filter_woocommerce_query' ), 10, 2 );
	}

	private static function shop_url() {
		return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/products/' );
	}

	private static function has_any_terms() {
		foreach ( array_keys( self::$taxonomies ) as $taxonomy ) {
			$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
			if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
				return true;
			}
		}

		return false;
	}

	public static function render( $atts ) {
		$atts = shortcode_atts(
			array(
				'compact' => 'false',
			),
			$atts,
			'kechoo_blade_selector'
		);

		$compact = 'true' === $atts['compact'];

		if ( ! self::has_any_terms() ) {
			return '<div class="kechoo-selector-empty"><p>' . esc_html__( 'Blade selection is being prepared. Send your application, material, and machine details for a technical quote.', 'kechoo-core' ) . '</p><a class="kechoo-button" href="' . esc_url( home_url( '/request-a-quote/' ) ) . '">' . esc_html__( 'Request a Blade Quote', 'kechoo-core' ) . '</a></div>';
		}

		ob_start();
		?>
		<form class="kechoo-selector<?php echo $compact ? ' kechoo-selector--compact' : ''; ?>" action="<?php echo esc_url( self::shop_url() ); ?>" method="get" data-kechoo-selector>
			<div class="kechoo-selector__fields">
				<?php
				$field_index = 0;
				foreach ( self::$taxonomies as $taxonomy => $label ) :
					if ( $compact && 'kechoo_blade_technology' === $taxonomy ) {
						continue;
					}

					$terms = get_terms(
						array(
							'taxonomy'   => $taxonomy,
							'hide_empty' => false,
						)
					);
					$current = isset( $_GET[ $taxonomy ] ) ? sanitize_title( wp_unslash( $_GET[ $taxonomy ] ) ) : '';
					?>
					<div class="kechoo-selector__field<?php echo ! $compact && 'kechoo_blade_technology' === $taxonomy ? ' kechoo-selector__field--wide' : ''; ?>">
						<label for="kechoo-selector-<?php echo esc_attr( $taxonomy ); ?>">
							<?php echo esc_html( ( $field_index + 1 ) . '. ' . $label ); ?>
						</label>
						<select id="kechoo-selector-<?php echo esc_attr( $taxonomy ); ?>" name="<?php echo esc_attr( $taxonomy ); ?>">
							<option value=""><?php echo esc_html( sprintf( __( 'Select %s', 'kechoo-core' ), strtolower( $label ) ) ); ?></option>
							<?php if ( ! is_wp_error( $terms ) ) : ?>
								<?php foreach ( $terms as $term ) : ?>
									<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $current, $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>
					<?php ++$field_index; ?>
				<?php endforeach; ?>
			</div>
			<div class="kechoo-selector__actions">
				<button class="kechoo-button" type="submit"><?php esc_html_e( 'Find Matching Blades', 'kechoo-core' ); ?></button>
				<?php if ( ! $compact ) : ?>
					<a class="kechoo-text-link" href="<?php echo esc_url( home_url( '/request-a-quote/' ) ); ?>"><?php esc_html_e( 'I do not know my specification', 'kechoo-core' ); ?> <span aria-hidden="true">→</span></a>
				<?php endif; ?>
			</div>
			<p class="kechoo-selector__help"><?php esc_html_e( 'Results show verified catalog matches. Confirm blade dimensions and machine compatibility before ordering.', 'kechoo-core' ); ?></p>
		</form>
		<?php
		return ob_get_clean();
	}

	public static function render_shop_filter_panel() {
		if ( ! ( is_shop() || is_product_taxonomy() ) ) {
			return;
		}

		echo '<section class="kechoo-shop-filter-panel" aria-labelledby="kechoo-shop-filter-title">';
		echo '<div class="kechoo-shop-filter-panel__copy">';
		echo '<h2 id="kechoo-shop-filter-title">' . esc_html__( 'Narrow the blade list', 'kechoo-core' ) . '</h2>';
		echo '<p>' . esc_html__( 'Filter by application, material, machine, or blade technology. If a stock size is not listed, send the same details for a custom quote.', 'kechoo-core' ) . '</p>';
		echo '</div>';
		echo self::render( array( 'compact' => 'false' ) );
		echo '</section>';
	}

	private static function requested_tax_filters() {
		$filters = array();
		foreach ( self::$taxonomies as $taxonomy => $label ) {
			if ( empty( $_GET[ $taxonomy ] ) ) {
				continue;
			}

			$filters[] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => sanitize_title( wp_unslash( $_GET[ $taxonomy ] ) ),
			);
		}
		return $filters;
	}

	public static function filter_product_query( $query ) {
		if ( is_admin() || ! $query->is_main_query() || ! ( is_post_type_archive( 'product' ) || is_tax( array_keys( self::$taxonomies ) ) ) ) {
			return;
		}

		$filters = self::requested_tax_filters();
		if ( empty( $filters ) ) {
			return;
		}

		$tax_query = (array) $query->get( 'tax_query' );
		$query->set( 'tax_query', array_merge( $tax_query, $filters ) );
	}

	public static function filter_woocommerce_query( $tax_query, $query ) {
		$filters = self::requested_tax_filters();
		if ( empty( $filters ) ) {
			return $tax_query;
		}

		return array_merge( $tax_query, $filters );
	}
}
