<?php
/**
 * Sidebar template
 */
?>
<aside id="secondary" class="widget-area sidebar" role="complementary">

	<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	<?php else : ?>

		<!-- Search -->
		<section class="widget widget_search">
			<h3 class="widget-title"><?php esc_html_e( 'Search', 'fmcg-theme' ); ?></h3>
			<?php get_search_form(); ?>
		</section>

		<!-- Recent Posts fallback -->
		<section class="widget">
			<h3 class="widget-title"><?php esc_html_e( 'Recent Articles', 'fmcg-theme' ); ?></h3>
			<ul>
				<?php
				$recent = get_posts( array(
					'numberposts' => 5,
					'post_status' => 'publish',
				) );
				foreach ( $recent as $post ) {
					printf(
						'<li><a href="%s">%s</a></li>',
						esc_url( get_permalink( $post ) ),
						esc_html( get_the_title( $post ) )
					);
				}
				wp_reset_postdata();
				?>
			</ul>
		</section>

		<!-- Categories -->
		<section class="widget">
			<h3 class="widget-title"><?php esc_html_e( 'Categories', 'fmcg-theme' ); ?></h3>
			<ul>
				<?php
				wp_list_categories( array(
					'title_li'   => '',
					'show_count' => true,
					'hide_empty' => true,
				) );
				?>
			</ul>
		</section>

	<?php endif; ?>

</aside>
