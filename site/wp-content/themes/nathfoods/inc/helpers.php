<?php
/**
 * Template helpers.
 */

defined( 'ABSPATH' ) || exit;

function nf_cart_count_html() {
	$count = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	return '<span class="nf-cart-count" data-count="' . esc_attr( $count ) . '">' . esc_html( $count ) . '</span>';
}

/** Inline Lucide icon. Names are theme-internal. */
function nf_icon( $name, $size = 20 ) {
	$paths = [
		'search'  => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
		'user'    => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
		'cart'    => '<circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>',
		'menu'    => '<path d="M4 6h16M4 12h16M4 18h16"/>',
		'close'   => '<path d="M18 6 6 18M6 6l12 12"/>',
		'plus'    => '<path d="M5 12h14M12 5v14"/>',
		'check'   => '<path d="M20 6 9 17l-5-5"/>',
		'left'    => '<path d="M19 12H5M12 19l-7-7 7-7"/>',
		'right'   => '<path d="M5 12h14M12 5l7 7-7 7"/>',
		'chevron' => '<path d="m6 9 6 6 6-6"/>',
		'truck'   => '<path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.62l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/>',
		'leaf'    => '<path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6"/>',
		'shield'  => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>',
	];
	return sprintf(
		'<svg class="nf-icon" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%2$s</svg>',
		(int) $size,
		$paths[ $name ] ?? ''
	);
}

function nf_logo() {
	echo '<a class="nf-logo" href="' . esc_url( home_url( '/' ) ) . '">';
	echo '<img src="' . esc_url( NF_URI . '/assets/img/logo-natha.webp' ) . '" width="360" height="215" alt="' . esc_attr__( 'Natha Foods Startseite', 'nathfoods' ) . '">';
	echo '</a>';
}

/** DE / EN switch. Links come from TranslatePress so each one keeps the current page. */
function nf_language_switcher() {
	if ( ! function_exists( 'trp_custom_language_switcher' ) ) {
		return;
	}
	$current = get_locale();
	echo '<div class="nf-lang" role="group" aria-label="' . esc_attr__( 'Sprache wählen', 'nathfoods' ) . '" data-no-translation>';
	foreach ( trp_custom_language_switcher() as $lang ) {
		$code = strtoupper( substr( $lang['language_code'], 0, 2 ) );
		printf(
			'<a href="%1$s" hreflang="%2$s"%3$s>%4$s</a>',
			esc_url( $lang['current_page_url'] ),
			esc_attr( strtolower( $code ) ),
			$lang['language_code'] === $current ? ' aria-current="true"' : '',
			esc_html( $code )
		);
	}
	echo '</div>';
}

/** Theme image, for sample artwork until the client supplies real photos. */
function nf_img( $slug ) {
	return NF_URI . '/assets/img/products/' . $slug . '.webp';
}

function nf_product_categories() {
	$terms = get_terms( [
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'exclude'    => [ (int) get_option( 'default_product_cat' ) ],
		'orderby'    => 'menu_order',
	] );
	return is_array( $terms ) ? $terms : [];
}

function nf_page_url( $wc_page ) {
	$id = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( $wc_page ) : 0;
	return $id > 0 ? get_permalink( $id ) : '';
}

/** Runs a product query through the shared card template. */
function nf_product_grid( $args, $class = '' ) {
	$query = new WP_Query( array_merge( [
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
	], $args ) );
	if ( ! $query->have_posts() ) {
		return;
	}
	echo '<ul class="products nf-grid ' . esc_attr( $class ) . '">';
	while ( $query->have_posts() ) {
		$query->the_post();
		wc_get_template_part( 'content', 'product' );
	}
	echo '</ul>';
	wp_reset_postdata();
}
