<?php
/**
 * Imports the Natha Foods invoice catalogue (natha-products.csv) into WooCommerce.
 *
 * Run:  dev-stack/wp.sh eval-file demo/import/import-products.php
 *
 * Safe to rerun: products are matched by SKU and updated, categories by slug.
 * The CSV's "Regular price" column holds PLACEHOLDER prices (net purchase cost x 1,6
 * plus VAT, rounded up to ,x9) until Natha Foods sets real selling prices.
 * The sample products from demo/seed.php are set to draft so only real stock shows.
 */

defined( 'ABSPATH' ) || exit;

global $wpdb;
wp_defer_term_counting( true );
wp_suspend_cache_invalidation( true );

// Tax classes and German VAT rates (created by seed.php too; repeated so this runs on its own).
if ( ! in_array( 'reduced-rate', WC_Tax::get_tax_class_slugs(), true ) ) {
	WC_Tax::create_tax_class( 'Reduced rate', 'reduced-rate' );
}
$rates = $wpdb->get_col( "SELECT tax_rate_class FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_country = 'DE'" );
if ( ! in_array( '', $rates, true ) ) {
	WC_Tax::_insert_tax_rate( [ 'tax_rate_country' => 'DE', 'tax_rate' => '19.0000', 'tax_rate_name' => 'MwSt.', 'tax_rate_priority' => 1, 'tax_rate_shipping' => 1, 'tax_rate_class' => '' ] );
}
if ( ! in_array( 'reduced-rate', $rates, true ) ) {
	WC_Tax::_insert_tax_rate( [ 'tax_rate_country' => 'DE', 'tax_rate' => '7.0000', 'tax_rate_name' => 'MwSt.', 'tax_rate_priority' => 1, 'tax_rate_shipping' => 0, 'tax_rate_class' => 'reduced-rate' ] );
}

// CSV category (English) => [ German name, slug, parent CSV category ].
$cats = [
	'Spices & Masala'                         => [ 'Gewürze & Masala', 'gewuerze-masala', '' ],
	'Lentils & Pulses (Dal)'                  => [ 'Linsen & Hülsenfrüchte (Dal)', 'linsen-dal', '' ],
	'Rice & Millets'                          => [ 'Reis & Hirse', 'reis-hirse', '' ],
	'Flour & Atta'                            => [ 'Mehl & Atta', 'mehl-atta', '' ],
	'Snacks & Namkeen'                        => [ 'Snacks & Namkeen', 'snacks-namkeen', '' ],
	'Biscuits & Rusk'                         => [ 'Kekse & Rusk', 'kekse-rusk', '' ],
	'Sweets & Desserts'                       => [ 'Süßigkeiten & Desserts', 'suessigkeiten-desserts', '' ],
	'Ready to Eat'                            => [ 'Fertiggerichte', 'fertiggerichte', '' ],
	'Instant Mixes'                           => [ 'Fertigmischungen', 'fertigmischungen', '' ],
	'Noodles & Vermicelli'                    => [ 'Nudeln & Vermicelli', 'nudeln-vermicelli', '' ],
	'Pickles, Pastes & Sauces'                => [ 'Pickles, Pasten & Saucen', 'pickles-pasten-saucen', '' ],
	'Oil & Ghee'                              => [ 'Öl & Ghee', 'oel-ghee', '' ],
	'Dairy & Paneer'                          => [ 'Milchprodukte & Paneer', 'milchprodukte-paneer', '' ],
	'Dry Fruits & Nuts'                       => [ 'Nüsse & Trockenfrüchte', 'nuesse-trockenfruechte', '' ],
	'Sugar & Jaggery'                         => [ 'Zucker & Jaggery', 'zucker-jaggery', '' ],
	'Beverages & Juices'                      => [ 'Getränke & Säfte', 'getraenke-saefte', '' ],
	'Tea & Coffee'                            => [ 'Tee & Kaffee', 'tee-kaffee', '' ],
	'Frozen Foods'                            => [ 'Tiefkühlkost', 'tiefkuehlkost', '' ],
	'Frozen Foods > Frozen Breads & Parathas' => [ 'TK Brot & Parathas', 'tk-brot-parathas', 'Frozen Foods' ],
	'Frozen Foods > Frozen Snacks'            => [ 'TK Snacks', 'tk-snacks', 'Frozen Foods' ],
	'Frozen Foods > Frozen Vegetables'        => [ 'TK Gemüse', 'tk-gemuese', 'Frozen Foods' ],
	'Frozen Foods > Frozen Meat'              => [ 'TK Fleisch', 'tk-fleisch', 'Frozen Foods' ],
	'Mouth Fresheners & Digestives'           => [ 'Mukhwas & Digestifs', 'mukhwas-digestifs', '' ],
	'Personal Care'                           => [ 'Körperpflege', 'koerperpflege', '' ],
	'Pooja & Incense'                         => [ 'Pooja & Räucherstäbchen', 'pooja-raeucherstaebchen', '' ],
];

$term_ids = [];
$dict     = []; // German => English, for TranslatePress (/en/).
$order    = 0;
foreach ( $cats as $csv => [ $name, $slug, $parent ] ) {
	$args = [ 'slug' => $slug, 'parent' => $parent ? $term_ids[ $parent ] : 0 ];
	$t    = term_exists( $slug, 'product_cat' );
	if ( $t ) {
		wp_update_term( (int) $t['term_id'], 'product_cat', $args + [ 'name' => $name ] );
	} else {
		$t = wp_insert_term( $name, 'product_cat', $args );
	}
	$term_ids[ $csv ] = (int) $t['term_id'];
	update_term_meta( $term_ids[ $csv ], 'order', $order++ );
	$en          = strpos( $csv, ' > ' ) ? substr( $csv, strpos( $csv, ' > ' ) + 3 ) : $csv;
	$dict[ $name ] = $en;
}

// Keep the old sample categories at the end of the menu (they empty out once the samples are drafted).
foreach ( get_terms( [ 'taxonomy' => 'product_cat', 'hide_empty' => false, 'exclude' => array_values( $term_ids ) ] ) as $old ) {
	update_term_meta( $old->term_id, 'order', 100 + $order++ );
}

// Sample products from seed.php have no SKU: take them off the shop.
$samples = get_posts( [ 'post_type' => 'product', 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids',
	'meta_query' => [ 'relation' => 'OR', [ 'key' => '_sku', 'value' => '' ], [ 'key' => '_sku', 'compare' => 'NOT EXISTS' ] ] ] );
foreach ( $samples as $id ) {
	wp_update_post( [ 'ID' => $id, 'post_status' => 'draft' ] );
}

foreach ( [ 'kg', 'l' ] as $u ) {
	if ( taxonomy_exists( 'product_unit' ) && ! term_exists( $u, 'product_unit' ) ) {
		wp_insert_term( $u, 'product_unit', [ 'slug' => $u ] );
	}
}

$fh   = fopen( __DIR__ . '/natha-products.csv', 'r' );
$head = fgetcsv( $fh );
$head[0] = preg_replace( '/^\xEF\xBB\xBF/', '', $head[0] );
$made = $updated = 0;
$n    = 0;
while ( ( $row = fgetcsv( $fh ) ) !== false ) {
	if ( count( $row ) < count( $head ) ) {
		continue;
	}
	$r  = array_combine( $head, $row );
	$id = wc_get_product_id_by_sku( $r['SKU'] );
	$p  = $id ? wc_get_product( $id ) : new WC_Product_Simple();
	$id ? $updated++ : $made++;

	$price = $r['Regular price'];
	$p->set_name( $r['Name'] );
	$p->set_sku( $r['SKU'] );
	$p->set_status( '1' === $r['Published'] ? 'publish' : 'draft' );
	$p->set_featured( '1' === $r['Is featured?'] );
	$p->set_catalog_visibility( $r['Visibility in catalog'] ?: 'visible' );
	$p->set_short_description( $r['Short description'] );
	$p->set_description( $r['Description'] );
	$p->set_tax_status( $r['Tax status'] ?: 'taxable' );
	$p->set_tax_class( $r['Tax class'] );
	$p->set_manage_stock( '' !== $r['Stock'] );
	if ( '' !== $r['Stock'] ) {
		$p->set_stock_quantity( (int) $r['Stock'] );
	}
	$p->set_stock_status( '1' === $r['In stock?'] ? 'instock' : 'outofstock' );
	$p->set_backorders( '1' === $r['Backorders allowed?'] ? 'yes' : 'no' );
	$p->set_sold_individually( '1' === $r['Sold individually?'] );
	$p->set_weight( $r['Weight (kg)'] );
	$p->set_regular_price( $price );
	$p->set_sale_price( $r['Sale price'] );
	$p->set_category_ids( [ $term_ids[ $r['Categories'] ] ] );
	$p->set_menu_order( $n++ );

	// Pack size on the card ("Inhalt: 500 g" => "500 g") and the German base price (Grundpreis).
	$pack = trim( preg_replace( '/^Inhalt:\s*/', '', $r['Short description'] ) );
	$p->update_meta_data( '_nf_pack', $pack );
	foreach ( [ '_unit', '_unit_base', '_unit_product', '_unit_price_regular', '_unit_price', '_unit_price_sale' ] as $k ) {
		$p->delete_meta_data( $k );
	}
	if ( $price && preg_match( '/([\d.,]+)\s*(kg|g|l|ml)\b/i', $pack, $m ) ) {
		$amount = (float) str_replace( ',', '.', $m[1] );
		$unit   = strtolower( $m[2] );
		if ( 'g' === $unit || 'ml' === $unit ) {
			$amount /= 1000;
			$unit    = 'g' === $unit ? 'kg' : 'l';
		}
		if ( $amount > 0 ) {
			$current = $r['Sale price'] ?: $price;
			$p->update_meta_data( '_unit', $unit );
			$p->update_meta_data( '_unit_base', '1' );
			$p->update_meta_data( '_unit_product', (string) $amount );
			$p->update_meta_data( '_unit_price_regular', (string) round( $price / $amount, 2 ) );
			$p->update_meta_data( '_unit_price', (string) round( $current / $amount, 2 ) );
			if ( $r['Sale price'] ) {
				$p->update_meta_data( '_unit_price_sale', (string) round( $current / $amount, 2 ) );
			}
		}
	}
	$pid = $p->save();

	if ( $r['Brands'] && taxonomy_exists( 'product_brand' ) ) {
		wp_set_object_terms( $pid, $r['Brands'], 'product_brand' );
	}
}
fclose( $fh );

// English category names for /en/.
if ( class_exists( 'TRP_Translate_Press' ) ) {
	$query = TRP_Translate_Press::get_trp_instance()->get_component( 'query' );
	$table = $query->get_table_name( 'en_US', 'de_DE' );
	foreach ( $dict as $de => $en ) {
		$de = str_replace( '&', '&amp;', $de );
		$en = str_replace( '&', '&amp;', $en );
		$wpdb->delete( $table, [ 'original' => $de ] );
		$wpdb->insert( $table, [ 'original' => $de, 'translated' => $en, 'status' => 2, 'block_type' => 0, 'original_id' => 0 ] );
	}
}

wp_suspend_cache_invalidation( false );
wp_defer_term_counting( false );
wc_delete_product_transients();
delete_transient( 'wc_term_counts' );
foreach ( $term_ids as $tid ) {
	_wc_term_recount( [ get_term( $tid, 'product_cat' ) ], get_taxonomy( 'product_cat' ), true, false );
}

printf( "Imported %d new, updated %d, drafted %d sample products.\n", $made, $updated, count( $samples ) );
