<?php
/**
 * Demo content for Natha Foods: German shop settings, DE / EN language setup and a sample catalogue.
 * Prices, names and photos are placeholders. Safe for a fresh install only.
 *
 * Local:      dev-stack/wp.sh eval-file demo/seed.php
 * Playground: blueprint.json loads this after installing the plugins.
 */

require_once ABSPATH . 'wp-admin/includes/image.php';

$opts = [
	'blogname' => 'Natha Foods', 'blogdescription' => 'Indische Lebensmittel aus Leipzig',
	'woocommerce_store_address' => 'Maximilianallee 14-16', 'woocommerce_store_address_2' => 'Dong Xuan Center, Raum 47',
	'woocommerce_store_city' => 'Leipzig', 'woocommerce_store_postcode' => '04129',
	'woocommerce_email_from_name' => 'Natha Foods',
	'woocommerce_default_country' => 'DE:SN', 'woocommerce_currency' => 'EUR',
	'woocommerce_currency_pos' => 'right_space', 'woocommerce_price_thousand_sep' => '.',
	'woocommerce_price_decimal_sep' => ',', 'woocommerce_price_num_decimals' => 2,
	'woocommerce_weight_unit' => 'kg', 'woocommerce_dimension_unit' => 'cm',
	'woocommerce_calc_taxes' => 'yes', 'woocommerce_prices_include_tax' => 'yes',
	'woocommerce_tax_display_shop' => 'incl', 'woocommerce_tax_display_cart' => 'incl',
	'woocommerce_tax_based_on' => 'base', 'woocommerce_allowed_countries' => 'specific',
	'woocommerce_specific_allowed_countries' => [ 'DE' ], 'woocommerce_ship_to_countries' => '',
	'woocommerce_enable_guest_checkout' => 'yes', 'woocommerce_enable_myaccount_registration' => 'yes',
	'woocommerce_coming_soon' => 'no', 'woocommerce_store_pages_only' => 'no',
	'woocommerce_task_list_hidden' => 'yes', 'timezone_string' => 'Europe/Berlin',
];
foreach ( $opts as $k => $v ) {
	update_option( $k, $v );
}
delete_option( '_wc_gzd_setup_wizard_redirect' );

// VAT: 19% standard, 7% reduced (most food).
global $wpdb;
$wpdb->query( "DELETE FROM {$wpdb->prefix}woocommerce_tax_rates" );
WC_Tax::create_tax_class( 'Reduced rate', 'reduced-rate' );
WC_Tax::_insert_tax_rate( [ 'tax_rate_country' => 'DE', 'tax_rate' => '19.0000', 'tax_rate_name' => 'MwSt.', 'tax_rate_priority' => 1, 'tax_rate_shipping' => 1, 'tax_rate_class' => '' ] );
WC_Tax::_insert_tax_rate( [ 'tax_rate_country' => 'DE', 'tax_rate' => '7.0000', 'tax_rate_name' => 'MwSt.', 'tax_rate_priority' => 1, 'tax_rate_shipping' => 0, 'tax_rate_class' => 'reduced-rate' ] );

// Shipping: Germany, 4,90 EUR flat, free from 49 EUR (placeholders until the client confirms).
foreach ( WC_Shipping_Zones::get_zones() as $z ) {
	WC_Shipping_Zones::delete_zone( $z['id'] );
}
$zone = new WC_Shipping_Zone();
$zone->set_zone_name( 'Deutschland' );
$zone->set_locations( [ [ 'code' => 'DE', 'type' => 'country' ] ] );
$zone->save();
$i = $zone->add_shipping_method( 'flat_rate' );
update_option( "woocommerce_flat_rate_{$i}_settings", [ 'title' => 'Standardversand', 'tax_status' => 'taxable', 'cost' => '4.90' ] );
$i = $zone->add_shipping_method( 'free_shipping' );
update_option( "woocommerce_free_shipping_{$i}_settings", [ 'title' => 'Kostenloser Versand', 'requires' => 'min_amount', 'min_amount' => '49', 'ignore_discounts' => 'no' ] );

if ( class_exists( 'WC_GZD_Install' ) && method_exists( 'WC_GZD_Install', 'create_pages' ) ) {
	WC_GZD_Install::create_pages();
}

// Sample catalogue.
$dir = get_template_directory() . '/assets/img/products/';

function nf_seed_image( $path ) {
	$upload = wp_upload_bits( basename( $path ), null, file_get_contents( $path ) );
	$id     = wp_insert_attachment( [ 'post_mime_type' => 'image/webp', 'post_title' => basename( $path, '.webp' ), 'post_status' => 'inherit' ], $upload['file'] );
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	return $id;
}

$cats = [
	'obst-gemuese'  => [ 'Obst & Gemüse', 'Fruit & Vegetables', 'mango' ],
	'reis-mehl'     => [ 'Reis & Mehl', 'Rice & Flour', 'basmati-rice' ],
	'linsen-bohnen' => [ 'Linsen & Bohnen', 'Lentils & Beans', 'red-lentils' ],
	'gewuerze'      => [ 'Gewürze & Masalas', 'Spices & Masalas', 'turmeric' ],
	'kokos'         => [ 'Kokos & Saucen', 'Coconut & Sauces', 'coconut-milk' ],
	'milchprodukte' => [ 'Milchprodukte', 'Dairy', 'paneer' ],
	'fleisch-fisch' => [ 'Fleisch & Fisch', 'Meat & Fish', 'salmon' ],
	'snacks'        => [ 'Nüsse & Snacks', 'Nuts & Snacks', 'cashew-nuts' ],
];
$order    = 0;
$term_ids = [];
$dict     = [];
foreach ( $cats as $slug => [ $name, $en, $img ] ) {
	$t                 = term_exists( $slug, 'product_cat' ) ?: wp_insert_term( $name, 'product_cat', [ 'slug' => $slug ] );
	$term_ids[ $slug ] = (int) $t['term_id'];
	update_term_meta( $t['term_id'], 'thumbnail_id', nf_seed_image( $dir . $img . '.webp' ) );
	update_term_meta( $t['term_id'], 'order', $order++ );
	$dict[ $name ] = $en;
}

// name, English name, category, image, price, sale, pack label, English label, pack amount, unit
$items = [
	[ 'Mango Alphonso', 'Alphonso Mango', 'obst-gemuese', 'mango', 3.49, 2.79, '1 Stück, ca. 350 g', '1 piece, approx. 350 g', 0, '' ],
	[ 'Frischer Ingwer', 'Fresh Ginger', 'obst-gemuese', 'ginger', 1.99, null, '250 g', '', 0.25, 'kg' ],
	[ 'Grüne Chilis', 'Green Chillies', 'obst-gemuese', 'chilli', 1.49, null, '100 g', '', 0.1, 'kg' ],
	[ 'Knoblauch', 'Garlic', 'obst-gemuese', 'garlic', 1.29, null, '200 g', '', 0.2, 'kg' ],
	[ 'Rote Zwiebeln', 'Red Onions', 'obst-gemuese', 'onion', 1.79, null, '1 kg', '', 1, 'kg' ],
	[ 'Rispentomaten', 'Vine Tomatoes', 'obst-gemuese', 'tomato', 2.49, 1.99, '500 g', '', 0.5, 'kg' ],
	[ 'Frischer Spinat', 'Fresh Spinach', 'obst-gemuese', 'spinach', 2.29, null, '300 g', '', 0.3, 'kg' ],
	[ 'Aubergine', 'Aubergine', 'obst-gemuese', 'aubergine', 1.59, null, '1 Stück, ca. 300 g', '1 piece, approx. 300 g', 0, '' ],
	[ 'Limetten', 'Limes', 'obst-gemuese', 'lime', 1.99, null, '4 Stück', '4 pieces', 0, '' ],
	[ 'Basmati Reis Premium', 'Premium Basmati Rice', 'reis-mehl', 'basmati-rice', 8.99, 7.49, '2 kg', '', 2, 'kg' ],
	[ 'Chakki Atta', 'Chakki Atta Flour', 'reis-mehl', 'flour', 6.49, null, '5 kg', '', 5, 'kg' ],
	[ 'Rote Linsen', 'Red Lentils', 'linsen-bohnen', 'red-lentils', 2.99, null, '1 kg', '', 1, 'kg' ],
	[ 'Kichererbsen', 'Chickpeas', 'linsen-bohnen', 'chickpeas', 2.79, 2.29, '1 kg', '', 1, 'kg' ],
	[ 'Kurkuma gemahlen', 'Ground Turmeric', 'gewuerze', 'turmeric', 2.49, null, '100 g', '', 0.1, 'kg' ],
	[ 'Kreuzkümmel ganz', 'Whole Cumin', 'gewuerze', 'cumin', 2.69, null, '100 g', '', 0.1, 'kg' ],
	[ 'Garam Masala', 'Garam Masala', 'gewuerze', 'garam-masala', 3.29, null, '100 g', '', 0.1, 'kg' ],
	[ 'Currypulver mild', 'Mild Curry Powder', 'gewuerze', 'curry-powder', 2.99, 2.49, '100 g', '', 0.1, 'kg' ],
	[ 'Kokosmilch', 'Coconut Milk', 'kokos', 'coconut-milk', 1.99, null, '400 ml', '', 0.4, 'l' ],
	[ 'Paneer', 'Paneer', 'milchprodukte', 'paneer', 4.49, null, '400 g', '', 0.4, 'kg' ],
	[ 'Joghurt natur', 'Natural Yoghurt', 'milchprodukte', 'yogurt', 1.99, null, '500 g', '', 0.5, 'kg' ],
	[ 'Butter', 'Butter', 'milchprodukte', 'butter', 2.79, null, '250 g', '', 0.25, 'kg' ],
	[ 'Hähnchenbrust', 'Chicken Breast', 'fleisch-fisch', 'chicken', 7.99, null, '500 g', '', 0.5, 'kg' ],
	[ 'Garnelen', 'Prawns', 'fleisch-fisch', 'prawns', 9.99, 8.49, '400 g', '', 0.4, 'kg' ],
	[ 'Lachsfilet', 'Salmon Fillet', 'fleisch-fisch', 'salmon', 11.49, null, '300 g', '', 0.3, 'kg' ],
	[ 'Cashewkerne', 'Cashew Nuts', 'snacks', 'cashew-nuts', 5.99, null, '200 g', '', 0.2, 'kg' ],
];

foreach ( [ 'kg', 'l' ] as $u ) {
	if ( taxonomy_exists( 'product_unit' ) && ! term_exists( $u, 'product_unit' ) ) {
		wp_insert_term( $u, 'product_unit', [ 'slug' => $u ] );
	}
}

$n = 0;
foreach ( $items as [ $name, $en, $cat, $img, $price, $sale, $pack, $pack_en, $amount, $unit ] ) {
	$p = new WC_Product_Simple();
	$p->set_name( $name );
	$p->set_regular_price( (string) $price );
	if ( $sale ) {
		$p->set_sale_price( (string) $sale );
	}
	$p->set_category_ids( [ $term_ids[ $cat ] ] );
	$p->set_image_id( nf_seed_image( $dir . $img . '.webp' ) );
	$p->set_tax_class( 'reduced-rate' );
	$p->set_manage_stock( false );
	$p->set_stock_status( 'instock' );
	$p->set_short_description( 'Beispielprodukt für die Design-Abnahme.' );
	$p->set_menu_order( $n++ );
	$p->update_meta_data( '_nf_pack', $pack );
	if ( $amount && $unit ) {
		$current = $sale ?: $price;
		$p->update_meta_data( '_unit', $unit );
		$p->update_meta_data( '_unit_base', '1' );
		$p->update_meta_data( '_unit_product', (string) $amount );
		$p->update_meta_data( '_unit_price_regular', (string) round( $price / $amount, 2 ) );
		$p->update_meta_data( '_unit_price', (string) round( $current / $amount, 2 ) );
		if ( $sale ) {
			$p->update_meta_data( '_unit_price_sale', (string) round( $current / $amount, 2 ) );
		}
	}
	$p->save();
	$dict[ $name ] = $en;
	if ( $pack_en ) {
		$dict[ $pack ] = $pack_en;
	}
}
$dict['Beispielprodukt für die Design-Abnahme.'] = 'Sample product for design review.';
$dict['Indische Lebensmittel aus Leipzig'] = 'Indian groceries from Leipzig';

// Languages: German default, English second, /en/ URLs.
if ( class_exists( 'TRP_Translate_Press' ) ) {
	$settings = (array) get_option( 'trp_settings', [] );
	update_option( 'trp_settings', array_merge( $settings, [
		'default-language'      => 'de_DE',
		'translation-languages' => [ 'de_DE', 'en_US' ],
		'publish-languages'     => [ 'de_DE', 'en_US' ],
		'url-slugs'             => [ 'de_DE' => 'de', 'en_US' => 'en' ],
		'add-subdirectory-to-default-language' => 'no',
		'force-language-to-custom-links'       => 'yes',
		'trp-ls-floater'        => 'no',
	] ) );
	$trp   = TRP_Translate_Press::get_trp_instance();
	$query = $trp->get_component( 'query' );
	$query->check_table( 'de_DE', 'en_US' );
	$gettext = new TRP_Gettext_Table_Creation( $trp->get_component( 'settings' )->get_settings() );
	$gettext->check_gettext_table( 'de_DE' );
	$gettext->check_gettext_table( 'en_US' );
	$table = $query->get_table_name( 'en_US', 'de_DE' );
	foreach ( $dict as $original => $translated ) {
		$wpdb->insert( $table, [
			'original'    => $original,
			'translated'  => $translated,
			'status'      => 2,
			'block_type'  => 0,
			'original_id' => 0,
		] );
	}
}

// Home page.
if ( ! get_page_by_path( 'start' ) ) {
	$front = wp_insert_post( [ 'post_type' => 'page', 'post_title' => 'Start', 'post_name' => 'start', 'post_status' => 'publish' ] );
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $front );
}
flush_rewrite_rules();
echo "seeded {$n} products\n";
