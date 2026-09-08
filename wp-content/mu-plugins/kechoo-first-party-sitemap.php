<?php
/**
 * Plugin Name: KECHOO First-Party Sitemap
 * Description: Backward-compatible first-party XML sitemap for KECHOO installations whose core plugin predates the sitemap service.
 * Version: 1.0.1
 * Requires PHP: 8.1
 *
 * @package Kechoo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Kechoo_First_Party_Sitemap {
	const VERSION  = '1.0.1';
	const PAGE_SIZE = 1000;

	private static $types = array(
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

	public static function init() {
		add_action( 'init', array( __CLASS__, 'routes' ), 9 );
		add_action( 'init', array( __CLASS__, 'maybe_flush_routes' ), 99 );
		add_filter( 'query_vars', array( __CLASS__, 'query_vars' ) );
		add_action( 'template_redirect', array( __CLASS__, 'serve' ), 0 );
		add_filter( 'robots_txt', array( __CLASS__, 'robots_txt' ), 9999, 2 );
	}

	public static function routes() {
		add_rewrite_rule( '^sitemap-index\.xml$', 'index.php?kechoo_sitemap=index', 'top' );
		add_rewrite_rule( '^sitemap-([a-z0-9-]+?)(?:-([0-9]+))?\.xml$', 'index.php?kechoo_sitemap=$matches[1]&kechoo_sitemap_page=$matches[2]', 'top' );
	}

	public static function maybe_flush_routes() {
		if ( self::VERSION === get_option( 'kechoo_mu_sitemap_version' ) ) {
			return;
		}
		flush_rewrite_rules( false );
		update_option( 'kechoo_mu_sitemap_version', self::VERSION, false );
	}

	public static function query_vars( $vars ) {
		$vars[] = 'kechoo_sitemap';
		$vars[] = 'kechoo_sitemap_page';
		return $vars;
	}

	public static function robots_txt( $output, $public ) {
		if ( ! $public ) {
			return $output;
		}
		$output = preg_replace( '/^Sitemap:\s*.+$/mi', '', $output );
		return rtrim( $output ) . "\n\nSitemap: " . home_url( '/sitemap-index.xml' ) . "\n";
	}

	public static function serve() {
		$slug = sanitize_key( (string) get_query_var( 'kechoo_sitemap' ) );
		$page = max( 1, absint( get_query_var( 'kechoo_sitemap_page' ) ) );
		$path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
		$file = basename( (string) $path );
		if ( 'wp-sitemap.xml' === $file ) {
			wp_safe_redirect( home_url( '/sitemap-index.xml' ), 301, 'KECHOO Sitemap' );
			exit;
		}

		if ( ! $slug && 'sitemap-index.xml' === $file ) {
			$slug = 'index';
		} elseif ( ! $slug && preg_match( '/^sitemap-([a-z0-9-]+?)(?:-([0-9]+))?\.xml$/', $file, $matches ) ) {
			$slug = sanitize_key( $matches[1] );
			$page = isset( $matches[2] ) ? max( 1, absint( $matches[2] ) ) : 1;
		}

		if ( ! $slug ) {
			return;
		}
		if ( 'index' !== $slug && ! isset( self::$types[ $slug ] ) ) {
			self::not_found();
		}
		if ( 'index' !== $slug ) {
			$count = self::count_items( self::$types[ $slug ] );
			if ( $count < 1 || $page > (int) ceil( $count / self::PAGE_SIZE ) ) {
				self::not_found();
			}
		}

		nocache_headers();
		status_header( 200 );
		header( 'Content-Type: application/xml; charset=UTF-8' );
		echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		'index' === $slug ? self::index() : self::urlset( $slug, $page );
		exit;
	}

	private static function not_found() {
		status_header( 404 );
		header( 'Content-Type: application/xml; charset=UTF-8' );
		echo '<?xml version="1.0" encoding="UTF-8"?><error>Unknown sitemap.</error>';
		exit;
	}

	private static function index() {
		echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		foreach ( self::$types as $slug => $config ) {
			$count = self::count_items( $config );
			for ( $page = 1, $pages = (int) ceil( $count / self::PAGE_SIZE ); $page <= $pages; $page++ ) {
				$file = 'sitemap-' . $slug . ( $page > 1 ? '-' . $page : '' ) . '.xml';
				echo "\t<sitemap><loc>" . esc_xml( home_url( '/' . $file ) ) . '</loc></sitemap>' . "\n";
			}
		}
		echo '</sitemapindex>';
	}

	private static function count_items( $config ) {
		if ( 'post' === $config['object'] ) {
			$args = self::post_args( $config['name'], 1 );
			$args['posts_per_page'] = 1;
			return (int) ( new WP_Query( $args ) )->found_posts;
		}
		return count( self::terms( $config['name'] ) );
	}

	private static function post_args( $type, $page ) {
		$args = array(
			'post_type' => $type,
			'post_status' => 'publish',
			'posts_per_page' => self::PAGE_SIZE,
			'paged' => $page,
			'orderby' => 'modified',
			'order' => 'DESC',
			'ignore_sticky_posts' => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);
		if ( 'page' === $type ) {
			$args['post__not_in'] = self::utility_pages();
		} elseif ( 'post' === $type ) {
			$args['post__not_in'] = self::sample_posts();
		}
		return $args;
	}

	private static function terms( $taxonomy ) {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			return array();
		}
		$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => true, 'orderby' => 'term_id', 'order' => 'ASC' ) );
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

	private static function urlset( $slug, $page ) {
		$config = self::$types[ $slug ];
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
		if ( 'post' === $config['object'] ) {
			foreach ( ( new WP_Query( self::post_args( $config['name'], $page ) ) )->posts as $post ) {
				self::post_url( $post );
			}
		} else {
			$terms = array_slice( self::terms( $config['name'] ), ( $page - 1 ) * self::PAGE_SIZE, self::PAGE_SIZE );
			foreach ( $terms as $term ) {
				$link = get_term_link( $term );
				if ( ! is_wp_error( $link ) ) {
					echo "\t<url><loc>" . esc_xml( $link ) . '</loc></url>' . "\n";
				}
			}
		}
		echo '</urlset>';
	}

	private static function post_url( $post ) {
		$link = get_permalink( $post );
		if ( ! $link ) {
			return;
		}
		echo "\t<url>\n\t\t<loc>" . esc_xml( $link ) . "</loc>\n";
		echo "\t\t<lastmod>" . esc_xml( get_post_modified_time( DATE_W3C, true, $post ) ) . "</lastmod>\n";
		foreach ( self::images( $post ) as $image_id ) {
			$src = wp_get_attachment_image_url( $image_id, 'full' );
			if ( $src ) {
				$title = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
				echo "\t\t<image:image><image:loc>" . esc_xml( $src ) . '</image:loc>';
				if ( $title ) {
					echo '<image:title>' . esc_xml( $title ) . '</image:title>';
				}
				echo "</image:image>\n";
			}
		}
		echo "\t</url>\n";
	}

	private static function images( $post ) {
		$ids = has_post_thumbnail( $post ) ? array( get_post_thumbnail_id( $post ) ) : array();
		if ( 'product' === $post->post_type && function_exists( 'wc_get_product' ) ) {
			$product = wc_get_product( $post->ID );
			if ( $product ) {
				$ids = array_merge( $ids, $product->get_gallery_image_ids() );
			}
		}
		return array_values( array_unique( array_filter( array_map( 'absint', $ids ) ) ) );
	}

	private static function utility_pages() {
		$ids = array();
		if ( function_exists( 'wc_get_page_id' ) ) {
			foreach ( array( 'cart', 'checkout', 'myaccount' ) as $key ) {
				$id = (int) wc_get_page_id( $key );
				if ( $id > 0 ) {
					$ids[] = $id;
				}
			}
		}
		return $ids;
	}

	private static function sample_posts() {
		$post = get_page_by_path( 'hello-world', OBJECT, 'post' );
		return $post instanceof WP_Post && 'Hello world!' === $post->post_title ? array( (int) $post->ID ) : array();
	}
}

add_action(
	'plugins_loaded',
	static function () {
		if ( class_exists( 'Kechoo_SEO' ) && method_exists( 'Kechoo_SEO', 'register_sitemap_routes' ) ) {
			return;
		}
		Kechoo_First_Party_Sitemap::init();
	},
	20
);
