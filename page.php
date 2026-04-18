<?php
/**
 * Static page template
 */
get_header();
?>

<div class="page-title-section">
	<div class="container">
		<?php while ( have_posts() ) : the_post(); ?>
			<h1 class="page-title"><?php the_title(); ?></h1>
		<?php endwhile; rewind_posts(); ?>
	</div>
</div>

<div class="site-main">
	<div class="content-area">

		<main id="primary" role="main">
			<?php while ( have_posts() ) : the_post(); ?>
			<div class="page-content-wrapper">
				<div class="entry-content">
					<?php the_content(); ?>
					<?php
					wp_link_pages( array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'fmcg-theme' ),
						'after'  => '</div>',
					) );
					?>
				</div>

				<?php if ( comments_open() || get_comments_number() ) : ?>
					<?php comments_template(); ?>
				<?php endif; ?>
			</div>
			<?php endwhile; ?>
		</main>

		<?php get_sidebar(); ?>

	</div><!-- .content-area -->
</div><!-- .site-main -->

<?php get_footer(); ?>
