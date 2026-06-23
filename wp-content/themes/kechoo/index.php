<?php
/**
 * Default content template.
 *
 * @package Kechoo
 */

get_header();
?>
<main id="main-content" class="kechoo-content-shell">
	<header class="kechoo-page-header">
		<h1><?php single_post_title(); ?></h1>
	</header>
	<div class="kechoo-post-list">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class( 'kechoo-post-summary' ); ?>>
					<p class="kechoo-post-summary__meta"><?php echo esc_html( get_the_date() ); ?></p>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>
				</article>
			<?php endwhile; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<div class="kechoo-empty-state"><h2><?php esc_html_e( 'No resources found.', 'kechoo' ); ?></h2><p><?php esc_html_e( 'Try another search or use Find Your Blade to start with your application.', 'kechoo' ); ?></p></div>
		<?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>

