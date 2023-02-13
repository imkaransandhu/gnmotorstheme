<?php
if ( false === stm_me_get_wpcfto_mod( 'enable_plans', false ) || false === stm_is_multiple_plans() ) {
	return false;
}

$plans         = MultiplePlan::getPlans();
$selected_plan = MultiplePlan::getCurrentPlan( $__vars['id'] );
$is_editing    = ( ! empty( $_GET['edit_car'] ) && ! empty( $_GET['item_id'] ) ) ? true : false;//phpcs:ignore

?>
<div class="stm-form-plans">
	<div class="stm-car-listing-data-single stm-border-top-unit ">
		<div class="title heading-font"><?php esc_html_e( 'Choose plan', 'motors' ); ?></div>
		<span class="step_number step_number_5 heading-font"><?php esc_html_e( 'step', 'motors' ); ?> 7</span>
	</div>
	<div id="user_plans_select_wrap">
		<?php if ( is_user_logged_in() ) { ?>
			<div class="user-plans-list" >
				<select name="selectedPlan">
					<option value=""><?php echo esc_html__( 'Select Plan', 'motors' ); ?></option>
					<?php
					foreach ( $plans['plans'] as $plan ) :
						$selected = '';
						if ( (string) $plan['plan_id'] === (string) $selected_plan && $plan['used_quota'] < $plan['total_quota'] ) {
							$selected = 'selected';
						} elseif ( (string) $plan['used_quota'] >= (string) $plan['total_quota'] ) {
							$selected = 'disabled';
						}

						if ( $is_editing && (string) $plan['plan_id'] === (string) $selected_plan && $plan['used_quota'] <= $plan['total_quota'] ) {
							$selected = 'selected';
						}
						?>

						<option value="<?php echo esc_attr( $plan['plan_id'] ); ?>" <?php echo esc_attr( $selected ); ?>>
							<?php echo wp_kses_post( sprintf( ( '%s %s / %s' ), $plan['label'], $plan['used_quota'], $plan['total_quota'] ) ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		<?php } else { ?>
			<p style="color: #888888; font-size: 13px;"><?php echo esc_html__( 'Please, log in to view your available plans', 'motors' ); ?></p>
		<?php } ?>
	</div>
</div>
