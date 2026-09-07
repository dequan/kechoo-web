<?php
/**
 * Search visibility and on-page support for catalog URLs.
 *
 * @package KechooCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Kechoo_SEO {
	const SITEMAP_REWRITE_VERSION = '1.0.1';
	const URLS_PER_SITEMAP        = 1000;

	private static $sitemap_types = array(
		'pages' => array( 'object' => 'post', 'name' => 'page' ),
		'posts' => array( 'object' => 'post', 'name' => 'post' ),
		'products' => array( 'object' => 'post', 'name' => 'product' ),
		'categories' => array( 'object' => 'term', 'name' => 'category' ),
		'product-categories' => array( 'object' => 'term', 'name' => 'product_cat' ),
		'product-brands' => array( 'object' => 'term', 'name' => 'product_brand' ),
		'applications' => array( 'object' => 'term', 'name' => 'kechoo_application' ),
		'blade-technologies' => array( 'object' => 'term', 'name' => 'kechoo_blade_technology' ),
		'cut-materials' => array( 'object' => 'term', 'name' => 'kechoo_cut_material' ),
		'machine-compatibility' => array( 'object' => 'term', 'name' => 'kechoo_machine' ),
	);

	private static $filter_parameters = array(
		'kechoo_application',
		'kechoo_cut_material',
		'kechoo_machine',
		'kechoo_blade_technology',
		'orderby',
		'min_price',
		'max_price',
	);

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_sitemap_routes' ), 9 );
		add_action( 'init', array( __CLASS__, 'maybe_flush_sitemap_routes' ), 99 );
		add_filter( 'query_vars', array( __CLASS__, 'sitemap_query_vars' ) );
		add_action( 'template_redirect', array( __CLASS__, 'serve_sitemap' ), 0 );
		add_filter( 'robots_txt', array( __CLASS__, 'robots_txt' ), 9999, 2 );
		add_filter( 'wp_robots', array( __CLASS__, 'robots' ) );
		add_filter( 'rank_math/frontend/robots', array( __CLASS__, 'rank_math_robots' ) );
		add_filter( 'wp_sitemaps_add_provider', array( __CLASS__, 'exclude_author_sitemap' ), 10, 2 );
		add_filter( 'wp_sitemaps_posts_query_args', array( __CLASS__, 'exclude_utility_pages_from_core_sitemap' ), 10, 2 );
		add_filter( 'rank_math/sitemap/entry', array( __CLASS__, 'exclude_utility_pages_from_rank_math_sitemap' ), 10, 3 );
		add_action( 'wp_head', array( __CLASS__, 'fallback_meta_description' ), 2 );
		add_action( 'woocommerce_after_shop_loop', array( __CLASS__, 'archive_selection_content' ), 24 );
		add_action( 'woocommerce_no_products_found', array( __CLASS__, 'archive_selection_content' ), 24 );
	}

	public static function register_sitemap_routes() {
		add_rewrite_rule( '^sitemap\.xml$', 'index.php?kechoo_sitemap=index', 'top' );
		add_rewrite_rule( '^sitemap-index\.xml$', 'index.php?kechoo_sitemap=index', 'top' );
		add_rewrite_rule( '^sitemap-([a-z0-9-]+?)(?:-([0-9]+))?\.xml$', 'index.php?kechoo_sitemap=$matches[1]&kechoo_sitemap_page=$matches[2]', 'top' );
	}

	public static function maybe_flush_sitemap_routes() {
		if ( self::SITEMAP_REWRITE_VERSION === get_option( 'kechoo_sitemap_rewrite_version' ) ) {
			return;
		}

		flush_rewrite_rules( false );
		update_option( 'kechoo_sitemap_rewrite_version', self::SITEMAP_REWRITE_VERSION, false );
	}

	public static function sitemap_query_vars( $query_vars ) {
		$query_vars[] = 'kechoo_sitemap';
		$query_vars[] = 'kechoo_sitemap_page';
		return $query_vars;
	}

	public static function robots_txt( $output, $public ) {
		if ( ! $public ) {
			return $output;
		}

		$sitemap_line = 'Sitemap: ' . home_url( '/sitemap-index.xml' );
		$output       = preg_replace( '/^Sitemap:\s*.+$/mi', '', $output );
		return rtrim( $output ) . "\n\n" . $sitemap_line . "\n";
	}

	public static function serve_sitemap() {
		$sitemap = sanitize_key( (string) get_query_var( 'kechoo_sitemap' ) );
		$page    = max( 1, absint( get_query_var( 'kechoo_sitemap_page' ) ) );
		$request_path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
		$filename     = basename( (string) $request_path );

		if ( ! $sitemap && in_array( $filename, array( 'sitemap.xml', 'sitemap-index.xml' ), true ) ) {
			$sitemap = 'index';
		} elseif ( ! $sitemap && preg_match( '/^sitemap-([a-z0-9-]+?)(?:-([0-9]+))?\.xml$/', $filename, $matches ) ) {
			$sitemap = sanitize_key( $matches[1] );
			$page    = isset( $matches[2] ) ? max( 1, absint( $matches[2] ) ) : 1;
		}

		if ( ! $sitemap ) {
			return;
		}

		if ( 'index' !== $sitemap && ! isset( self::$sitemap_types[ $sitemap ] ) ) {
			self::xml_not_found();
		}
		if ( 'index' !== $sitemap ) {
			$item_count = self::sitemap_item_count( self::$sitemap_types[ $sitemap ] );
			$max_pages = (int) ceil( $item_count / self::URLS_PER_SITEMAP );
			if ( $item_count < 1 || $page > $max_pages ) {
				self::xml_not_found();
			}
		}

		nocache_headers();
		status_header( 200 );
		header( 'Content-Type: application/xml; charset=UTF-8' );
		echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		if ( 'index' === $sitemap ) {
			self::render_sitemap_index();
		} else {
			self::render_urlset( $sitemap, $page );
		}
		exit;
	}

	private static function xml_not_found() {
		status_header( 404 );
		header( 'Content-Type: application/xml; charset=UTF-8' );
		echo '<?xml version="1.0" encoding="UTF-8"?><error>Unknown sitemap.</error>';
		exit;
	}

	private static function render_sitemap_index() {
		echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		foreach ( self::$sitemap_types as $slug => $config ) {
			$count = self::sitemap_item_count( $config );
			if ( $count < 1 ) {
				continue;
			}
			$page_count = (int) ceil( $count / self::URLS_PER_SITEMAP );
			for ( $page = 1; $page <= $page_count; $page++ ) {
				$filename = 'sitemap-' . $slug . ( $page > 1 ? '-' . $page : '' ) . '.xml';
				echo "\t<sitemap><loc>" . esc_xml( home_url( '/' . $filename ) ) . '</loc></sitemap>' . "\n";
			}
		}
		echo '</sitemapindex>';
	}

	private static function sitemap_item_count( $config ) {
		if ( 'post' === $config['object'] ) {
			$args = self::post_query_args( $config['name'], 1 );
			$args['posts_per_page'] = 1;
			$query = new WP_Query( $args );
			return (int) $query->found_posts;
		}

		return count( self::indexable_terms( $config['name'] ) );
	}

	private static function post_query_args( $post_type, $page ) {
		$args = array(
			'post_type'              => $post_type,
			'post_status'            => 'publish',
			'posts_per_page'         => self::URLS_PER_SITEMAP,
			'paged'                  => $page,
			'orderby'                => 'modified',
			'order'                  => 'DESC',
			'ignore_sticky_posts'    => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		if ( 'page' === $post_type ) {
			$args['post__not_in'] = self::utility_page_ids();
		} elseif ( 'post' === $post_type ) {
			$args['post__not_in'] = self::sample_post_ids();
		}
		return $args;
	}

	private static function indexable_terms( $taxonomy ) {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			return array();
		}

		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => true,
				'orderby'    => 'term_id',
				'order'      => 'ASC',
			)
		);
		if ( is_wp_error( $terms ) ) {
			return array();
		}

		return array_values(
			array_filter(
				$terms,
				static function ( $term ) {
					return '' !== trim( wp_strip_all_tags( $term->description ) );
				}
			)
		);
	}

	private static function render_urlset( $slug, $page ) {
		$config = self::$sitemap_types[ $slug ];
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
		if ( 'post' === $config['object'] ) {
			$query = new WP_Query( self::post_query_args( $config['name'], $page ) );
			foreach ( $query->posts as $post ) {
				self::render_post_url( $post );
			}
		} elseif ( taxonomy_exists( $config['name'] ) ) {
			$terms = array_slice(
				self::indexable_terms( $config['name'] ),
				( $page - 1 ) * self::URLS_PER_SITEMAP,
				self::URLS_PER_SITEMAP
			);
			foreach ( $terms as $term ) {
				$link = get_term_link( $term );
				if ( ! is_wp_error( $link ) ) {
					echo "\t<url><loc>" . esc_xml( $link ) . '</loc></url>' . "\n";
				}
			}
		}
		echo '</urlset>';
	}

	private static function render_post_url( $post ) {
		$permalink = get_permalink( $post );
		if ( ! $permalink ) {
			return;
		}

		echo "\t<url>\n";
		echo "\t\t<loc>" . esc_xml( $permalink ) . "</loc>\n";
		echo "\t\t<lastmod>" . esc_xml( get_post_modified_time( DATE_W3C, true, $post ) ) . "</lastmod>\n";
		foreach ( self::post_image_ids( $post ) as $image_id ) {
			$image_url = wp_get_attachment_image_url( $image_id, 'full' );
			if ( ! $image_url ) {
				continue;
			}
			$image_title = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
			if ( ! $image_title ) {
				$image_title = get_the_title( $image_id );
			}
			echo "\t\t<image:image><image:loc>" . esc_xml( $image_url ) . '</image:loc>';
			if ( $image_title ) {
				echo '<image:title>' . esc_xml( $image_title ) . '</image:title>';
			}
			echo "</image:image>\n";
		}
		echo "\t</url>\n";
	}

	private static function post_image_ids( $post ) {
		$image_ids = array();
		if ( has_post_thumbnail( $post ) ) {
			$image_ids[] = get_post_thumbnail_id( $post );
		}
		if ( 'product' === $post->post_type && function_exists( 'wc_get_product' ) ) {
			$product = wc_get_product( $post->ID );
			if ( $product ) {
				$image_ids = array_merge( $image_ids, $product->get_gallery_image_ids() );
			}
		}
		return array_values( array_unique( array_filter( array_map( 'absint', $image_ids ) ) ) );
	}

	private static function utility_page_ids() {
		$ids = array();
		if ( function_exists( 'wc_get_page_id' ) ) {
			foreach ( array( 'cart', 'checkout', 'myaccount' ) as $page_key ) {
				$page_id = (int) wc_get_page_id( $page_key );
				if ( $page_id > 0 ) {
					$ids[] = $page_id;
				}
			}
		}

		return array_values( array_unique( $ids ) );
	}

	private static function sample_post_ids() {
		$sample = get_page_by_path( 'hello-world', OBJECT, 'post' );
		if ( $sample instanceof WP_Post && 'Hello world!' === $sample->post_title ) {
			return array( (int) $sample->ID );
		}
		return array();
	}

	private static function has_catalog_filters() {
		foreach ( self::$filter_parameters as $parameter ) {
			if ( isset( $_GET[ $parameter ] ) && '' !== (string) wp_unslash( $_GET[ $parameter ] ) ) {
				return true;
			}
		}

		return false;
	}

	private static function is_low_value_request() {
		$is_utility = is_search() || is_404() || is_author();
		if ( function_exists( 'is_cart' ) ) {
			$is_utility = $is_utility || is_cart() || is_checkout() || is_account_page();
		}
		if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) && self::has_catalog_filters() ) {
			$is_utility = true;
		}
		return $is_utility;
	}

	public static function robots( $robots ) {
		if ( self::is_low_value_request() ) {
			$robots['noindex'] = true;
			unset( $robots['index'] );
		}

		return $robots;
	}

	public static function exclude_author_sitemap( $provider, $name ) {
		return 'users' === $name ? false : $provider;
	}

	public static function rank_math_robots( $robots ) {
		if ( self::is_low_value_request() ) {
			$robots['index'] = 'noindex';
		}

		return $robots;
	}

	public static function exclude_utility_pages_from_core_sitemap( $args, $post_type ) {
		if ( 'page' !== $post_type ) {
			return $args;
		}

		$args['post__not_in'] = array_values(
			array_unique(
				array_merge( isset( $args['post__not_in'] ) ? (array) $args['post__not_in'] : array(), self::utility_page_ids() )
			)
		);
		return $args;
	}

	public static function exclude_utility_pages_from_rank_math_sitemap( $url, $type, $object ) {
		if ( 'user' === $type ) {
			return false;
		}
		if ( 'post' !== $type || ! is_object( $object ) || empty( $object->ID ) ) {
			return $url;
		}

		return in_array( (int) $object->ID, self::utility_page_ids(), true ) ? false : $url;
	}

	public static function fallback_meta_description() {
		if ( defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' ) || defined( 'AIOSEO_VERSION' ) ) {
			return;
		}

		$description = '';
		if ( is_singular() ) {
			$post = get_queried_object();
			if ( $post instanceof WP_Post ) {
				$description = $post->post_excerpt ? $post->post_excerpt : $post->post_content;
			}
		} elseif ( is_tax() || is_category() || is_tag() ) {
			$term = get_queried_object();
			if ( $term instanceof WP_Term ) {
				$description = $term->description;
			}
		} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
			$description = __( 'Shop KECHOO industrial bandsaw blades for food and bone, woodworking, and metal cutting. Compare hardened, bi-metal, and carbide blade specifications.', 'kechoo-core' );
		} elseif ( is_home() || is_front_page() ) {
			$description = get_bloginfo( 'description', 'display' );
		}

		$description = wp_strip_all_tags( strip_shortcodes( (string) $description ) );
		$description = preg_replace( '/\s+/', ' ', $description );
		$description = trim( wp_html_excerpt( $description, 158, '…' ) );
		if ( $description ) {
			echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
		}
	}

	public static function archive_selection_content() {
		if ( ! ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) ) ) {
			return;
		}
		?>
		<section class="kechoo-archive-guide" aria-labelledby="kechoo-archive-guide-title">
			<h2 id="kechoo-archive-guide-title"><?php esc_html_e( 'Confirm the bandsaw blade specification', 'kechoo-core' ); ?></h2>
			<p><?php esc_html_e( 'A compatible blade must match the saw and the workpiece. Confirm length, width, thickness, tooth pitch, blade technology, and the material section before ordering. For metal cutting, also check speed, feed, coolant, guides, tension, and the recommended break-in procedure.', 'kechoo-core' ); ?></p>
			<ul>
				<li><?php esc_html_e( 'Use the machine plate or manual to verify blade dimensions.', 'kechoo-core' ); ?></li>
				<li><?php esc_html_e( 'Choose tooth pitch from the smallest and largest section in the cut.', 'kechoo-core' ); ?></li>
				<li><?php esc_html_e( 'For an unlisted size, send the machine model and current blade label.', 'kechoo-core' ); ?></li>
			</ul>
			<p class="kechoo-archive-guide__links">
				<a href="<?php echo esc_url( home_url( '/find-your-blade/' ) ); ?>"><?php esc_html_e( 'Use the blade selector', 'kechoo-core' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/request-a-quote/' ) ); ?>"><?php esc_html_e( 'Request a technical quote', 'kechoo-core' ); ?></a>
			</p>
		</section>
		<?php
	}
}
