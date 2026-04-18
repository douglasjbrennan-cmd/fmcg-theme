<?php
/**
 * FMCG.ie theme functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FMCG_VERSION', '1.0.0' );

require_once get_template_directory() . '/inc/template-tags.php';

/* ------------------------------------------------------------------
   Theme Setup
   ------------------------------------------------------------------ */
function fmcg_setup() {
	load_theme_textdomain( 'fmcg-theme', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 60,
		'width'       => 200,
		'flex-width'  => true,
		'flex-height' => true,
	) );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'responsive-embeds' );

	add_image_size( 'fmcg-hero',     1200, 675,  true );
	add_image_size( 'fmcg-card',      600, 338,  true );
	add_image_size( 'fmcg-hero-side', 480, 270,  true );

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'fmcg-theme' ),
		'footer'  => __( 'Footer Menu',  'fmcg-theme' ),
	) );
}
add_action( 'after_setup_theme', 'fmcg_setup' );

/* ------------------------------------------------------------------
   Content Width
   ------------------------------------------------------------------ */
function fmcg_content_width() {
	$GLOBALS['content_width'] = 820;
}
add_action( 'after_setup_theme', 'fmcg_content_width', 0 );

/* ------------------------------------------------------------------
   Enqueue Scripts & Styles
   ------------------------------------------------------------------ */
function fmcg_scripts() {
	wp_enqueue_style(
		'fmcg-style',
		get_stylesheet_uri(),
		array(),
		FMCG_VERSION
	);

	wp_enqueue_script(
		'fmcg-main',
		get_template_directory_uri() . '/js/main.js',
		array(),
		FMCG_VERSION,
		true
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'fmcg_scripts' );

/* ------------------------------------------------------------------
   Widgets
   ------------------------------------------------------------------ */
function fmcg_widgets_init() {
	$shared = array(
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
	);

	register_sidebar( array_merge( $shared, array(
		'name' => __( 'Sidebar', 'fmcg-theme' ),
		'id'   => 'sidebar-1',
	) ) );

	register_sidebar( array_merge( $shared, array(
		'name' => __( 'Footer Column 1', 'fmcg-theme' ),
		'id'   => 'footer-1',
	) ) );

	register_sidebar( array_merge( $shared, array(
		'name' => __( 'Footer Column 2', 'fmcg-theme' ),
		'id'   => 'footer-2',
	) ) );

	register_sidebar( array_merge( $shared, array(
		'name' => __( 'Footer Column 3', 'fmcg-theme' ),
		'id'   => 'footer-3',
	) ) );
}
add_action( 'widgets_init', 'fmcg_widgets_init' );

/* ------------------------------------------------------------------
   Excerpt
   ------------------------------------------------------------------ */
function fmcg_excerpt_length() {
	return 20;
}
add_filter( 'excerpt_length', 'fmcg_excerpt_length' );

function fmcg_excerpt_more( $more ) {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'fmcg_excerpt_more' );

/* ------------------------------------------------------------------
   Body Classes
   ------------------------------------------------------------------ */
function fmcg_body_classes( $classes ) {
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}
	if ( is_singular() && ! is_page() ) {
		$classes[] = 'single-post-layout';
	}
	return $classes;
}
add_filter( 'body_class', 'fmcg_body_classes' );
