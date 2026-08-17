<?php
/**
 * Product taxonomies.
 *
 * @package KechooCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Kechoo_Taxonomies {
	const TERMS_VERSION = '1.1.0';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
		add_action( 'init', array( __CLASS__, 'seed_terms' ), 20 );
	}

	public static function register() {
		$taxonomies = array(
			'kechoo_application' => array(
				'singular' => __( 'Application', 'kechoo-core' ),
				'plural'   => __( 'Applications', 'kechoo-core' ),
				'rewrite'  => 'blade-application',
			),
			'kechoo_blade_technology' => array(
				'singular' => __( 'Blade technology', 'kechoo-core' ),
				'plural'   => __( 'Blade technologies', 'kechoo-core' ),
				'rewrite'  => 'blade-technology',
			),
			'kechoo_cut_material' => array(
				'singular' => __( 'Cut material', 'kechoo-core' ),
				'plural'   => __( 'Cut materials', 'kechoo-core' ),
				'rewrite'  => 'cut-material',
			),
			'kechoo_machine' => array(
				'singular' => __( 'Machine compatibility', 'kechoo-core' ),
				'plural'   => __( 'Machine compatibility', 'kechoo-core' ),
				'rewrite'  => 'machine-compatibility',
			),
		);

		foreach ( $taxonomies as $taxonomy => $config ) {
			register_taxonomy(
				$taxonomy,
				array( 'product' ),
				array(
					'labels' => array(
						'name'          => $config['plural'],
						'singular_name' => $config['singular'],
						'search_items'  => sprintf( __( 'Search %s', 'kechoo-core' ), $config['plural'] ),
						'all_items'     => sprintf( __( 'All %s', 'kechoo-core' ), $config['plural'] ),
						'edit_item'     => sprintf( __( 'Edit %s', 'kechoo-core' ), $config['singular'] ),
						'add_new_item'  => sprintf( __( 'Add %s', 'kechoo-core' ), $config['singular'] ),
						'menu_name'     => $config['plural'],
					),
					'public'            => true,
					'hierarchical'      => true,
					'show_admin_column' => false,
					'show_in_rest'      => true,
					'rewrite'           => array( 'slug' => $config['rewrite'] ),
				)
			);
		}
	}

	public static function seed_terms() {
		if ( self::TERMS_VERSION === get_option( 'kechoo_terms_seeded' ) ) {
			return;
		}

		$terms = array(
			'kechoo_application' => array(
				'food-bone' => 'Food & Bone',
				'wood'      => 'Wood',
				'metal'     => 'Metal',
			),
			'kechoo_blade_technology' => array(
				'hardened' => 'Hardened',
				'bi-metal' => 'Bi-Metal',
				'carbide'  => 'Carbide',
			),
			'kechoo_cut_material' => array(
				'frozen-meat-bone'        => 'Frozen Meat & Bone',
				'softwood-hardwood'       => 'Softwood & Hardwood',
				'wood-plywood-composite'  => 'Wood, Plywood & Composite',
				'carbon-steel'            => 'Carbon Steel',
				'steel-tube-profile'      => 'Steel Tube & Profile',
				'thin-gauge-metal'        => 'Thin Gauge Metal',
				'high-alloy-abrasive-metal'=> 'High-Alloy & Abrasive Metal',
			),
			'kechoo_machine' => array(
				'butcher-band-saws'          => 'Butcher Band Saws',
				'woodworking-band-saws'      => 'Woodworking Band Saws',
				'vertical-metal-band-saws'   => 'Vertical Metal Band Saws',
				'rigid-production-band-saws' => 'Rigid Production Band Saws',
			),
		);

		foreach ( $terms as $taxonomy => $items ) {
			foreach ( $items as $slug => $name ) {
				if ( ! term_exists( $slug, $taxonomy ) ) {
					wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) );
				}
			}
		}

		update_option( 'kechoo_terms_seeded', self::TERMS_VERSION, false );
	}
}
