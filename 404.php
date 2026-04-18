<?php
/**
 * 404 Not Found template
 */
get_header();
?>

<div class="site-main">
	<div class="container">
		<div class="error-404">
			<div class="error-code">404</div>
			<h2><?php esc_html_e( 'Page Not Found', 'fmcg-theme' ); ?></h2>
			<p><?php esc_html_e( 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'fmcg-theme' ); ?></p>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary">
				<?php esc_html_e( 'Return to Homepage', 'fmcg-theme' ); ?>
			</a>
			<div style="margin-top:2rem;max-width:400px;margin-inline:auto;">
				<?php get_search_form(); ?>
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>
