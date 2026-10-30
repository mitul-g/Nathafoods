<?php
/**
 * Product card. Shared by the home page and the catalogue.
 */

defined( 'ABSPATH' ) || exit;

global $product;
if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}

$link    = get_permalink( $product->get_id() );
$cats    = wp_get_post_terms( $product->get_id(), 'product_cat', [ 'fields' => 'slugs' ] );
$gzd     = function_exists( 'wc_gzd_get_product' ) ? wc_gzd_get_product( $product ) : null;
$ship_id = wc_get_page_id( 'shipping_costs' );
?>
<li <?php wc_product_class( 'nf-card', $product ); ?> data-cats="<?php echo esc_attr( implode( ' ', $cats ) ); ?>" data-reveal>
	<a class="nf-card__media" href="<?php echo esc_url( $link ); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( $product->is_on_sale() ) : ?>
			<span class="nf-badge"><?php esc_html_e( 'Angebot', 'nathfoods' ); ?></span>
		<?php endif; ?>
		<?php echo $product->get_image( 'woocommerce_thumbnail', [ 'loading' => 'lazy', 'alt' => $product->get_name() ] ); // phpcs:ignore ?>
	</a>
	<div class="nf-card__body">
		<?php echo wc_get_rating_html( $product->get_average_rating(), $product->get_rating_count() ); // phpcs:ignore ?>
		<h3 class="nf-card__title"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
		<p class="nf-card__unit"><?php echo esc_html( $product->get_meta( '_nf_pack' ) ); ?></p>
		<div class="nf-card__buy">
			<span class="nf-price"><?php echo $product->get_price_html(); // phpcs:ignore ?></span>
			<?php if ( $product->is_purchasable() && $product->is_in_stock() ) : ?>
				<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>" data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>" class="nf-add add_to_cart_button ajax_add_to_cart" rel="nofollow" aria-label="<?php echo esc_attr( sprintf( __( '%s in den Warenkorb legen', 'nathfoods' ), $product->get_name() ) ); ?>">
					<span class="nf-add__plus"><?php echo nf_icon( 'plus', 18 ); // phpcs:ignore ?></span>
					<span class="nf-add__check"><?php echo nf_icon( 'check', 18 ); // phpcs:ignore ?></span>
				</a>
			<?php endif; ?>
		</div>
		<p class="nf-card__legal">
			<?php if ( $gzd && $gzd->has_unit() ) : ?>
				<span class="nf-card__baseprice"><?php echo wp_kses_post( $gzd->get_unit_price_html() ); ?></span>
			<?php endif; ?>
			<span><?php esc_html_e( 'inkl. MwSt., zzgl.', 'nathfoods' ); ?>
				<?php if ( $ship_id > 0 ) : ?>
					<a href="<?php echo esc_url( get_permalink( $ship_id ) ); ?>"><?php esc_html_e( 'Versand', 'nathfoods' ); ?></a>
				<?php else : ?>
					<?php esc_html_e( 'Versand', 'nathfoods' ); ?>
				<?php endif; ?>
			</span>
		</p>
	</div>
</li>
