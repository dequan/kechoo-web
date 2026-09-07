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
	const TERMS_VERSION = '1.2.2';

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
			'product_brand' => array(
				'kechoo' => array(
					'name'        => 'KECHOO',
					'description' => '<p>Shop KECHOO bandsaw blades for food and bone processing, woodworking, fabrication, and industrial metal cutting. The range includes hardened carbon-steel, bi-metal M42, and carbide-tipped blade constructions in common machine sizes, with custom lengths and technical selection support available for factories, distributors, and OEM buyers.</p>',
				),
			),
			'product_cat' => array(
				'food-bone' => array(
					'name'        => 'Food & Bone',
					'description' => '<p>Explore food and bone bandsaw blades for commercial butcher saws and meat-processing lines. Compare common blade lengths, gauges, and 3–4 TPI configurations for fresh meat, frozen products, fish, poultry, carcasses, and bone. Confirm the machine specification before ordering or request a custom-length quotation.</p>',
				),
				'wood' => array(
					'name'        => 'Wood',
					'description' => '<p>Explore hardened woodworking bandsaw blades for softwood, hardwood, plywood, composites, furniture production, contour work, and resawing. Compare blade dimensions and tooth pitches for the stock and cut, then verify the exact length and permitted blade width for your saw.</p>',
				),
				'metal' => array(
					'name'        => 'Metal',
					'description' => '<p>Explore bi-metal and carbide-tipped bandsaw blades for fabrication and production cutting. Compare solutions for carbon steel, tube and profiles, thin-wall material, stainless and high-alloy metals, bundles, and large solid sections. Select tooth pitch for the workpiece and confirm all machine dimensions before ordering.</p>',
				),
			),
			'kechoo_application' => array(
				'food-bone' => array(
					'name'        => 'Food & Bone',
					'description' => '<p>Shop food and bone bandsaw blades for butcher shops, meat processors, fish plants, poultry operations, and frozen-food production. These hardened high-carbon steel blades are made for clean, repeatable cutting of fresh or frozen meat and bone.</p><p>Choose the blade length specified by your machine manufacturer, then confirm blade width, thickness, and tooth pitch. A coarser pitch suits larger bone sections, while a finer pitch can improve control on smaller products.</p>',
				),
				'wood' => array(
					'name'        => 'Wood',
					'description' => '<p>Compare woodworking bandsaw blades for softwood, hardwood, plywood, composites, furniture production, resawing, and sawmill work. Hardened carbon-steel blades combine a flexible back with a durable cutting edge for general shop and production use.</p><p>Match blade width to straight or contour cutting, select tooth pitch for the stock thickness, and verify the exact blade length and gauge required by your bandsaw before ordering.</p>',
				),
				'metal' => array(
					'name'        => 'Metal',
					'description' => '<p>Find metal-cutting bandsaw blades for solid bar, tube, structural profiles, sheet, bundles, stainless steel, high-alloy material, and abrasive production work. Choose bi-metal M42 blades for versatile fabrication or carbide-tipped blades for rigid machines and demanding materials.</p><p>Use workpiece size and shape to select tooth pitch, then confirm blade length, width, thickness, machine condition, coolant, and cutting parameters for reliable blade life.</p>',
				),
			),
			'kechoo_blade_technology' => array(
				'hardened' => array(
					'name'        => 'Hardened',
					'description' => '<p>Hardened bandsaw blades use high-carbon steel with hardened teeth to provide an economical, flexible cutting solution. They are commonly selected for butcher and food-processing saws, woodworking bandsaws, and general-purpose cutting where blade flexibility and dependable tooth life matter.</p><p>Compare available lengths, widths, gauges, and tooth pitches, and always match the blade specification to the machine and material.</p>',
				),
				'bi-metal' => array(
					'name'        => 'Bi-Metal',
					'description' => '<p>Bi-metal bandsaw blades combine a flexible alloy-steel back with an M42 high-speed steel tooth edge. This construction is a versatile choice for fabrication shops and production cutting of carbon steel, stainless steel, tube, profiles, bundles, and solid sections.</p><p>Select tooth pitch from the workpiece cross-section and tooth engagement, then use correct break-in, tension, coolant, and feed settings to improve cutting performance and blade life.</p>',
				),
				'carbide' => array(
					'name'        => 'Carbide',
					'description' => '<p>Carbide-tipped bandsaw blades are engineered for high-alloy steels, abrasive materials, large solid sections, and demanding production cutting. The carbide tooth edge offers wear resistance and cutting capability beyond conventional bi-metal blades when the machine and setup are sufficiently rigid.</p><p>Confirm that your saw, guides, tension system, coolant delivery, and cutting parameters are suitable for carbide before selecting the blade specification.</p>',
				),
			),
			'kechoo_cut_material' => array(
				'frozen-meat-bone' => array(
					'name'        => 'Frozen Meat & Bone',
					'description' => '<p>Browse bandsaw blades for fresh and frozen meat, carcasses, poultry, fish, and bone. Hardened food-grade cutting blades are available in common butcher-saw lengths and tooth pitches for different product sizes. Verify the blade length, width, and thickness against your machine manual before choosing 3 TPI, 4 TPI, or another pitch for the section being cut.</p>',
				),
				'softwood-hardwood' => array(
					'name'        => 'Softwood & Hardwood',
					'description' => '<p>Shop bandsaw blades for cutting softwood and hardwood in workshops, furniture plants, and general woodworking production. Narrow blades support tighter curves; wider blades improve stability in straight cuts and resawing. Match tooth pitch to timber thickness and confirm blade length, width, and gauge for your woodworking bandsaw.</p>',
				),
				'wood-plywood-composite' => array(
					'name'        => 'Wood, Plywood & Composite',
					'description' => '<p>Compare bandsaw blades for timber, plywood, laminated panels, and wood-composite materials. Blade width, tooth pitch, feed rate, and material abrasiveness all affect cut quality and service life. Select the specification for the workpiece and machine, and request technical help for production laminates or unusually abrasive panels.</p>',
				),
				'carbon-steel' => array(
					'name'        => 'Carbon Steel',
					'description' => '<p>Find bi-metal bandsaw blades for carbon-steel bar, plate, tube, profiles, and fabrication stock. M42 high-speed steel teeth provide a practical balance of toughness and wear resistance. Choose tooth pitch so several teeth remain engaged in the cut, and confirm blade dimensions, speed, feed, coolant, and break-in procedure.</p>',
				),
				'steel-tube-profile' => array(
					'name'        => 'Steel Tube & Profile',
					'description' => '<p>Browse metal bandsaw blades for steel tube, pipe, angle, channel, structural profiles, and bundle cutting. Variable-pitch bi-metal blades help manage vibration as the number of engaged teeth changes through interrupted sections. Match pitch to wall thickness and bundle size, and avoid a pitch that is too coarse for thin walls.</p>',
				),
				'thin-gauge-metal' => array(
					'name'        => 'Thin Gauge Metal',
					'description' => '<p>Shop fine-pitch bandsaw blades for thin-wall tube, sheet, light profiles, and small metal sections. Keeping multiple teeth engaged helps reduce tooth snagging and stripping. Confirm the minimum wall thickness, bundle arrangement, blade dimensions, and machine speed before selecting a blade for thin-gauge metal.</p>',
				),
				'high-alloy-abrasive-metal' => array(
					'name'        => 'High-Alloy & Abrasive Metal',
					'description' => '<p>Compare carbide-tipped bandsaw blades for high-alloy steels, abrasive metals, difficult-to-machine stock, and large production sections. These applications require a rigid, well-maintained machine and controlled speed, feed, tension, coolant, and break-in. Send the alloy grade, section size, machine model, and production target for a technical recommendation.</p>',
				),
			),
			'kechoo_machine' => array(
				'butcher-band-saws' => array(
					'name'        => 'Butcher Band Saws',
					'description' => '<p>Find replacement bandsaw blades for butcher, meat, fish, and bone saws. Machine compatibility starts with the exact blade length, width, and thickness—not the saw type alone. Check the machine plate or manual, then choose tooth pitch for the size and condition of the food or bone being cut.</p>',
				),
				'woodworking-band-saws' => array(
					'name'        => 'Woodworking Band Saws',
					'description' => '<p>Compare replacement blades for woodworking bandsaws used in contour cutting, general shop work, resawing, furniture production, and sawmill applications. Confirm blade length and permitted width range for your machine, then match blade width and tooth pitch to the cut radius and timber thickness.</p>',
				),
				'vertical-metal-band-saws' => array(
					'name'        => 'Vertical Metal Band Saws',
					'description' => '<p>Browse bi-metal bandsaw blades for vertical metal-cutting saws used on bar, plate, tube, profiles, and fabrication parts. Verify the machine blade length, width, thickness, and speed range, then select tooth pitch for the smallest and largest cross-sections in the planned work.</p>',
				),
				'rigid-production-band-saws' => array(
					'name'        => 'Rigid Production Band Saws',
					'description' => '<p>Find carbide-tipped blades for rigid production bandsaws cutting high alloys, abrasive metals, and large sections. Successful carbide cutting depends on machine rigidity, accurate guides, stable tension, effective coolant, and controlled parameters. Confirm your machine capability and application details before ordering.</p>',
				),
			),
		);

		foreach ( $terms as $taxonomy => $items ) {
			if ( ! taxonomy_exists( $taxonomy ) ) {
				continue;
			}
			foreach ( $items as $slug => $term_data ) {
				$existing = term_exists( $slug, $taxonomy );
				if ( ! $existing ) {
					$created = wp_insert_term(
						$term_data['name'],
						$taxonomy,
						array(
							'slug'        => $slug,
							'description' => $term_data['description'],
						)
					);
					if ( ! is_wp_error( $created ) ) {
						update_term_meta( (int) $created['term_id'], '_kechoo_managed_description', self::TERMS_VERSION );
					}
					continue;
				}

				$term_id             = (int) ( is_array( $existing ) ? $existing['term_id'] : $existing );
				$current_description = (string) term_description( $term_id, $taxonomy );
				$managed_description = get_term_meta( $term_id, '_kechoo_managed_description', true );

				if ( '' === trim( wp_strip_all_tags( $current_description ) ) || $managed_description ) {
					wp_update_term( $term_id, $taxonomy, array( 'description' => $term_data['description'] ) );
					update_term_meta( $term_id, '_kechoo_managed_description', self::TERMS_VERSION );
				}
			}
		}

		self::assign_default_product_brand();

		update_option( 'kechoo_terms_seeded', self::TERMS_VERSION, false );
	}

	private static function assign_default_product_brand() {
		if ( ! taxonomy_exists( 'product_brand' ) ) {
			return;
		}

		$brand = get_term_by( 'slug', 'kechoo', 'product_brand' );
		if ( ! $brand instanceof WP_Term ) {
			return;
		}

		$unbranded_products = get_posts(
			array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'tax_query'      => array(
					array(
						'taxonomy' => 'product_brand',
						'operator' => 'NOT EXISTS',
					),
				),
			)
		);

		foreach ( $unbranded_products as $product_id ) {
			wp_set_object_terms( $product_id, array( (int) $brand->term_id ), 'product_brand', true );
		}
	}
}
