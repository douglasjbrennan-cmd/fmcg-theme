<?php
/**
 * Custom template tags for FMCG.ie theme
 */

if ( ! function_exists( 'fmcg_posted_on' ) ) :
	function fmcg_posted_on() {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() )
		);

		$author_id   = get_the_author_meta( 'ID' );
		$author_name = get_the_author();
		$author_url  = get_author_posts_url( $author_id );

		printf(
			'<span class="posted-on">%s</span><span class="byline"> &mdash; <a href="%s" rel="author">%s</a></span>',
			$time_string,
			esc_url( $author_url ),
			esc_html( $author_name )
		);
	}
endif;

if ( ! function_exists( 'fmcg_post_thumbnail' ) ) :
	function fmcg_post_thumbnail( $size = 'large', $class = '' ) {
		if ( post_password_required() || is_attachment() ) {
			return;
		}

		if ( has_post_thumbnail() ) {
			echo '<div class="post-thumbnail ' . esc_attr( $class ) . '">';
			the_post_thumbnail( $size );
			echo '</div>';
		}
	}
endif;

if ( ! function_exists( 'fmcg_category_label' ) ) :
	function fmcg_category_label() {
		$categories = get_the_category();
		if ( ! empty( $categories ) ) {
			$cat = $categories[0];
			printf(
				'<a href="%s" class="post-card-category">%s</a>',
				esc_url( get_category_link( $cat->term_id ) ),
				esc_html( $cat->name )
			);
		}
	}
endif;

if ( ! function_exists( 'fmcg_single_category_label' ) ) :
	function fmcg_single_category_label() {
		$categories = get_the_category();
		if ( ! empty( $categories ) ) {
			$cat = $categories[0];
			printf(
				'<a href="%s" class="single-category-label">%s</a>',
				esc_url( get_category_link( $cat->term_id ) ),
				esc_html( $cat->name )
			);
		}
	}
endif;
