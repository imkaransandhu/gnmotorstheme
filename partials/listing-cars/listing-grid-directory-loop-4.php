<?php
$regular_price_label = get_post_meta(get_the_ID(), 'regular_price_label', true);
$special_price_label = get_post_meta(get_the_ID(),'special_price_label',true);

$price = get_post_meta(get_the_id(),'price',true);
$sale_price = get_post_meta(get_the_id(),'sale_price',true);

$car_price_form_label = get_post_meta(get_the_ID(), 'car_price_form_label', true);

$data = array(
    'data_price' => 0,
    'data_mileage' => 0,
);

if(!empty($price)) {
    $data['data_price'] = $price;
}

if(!empty($sale_price)) {
    $data['data_price'] = $sale_price;
}

if(empty($price) and !empty($sale_price)) {
    $price = $sale_price;
}

$mileage = get_post_meta(get_the_id(),'mileage',true);

if(!empty($mileage)) {
    $data['data_mileage'] = $mileage;
}

$data['class'] = array('col-md-3 col-sm-4 col-xs-12 col-xxs-12 stm-directory-grid-loop stm-isotope-listing-item all');

?>

<?php stm_listings_load_template('loop/classified/grid/start', $data); ?>

        <?php stm_listings_load_template('loop/classified/grid/image', $data); ?>

        <div class="listing-car-item-meta">
            <?php stm_listings_load_template('loop/default/grid/title_price', array('price' => $price, 'sale_price' => $sale_price, 'car_price_form_label' => $car_price_form_label)); ?>

            <?php
                // get data using Multilisting function, if exists. Otherwise fallback to default
                if ( function_exists( 'stm_multilisting_load_template' ) ) {
                    stm_multilisting_load_template('templates/grid-listing-data');
                } else {
                    stm_listings_load_template('loop/classified/grid/data');
                }
                    
            ?>

        </div>
    </a>
</div>