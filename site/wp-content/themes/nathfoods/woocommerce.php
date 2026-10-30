<?php
get_header();

if ( ! is_singular( 'product' ) ) {
	get_template_part( 'template-parts/shop-banner' );
}
?>
<div class="nf-wrap nf-content nf-shop">
	<?php woocommerce_content(); ?>
</div>
<?php
get_footer();
