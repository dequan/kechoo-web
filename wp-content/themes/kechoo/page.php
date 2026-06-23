<?php
/**
 * Page template.
 *
 * @package Kechoo
 */

get_header();
?>
<main id="main-content" class="kechoo-content-shell">
	<?php while ( have_posts() ) : the_post(); ?>
		<article <?php post_class( 'kechoo-page' ); ?>>
			<header class="kechoo-page-header"><h1><?php the_title(); ?></h1></header>
			<div class="kechoo-entry-content"><?php the_content(); ?></div>
		</article>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>

