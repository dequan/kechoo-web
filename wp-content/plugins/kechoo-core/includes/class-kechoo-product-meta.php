<?php
/**
 * Product technical fields.
 *
 * @package KechooCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Kechoo_Product_Meta {
	private static $fields = array(
		'blade_length'       => 'Blade length',
		'blade_width'        => 'Blade width',
		'blade_thickness'    => 'Blade thickness',
		'tooth_pitch'        => 'TPI / tooth pitch',
		'tooth_form'         => 'Tooth form / set',
		'backing_material'   => 'Backing material',
		'tooth_material'     => 'Tooth material',
		'recommended_range'  => 'Recommended cutting range',
		'pack_quantity'      => 'Pack quantity',
		'dispatch_estimate'  => 'Dispatch estimate',
		'moq'                => 'MOQ for custom orders',
		'custom_size'        => 'Custom-size availability',
		'selection_rationale'=> 'Selection rationale',
	);

	public static function init() {
		add_action( 'add_meta_boxes_product', array( __CLASS__, 'add_meta_box' ) );
		add_action( 'save_post_product', array( __CLASS__, 'save_meta' ) );
		add_action( 'wp', array( __CLASS__, 'apply_public_lite_mode' ) );
		add_action( 'woocommerce_after_shop_loop_item_title', array( __CLASS__, 'render_loop_summary' ), 11 );
		add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'render_buying_note' ), 35 );
		add_action( 'woocommerce_after_single_product_summary', array( __CLASS__, 'render_specifications' ), 6 );
	}

	public static function apply_public_lite_mode() {
		if ( ! function_exists( 'kechoo_is_public_lite_mode' ) || ! kechoo_is_public_lite_mode() ) {
			return;
		}

		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		add_filter( 'woocommerce_get_price_html', array( __CLASS__, 'public_lite_price_html' ), 20, 2 );
		add_filter( 'woocommerce_is_purchasable', array( __CLASS__, 'public_lite_is_purchasable' ) );
		add_action( 'woocommerce_after_shop_loop_item', array( __CLASS__, 'render_loop_quote_button' ), 10 );
		add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'render_public_lite_quote_panel' ), 30 );
	}

	public static function public_lite_price_html( $price, $product ) {
		if ( ! function_exists( 'kechoo_is_public_lite_mode' ) || ! kechoo_is_public_lite_mode() ) {
			return $price;
		}

		return '<span class="kechoo-quote-price">' . esc_html__( 'Quote on request', 'kechoo-core' ) . '</span>';
	}

	public static function public_lite_is_purchasable( $is_purchasable ) {
		if ( ! function_exists( 'kechoo_is_public_lite_mode' ) || ! kechoo_is_public_lite_mode() ) {
			return $is_purchasable;
		}

		return false;
	}

	public static function add_meta_box() {
		add_meta_box(
			'kechoo-product-specifications',
			__( 'KECHOO technical specifications', 'kechoo-core' ),
			array( __CLASS__, 'render_meta_box' ),
			'product',
			'normal',
			'high'
		);
	}

	public static function render_meta_box( $post ) {
		wp_nonce_field( 'kechoo_save_product_meta', 'kechoo_product_meta_nonce' );
		echo '<div class="kechoo-admin-specs">';

		foreach ( self::$fields as $key => $label ) {
			$value = get_post_meta( $post->ID, '_kechoo_' . $key, true );
			printf(
				'<p><label for="kechoo_%1$s"><strong>%2$s</strong></label><br><input type="text" class="widefat" id="kechoo_%1$s" name="kechoo_%1$s" value="%3$s" maxlength="300"></p>',
				esc_attr( $key ),
				esc_html( $label ),
				esc_attr( $value )
			);
		}

		echo '</div>';
	}

	public static function save_meta( $post_id ) {
		if ( ! isset( $_POST['kechoo_product_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kechoo_product_meta_nonce'] ) ), 'kechoo_save_product_meta' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		foreach ( self::$fields as $key => $label ) {
			$field_name = 'kechoo_' . $key;
			if ( ! isset( $_POST[ $field_name ] ) ) {
				continue;
			}

			$value = sanitize_text_field( wp_unslash( $_POST[ $field_name ] ) );
			if ( '' === $value ) {
				delete_post_meta( $post_id, '_kechoo_' . $key );
			} else {
				update_post_meta( $post_id, '_kechoo_' . $key, $value );
			}
		}
	}

	public static function render_buying_note() {
		global $product;
		if ( ! $product ) {
			return;
		}

		$dispatch   = get_post_meta( $product->get_id(), '_kechoo_dispatch_estimate', true );
		$custom     = get_post_meta( $product->get_id(), '_kechoo_custom_size', true );
		$quote_url  = add_query_arg(
			array(
				'product_id' => $product->get_id(),
				'product'    => $product->get_name(),
			),
			home_url( '/request-a-quote/' )
		);

		echo '<div class="kechoo-product-buying-note">';
		if ( $dispatch ) {
			printf( '<p><strong>%1$s</strong> %2$s</p>', esc_html__( 'Dispatch:', 'kechoo-core' ), esc_html( $dispatch ) );
		}
		if ( $custom ) {
			printf( '<p><strong>%1$s</strong> %2$s</p>', esc_html__( 'Custom sizes:', 'kechoo-core' ), esc_html( $custom ) );
		}
		if ( ! function_exists( 'kechoo_is_public_lite_mode' ) || ! kechoo_is_public_lite_mode() ) {
			printf( '<a class="button alt kechoo-product-quote" href="%1$s">%2$s</a>', esc_url( $quote_url ), esc_html__( 'Request a Custom Size', 'kechoo-core' ) );
		}
		echo '</div>';
	}

	public static function render_loop_quote_button() {
		global $product;
		if ( ! $product ) {
			return;
		}

		printf(
			'<a class="button kechoo-loop-quote-button" href="%1$s">%2$s</a>',
			esc_url( self::quote_url( $product ) ),
			esc_html__( 'Request quote', 'kechoo-core' )
		);
	}

	public static function render_public_lite_quote_panel() {
		global $product;
		if ( ! $product ) {
			return;
		}

		echo '<div class="kechoo-public-lite-panel">';
		echo '<h2>' . esc_html__( 'Need this blade?', 'kechoo-core' ) . '</h2>';
		echo '<p>' . esc_html__( 'Send the specification, quantity, and destination. KECHOO will confirm price, lead time, shipping, and the best blade choice before you order.', 'kechoo-core' ) . '</p>';
		printf(
			'<a class="button alt kechoo-product-quote" href="%1$s">%2$s</a>',
			esc_url( self::quote_url( $product ) ),
			esc_html__( 'Request price and availability', 'kechoo-core' )
		);
		echo '</div>';
	}

	public static function render_loop_summary() {
		global $product;
		if ( ! $product ) {
			return;
		}

		$product_id   = $product->get_id();
		$application  = self::first_term_name( $product_id, 'kechoo_application' );
		$technology   = self::first_term_name( $product_id, 'kechoo_blade_technology' );
		$length       = get_post_meta( $product_id, '_kechoo_blade_length', true );
		$width        = get_post_meta( $product_id, '_kechoo_blade_width', true );
		$thickness    = get_post_meta( $product_id, '_kechoo_blade_thickness', true );
		$tooth_pitch  = get_post_meta( $product_id, '_kechoo_tooth_pitch', true );
		$dispatch     = get_post_meta( $product_id, '_kechoo_dispatch_estimate', true );
		$profile_bits = array_filter( array( $application, $technology ) );
		$size_bits    = array_filter( array( $length, $width, $thickness ) );

		if ( empty( $profile_bits ) && empty( $size_bits ) && ! $tooth_pitch && ! $dispatch ) {
			return;
		}

		echo '<div class="kechoo-loop-specs" aria-label="' . esc_attr__( 'Blade summary', 'kechoo-core' ) . '">';
		if ( ! empty( $profile_bits ) ) {
			echo '<p class="kechoo-loop-specs__profile">' . esc_html( implode( ' · ', $profile_bits ) ) . '</p>';
		}
		if ( ! empty( $size_bits ) || $tooth_pitch ) {
			echo '<p><strong>' . esc_html__( 'Spec:', 'kechoo-core' ) . '</strong> ' . esc_html( implode( ' × ', $size_bits ) );
			if ( $tooth_pitch ) {
				echo esc_html( ( empty( $size_bits ) ? '' : ' · ' ) . $tooth_pitch );
			}
			echo '</p>';
		}
		if ( $dispatch ) {
			echo '<p><strong>' . esc_html__( 'Dispatch:', 'kechoo-core' ) . '</strong> ' . esc_html( $dispatch ) . '</p>';
		}
		echo '</div>';
	}

	public static function render_specifications() {
		global $product;
		if ( ! $product ) {
			return;
		}

		$rows = array();
		foreach ( self::$fields as $key => $label ) {
			$value = get_post_meta( $product->get_id(), '_kechoo_' . $key, true );
			if ( '' !== $value ) {
				$rows[ $label ] = $value;
			}
		}

		if ( empty( $rows ) ) {
			return;
		}

		echo '<section class="kechoo-product-specifications" aria-labelledby="kechoo-specifications-title">';
		echo '<h2 id="kechoo-specifications-title">' . esc_html__( 'Technical specifications', 'kechoo-core' ) . '</h2>';
		echo '<div class="kechoo-specification-table" role="table">';
		foreach ( $rows as $label => $value ) {
			echo '<div class="kechoo-specification-table__row" role="row">';
			echo '<div role="rowheader">' . esc_html( $label ) . '</div>';
			echo '<div role="cell">' . esc_html( $value ) . '</div>';
			echo '</div>';
		}
		echo '</div></section>';
	}

	private static function first_term_name( $post_id, $taxonomy ) {
		$terms = get_the_terms( $post_id, $taxonomy );
		return $terms && ! is_wp_error( $terms ) ? $terms[0]->name : '';
	}

	private static function quote_url( $product ) {
		return add_query_arg(
			array(
				'product_id' => $product->get_id(),
				'product'    => $product->get_name(),
			),
			home_url( '/request-a-quote/' )
		);
	}
}
