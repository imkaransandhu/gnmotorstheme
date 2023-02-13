<?php

//Getting gallery list
$gallery = apply_filters( 'stm_listing_gallery', get_post_meta( get_the_id(), 'gallery', true ), get_the_ID() );
$video_preview = get_post_meta( get_the_id(), 'video_preview', true );
$gallery_video = get_post_meta( get_the_id(), 'gallery_video', true );

$sold = get_post_meta(get_the_ID(), 'car_mark_as_sold', true);
$sold_badge_color = stm_me_get_wpcfto_mod('sold_badge_bg_color');

// remove "special" if the listing is sold
if(!empty($sold)) {
    delete_post_meta(get_the_ID(), 'special_car');
}

$special_car = get_post_meta( get_the_id(), 'special_car', true );

$badge_text = get_post_meta( get_the_ID(), 'badge_text', true );
$badge_bg_color = get_post_meta( get_the_ID(), 'badge_bg_color', true );

if ( empty( $badge_text ) ) {
	$badge_text = esc_html__('Special', 'motors');
}

$badge_style = '';
if ( !empty( $badge_bg_color ) ) {
    $badge_style = 'style=background-color:' . $badge_bg_color . ';';
}

$image_limit = '';

if ( stm_pricing_enabled() and !empty( $gallery ) ) {
    $user_added = get_post_meta( get_the_id(), 'stm_car_user', true );
    if ( !empty( $user_added ) ) {
        $limits = stm_get_post_limits( $user_added );
        $image_limit = $limits['images'] - 1;
        $gallery = array_slice( $gallery, 0, $image_limit );
    }
}

?>

<?php if ( !has_post_thumbnail() and stm_check_if_car_imported( get_the_id() ) ): ?>
    <img
            src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/automanager_placeholders/plchldr798automanager.png' ); ?>"
            class="img-responsive"
            alt="<?php esc_attr_e( 'Placeholder', 'motors' ); ?>"
    />
<?php endif; ?>


<div class="stm-car-carousels stm-listing-car-gallery">
    <!--Actions-->
    <?php
    $show_print = stm_me_get_wpcfto_mod( 'show_print_btn', false );
    $show_compare = stm_me_get_wpcfto_mod( 'show_compare', false );
    $show_share = stm_me_get_wpcfto_mod( 'show_share', false );
    $show_featured_btn = stm_me_get_wpcfto_mod( 'show_featured_btn', false );
    $show_test_drive = stm_me_get_wpcfto_mod('show_test_drive', false);
	$show_pdf = stm_me_get_wpcfto_mod('show_pdf', false);
	$car_brochure = get_post_meta(get_the_ID(), 'car_brochure', true);
    ?>
    <div class="stm-gallery-actions">
		<?php if (!empty($show_pdf) and $show_pdf): ?>
			<?php if (!empty($car_brochure)): ?>
                <div class="stm-gallery-action-unit">
                <a href="<?php echo esc_url(wp_get_attachment_url($car_brochure)); ?>" class="stm-brochure"
                   title="<?php esc_html_e('Download brochure', 'motors'); ?>" download>
                    <i class="stm-icon-brochure"></i>
                </a>
                </div>
			<?php endif; ?>
		<?php endif; ?>
        <?php if ( !empty( $show_print ) ): ?>
            <div class="stm-gallery-action-unit stm-listing-print-action">
                <a href="javascript:window.print()" class="car-action-unit stm-car-print">
                    <i class="fas fa-print"></i>
                </a>
            </div>
        <?php endif; ?>
        <?php if ( !empty( $show_featured_btn ) ): ?>
            <div class="stm-gallery-action-unit stm-listing-favorite-action"
                 data-id="<?php echo esc_attr( get_the_id() ); ?>">
                <i class="stm-service-icon-staricon"></i>
            </div>
        <?php endif; ?>
        <?php if ( !empty( $show_compare ) ): ?>
            <div class="stm-gallery-action-unit compare" data-id="<?php echo esc_attr( get_the_ID() ); ?>"
                 data-title="<?php echo esc_attr( stm_generate_title_from_slugs( get_the_id() ) ); ?>"
                 data-post-type="<?php echo esc_attr( get_post_type( get_the_ID() ) ); ?>"
				<?php if(stm_is_dealer_two()) echo 'data-placement="bottom"'; ?>>
                <i class="stm-service-icon-compare-new"></i>
            </div>
        <?php endif; ?>
		<?php if (!empty($show_test_drive)): ?>
            <div class="stm-gallery-action-unit stm-schedule" data-toggle="modal" data-target="#test-drive"
               onclick="stm_test_drive_car_title(<?php echo esc_js(get_the_ID()); ?>, '<?php echo esc_js(get_the_title(get_the_ID())) ?>')">
                <i class="stm-icon-steering_wheel"></i>
            </div>
		<?php endif; ?>
        <?php if ( !empty( $show_share ) ): ?>
            <div class="stm-gallery-action-unit">
                <i class="stm-icon-share"></i>
                <?php if ( function_exists( 'ADDTOANY_SHARE_SAVE_KIT' ) && !get_post_meta( get_the_ID(), 'sharing_disabled', true ) ): ?>
                    <div class="stm-a2a-popup">
                        <?php echo stm_add_to_any_shortcode(get_the_ID()); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php $car_media = stm_get_car_medias( get_the_id() ); ?>
    <?php if ( !empty( $car_media['car_videos_count'] ) ): ?>
        <div class="stm-car-medias">
            <div class="stm-listing-videos-unit stm-car-videos-<?php echo get_the_id(); ?>">
                <i class="fas fa-film"></i>
                <span><?php printf(esc_html__('%s Video', 'motors'), $car_media['car_videos_count']); ?></span>
            </div>
        </div>

        <script>
            jQuery(document).ready(function () {

                jQuery(".stm-car-videos-<?php echo get_the_id(); ?>").on('click', function () {
                    jQuery(this).lightGallery({
                        dynamic: true,
                        dynamicEl: [
                            <?php foreach($car_media['car_videos'] as $car_video): ?>
                            {
                                src  : "<?php echo esc_url($car_video); ?>"
                            },
                            <?php endforeach; ?>
                        ],
                        download: false,
                        mode: 'lg-fade',
                    })
                }); //click
            }); //ready

        </script>
    <?php endif; ?>

    <?php if ( empty($sold) && !empty( $special_car ) && $special_car == 'on' ): ?>
        <div class="special-label h5" <?php echo esc_attr( $badge_style ); ?>>
            <?php stm_dynamic_string_translation_e('Special Badge Text', $badge_text ); ?>
        </div>
    <?php elseif(stm_sold_status_enabled() && !empty($sold)): ?>
        <?php $badge_style = 'style=background-color:' . $sold_badge_color . ';'; ?>
		<div class="special-label h5" <?php echo esc_attr( $badge_style ); ?>>
            <?php _e('Sold', 'motors'); ?>
        </div>
    <?php endif; ?>

    <div class="stm-big-car-gallery owl-carousel">
        <?php if ( has_post_thumbnail() ):
            $full_src = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_id() ), 'full' );
            //Post thumbnail first
            ?>
            <div class="stm-single-image"
                 data-id="big-image-<?php echo esc_attr( get_post_thumbnail_id( get_the_id() ) ); ?>">
                <a href="<?php echo esc_url( $full_src[0] ); ?>" class="stm_fancybox" rel="stm-car-gallery">
                    <?php the_post_thumbnail( 'stm-img-796-466', array( 'class' => 'img-responsive' ) ); ?>
                </a>
            </div>
        <?php endif; ?>

        <?php if ( !empty( $gallery ) ): ?>
            <?php foreach ( $gallery as $gallery_image ): ?>
                <?php $src = wp_get_attachment_image_src( $gallery_image, 'stm-img-796-466' ); ?>
                <?php $full_src = wp_get_attachment_image_src( $gallery_image, 'full' ); ?>
                <?php if ( !empty( $src[0] ) && $gallery_image != get_post_thumbnail_id( get_the_ID() ) ): ?>
                    <div class="stm-single-image" data-id="big-image-<?php echo esc_attr( $gallery_image ); ?>">
                        <a href="<?php echo esc_url( $full_src[0] ); ?>" class="stm_fancybox" rel="stm-car-gallery">
                            <img src="<?php echo esc_url( $src[0] ); ?>"
                                 alt="<?php printf(esc_attr__("%s full", 'motors'), get_the_title(get_the_ID())); ?>" />
                        </a>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>


    <?php if ( has_post_thumbnail() ): ?>
        <div class="stm-thumbs-car-gallery owl-carousel">
            <?php if ( has_post_thumbnail() ):
                //Post thumbnail first ?>
                <div class="stm-single-image"
                     id="big-image-<?php echo esc_attr( get_post_thumbnail_id( get_the_id() ) ); ?>">
                    <?php the_post_thumbnail( 'stm-img-350-205', array( 'class' => 'img-responsive' ) ); ?>
                </div>
            <?php endif; ?>
            <?php if ( !empty( $gallery ) && count( $gallery ) > 0 ): ?>
                <?php foreach ( $gallery as $gallery_image ): ?>
                    <?php $src = wp_get_attachment_image_src( $gallery_image, 'stm-img-350-205' ); ?>
                    <?php if ( !empty( $src[0] ) && $gallery_image != get_post_thumbnail_id( get_the_ID() ) ): ?>
                        <div class="stm-single-image" id="big-image-<?php echo esc_attr( $gallery_image ); ?>">
                            <img src="<?php echo esc_url( $src[0] ); ?>"
                                 alt="<?php printf(esc_attr__("%s full", 'motors'), get_the_title(get_the_ID())); ?>" />
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    <?php endif; ?>
</div>


<!--Enable carousel-->
<script>
    jQuery(document).ready(function ($) {
        var big = jQuery('.stm-big-car-gallery');
        var small = jQuery('.stm-thumbs-car-gallery');
        var flag = false;
        var duration = 800;

        var owlRtl = false;
        if (jQuery('body').hasClass('rtl')) {
            owlRtl = true;
        }

        big
            .owlCarousel({
                items: 1,
                rtl: owlRtl,
                smartSpeed: 800,
                dots: false,
                nav: false,
                margin: 0,
                autoplay: false,
                loop: false,
                responsiveRefreshRate: 1000
            })
            .on('changed.owl.carousel', function (e) {
                jQuery('.stm-thumbs-car-gallery .owl-item').removeClass('current');
                jQuery('.stm-thumbs-car-gallery .owl-item').eq(e.item.index).addClass('current');
                if (!flag) {
                    flag = true;
                    small.trigger('to.owl.carousel', [e.item.index, duration, true]);
                    flag = false;
                }
            });

        small
            .owlCarousel({
                items: 5,
                rtl: owlRtl,
                smartSpeed: 800,
                dots: false,
                margin: 22,
                autoplay: false,
                nav: true,
                navElement: 'div',
                loop: false,
                navText: [],
                responsiveRefreshRate: 1000,
                responsive: {
                    0: {
                        items: 2
                    },
                    500: {
                        items: 4
                    },
                    768: {
                        items: 5
                    },
                    1000: {
                        items: 5
                    }
                }
            })
            .on('click', '.owl-item', function (event) {
                big.trigger('to.owl.carousel', [jQuery(this).index(), 400, true]);
            })
            .on('changed.owl.carousel', function (e) {
                if (!flag) {
                    flag = true;
                    big.trigger('to.owl.carousel', [e.item.index, duration, true]);
                    flag = false;
                }
            });

        if (jQuery('.stm-thumbs-car-gallery .stm-single-image').length < 6) {
            jQuery('.stm-single-car-page .owl-controls').hide();
            jQuery('.stm-thumbs-car-gallery').css({'margin-top': '22px'});
        }

        jQuery('.stm-big-car-gallery .owl-dots').remove();
		jQuery('.stm-big-car-gallery .owl-nav').remove();
    })
</script>