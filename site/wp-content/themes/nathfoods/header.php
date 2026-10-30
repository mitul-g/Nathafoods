<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script>document.documentElement.classList.add('nf-js');setTimeout(function(){if(!window.NF_READY){document.documentElement.classList.remove('nf-js');}},3000);</script>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="nf-skip" href="#main"><?php esc_html_e( 'Zum Inhalt springen', 'nathfoods' ); ?></a>

<header class="nf-header" id="nf-header">
	<div class="nf-wrap nf-header__row">
		<button class="nf-iconbtn nf-menu-toggle" type="button" aria-expanded="false" aria-controls="nf-drawer" aria-label="<?php esc_attr_e( 'Menü öffnen', 'nathfoods' ); ?>"><?php echo nf_icon( 'menu', 22 ); // phpcs:ignore ?></button>
		<?php nf_logo(); ?>

		<nav class="nf-nav" aria-label="<?php esc_attr_e( 'Hauptmenü', 'nathfoods' ); ?>">
			<ul>
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"<?php echo is_front_page() ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'Start', 'nathfoods' ); ?></a></li>
				<li class="nf-has-mega">
					<a href="<?php echo esc_url( nf_page_url( 'shop' ) ); ?>"<?php echo is_woocommerce() ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'Sortiment', 'nathfoods' ); ?> <?php echo nf_icon( 'chevron', 14 ); // phpcs:ignore ?></a>
					<div class="nf-mega">
						<ul>
							<?php foreach ( nf_product_categories() as $term ) : ?>
								<li><a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</li>
				<li><a href="<?php echo esc_url( home_url( '/#angebote' ) ); ?>"><?php esc_html_e( 'Angebote', 'nathfoods' ); ?></a></li>
				<li><a href="<?php echo esc_url( add_query_arg( 'orderby', 'date', nf_page_url( 'shop' ) ) ); ?>"><?php esc_html_e( 'Neuheiten', 'nathfoods' ); ?></a></li>
				<?php $nf_delivery = get_page_by_path( 'versandarten' ); ?>
				<?php if ( $nf_delivery ) : ?>
					<li><a href="<?php echo esc_url( get_permalink( $nf_delivery ) ); ?>"<?php echo is_page( $nf_delivery->ID ) ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'Lieferung', 'nathfoods' ); ?></a></li>
				<?php endif; ?>
			</ul>
		</nav>

		<form class="nf-search" role="search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="nf-s"><?php esc_html_e( 'Produkte suchen', 'nathfoods' ); ?></label>
			<input id="nf-s" type="search" name="s" placeholder="<?php esc_attr_e( 'Produkte suchen', 'nathfoods' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
			<input type="hidden" name="post_type" value="product">
			<button type="submit" aria-label="<?php esc_attr_e( 'Suchen', 'nathfoods' ); ?>"><?php echo nf_icon( 'search', 18 ); // phpcs:ignore ?></button>
		</form>

		<div class="nf-actions">
			<?php nf_language_switcher(); ?>
			<a class="nf-action" href="<?php echo esc_url( nf_page_url( 'myaccount' ) ); ?>"><?php echo nf_icon( 'user' ); // phpcs:ignore ?><span><?php esc_html_e( 'Konto', 'nathfoods' ); ?></span></a>
			<a class="nf-action nf-cart" href="<?php echo esc_url( nf_page_url( 'cart' ) ); ?>"><?php echo nf_icon( 'bag' ); // phpcs:ignore ?><span><?php esc_html_e( 'Warenkorb', 'nathfoods' ); ?></span><?php echo nf_cart_count_html(); // phpcs:ignore ?></a>
		</div>
	</div>
</header>

<div class="nf-drawer" id="nf-drawer" hidden>
	<div class="nf-drawer__panel">
		<button class="nf-iconbtn nf-drawer__close" type="button" aria-label="<?php esc_attr_e( 'Menü schließen', 'nathfoods' ); ?>"><?php echo nf_icon( 'close', 22 ); // phpcs:ignore ?></button>
		<ul>
			<li><a href="<?php echo esc_url( nf_page_url( 'shop' ) ); ?>"><strong><?php esc_html_e( 'Gesamtes Sortiment', 'nathfoods' ); ?></strong></a></li>
			<?php foreach ( nf_product_categories() as $term ) : ?>
				<li><a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a></li>
			<?php endforeach; ?>
			<li><a href="<?php echo esc_url( nf_page_url( 'myaccount' ) ); ?>"><?php esc_html_e( 'Konto', 'nathfoods' ); ?></a></li>
		</ul>
		<?php nf_language_switcher(); ?>
	</div>
</div>

<main id="main">
