</main>

<footer class="nf-footer">
	<div class="nf-wrap nf-footer__grid">
		<div>
			<a class="nf-logo nf-logo--text" href="<?php echo esc_url( home_url( '/' ) ); ?>">Natha Foods</a>
			<p class="nf-footer__lead"><?php esc_html_e( 'Indische Lebensmittel aus Leipzig, temperaturgeschützt in ganz Deutschland geliefert.', 'nathfoods' ); ?></p>
		</div>
		<div>
			<h2><?php esc_html_e( 'Einkaufen', 'nathfoods' ); ?></h2>
			<ul>
				<?php foreach ( array_slice( nf_product_categories(), 0, 5 ) as $term ) : ?>
					<li><a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div>
			<h2><?php esc_html_e( 'Rechtliches', 'nathfoods' ); ?></h2>
			<ul>
				<?php foreach ( [ 'impressum', 'datenschutzerklaerung', 'agb', 'widerrufsbelehrung', 'versandarten', 'bezahlmoeglichkeiten' ] as $slug ) : ?>
					<?php $legal = get_page_by_path( $slug ); ?>
					<?php if ( $legal ) : ?>
						<li><a href="<?php echo esc_url( get_permalink( $legal ) ); ?>"><?php echo esc_html( get_the_title( $legal ) ); ?></a></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>
		<div>
			<h2><?php esc_html_e( 'Kontakt', 'nathfoods' ); ?></h2>
			<address class="nf-footer__address">
				Natha Foods indische Lebensmittel<br>
				Dong Xuan Center, Maximilianallee 14-16, Raum 47<br>
				04129 Leipzig<br>
				<a href="tel:+4915216046764">+49 152 16046764</a><br>
				<a href="mailto:contact@nathafoods.shop">contact@nathafoods.shop</a>
			</address>
			<h2 class="nf-footer__sub"><?php esc_html_e( 'Newsletter', 'nathfoods' ); ?></h2>
			<p><?php esc_html_e( 'Angebote und Rezepte per E-Mail. Abmeldung jederzeit möglich.', 'nathfoods' ); ?></p>
			<form class="nf-newsletter" onsubmit="return false;">
				<label class="screen-reader-text" for="nf-news"><?php esc_html_e( 'E-Mail-Adresse', 'nathfoods' ); ?></label>
				<input id="nf-news" type="email" autocomplete="email" placeholder="<?php esc_attr_e( 'E-Mail-Adresse', 'nathfoods' ); ?>">
				<button type="submit"><?php esc_html_e( 'Anmelden', 'nathfoods' ); ?></button>
			</form>
		</div>
	</div>
	<div class="nf-wrap nf-footer__base">
		<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> Natha Foods</span>
		<span><?php esc_html_e( 'Zahlung per Stripe und PayPal', 'nathfoods' ); ?></span>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
