<?php
/**
 * Banner above every shop listing: shop, category, tag and search results.
 * A category uses its own image and description, both editable in the admin.
 */

defined( 'ABSPATH' ) || exit;

$term  = is_product_taxonomy() ? get_queried_object() : null;
$thumb = $term ? (int) get_term_meta( $term->term_id, 'thumbnail_id', true ) : 0;
$lead  = $term && $term->description ? $term->description : __( 'Indische Lebensmittel aus Leipzig, temperaturgeschützt in ganz Deutschland geliefert.', 'nathfoods' );
?>
<section class="nf-wrap">
	<div class="nf-shop-banner">
		<div class="nf-shop-banner__copy">
			<?php woocommerce_breadcrumb(); ?>
			<h1><?php woocommerce_page_title(); ?></h1>
			<p><?php echo wp_kses_post( $lead ); ?></p>
		</div>
		<div class="nf-shop-banner__art" aria-hidden="true">
			<span class="nf-shop-banner__disc"></span>
			<?php if ( $thumb ) : ?>
				<?php echo wp_get_attachment_image( $thumb, 'woocommerce_thumbnail', false, [ 'alt' => '', 'class' => 'is-single' ] ); ?>
			<?php else : ?>
				<img class="a" src="<?php echo esc_url( nf_img( 'mango' ) ); ?>" alt="" width="600" height="600">
				<img class="b" src="<?php echo esc_url( nf_img( 'spinach' ) ); ?>" alt="" width="600" height="600">
				<img class="c" src="<?php echo esc_url( nf_img( 'tomato' ) ); ?>" alt="" width="600" height="600">
			<?php endif; ?>
		</div>
	</div>
</section>
