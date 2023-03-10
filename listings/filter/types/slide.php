<?php
if ( empty( $options ) ) {
	return;
}

/*Get min and max value*/
reset( $options );
asort( $options );

$start_value = null;

foreach ( $options as $v => $k ) {
	if ( empty( $start_value ) && ( 0 === $v || ! empty( $v ) ) ) {
		$start_value = $v;
	}
}
end( $options );
$end_value = key( $options );

/*Current slug*/
$slug = $taxonomy['slug'];

if ( 'search_radius' === $slug ) {
	$affix = '';
}

$max_value = $end_value;

if ( ! empty( $_GET[ 'max_' . $slug ] ) ) {
	$max_value = intval( $_GET[ 'max_' . $slug ] );
}

$vars = array(
	'slug'        => $slug,
	'affix'       => $affix,
	'js_slug'     => str_replace( '-', 'stmdash', $slug ),
	'start_value' => $start_value,
	'end_value'   => $end_value,
	'max_value'   => $max_value,
);

$label_affix = $vars['max_value'] . $affix;
if ( stm_is_listing_price_field( $slug ) ) {
	$label_affix = stm_listing_price_view( $vars['max_value'] );
}

$vars['label'] = stripslashes( $label_affix );

?>

<div class="col-md-12 col-sm-12">
	<div class="filter-<?php echo esc_attr( $vars['slug'] ); ?> stm-slider-filter-type-unit">
		<div class="clearfix">
			<h5 class="pull-left"><?php echo esc_html( $taxonomy['single_name'] ); ?></h5>
			<div class="stm-current-slider-labels"><?php echo esc_html( $vars['label'] ); ?></div>
		</div>
		<div class="stm-price-range-unit">
			<div class="stm-<?php echo esc_attr( $vars['slug'] ); ?>-range stm-filter-type-slider"></div>
		</div>
		<div class="row">
			<div class="col-md-12 col-sm-12">
				<input type="text" name="max_<?php echo esc_attr( $vars['slug'] ); ?>" id="stm_slide_filter_max_<?php echo esc_attr( $vars['slug'] ); ?>" class="form-control" />
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	var stmOptions_<?php echo esc_js( stm_do_lmth( $vars['js_slug'] ) ); ?>;
	(function ($) {
		$(document).ready(function () {
			var stmMaxRadiusValue = <?php echo esc_js( $end_value ); ?>;
			stmOptions_<?php echo esc_attr( $vars['js_slug'] ); ?> = {
				step: 1,
				min: <?php echo esc_js( $start_value ); ?>,
				max: <?php echo esc_js( $end_value ); ?>,
				value: <?php echo esc_js( $max_value ); ?>,
				slide: function (event, ui) {
					$("#stm_slide_filter_max_<?php echo esc_attr( $slug ); ?>").val(ui.value);
					var stmText = ui.value;

					$('.filter-<?php echo esc_attr( $slug ); ?> .stm-current-slider-labels').html(stmText);
				}
			};
			$(".stm-<?php echo esc_attr( $slug ); ?>-range").slider(stmOptions_<?php echo esc_attr( $vars['js_slug'] ); ?>);


			$("#stm_slide_filter_max_<?php echo esc_attr( $slug ); ?>").val($(".stm-<?php echo esc_attr( $slug ); ?>-range").slider("values", 1));

			$("#stm_slide_filter_max_<?php echo esc_attr( $slug ); ?>").on('keyup', function () {
				$(".stm-<?php echo esc_attr( $slug ); ?>-range").slider("values", 1, $(this).val());
			});

			$("#stm_slide_filter_max_<?php echo esc_attr( $slug ); ?>").on('focusout', function () {
				if ($(this).val() > stmMaxRadiusValue) {
					$(".stm-<?php echo esc_attr( $slug ); ?>-range").slider("values", 1, stmMaxRadiusValue);
					$(this).val(stmMaxRadiusValue);
				}
			});
		})
	})(jQuery);
</script>
