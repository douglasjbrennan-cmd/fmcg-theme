<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="sr-only" for="s"><?php esc_html_e( 'Search for:', 'fmcg-theme' ); ?></label>
	<input type="search"
	       class="search-field"
	       id="s"
	       name="s"
	       placeholder="<?php esc_attr_e( 'Search articles&hellip;', 'fmcg-theme' ); ?>"
	       value="<?php echo esc_attr( get_search_query() ); ?>">
	<button type="submit" class="search-submit">
		<?php esc_html_e( 'Search', 'fmcg-theme' ); ?>
	</button>
</form>
