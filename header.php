<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php
	if ( get_theme_mod( 'listing_archive', '' ) == get_the_ID() || get_theme_mod( 'rental_datepick', '' ) == get_the_ID() || ( function_exists( 'is_shop' ) && is_shop() ) || is_category() ) :
		$page_id = get_the_ID();
		if ( ( function_exists( 'is_shop' ) && is_shop() ) ) {
			$page_id = get_option( 'woocommerce_shop_page_id' );
		}
		if ( is_category() ) {
			$page_id = get_option( 'page_for_posts' );
		}
		?>
		<link rel="canonical" href="<?php echo esc_url( get_the_permalink( $page_id ) ); ?>" />
	<?php endif; ?>

	<?php
	if ( is_single( get_the_ID() ) ) :
		$item_id = get_the_ID();

		echo '
        <meta property="og:title" content="' . esc_attr( get_the_title( $item_id ) ) . '">
        <meta property="og:image" content="' . esc_url( get_the_post_thumbnail_url( $item_id ) ) . '">
        <meta property="og:description" content="' . esc_attr( wp_strip_all_tags( get_the_excerpt( $item_id ) ) ) . '">
        <meta property="og:url" content="' . esc_url( get_the_permalink( $item_id ) ) . '">
        <meta name="twitter:card" content="' . esc_url( get_the_post_thumbnail_url( $item_id ) ) . '">
        ';
	endif;
	?>

	<?php wp_head(); ?>
</head>

<?php
$class = '';
if ( stm_is_dealer_two() ) {
	$show_title_box = get_post_meta( get_queried_object_id(), 'title', true );
	if ( ( 'show' === $show_title_box || is_front_page() || is_tag() || is_category() || is_archive() || is_date() ) && ! is_post_type_archive( 'listings' ) ) {
		$class = 'header-position-absolute';
	}

	$transparent_header = get_post_meta( get_queried_object_id(), 'transparent_header', true );

	if ( empty( $transparent_header ) ) {
		$class .= ' listing-nontransparent-header';
	}
}
$body_custom_image = stm_me_get_wpcfto_img_src( 'custom_bg_image', '' );

?>

<body <?php body_class(); ?> <?php
if ( ! empty( $body_custom_image ) ) :
	?>
	style="background-image: url('<?php echo esc_url( $body_custom_image ); ?>')" <?php endif; ?> ontouchstart="">
<?php wp_body_open(); ?>
<?php do_action( 'motors_before_header' ); ?>
<div id="wrapper">
<?php
if ( ! apply_filters( 'stm_hide_old_headers', false ) ) :

	$header_layout  = stm_get_header_layout();
	$top_bar_layout = '';

	if ( 'boats' === $header_layout || 'car_dealer_two' === $header_layout ) {
		$top_bar_layout = '-boats';
	}

	if ( ! stm_is_auto_parts() ) {
		if ( 'boats' === $header_layout || 'car_dealer_two' === $header_layout ) {
			?>
				<div id="stm-boats-header" class="<?php echo esc_attr( $class ); ?>">
			<?php
		}

		if ( ! is_404() && ! is_page_template( 'coming-soon.php' ) ) {
			if ( 'listing_five' === $header_layout ) {
				get_template_part( 'partials/header/header-classified-five/header' );
			} elseif ( 'ev_dealer' === $header_layout ) {
				get_template_part( 'partials/header/header-ev-dealer/header' );
			} else {
				get_template_part( 'partials/top', 'bar' . $top_bar_layout );
				?>
				<div id="header">
				<?php get_template_part( 'partials/header/header-' . $header_layout ); ?>
				</div> <!-- id header -->
				<?php
			}
		} elseif ( is_page_template( 'coming-soon.php' ) ) {
			get_template_part( 'partials/header/header-coming', 'soon' );
		} else {
			get_template_part( 'partials/header/header', '404' );
		}
		?>

		<?php
		if ( ( ! is_404() && ! is_page_template( 'coming-soon.php' ) ) && 'boats' === $header_layout || 'car_dealer_two' === $header_layout ) {
			?>
				</div>
			<?php
			get_template_part( 'partials/header/header-boats-mobile' );
		}
		?>
		<?php
	} else {
		if ( is_404() ) {
			get_template_part( 'partials/header/header', '404' );
		} elseif ( is_page_template( 'coming-soon.php' ) ) {
			get_template_part( 'partials/header/header-coming', 'soon' );
		} else {
			do_action( 'stm_hb', array( 'header' => 'stm_hb_settings' ) );
		}
	}
	?>
		<div id="main">
	<?php
	else :
		if ( is_404() ) {
			get_template_part( 'partials/header/header', '404' );
		} else {
			do_action( 'stm_motors_header' );
		}
	endif;

	wp_reset_postdata();
	?>
