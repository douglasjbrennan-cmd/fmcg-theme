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
<?php
$homepage_cats = array(
	array( 'name' => 'Branding',            'color' => '#c0392b' ),
	array( 'name' => 'Advertising',         'color' => '#1565c0' ),
	array( 'name' => 'Consumer Behaviour',  'color' => '#6a1b9a' ),
	array( 'name' => 'Pricing',             'color' => '#2e7d32' ),
	array( 'name' => 'Promotions',          'color' => '#e65100' ),
	array( 'name' => 'Category Management', 'color' => '#00838f' ),
);

$section_index = 0;
?>
<div class="homepage-categories">
<?php foreach ( $homepage_cats as $cat_config ) :
	$cat = get_category_by_slug( sanitize_title( $cat_config['name'] ) );
	if ( ! $cat ) {
		$found = get_terms( array(
			'taxonomy'   => 'category',
			'name'       => $cat_config['name'],
			'hide_empty' => false,
			'number'     => 1,
		) );
		$cat = ( ! empty( $found ) && ! is_wp_error( $found ) ) ? $found[0] : null;
	}
	if ( ! $cat ) continue;

	$cat_query = new WP_Query( array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 3,
		'cat'                 => $cat->term_id,
		'orderby'             => 'date',
		'order'               => 'DESC',
		'ignore_sticky_posts' => 1,
	) );

	if ( ! $cat_query->have_posts() ) continue;
	$section_index++;
?>
<section class="cat-section<?php echo $section_index % 2 === 0 ? ' cat-section--alt' : ''; ?>"
         style="--cat-color: <?php echo esc_attr( $cat_config['color'] ); ?>">
	<div class="container">
		<div class="cat-section-header">
			<h2 class="cat-section-title"><?php echo esc_html( $cat->name ); ?></h2>
			<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="cat-section-link">
				<?php esc_html_e( 'View all', 'fmcg-theme' ); ?> &rarr;
			</a>
		</div>
		<div class="cat-grid">
			<?php $card_n = 0; while ( $cat_query->have_posts() ) : $cat_query->the_post(); $card_n++; ?>
			<article class="cat-card<?php echo $card_n === 1 ? ' cat-card--lead' : ' cat-card--side'; ?>">
				<a href="<?php echo esc_url( get_permalink() ); ?>" class="cat-card-thumb-link">
					<div class="cat-card-thumb">
						<?php if ( has_post_thumbnail() ) :
							the_post_thumbnail( 'fmcg-card', array( 'alt' => esc_attr( get_the_title() ) ) );
						else : ?>
							<div class="cat-card-no-image"></div>
						<?php endif; ?>
					</div>
				</a>
				<div class="cat-card-body">
					<h3 class="cat-card-title">
						<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
					</h3>
					<p class="cat-card-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<p class="cat-card-date"><?php echo esc_html( get_the_date( 'd M Y' ) ); ?></p>
				</div>
			</article>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</div>
</section>
<?php endforeach; ?>
</div>

<?php get_footer(); ?>
