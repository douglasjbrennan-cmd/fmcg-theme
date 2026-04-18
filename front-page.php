<?php
/**
 * Front page template — works whether WordPress is set to show
 * a static front page OR the blog posts index.
 * Uses explicit WP_Query so the main loop setting doesn't matter.
 */
get_header();

// Fetch latest posts explicitly (bypasses front-page loop issues)
$hero_query = new WP_Query( array(
	'posts_per_page' => 4,
	'post_status'    => 'publish',
	'ignore_sticky_posts' => false,
) );

$hero_posts = $hero_query->posts;
$hero_main  = ! empty( $hero_posts[0] ) ? $hero_posts[0] : null;
$hero_sides = array_slice( $hero_posts, 1, 3 );
wp_reset_postdata();
?>

<!-- ===================================================
     HERO
     =================================================== -->
<?php if ( $hero_main ) : ?>
<section class="hero-section" aria-label="<?php esc_attr_e( 'Featured Story', 'fmcg-theme' ); ?>">
	<div class="container">
		<div class="hero-inner">

			<!-- Main featured post -->
			<article class="hero-main">
				<a href="<?php echo esc_url( get_permalink( $hero_main ) ); ?>"
				   aria-label="<?php echo esc_attr( get_the_title( $hero_main ) ); ?>">

					<?php if ( has_post_thumbnail( $hero_main ) ) : ?>
						<?php echo get_the_post_thumbnail(
							$hero_main,
							'fmcg-hero',
							array( 'class' => 'hero-thumbnail', 'alt' => esc_attr( get_the_title( $hero_main ) ) )
						); ?>
					<?php else : ?>
						<div class="hero-thumbnail" style="background:linear-gradient(135deg,#1a2744 0%,#2a3f6e 100%);position:absolute;inset:0;"></div>
					<?php endif; ?>

					<div class="hero-overlay"></div>
					<div class="hero-content">
						<?php
						$cats = get_the_category( $hero_main->ID );
						if ( ! empty( $cats ) ) :
						?>
							<a href="<?php echo esc_url( get_category_link( $cats[0]->term_id ) ); ?>"
							   class="hero-category"><?php echo esc_html( $cats[0]->name ); ?></a>
						<?php endif; ?>

						<h1 class="hero-title"><?php echo esc_html( get_the_title( $hero_main ) ); ?></h1>

						<p class="hero-excerpt">
							<?php echo esc_html( wp_trim_words( get_the_excerpt( $hero_main ), 25, '&hellip;' ) ); ?>
						</p>

						<div class="hero-meta">
							<span><?php echo esc_html( get_the_author_meta( 'display_name', $hero_main->post_author ) ); ?></span>
							<span>&middot;</span>
							<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C, $hero_main ) ); ?>">
								<?php echo esc_html( get_the_date( 'd M Y', $hero_main ) ); ?>
							</time>
						</div>
					</div>
				</a>
			</article>

			<!-- Sidebar hero posts -->
			<?php if ( ! empty( $hero_sides ) ) : ?>
			<aside class="hero-sidebar" aria-label="<?php esc_attr_e( 'More Stories', 'fmcg-theme' ); ?>">
				<?php foreach ( $hero_sides as $side_post ) : ?>
				<article class="hero-side-post">
					<a href="<?php echo esc_url( get_permalink( $side_post ) ); ?>">
						<?php if ( has_post_thumbnail( $side_post ) ) : ?>
							<?php echo get_the_post_thumbnail(
								$side_post,
								'fmcg-hero-side',
								array( 'class' => 'hero-side-thumb', 'alt' => esc_attr( get_the_title( $side_post ) ) )
							); ?>
						<?php else : ?>
							<div class="hero-side-thumb" style="background:linear-gradient(135deg,#1a2744 0%,#2a3f6e 100%);height:140px;"></div>
						<?php endif; ?>
						<div class="hero-side-body">
							<?php
							$sc = get_the_category( $side_post->ID );
							if ( ! empty( $sc ) ) :
							?>
								<div class="hero-side-cat"><?php echo esc_html( $sc[0]->name ); ?></div>
							<?php endif; ?>
							<h2 class="hero-side-title"><?php echo esc_html( get_the_title( $side_post ) ); ?></h2>
						</div>
					</a>
				</article>
				<?php endforeach; ?>
			</aside>
			<?php endif; ?>

		</div><!-- .hero-inner -->
	</div><!-- .container -->
</section>
<?php endif; ?>

<!-- ===================================================
     RECENT POSTS GRID (always visible, no dependency on loop)
     =================================================== -->
<section class="latest-posts-section" style="padding:2.5rem 0;">
	<div class="container">
		<div class="section-header">
			<h2 class="section-title"><?php esc_html_e( 'Latest News &amp; Insights', 'fmcg-theme' ); ?></h2>
		</div>

		<?php
		$recent_query = new WP_Query( array(
			'posts_per_page' => 8,
			'post_status'    => 'publish',
			'offset'         => 4,
		) );
		?>

		<?php if ( $recent_query->have_posts() ) : ?>
		<div class="grid-4">
			<?php while ( $recent_query->have_posts() ) : $recent_query->the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>

				<a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
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

					<h3 class="post-card-title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h3>

					<p class="post-card-excerpt">
						<?php echo esc_html( wp_trim_words( get_the_excerpt(), 18, '&hellip;' ) ); ?>
					</p>

					<div class="post-card-meta">
						<span class="author-name"><?php the_author(); ?></span>
						<span>&middot;</span>
						<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
							<?php the_date( 'd M Y' ); ?>
						</time>
					</div>
				</div>

			</article>
			<?php endwhile; wp_reset_postdata(); ?>
		</div><!-- .grid-4 -->

		<?php else : ?>
		<!-- Fallback: no posts beyond offset — show first 8 instead -->
		<?php
		wp_reset_postdata();
		$fallback_query = new WP_Query( array(
			'posts_per_page' => 8,
			'post_status'    => 'publish',
		) );
		if ( $fallback_query->have_posts() ) :
		?>
		<div class="grid-4">
			<?php while ( $fallback_query->have_posts() ) : $fallback_query->the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
				<a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
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
					<h3 class="post-card-title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h3>
					<p class="post-card-excerpt">
						<?php echo esc_html( wp_trim_words( get_the_excerpt(), 18, '&hellip;' ) ); ?>
					</p>
					<div class="post-card-meta">
						<span class="author-name"><?php the_author(); ?></span>
						<span>&middot;</span>
						<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
							<?php the_date( 'd M Y' ); ?>
						</time>
					</div>
				</div>
			</article>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
		<?php endif; ?>
		<?php endif; ?>

	</div><!-- .container -->
</section>

<?php get_footer(); ?>
