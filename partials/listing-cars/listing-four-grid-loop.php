<?php
$price                = get_post_meta( get_the_id(), 'price', true );
$sale_price           = get_post_meta( get_the_id(), 'sale_price', true );
$car_price_form_label = get_post_meta( get_the_ID(), 'car_price_form_label', true );
$regular_price_label  = get_post_meta( get_the_ID(), 'regular_price_label', true );
$special_price_label  = get_post_meta( get_the_ID(), 'special_price_label', true );

$data = array(
	'data_price' => 0,
  // 'data_mileage' => 0,
);

if ( ! empty( $price ) ) {
	$data['data_price'] = $price;
}

if ( ! empty( $sale_price ) ) {
	$data['data_price'] = $sale_price;
}

if ( empty( $price ) and ! empty( $sale_price ) ) {
	$price = $sale_price;
}

/*
$mileage = get_post_meta(get_the_id(),'mileage',true);

if(!empty($mileage)) {
	$data['data_mileage'] = $mileage;
}*/

$taxonomies = stm_get_taxonomies();
foreach ( $taxonomies as $val ) {
	$tax_data = stm_get_taxonomies_with_type( $val );
	if ( ! empty( $tax_data['numeric'] ) && ! empty( $tax_data['slider'] ) ) {
		$value                       = get_post_meta( get_the_id(), $val, true );
		$replaced                    = str_replace( '-', '__', $val );
		$data[ 'data_' . $replaced ] = $value;
		$data['atts'][]              = $replaced;
	}
}

?>

<?php if ( ! stm_is_magazine() ) : ?>
	<?php stm_listings_load_template( 'loop/classified/grid/start', $data ); ?>

		<?php stm_listings_load_template( 'loop/default/grid/image' ); ?>

		<div class="listing-car-item-meta">

			<?php
			stm_listings_load_template(
				'loop/default/grid/title_price',
				array(
					'price'                => $price,
					'sale_price'           => $sale_price,
					'car_price_form_label' => $car_price_form_label,
				)
			);
			?>

			<?php
			if ( function_exists( 'stm_multilisting_load_template' ) ) {
				stm_multilisting_load_template( 'templates/grid-listing-data' );
			} else {
				stm_listings_load_template( 'loop/default/grid/data' );
			}
			?>

		</div>
	</a>
</div>
<?php else :

	get_template_part( 'partials/listing-cars/listing-grid-loop-magazine' );

endif; ?>
