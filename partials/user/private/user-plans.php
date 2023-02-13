<?php
$plans               = ( class_exists( 'Subscriptio_User' ) ) ? Subscriptio_User::find_subscriptions( true, get_current_user_id() ) : subscriptio_get_customer_subscriptions( get_current_user_id() );
$subscription_option = ( class_exists( 'RP_SUB' ) ) ? get_option( 'rp_sub_settings', '' ) : '';
$allow_pausing       = false;
$allow_canceling     = false;
$renewal_day         = 1;

if ( $subscription_option ) {
	$renewal_day     = ( ! empty( $subscription_option[1]['renewal_order_offset'] ) ) ? $subscription_option[1]['renewal_order_offset'] : $renewal_day;
	$allow_pausing   = 'not_allowed' !== $subscription_option[1]['customer_pausing'];
	$allow_canceling = 'not_allowed' !== $subscription_option[1]['customer_cancelling'];
}

?>
<div class="stm-plans-grid">
<?php

foreach ( $plans as $plan ) :
	/*
	 * TODO
	 * 'Subscriptio_User' will be removed
	*/

	if ( ! $plan ) {
		continue;
	}

	$stm_status = ( class_exists( 'Subscriptio_User' ) ) ? $plan->status : $plan->get_status();

	if ( class_exists( 'Subscriptio_User' ) ) {
		$subs_id    = $plan->id;
		$plan_name  = ( ! empty( $plan->products_multiple ) ) ? $plan->products_multiple[0]['product_name'] : $plan->product_name;
		$product_id = $plan->product_id;
		$expires    = $plan->payment_due_readable;
		$used_quota = MultiplePlan::getUsedQuota( $subs_id );

		if ( empty( $product_id ) && ! empty( $plan->products_multiple ) && is_array( $plan->products_multiple ) ) {
			$products = $plan->products_multiple;
			if ( ! empty( $products[0] ) && ! empty( $products[0]['product_id'] ) ) {
				$product_id = $products[0]['product_id'];
			}
		}
	} else {
		$initial_order = $plan->get_initial_order()->get_data();
		$key           = key( $initial_order['line_items'] );
		$order_data    = $initial_order['line_items'][ $key ]->get_data();
		$subs_id       = $plan->get_id();
		$plan_name     = $order_data['name'];
		$product_id    = $order_data['product_id'];
		$expires       = ( ! empty( $plan->get_scheduled_subscription_expire() ) && in_array( $stm_status, array( 'active', 'trial' ), true ) ) ? $plan->get_scheduled_subscription_expire()->format( 'm/d/Y H:i' ) : esc_html__( 'Expired', 'motors' );
		$renew         = false;
		$date_expires  = strtotime( $expires );
		$date_now      = time();
		$date_diff     = ( $date_expires - $date_now ) / ( 60 * 60 * 24 );

		if ( 0 !== $renewal_day && $date_diff <= 0 ) {
			$renew = true;
		}

		$used_quota = MultiplePlan::getUsedQuota( $plan->get_id() );
	}

	$post_limit     = intval( get_post_meta( $product_id, 'stm_price_plan_quota', true ) );
	$plan_unique_id = 'stm-start-countdown-plan-' . wp_rand( 1, 99999 );

	?>
	<div class='stm-plan-grid-item-wrap'>
		<div class='stm-pricing-table heading-font'>
			<div class='stm-pricing-table__title'><?php echo esc_html( $plan_name ); ?></div>
			<ul class='stm-pricing-table__features'>
				<li class='stm-pricing-table__feature'>
					<div class='stm-pricing-table__feature-label'><?php echo esc_html__( 'Status', 'motors' ); ?></div>
					<div class='stm-pricing-table__feature-value'>
						<?php echo esc_html( strtoupper( $stm_status ) ); ?>
					</div>
				</li>
				<li class='stm-pricing-table__feature'>
					<div class='stm-pricing-table__feature-label'><?php echo esc_html__( 'Used slots', 'motors' ); ?></div>
					<div class='stm-pricing-table__feature-value'>
						<?php echo esc_html( $used_quota ); ?> / <?php echo esc_html( $post_limit ); ?>
					</div>
				</li>
				<li class='stm-pricing-table__feature'>
					<div class='stm-pricing-table__feature-label'><?php echo esc_html__( 'Expire Through', 'motors' ); ?></div>
					<div id='<?php echo esc_attr( $plan_unique_id ); ?>' class='stm-pricing-table__feature-value'>
						<?php echo wp_kses_post( stm_do_lmth( $expires ) ); ?>
					</div>
				</li>
				<li class='stm-pricing-table__feature btn-wrap'>
					<div class='stm-pricing-table__feature-value'>
						<?php if ( $allow_pausing ) : ?>
							<?php if ( ! $renew && 'paused' !== $stm_status && in_array( $plan->get_previous_status(), array( 'trial', 'paused', 'pending' ), true ) ) : ?>
								<button class="stm-btn-plan-pause" data-msgblock="<?php echo esc_attr( $plan_unique_id . '-msg' ); ?>" data-userid="<?php echo esc_attr( get_current_user_id() ); ?>" data-subsid="<?php echo esc_attr( $subs_id ); ?>" data-status="wc-paused"><?php echo esc_html__( 'Pause', 'motors' ); ?></button>
							<?php elseif ( 'paused' === $stm_status ) : ?>
								<button class="stm-btn-plan-trial" data-msgblock="<?php echo esc_attr( $plan_unique_id . '-msg' ); ?>"  data-userid="<?php echo esc_attr( get_current_user_id() ); ?>" data-subsid="<?php echo esc_attr( $subs_id ); ?>" data-status="wc-trial"><?php echo esc_html__( 'Start', 'motors' ); ?></button>
							<?php endif; ?>
						<?php endif; ?>
						<?php if ( $allow_canceling ) : ?>
							<button class="stm-btn-plan-cancel" data-msgblock="<?php echo esc_attr( $plan_unique_id . '-msg' ); ?>"  data-userid="<?php echo esc_attr( get_current_user_id() ); ?>" data-subsid="<?php echo esc_attr( $subs_id ); ?>" data-status="wc-cancelled"><?php echo esc_html__( 'Cancel', 'motors' ); ?></button>
						<?php endif; ?>
					</div>
				</li>
			</ul>
			<div class="<?php echo esc_attr( $plan_unique_id . '-msg' ); ?> stm-response-msg"></div>
		</div>
		<?php
		if ( $renew && in_array( $stm_status, array( 'active', 'trial' ), true ) && 'Expired' !== $expires ) :
			?>
			<script type='text/javascript'>
				jQuery(document).ready(function(){
					var $ = jQuery;
					$('#<?php echo esc_attr( $plan_unique_id ); ?>')
						.countdown('<?php echo esc_js( stm_do_lmth( $expires ) ); ?>', function (event) {
							$(this).text(
								<?php if ( $renewal_day > 1 ) : ?>
								event.strftime('%d day %H:%M:%S')
								<?php else : ?>
								event.strftime('%H:%M:%S')
								<?php endif; ?>
							);
						});
				})
			</script>
		<?php endif; ?>
	</div>
<?php endforeach; ?>
	<div class='stm-plan-grid-item-wrap'>
		<div class='stm-pricing-table heading-font'>
			<div class='stm-pricing-table__title'><?php echo esc_html__( 'Get Plan', 'motors' ); ?></div>
			<a href="<?php echo esc_url( stm_pricing_link() ); ?>" class="get-new-link">
				<div class="get-new-btn">
					<i class="fas fa-plus"></i>
				</div>
			</a>
		</div>
	</div>
</div>
