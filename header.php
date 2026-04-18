<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link sr-only" href="#primary"><?php esc_html_e( 'Skip to content', 'fmcg-theme' ); ?></a>

<header id="masthead" class="site-header" role="banner">
	<div class="header-inner">

		<!-- Branding -->
		<div class="site-branding">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<span class="site-title-text">
						<?php
						$name_parts = explode( '.', get_bloginfo( 'name' ), 2 );
						echo esc_html( $name_parts[0] );
						if ( ! empty( $name_parts[1] ) ) {
							echo '<span>.' . esc_html( $name_parts[1] ) . '</span>';
						}
						?>
					</span>
				</a>
			<?php endif; ?>
		</div>

		<!-- Desktop Primary Nav -->
		<nav id="site-navigation" class="primary-nav" aria-label="<?php esc_attr_e( 'Primary Navigation', 'fmcg-theme' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'menu_id'        => 'primary-menu',
				'container'      => false,
				'fallback_cb'    => 'fmcg_fallback_menu',
			) );
			?>
		</nav>

		<!-- Mobile toggle -->
		<button class="nav-toggle" id="nav-toggle" aria-controls="mobile-nav" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle navigation', 'fmcg-theme' ); ?>">
			<span></span>
			<span></span>
			<span></span>
		</button>

	</div>

	<!-- Mobile Nav -->
	<div id="mobile-nav" class="mobile-nav" aria-hidden="true">
		<?php
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'menu_id'        => 'mobile-menu',
			'container'      => false,
			'fallback_cb'    => 'fmcg_fallback_menu',
		) );
		?>
	</div>

	<!-- Category quick-links bar -->
	<div class="category-bar">
		<div class="container">
			<span class="category-bar-label"><?php esc_html_e( 'Explore:', 'fmcg-theme' ); ?></span>
			<?php
			$cats = get_categories( array(
				'orderby'    => 'count',
				'order'      => 'DESC',
				'number'     => 8,
				'hide_empty' => true,
			) );
			foreach ( $cats as $cat ) {
				printf(
					'<a href="%s">%s</a>',
					esc_url( get_category_link( $cat->term_id ) ),
					esc_html( $cat->name )
				);
			}
			?>
		</div>
	</div>

</header><!-- #masthead -->

<?php
function fmcg_fallback_menu() {
	echo '<ul id="primary-menu">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'fmcg-theme' ) . '</a></li>';
	wp_list_pages( array( 'title_li' => '' ) );
	echo '</ul>';
}
?>
