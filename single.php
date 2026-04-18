<?php
/**
 * Single post template
 */
get_header();
?>

<div class="site-main">
	<div class="content-area">

		<main id="primary" role="main">
			<?php while ( have_posts() ) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post-wrapper' ); ?>>

				<!-- Featured Image Hero -->
				<?php if ( has_post_thumbnail() ) : ?>
					<?php the_post_thumbnail( 'fmcg-hero', array( 'class' => 'single-hero-image', 'alt' => esc_attr( get_the_title() ) ) ); ?>
				<?php endif; ?>

				<div class="single-post-body">

					<!-- Article Header -->
					<header class="single-post-header">
						<?php fmcg_single_category_label(); ?>
						<h1 class="single-post-title"><?php the_title(); ?></h1>
						<div class="single-post-meta">
							<?php fmcg_posted_on(); ?>
						</div>
					</header>

					<!-- Content -->
					<div class="entry-content">
						<?php the_content(); ?>
						<?php
						wp_link_pages( array(
							'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'fmcg-theme' ),
							'after'  => '</div>',
						) );
						?>
					</div>

					<!-- Share Buttons -->
					<div class="share-buttons">
						<p class="share-buttons-label"><?php esc_html_e( 'Share this article:', 'fmcg-theme' ); ?></p>
						<div class="share-buttons-list">
							<?php
							$post_url   = rawurlencode( get_permalink() );
							$post_title = rawurlencode( get_the_title() );
							?>
							<a href="https://twitter.com/intent/tweet?url=<?php echo $post_url; ?>&text=<?php echo $post_title; ?>"
							   class="share-btn share-btn-twitter"
							   target="_blank"
							   rel="noopener noreferrer">
								&#120143; / X
							</a>
							<a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $post_url; ?>"
							   class="share-btn share-btn-linkedin"
							   target="_blank"
							   rel="noopener noreferrer">
								LinkedIn
							</a>
							<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $post_url; ?>"
							   class="share-btn share-btn-facebook"
							   target="_blank"
							   rel="noopener noreferrer">
								Facebook
							</a>
						</div>
					</div>

					<!-- Related Posts -->
					<?php
					$cats = get_the_category();
					if ( ! empty( $cats ) ) :
						$related = get_posts( array(
							'category'    => $cats[0]->term_id,
							'numberposts' => 3,
							'post__not_in' => array( get_the_ID() ),
							'post_status' => 'publish',
						) );
						if ( ! empty( $related ) ) :
					?>
					<div class="related-posts">
						<h3 class="section-title"><?php esc_html_e( 'Related Articles', 'fmcg-theme' ); ?></h3>
						<div class="grid-3" style="margin-top:1rem;">
							<?php foreach ( $related as $rpost ) : ?>
							<article class="post-card">
								<a href="<?php echo esc_url( get_permalink( $rpost ) ); ?>">
									<div class="post-card-image">
										<?php if ( has_post_thumbnail( $rpost ) ) : ?>
											<?php echo get_the_post_thumbnail( $rpost, 'fmcg-card', array( 'alt' => esc_attr( get_the_title( $rpost ) ) ) ); ?>
										<?php else : ?>
											<div class="post-card-image-placeholder"><span>&#9679;</span></div>
										<?php endif; ?>
									</div>
								</a>
								<div class="post-card-body">
									<h4 class="post-card-title">
										<a href="<?php echo esc_url( get_permalink( $rpost ) ); ?>"><?php echo esc_html( get_the_title( $rpost ) ); ?></a>
									</h4>
									<div class="post-card-meta">
										<span><?php echo esc_html( get_the_date( 'd M Y', $rpost ) ); ?></span>
									</div>
								</div>
							</article>
							<?php endforeach; wp_reset_postdata(); ?>
						</div>
					</div>
					<?php endif; endif; ?>

				</div><!-- .single-post-body -->

			</article><!-- .single-post-wrapper -->

			<!-- Comments -->
			<?php if ( comments_open() || get_comments_number() ) : ?>
			<div class="comments-area">
				<?php comments_template(); ?>
			</div>
			<?php endif; ?>

			<?php endwhile; ?>
		</main>

		<?php get_sidebar(); ?>

	</div><!-- .content-area -->
</div><!-- .site-main -->

<?php get_footer(); ?>
