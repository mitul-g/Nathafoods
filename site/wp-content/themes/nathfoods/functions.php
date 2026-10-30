<?php
/**
 * Nathfoods theme bootstrap.
 */

defined( 'ABSPATH' ) || exit;

define( 'NF_VERSION', '0.1.0' );
define( 'NF_URI', get_template_directory_uri() );

add_action( 'after_setup_theme', function () {
	load_theme_textdomain( 'nathfoods', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'html5', [ 'search-form', 'comment-form', 'gallery', 'caption', 'style', 'script' ] );
	add_theme_support( 'woocommerce', [ 'thumbnail_image_width' => 480, 'single_image_width' => 720 ] );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
	register_nav_menus( [
		'primary' => __( 'Hauptmenü', 'nathfoods' ),
		'footer'  => __( 'Fußzeile', 'nathfoods' ),
	] );
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'nathfoods', NF_URI . '/assets/css/main.css', [], NF_VERSION );
	wp_enqueue_script( 'motion', NF_URI . '/assets/js/motion.js', [], '12.23.12', true );
	wp_enqueue_script( 'nathfoods', NF_URI . '/assets/js/main.js', [ 'motion', 'jquery' ], NF_VERSION, true );
} );

// Own card and grid styles replace WooCommerce's on the home page and in the catalogue.
add_filter( 'woocommerce_enqueue_styles', function ( $styles ) {
	return ( is_front_page() || is_shop() || is_product_taxonomy() ) ? [] : $styles;
} );

// Cards and the home page use AJAX add to cart everywhere.
add_action( 'wp_enqueue_scripts', function () {
	if ( function_exists( 'WC' ) ) {
		wp_enqueue_script( 'wc-add-to-cart' );
		wp_enqueue_script( 'wc-cart-fragments' );
	}
}, 20 );

// Keeps the header cart count in sync after AJAX add to cart.
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
	$fragments['span.nf-cart-count'] = nf_cart_count_html();
	return $fragments;
} );

// The shop banner carries the title, breadcrumb and description.
add_filter( 'woocommerce_show_page_title', '__return_false' );
add_action( 'init', function () {
	remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
	remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );
} );

// The header has its own DE / EN switch.
add_filter( 'trp_floater_ls_html_v2', '__return_empty_string' );

require get_template_directory() . '/inc/helpers.php';
