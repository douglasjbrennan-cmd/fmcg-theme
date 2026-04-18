<?php
/**
 * Archive template (categories, dates, authors)
 */
get_header();
?>

<!-- Archive Header -->
<div class="archive-header">
	<div class="container">
		<?php the_archive_title( '<h1 class="archive-title">', '</h1>' ); ?>
		<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
	</div>
</div>

<div class="archive-content">
	<div class="content-area">

		<main id="primary" role="main">
			<?php if ( have_posts() ) : ?>

			<div class="grid-3">
				<?php while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
					<a href="<?php the_permalink(); ?>" class="post-card-image-link">
						<div class="post-card-image">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'fmcg-card', array( 'alt' => esc_attr( get_the_title() ) ) ); ?>
							<?php else : ?>
								<div class="post-card-image-placeholder"><span>&#9679;</span></div>
							<?php endif; ?>
						</div>
					</a>
					<div class="post-card-body">
						<?php fmcg_category_label(); ?>
						<h2 class="post-card-title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h2>
						<p class="post-card-excerpt"><?php the_excerpt(); ?></p>
						<div class="post-card-meta">
							<span class="author-name"><?php the_author(); ?></span>
							<span>&middot;</span>
							<span><?php the_date( 'd M Y' ); ?></span>
						</div>
					</div>
				</article>
				<?php endwhile; ?>
			</div>

			<div class="posts-pagination">
				<?php
				the_posts_pagination( array(
					'mid_size'  => 2,
					'prev_text' => __( '&larr; Prev', 'fmcg-theme' ),
					'next_text' => __( 'Next &rarr;', 'fmcg-theme' ),
				) );
				?>
			</div>

			<?php else : ?>

			<div class="no-results">
				<h2><?php esc_html_e( 'Nothing found', 'fmcg-theme' ); ?></h2>
				<p><?php esc_html_e( 'It looks like nothing matched your search. Try a different search term.', 'fmcg-theme' ); ?></p>
				<?php get_search_form(); ?>
			</div>

			<?php endif; ?>
		</main>

		<?php get_sidebar(); ?>

	</div><!-- .content-area -->
</div><!-- .archive-content -->

<?php get_footer(); ?>
