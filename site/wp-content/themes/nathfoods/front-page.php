<?php
/**
 * Home page.
 */

get_header();

$shop_url = nf_page_url( 'shop' );
$cats     = nf_product_categories();
?>

<section class="nf-hero">
	<div class="nf-wrap nf-hero__inner">
		<div class="nf-hero__copy" data-hero>
			<p class="nf-eyebrow"><?php esc_html_e( 'Natha Foods Leipzig', 'nathfoods' ); ?></p>
			<h1><?php echo wp_kses( __( 'Authentische indische Lebensmittel, <em>schnell geliefert</em>', 'nathfoods' ), [ 'em' => [] ] ); ?></h1>
			<p class="nf-hero__lead"><?php esc_html_e( 'Frisch gemahlenes Chakki-Mehl, regionale Masalas und frische Zutaten, temperaturgeschützt in ganz Deutschland geliefert.', 'nathfoods' ); ?></p>
			<a class="nf-btn nf-btn--lime" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Jetzt einkaufen', 'nathfoods' ); ?> <?php echo nf_icon( 'right', 18 ); // phpcs:ignore ?></a>
		</div>
		<div class="nf-hero__art" aria-hidden="true" data-hero-art>
			<span class="nf-hero__disc"></span>
			<img class="a" src="<?php echo esc_url( nf_img( 'mango' ) ); ?>" alt="" width="600" height="600">
			<img class="b" src="<?php echo esc_url( nf_img( 'spinach' ) ); ?>" alt="" width="600" height="600">
			<img class="c" src="<?php echo esc_url( nf_img( 'tomato' ) ); ?>" alt="" width="600" height="600">
			<img class="d" src="<?php echo esc_url( nf_img( 'chilli' ) ); ?>" alt="" width="600" height="600">
			<img class="e" src="<?php echo esc_url( nf_img( 'ginger' ) ); ?>" alt="" width="600" height="600">
		</div>
	</div>
</section>

<section class="nf-trust nf-wrap" aria-label="<?php esc_attr_e( 'Unsere Leistungen', 'nathfoods' ); ?>">
	<div><?php echo nf_icon( 'truck', 24 ); // phpcs:ignore ?><p><strong><?php esc_html_e( 'Versand am selben Tag', 'nathfoods' ); ?></strong><span><?php esc_html_e( 'bei Bestellung bis 12:00 Uhr', 'nathfoods' ); ?></span></p></div>
	<div><?php echo nf_icon( 'leaf', 24 ); // phpcs:ignore ?><p><strong><?php esc_html_e( 'Temperaturgeschützt', 'nathfoods' ); ?></strong><span><?php esc_html_e( 'Versand frischer Ware aus Leipzig', 'nathfoods' ); ?></span></p></div>
	<div><?php echo nf_icon( 'shield', 24 ); // phpcs:ignore ?><p><strong><?php esc_html_e( 'Sichere Zahlung', 'nathfoods' ); ?></strong><span><?php esc_html_e( 'mit Karte oder PayPal', 'nathfoods' ); ?></span></p></div>
</section>

<?php if ( $cats ) : ?>
<section class="nf-section nf-wrap">
	<div class="nf-section__head">
		<h2><?php echo wp_kses( __( 'Beliebte <em>Kategorien</em>', 'nathfoods' ), [ 'em' => [] ] ); ?></h2>
		<div class="nf-arrows" data-scroll-target="#nf-cats">
			<button class="nf-iconbtn" type="button" data-dir="-1" aria-label="<?php esc_attr_e( 'Zurück', 'nathfoods' ); ?>"><?php echo nf_icon( 'left', 18 ); // phpcs:ignore ?></button>
			<button class="nf-iconbtn is-accent" type="button" data-dir="1" aria-label="<?php esc_attr_e( 'Weiter', 'nathfoods' ); ?>"><?php echo nf_icon( 'right', 18 ); // phpcs:ignore ?></button>
		</div>
	</div>
	<ul class="nf-cats" id="nf-cats">
		<?php foreach ( $cats as $term ) : ?>
			<?php $thumb = get_term_meta( $term->term_id, 'thumbnail_id', true ); ?>
			<li data-reveal>
				<a href="<?php echo esc_url( get_term_link( $term ) ); ?>">
					<span class="nf-cats__img"><?php echo wp_get_attachment_image( $thumb, 'woocommerce_thumbnail', false, [ 'alt' => '', 'loading' => 'lazy' ] ); ?></span>
					<span class="nf-cats__name"><?php echo esc_html( $term->name ); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
<?php endif; ?>

<section class="nf-section nf-wrap">
	<div class="nf-section__head">
		<h2><?php echo wp_kses( __( 'Heute frisch <em>für dich</em>', 'nathfoods' ), [ 'em' => [] ] ); ?></h2>
		<a class="nf-btn nf-btn--lime nf-btn--sm" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Alle ansehen', 'nathfoods' ); ?></a>
	</div>
	<div class="nf-tabs" role="group" aria-label="<?php esc_attr_e( 'Nach Kategorie filtern', 'nathfoods' ); ?>" data-filter="#nf-picks">
		<button type="button" class="is-active" aria-pressed="true" data-cat=""><?php esc_html_e( 'Alle', 'nathfoods' ); ?></button>
		<?php foreach ( array_slice( $cats, 0, 5 ) as $term ) : ?>
			<button type="button" aria-pressed="false" data-cat="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></button>
		<?php endforeach; ?>
	</div>
	<div id="nf-picks">
		<?php nf_product_grid( [ 'posts_per_page' => 12, 'orderby' => 'menu_order title', 'order' => 'ASC' ] ); ?>
	</div>
</section>

<?php $sale_ids = wc_get_product_ids_on_sale(); ?>
<?php if ( $sale_ids ) : ?>
<section class="nf-section nf-section--tint" id="angebote">
	<div class="nf-wrap">
		<div class="nf-section__head">
			<h2><?php echo wp_kses( __( 'Frische <em>Angebote</em>', 'nathfoods' ), [ 'em' => [] ] ); ?></h2>
			<div class="nf-arrows" data-scroll-target="#nf-deals .nf-grid">
				<button class="nf-iconbtn" type="button" data-dir="-1" aria-label="<?php esc_attr_e( 'Zurück', 'nathfoods' ); ?>"><?php echo nf_icon( 'left', 18 ); // phpcs:ignore ?></button>
				<button class="nf-iconbtn is-accent" type="button" data-dir="1" aria-label="<?php esc_attr_e( 'Weiter', 'nathfoods' ); ?>"><?php echo nf_icon( 'right', 18 ); // phpcs:ignore ?></button>
			</div>
		</div>
		<div id="nf-deals">
			<?php nf_product_grid( [ 'post__in' => $sale_ids, 'posts_per_page' => 8 ], 'nf-grid--row' ); ?>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="nf-section nf-wrap nf-why">
	<div class="nf-why__art" aria-hidden="true" data-reveal>
		<span class="nf-why__blob"></span>
		<img src="<?php echo esc_url( nf_img( 'basmati-rice' ) ); ?>" alt="" width="600" height="600" loading="lazy">
		<img src="<?php echo esc_url( nf_img( 'aubergine' ) ); ?>" alt="" width="600" height="600" loading="lazy">
		<img src="<?php echo esc_url( nf_img( 'lime' ) ); ?>" alt="" width="600" height="600" loading="lazy">
	</div>
	<div data-reveal>
		<h2><?php echo wp_kses( __( 'Warum bei <em>Natha Foods</em> einkaufen?', 'nathfoods' ), [ 'em' => [] ] ); ?></h2>
		<p><?php esc_html_e( 'Alles für die indische Küche an einem Ort, direkt aus Leipzig zu dir nach Hause.', 'nathfoods' ); ?></p>
		<ul class="nf-checks">
			<li><?php echo nf_icon( 'check', 18 ); // phpcs:ignore ?><?php esc_html_e( 'Chakki-Mehl, regionale Masalas und frische Zutaten', 'nathfoods' ); ?></li>
			<li><?php echo nf_icon( 'check', 18 ); // phpcs:ignore ?><?php esc_html_e( 'Klare Preise mit Grundpreis pro Kilo oder Liter', 'nathfoods' ); ?></li>
			<li><?php echo nf_icon( 'check', 18 ); // phpcs:ignore ?><?php esc_html_e( 'Bestellungen und Rechnungen immer in deinem Konto', 'nathfoods' ); ?></li>
			<li><?php echo nf_icon( 'check', 18 ); // phpcs:ignore ?><?php esc_html_e( 'Zahlung per Karte oder PayPal', 'nathfoods' ); ?></li>
		</ul>
		<a class="nf-btn nf-btn--lime" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Zum Sortiment', 'nathfoods' ); ?> <?php echo nf_icon( 'right', 18 ); // phpcs:ignore ?></a>
	</div>
</section>

<section class="nf-section nf-wrap">
	<div class="nf-section__head nf-section__head--center">
		<h2><?php echo wp_kses( __( 'Das sagen unsere <em>Kunden</em>', 'nathfoods' ), [ 'em' => [] ] ); ?></h2>
		<p class="nf-placeholder-note"><?php esc_html_e( 'Platzhalter: vor dem Launch durch echte Kundenbewertungen ersetzen.', 'nathfoods' ); ?></p>
	</div>
	<ul class="nf-reviews">
		<?php for ( $i = 1; $i <= 6; $i++ ) : ?>
			<li data-reveal>
				<p><?php esc_html_e( 'Hier steht später eine echte Bewertung einer Kundin oder eines Kunden.', 'nathfoods' ); ?></p>
				<span class="nf-reviews__who"><?php echo esc_html( sprintf( __( 'Kundenname %d', 'nathfoods' ), $i ) ); ?></span>
			</li>
		<?php endfor; ?>
	</ul>
</section>

<section class="nf-wrap">
	<div class="nf-cta" data-reveal>
		<div>
			<h2><?php echo wp_kses( __( 'Bereit für deinen <em>Einkauf?</em>', 'nathfoods' ), [ 'em' => [] ] ); ?></h2>
			<p><?php esc_html_e( 'Stöbere durch das Sortiment und lass dir alles nach Hause liefern.', 'nathfoods' ); ?></p>
			<a class="nf-btn nf-btn--ink" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Zu den Produkten', 'nathfoods' ); ?></a>
		</div>
		<div class="nf-cta__art" aria-hidden="true">
			<img src="<?php echo esc_url( nf_img( 'tomato' ) ); ?>" alt="" width="600" height="600" loading="lazy">
			<img src="<?php echo esc_url( nf_img( 'avocado' ) ); ?>" alt="" width="600" height="600" loading="lazy">
			<img src="<?php echo esc_url( nf_img( 'potatoes' ) ); ?>" alt="" width="600" height="600" loading="lazy">
		</div>
	</div>
</section>

<?php
get_footer();
