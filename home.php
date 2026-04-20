<?php
/**
 * Homepage template (Blog posts index)
 */
get_header();

// Hero: fetch latest 4 posts (1 main + 3 sidebar)
$hero_posts = get_posts( array(
	'numberposts' => 4,
	'post_status' => 'publish',
) );

$hero_main  = ! empty( $hero_posts[0] ) ? $hero_posts[0] : null;
$hero_sides = array_slice( $hero_posts, 1, 3 );
?>

<!-- ===================================================
     HERO
     =================================================== -->
<?php if ( $hero_main ) : ?>
<section class="hero-section" aria-label="<?php esc_attr_e( 'Featured Story', 'fmcg-theme' ); ?>">
	<div class="container">
		<div class="hero-inner">

			<!-- Main hero post -->
			<article class="hero-main">
				<a href="<?php echo esc_url( get_permalink( $hero_main ) ); ?>" aria-label="<?php echo esc_attr( get_the_title( $hero_main ) ); ?>">
					<?php if ( has_post_thumbnail( $hero_main ) ) : ?>
						<?php echo get_the_post_thumbnail( $hero_main, 'fmcg-hero', array( 'class' => 'hero-thumbnail', 'alt' => esc_attr( get_the_title( $hero_main ) ) ) ); ?>
					<?php else : ?>
						<div class="hero-thumbnail" style="background:linear-gradient(135deg,#1a2744 0%,#2a3f6e 100%);"></div>
					<?php endif; ?>
					<div class="hero-overlay"></div>
					<div class="hero-content">
						<?php
						$cats = get_the_category( $hero_main->ID );
						if ( ! empty( $cats ) ) :
							?>
							<span class="hero-category"><?php echo esc_html( $cats[0]->name ); ?></span>
						<?php endif; ?>
						<h1 class="hero-title"><?php echo esc_html( get_the_title( $hero_main ) ); ?></h1>
						<p class="hero-excerpt"><?php echo esc_html( get_the_excerpt( $hero_main ) ); ?></p>
						<div class="hero-meta">
							<span><?php echo esc_html( get_the_author_meta( 'display_name', $hero_main->post_author ) ); ?></span>
							<span>&middot;</span>
							<span><?php echo esc_html( get_the_date( '', $hero_main ) ); ?></span>
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
							<?php echo get_the_post_thumbnail( $side_post, 'fmcg-hero-side', array( 'class' => 'hero-side-thumb', 'alt' => esc_attr( get_the_title( $side_post ) ) ) ); ?>
						<?php endif; ?>
						<div class="hero-side-body">
							<?php
							$sc = get_the_category( $side_post->ID );
							if ( ! empty( $sc ) ) : ?>
								<div class="hero-side-cat"><?php echo esc_html( $sc[0]->name ); ?></div>
							<?php endif; ?>
							<h2 class="hero-side-title"><?php echo esc_html( get_the_title( $side_post ) ); ?></h2>
						</div>
					</a>
				</article>
				<?php endforeach; ?>
			</aside>
			<?php endif; ?>

		</div>
	</div>
</section>
<?php endif; ?>

<!-- ===================================================
     CATEGORY SECTIONS
     =================================================== -->
<div class="content-sections">
	<div class="container">

		<?php
		$featured_cats = array( 'Insights', 'Branding', 'Pricing' );

		foreach ( $featured_cats as $cat_name ) :
			$cat = get_category_by_slug( sanitize_title( $cat_name ) );
			if ( ! $cat ) {
				$cats_found = get_categories( array(
					'name'       => $cat_name,
					'hide_empty' => true,
					'number'     => 1,
				) );
				$cat = ! empty( $cats_found ) ? $cats_found[0] : null;
			}

			if ( ! $cat ) continue;

			$cat_posts = get_posts( array(
				'numberposts' => 3,
				'category'    => $cat->term_id,
				'post_status' => 'publish',
			) );

			if ( empty( $cat_posts ) ) continue;
		?>

		<section class="content-section">
			<div class="section-header">
				<h2 class="section-title"><?php echo esc_html( $cat->name ); ?></h2>
				<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="section-link">
					<?php esc_html_e( 'View all', 'fmcg-theme' ); ?> &rarr;
				</a>
			</div>
			<div class="grid-3">
				<?php foreach ( $cat_posts as $post ) : setup_postdata( $post ); ?>
				<article class="post-card">
					<a href="<?php echo esc_url( get_permalink( $post ) ); ?>" class="post-card-image-link">
						<div class="post-card-image">
							<?php if ( has_post_thumbnail( $post ) ) : ?>
								<?php echo get_the_post_thumbnail( $post, 'fmcg-card', array( 'alt' => esc_attr( get_the_title( $post ) ) ) ); ?>
							<?php else : ?>
								<div class="post-card-image-placeholder"><span>&#9679;</span></div>
							<?php endif; ?>
						</div>
					</a>
					<div class="post-card-body">
						<?php
						$pc = get_the_category( $post->ID );
						if ( ! empty( $pc ) ) {
							printf(
								'<a href="%s" class="post-card-category">%s</a>',
								esc_url( get_category_link( $pc[0]->term_id ) ),
								esc_html( $pc[0]->name )
							);
						}
						?>
						<h3 class="post-card-title">
							<a href="<?php echo esc_url( get_permalink( $post ) ); ?>"><?php echo esc_html( get_the_title( $post ) ); ?></a>
						</h3>
						<p class="post-card-excerpt"><?php echo esc_html( get_the_excerpt( $post ) ); ?></p>
						<div class="post-card-meta">
							<span class="author-name"><?php echo esc_html( get_the_author_meta( 'display_name', $post->post_author ) ); ?></span>
							<span>&middot;</span>
							<span><?php echo esc_html( get_the_date( 'd M Y', $post ) ); ?></span>
						</div>
					</div>
				</article>
				<?php endforeach; wp_reset_postdata(); ?>
			</div>
		</section>

		<?php endforeach; ?>

	</div>
</div>

<!-- ===================================================
     LATEST POSTS GRID
     =================================================== -->
<section class="latest-posts-section">
	<div class="container">
		<div class="section-header">
			<h2 class="section-title"><?php esc_html_e( 'Latest News', 'fmcg-theme' ); ?></h2>
		</div>
		<div class="grid-4">
			<?php
			$latest_query = new WP_Query( array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => 6,
				'offset'              => 4,
				'orderby'             => 'date',
				'order'               => 'DESC',
				'ignore_sticky_posts' => 1,
			) );
			while ( $latest_query->have_posts() ) : $latest_query->the_post();
			?>
			<article class="post-card">
				<a href="<?php echo esc_url( get_permalink() ); ?>">
					<div class="post-card-image">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php echo get_the_post_thumbnail( null, 'fmcg-card', array( 'alt' => esc_attr( get_the_title() ) ) ); ?>
						<?php else : ?>
							<div class="post-card-image-placeholder"><span>&#9679;</span></div>
						<?php endif; ?>
					</div>
				</a>
				<div class="post-card-body">
					<?php fmcg_category_label(); ?>
					<h3 class="post-card-title">
						<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
					</h3>
					<div class="post-card-meta">
						<span><?php echo esc_html( get_the_date( 'd M Y' ) ); ?></span>
					</div>
				</div>
			</article>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</div>
</section>

<?php get_footer(); ?>
