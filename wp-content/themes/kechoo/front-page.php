<?php
/**
 * KECHOO home page.
 *
 * @package Kechoo
 */

get_header();

$shop_url = kechoo_shop_url();
$applications = array(
	array(
		'slug'        => 'food-bone',
		'title'       => __( 'Food & Bone', 'kechoo' ),
		'description' => __( 'Clean, consistent cutting for butcher shops, meat processors, and food production.', 'kechoo' ),
		'image'       => 'application-food-bone.png',
		'alt'         => __( 'Bandsaw cutting frozen meat and bone in a hygienic processing room', 'kechoo' ),
	),
	array(
		'slug'        => 'wood',
		'title'       => __( 'Wood', 'kechoo' ),
		'description' => __( 'Reliable blades for sawmills, furniture production, resawing, and general woodworking.', 'kechoo' ),
		'image'       => 'application-wood.png',
		'alt'         => __( 'Industrial bandsaw cutting a solid timber beam', 'kechoo' ),
	),
	array(
		'slug'        => 'metal',
		'title'       => __( 'Metal', 'kechoo' ),
		'description' => __( 'Controlled cutting for fabrication, steel service centers, and demanding alloys.', 'kechoo' ),
		'image'       => 'application-metal.png',
		'alt'         => __( 'Horizontal bandsaw cutting rectangular steel tube', 'kechoo' ),
	),
);
?>
<main id="main-content">
	<section class="kechoo-hero" aria-labelledby="kechoo-hero-title">
		<div class="kechoo-hero__copy">
			<p class="kechoo-kicker"><?php esc_html_e( 'Exceptional process. Outstanding quality.', 'kechoo' ); ?></p>
			<h1 id="kechoo-hero-title"><?php esc_html_e( 'Choose Better Cutting.', 'kechoo' ); ?></h1>
			<p class="kechoo-hero__lead"><?php esc_html_e( 'Hardened, bi-metal and carbide bandsaw blades engineered for consistent results.', 'kechoo' ); ?></p>
			<div class="kechoo-actions">
				<a class="kechoo-button kechoo-button--light" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Shop In-Stock', 'kechoo' ); ?></a>
				<a class="kechoo-button kechoo-button--outline-light" href="<?php echo esc_url( home_url( '/request-a-quote/' ) ); ?>"><?php esc_html_e( 'Request a Quote', 'kechoo' ); ?></a>
			</div>
			<ul class="kechoo-hero__proof" aria-label="<?php esc_attr_e( 'KECHOO product strengths', 'kechoo' ); ?>">
				<li><?php esc_html_e( 'Application-led selection', 'kechoo' ); ?></li>
				<li><?php esc_html_e( 'Verified stock specifications', 'kechoo' ); ?></li>
				<li><?php esc_html_e( 'Global supply from China', 'kechoo' ); ?></li>
			</ul>
		</div>
		<div class="kechoo-hero__media" aria-hidden="true">
			<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/hero-bandsaw-blade.png' ) ); ?>" alt="" width="1586" height="992" fetchpriority="high">
		</div>
	</section>

	<section class="kechoo-section kechoo-applications" aria-labelledby="applications-title">
		<div class="kechoo-section__heading kechoo-section__heading--center">
			<h2 id="applications-title"><?php esc_html_e( 'Start With What You Cut', 'kechoo' ); ?></h2>
			<p><?php esc_html_e( 'Choose an application first. We will guide you to the right blade technology and specification.', 'kechoo' ); ?></p>
		</div>
		<div class="kechoo-applications__grid">
			<?php foreach ( $applications as $application ) : ?>
				<article class="kechoo-application">
					<a class="kechoo-application__image" href="<?php echo esc_url( $shop_url . '?kechoo_application=' . $application['slug'] ); ?>" tabindex="-1" aria-hidden="true">
						<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/' . $application['image'] ) ); ?>" alt="<?php echo esc_attr( $application['alt'] ); ?>" width="1200" height="800" loading="lazy">
					</a>
					<div class="kechoo-application__content">
						<h3><?php echo esc_html( $application['title'] ); ?></h3>
						<p><?php echo esc_html( $application['description'] ); ?></p>
						<a class="kechoo-text-link" href="<?php echo esc_url( $shop_url . '?kechoo_application=' . $application['slug'] ); ?>"><?php esc_html_e( 'Find Your Blade', 'kechoo' ); ?> <span aria-hidden="true">→</span></a>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="kechoo-selector-band" aria-labelledby="selector-title">
		<div class="kechoo-selector-band__intro">
			<h2 id="selector-title"><?php esc_html_e( 'Find Your Blade', 'kechoo' ); ?></h2>
			<p><?php esc_html_e( 'Answer a few questions to narrow the catalog. If there is no verified stock match, send the same details to our technical team.', 'kechoo' ); ?></p>
		</div>
		<div class="kechoo-selector-band__form">
			<?php
			if ( shortcode_exists( 'kechoo_blade_selector' ) ) {
				echo do_shortcode( '[kechoo_blade_selector compact="true"]' );
			} else {
				?>
				<a class="kechoo-button" href="<?php echo esc_url( home_url( '/find-your-blade/' ) ); ?>"><?php esc_html_e( 'Start Blade Selection', 'kechoo' ); ?></a>
				<?php
			}
			?>
		</div>
	</section>

	<section class="kechoo-technologies" aria-labelledby="technology-title">
		<div class="kechoo-section__heading">
			<p class="kechoo-kicker kechoo-kicker--dark"><?php esc_html_e( 'Three technologies. One clear choice.', 'kechoo' ); ?></p>
			<h2 id="technology-title"><?php esc_html_e( 'Match the blade to the work', 'kechoo' ); ?></h2>
		</div>
		<div class="kechoo-technologies__grid">
			<article>
				<span class="kechoo-tech-index">01</span>
				<h3><?php esc_html_e( 'Hardened', 'kechoo' ); ?></h3>
				<p><?php esc_html_e( 'High-carbon steel with precision induction-hardened teeth for food, bone, wood, and general-purpose cutting.', 'kechoo' ); ?></p>
				<a href="<?php echo esc_url( $shop_url . '?kechoo_blade_technology=hardened' ); ?>"><?php esc_html_e( 'Explore hardened blades', 'kechoo' ); ?> <span aria-hidden="true">→</span></a>
			</article>
			<article>
				<span class="kechoo-tech-index">02</span>
				<h3><?php esc_html_e( 'Bi-Metal', 'kechoo' ); ?></h3>
				<p><?php esc_html_e( 'A flexible alloy-steel backing joined to hardened high-speed steel teeth for reliable metal production cutting.', 'kechoo' ); ?></p>
				<a href="<?php echo esc_url( $shop_url . '?kechoo_blade_technology=bi-metal' ); ?>"><?php esc_html_e( 'Explore bi-metal blades', 'kechoo' ); ?> <span aria-hidden="true">→</span></a>
			</article>
			<article>
				<span class="kechoo-tech-index">03</span>
				<h3><?php esc_html_e( 'Carbide', 'kechoo' ); ?></h3>
				<p><?php esc_html_e( 'Carbide-tipped performance for high-alloy, abrasive, large-section, and demanding production applications.', 'kechoo' ); ?></p>
				<a href="<?php echo esc_url( $shop_url . '?kechoo_blade_technology=carbide' ); ?>"><?php esc_html_e( 'Explore carbide blades', 'kechoo' ); ?> <span aria-hidden="true">→</span></a>
			</article>
		</div>
	</section>

	<section class="kechoo-section kechoo-stock" aria-labelledby="stock-title">
		<div class="kechoo-section__heading kechoo-section__heading--split">
			<div>
				<p class="kechoo-kicker kechoo-kicker--dark"><?php esc_html_e( 'Ready to ship', 'kechoo' ); ?></p>
				<h2 id="stock-title"><?php esc_html_e( 'Hot-Selling Specifications', 'kechoo' ); ?></h2>
			</div>
			<a class="kechoo-text-link" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'View all in-stock blades', 'kechoo' ); ?> <span aria-hidden="true">→</span></a>
		</div>
		<?php if ( class_exists( 'WooCommerce' ) ) : ?>
			<div class="kechoo-stock__products"><?php echo do_shortcode( '[products limit="4" columns="4" orderby="popularity" visibility="visible"]' ); ?></div>
		<?php else : ?>
			<div class="kechoo-empty-state">
				<h3><?php esc_html_e( 'Stock products will appear here after WooCommerce is connected.', 'kechoo' ); ?></h3>
				<p><?php esc_html_e( 'Meanwhile, send your blade dimensions and application for a technical quotation.', 'kechoo' ); ?></p>
				<a class="kechoo-button" href="<?php echo esc_url( home_url( '/request-a-quote/' ) ); ?>"><?php esc_html_e( 'Request a Blade Quote', 'kechoo' ); ?></a>
			</div>
		<?php endif; ?>
	</section>

	<section class="kechoo-proof" aria-labelledby="proof-title">
		<div class="kechoo-proof__statement">
			<p class="kechoo-kicker"><?php esc_html_e( 'Process earns trust', 'kechoo' ); ?></p>
			<h2 id="proof-title"><?php esc_html_e( 'A better cut starts before the blade reaches your machine.', 'kechoo' ); ?></h2>
		</div>
		<ol class="kechoo-proof__steps">
			<li><span>01</span><strong><?php esc_html_e( 'Material control', 'kechoo' ); ?></strong><p><?php esc_html_e( 'Specify the backing and tooth material for the application, not for a catalog label.', 'kechoo' ); ?></p></li>
			<li><span>02</span><strong><?php esc_html_e( 'Tooth consistency', 'kechoo' ); ?></strong><p><?php esc_html_e( 'Control tooth form, set, and heat treatment for repeatable chip formation.', 'kechoo' ); ?></p></li>
			<li><span>03</span><strong><?php esc_html_e( 'Weld inspection', 'kechoo' ); ?></strong><p><?php esc_html_e( 'Inspect blade length, alignment, finish, and weld quality before packing.', 'kechoo' ); ?></p></li>
		</ol>
	</section>

	<section class="kechoo-distributor-cta" aria-labelledby="distributor-title">
		<div>
			<p class="kechoo-kicker"><?php esc_html_e( 'For distributors and volume buyers', 'kechoo' ); ?></p>
			<h2 id="distributor-title"><?php esc_html_e( 'Build a dependable bandsaw blade range.', 'kechoo' ); ?></h2>
		</div>
		<p><?php esc_html_e( 'Discuss bulk supply, OEM packaging, regional demand, and a product mix built around the customers you serve.', 'kechoo' ); ?></p>
		<a class="kechoo-button kechoo-button--light" href="<?php echo esc_url( home_url( '/distributors/' ) ); ?>"><?php esc_html_e( 'Discuss Distribution', 'kechoo' ); ?></a>
	</section>
</main>
<?php get_footer(); ?>

