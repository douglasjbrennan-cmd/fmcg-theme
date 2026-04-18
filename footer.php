<footer id="colophon" class="site-footer" role="contentinfo">

	<!-- Footer Widget Columns -->
	<div class="footer-widgets">
		<div class="container">
			<div class="footer-widgets-grid">

				<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
					<div class="footer-widget">
						<?php if ( is_active_sidebar( 'footer-' . $i ) ) : ?>
							<?php dynamic_sidebar( 'footer-' . $i ); ?>
						<?php else : ?>
							<?php if ( $i === 1 ) : ?>
								<h3 class="footer-widget-title"><?php bloginfo( 'name' ); ?></h3>
								<p style="font-size:.875rem;color:rgba(255,255,255,.55);line-height:1.6;">
									<?php esc_html_e( 'Ireland\'s trade magazine for the FMCG industry — covering advertising, branding, consumer behaviour, pricing, promotions and category management.', 'fmcg-theme' ); ?>
								</p>
							<?php elseif ( $i === 2 ) : ?>
								<h3 class="footer-widget-title"><?php esc_html_e( 'Quick Links', 'fmcg-theme' ); ?></h3>
								<ul>
									<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'fmcg-theme' ); ?></a></li>
									<li><a href="<?php echo esc_url( home_url( '/about-us' ) ); ?>"><?php esc_html_e( 'About Us', 'fmcg-theme' ); ?></a></li>
									<li><a href="<?php echo esc_url( home_url( '/contact-us' ) ); ?>"><?php esc_html_e( 'Contact Us', 'fmcg-theme' ); ?></a></li>
								</ul>
							<?php else : ?>
								<h3 class="footer-widget-title"><?php esc_html_e( 'Categories', 'fmcg-theme' ); ?></h3>
								<ul>
									<?php
									$footer_cats = get_categories( array(
										'orderby'    => 'count',
										'order'      => 'DESC',
										'number'     => 6,
										'hide_empty' => true,
									) );
									foreach ( $footer_cats as $fc ) {
										printf(
											'<li><a href="%s">%s</a></li>',
											esc_url( get_category_link( $fc->term_id ) ),
											esc_html( $fc->name )
										);
									}
									?>
								</ul>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				<?php endfor; ?>

			</div>
		</div>
	</div>

	<!-- Footer Bottom Bar -->
	<div class="container">
		<div class="footer-bottom">
			<p class="footer-copyright">
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>.
				<?php esc_html_e( 'All rights reserved.', 'fmcg-theme' ); ?>
			</p>
			<div class="footer-bottom-links">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'footer',
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => false,
				) );
				?>
			</div>
		</div>
	</div>

</footer><!-- #colophon -->

<?php wp_footer(); ?>
</body>
</html>
