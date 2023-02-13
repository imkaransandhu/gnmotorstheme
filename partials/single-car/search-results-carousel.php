<?php
$show_search_results = stm_me_get_wpcfto_mod( 'show_search_results', false );

if ( $show_search_results ) :

	$search_results_full_width = stm_me_get_wpcfto_mod( 'search_results_full_width', false );

	$container_classes = array();
	$column_classes    = array();

	if ( $search_results_full_width ) {
		$container_classes[] = 'container-fluid';
		$column_classes[]    = 'col';
		$desktop_items       = stm_me_get_wpcfto_mod( 'search_results_desktop_items', 4 );
		$tablet_items        = stm_me_get_wpcfto_mod( 'search_results_tablet_items', 3 );
	} else {
		$container_classes[] = 'container';
		$column_classes[]    = 'col-sm-12';
		$desktop_items       = 4;
		$tablet_items        = 3;
	}

	$args['post_type']              = stm_listings_post_type();
	$args['order']                  = 'DESC';
	$args['orderby']                = 'date';
	$args['meta_query']['relation'] = 'AND';

	include get_template_directory() . '/partials/inventory-search-results-query.php';

	$posts = new WP_Query( $args );

	$random_id = wp_rand( 1, 99 ) . '_sr_' . wp_rand( 101, 999 );

	?>
	<div class="search_results_container <?php echo esc_attr( implode( ' ', $container_classes ) ); ?>">
		<div class="row">
			<div class="<?php echo esc_attr( implode( ' ', $column_classes ) ); ?>">
				<div class="stm-isearch-results-carousel-wrap <?php echo esc_attr( $random_id ); ?>">
					<div class="navigation-controls">
						<div class="back-search-results heading-font">
							<a href="<?php echo esc_url( $back_inventory_link ); ?>">
								<h4><i class="fas fa-arrow-left"></i> <?php _e( 'Search results', 'motors' ); ?></h4>
							</a>
						</div>
						<div class="next-prev-controls">
							<div class="stm-isearch-prev"><i class="fas fa-angle-left"></i></div>
							<div class="stm-isearch-next"><i class="fas fa-angle-right"></i></div>
						</div>
					</div>

					<div id="<?php echo esc_attr( $random_id ); ?>" class="stm-carousel owl-carousel stm-isearch-results-carousel car-listing-row">

						<?php
						if ( $posts->have_posts() ) :
							$current_vehicle_id = get_queried_object_ID();
							while ( $posts->have_posts() ) :
								$posts->the_post();
								?>

								<div class="media-carousel-item">
									<?php get_template_part( 'partials/inventory-search-results-carousel-loop', null, array( 'current_vehicle_id' => $current_vehicle_id ) ); ?>
								</div>

								<?php
							endwhile;
						endif;
						?>

					</div>
				</div>
			</div>
		</div>
	</div>

	<style>
		.stm-isearch-results-carousel-wrap .owl-nav, .stm-isearch-results-carousel-wrap .owl-dots {
			display: none!important;
		}
	</style>

	<script>
		(function ($) {
			"use strict";

			var owl_id = '<?php echo esc_attr( $random_id ); ?>';

			var $owl = $('#'+owl_id);

			$(window).on('load', function () {

				var owlRtl = false;
				if ($('body').hasClass('rtl')) {
					owlRtl = true;
				}

				$owl.on('initialized.owl.carousel', function(e){
					setTimeout(function () {
						$owl.find('.owl-nav, .owl-dots').remove();
						$('#' + owl_id + ' .tmb-wrap-table div:first-child').trigger('mouseenter');
					}, 100);
				});

				$owl.owlCarousel({
					rtl: owlRtl,
					items: 3,
					smartSpeed: 800,
					dots: false,
					margin: 10,
					autoplay: false,
					loop: false,
					responsiveRefreshRate: 1000,
					stagePadding: 50,
					responsive: {
						0: {
							items: 1,
						},
						500: {
							items: 2,
						},
						768: {
							items: <?php echo esc_attr( $tablet_items ); ?>,
						},
						991: {
							items: <?php echo esc_attr( $tablet_items ); ?>,
						},
						1025: {
							items: <?php echo esc_attr( $desktop_items ); ?>,
						}
					}
				});

				var toIndex = 0;
				var count = 0;

				$('#'+owl_id+' .owl-stage .owl-item').each(function(){

					if($(this).find('.stm-template-front-loop').hasClass('current')) {
						toIndex = parseInt(count);
					}

					count++;
				});

				$owl.trigger('to.owl.carousel', [toIndex, 1, true]);

				$('.'+owl_id+' .stm-isearch-prev').on('click', function () {
					if($(this).hasClass('disabled')) return;

					$owl.trigger('prev.owl.carousel');

					$('.'+owl_id+' .stm-isearch-next').removeClass('disabled');

					var first_slide = $('#'+owl_id+' .owl-stage .owl-item').first();
					if(first_slide.hasClass('active')) {
						$(this).addClass('disabled');
					} else {
						$(this).removeClass('disabled');
					}
				})

				$('.'+owl_id+' .stm-isearch-next').on('click', function () {
					if($(this).hasClass('disabled')) return;

					$owl.trigger('next.owl.carousel');

					$('.'+owl_id+' .stm-isearch-prev').removeClass('disabled');

					var last_slide = $('#'+owl_id+' .owl-stage .owl-item').last();
					if(last_slide.hasClass('active')) {
						$(this).addClass('disabled');
					} else {
						$(this).removeClass('disabled');
					}
				});

			});

		})(jQuery);
	</script>
	<?php
endif;
