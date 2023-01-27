<?php
/**
 * @var int $post_id
 * @var array $va_settings
 */
$sheltered_animal = get_post( $post_id );
$paypal_client_id = $va_settings['payment-methods']['paypal']['client_id'];
if ( empty( $sheltered_animal ) ) {
	include_once( VA_ABS . '/templates/parts/no-animals-found.php' );
} else {
	$age   = get_post_meta( $post_id, 'animals-age', true );
	$image = get_the_post_thumbnail_url( $post_id, 'small' );
	$name  = $sheltered_animal->post_title;
	$user  = wp_get_current_user();

	$subscription_plans = get_posts( [
		'post_type'     => 'va-subscription-plan',
		'post_status'   => 'publish',
		'posts_per_age' => - 1,
		'meta_key'      => 'cost',
		'orderby'       => 'meta_value_num',
		'order'         => 'ASC',
	] );

	$plans          = [];
	$currency_signs = [
		'eur' => '€',
		'usd' => '$',
		'gbp' => '£'
	];
	if ( ! empty( $subscription_plans ) ) {
		foreach ( $subscription_plans as $plan ) {
			$meta    = get_post_meta( $plan->ID );
			$plans[] = [
				'price'                  => $meta['cost'][0],
				'currency'               => $currency_signs[ $meta['currency'][0] ],
				'subscription_paypal_id' => $meta['paypal_plan_id'][0]
			];
		}
	}
	?>
	<div class="checkout-container">
		<div class="checkout-donation-list">
			<div class="single-donation-block">
				<div class="animal-profile-imag" style="background-image: url('<?php echo $image; ?>')"></div>
				<div class="animal-details">
					<h5><?php echo $name ?></h5>
					<span><strong><?php _e( 'Age', 'virtual-adoption' ) ?>:</strong> <?php echo $age ?></span>
				</div>
			</div>
		</div>
		<div>
			<h4><strong><?php _e( 'Sponsorship amount per month', 'virtual-adoption' ) ?></strong></h4>
			<div class="donation-amounts">
				<?php
				if ( $plans !== [] ) {
					foreach ( $plans as $plan ) {
						?>
						<label>
							<input value="<?php echo $plan['price'] ?>" type="radio" name="selected-amount"
								   data-subscription="<?php echo $plan['subscription_paypal_id'] ?>">
							<?php echo $plan['currency'] . ' ' . $plan['price'] ?>
						</label>
						<?php
					}
				} else {
					_e( 'No subscription plans are found.', 'virtual-donations' );
				}
				?>

			</div>

			<h4><strong><?php _e( 'Account Details', 'virtual-adoption' ) ?></strong></h4>
			<div class="contact-details">
				<?php
				if ( $user->ID === 0 ) {
					include_once 'registration-form.php';
				}
				?>
				<p>
					<label for="gift-donation">
						<input name="gift-donation" id="gift-donation" type="checkbox">
						<?php _e( 'This will be a gift', 'virtual-adoption' ) ?>
					</label>
				</p>
				<p class="email-gift">
					<label for="email-gift">
						<input id="email-gift" name="email-gift" type="email"
							   placeholder="<?php _e( "Gift receiver's email", 'virtual-adoption' ); ?>">
					</label><br>
					<small><?php _e( 'The updates about the selected animal will be sent to that email.' ) ?></small>
				</p>
			</div>
			<div class="donation-payment-methods">
				<p class="form-row">
					<label for="terms">
						<input type="checkbox" name="terms" id="terms">
						<span>
							I have read and agree to the website
							<a href="https://toafl.com/terms-conditions" target="_blank">terms and conditions</a>
						</span>&nbsp;<abbr class="required" title="required">*</abbr>
					</label>
					<input type="hidden" name="terms-field" value="1">
				</p>
				<div id='terms-error' class="alert-danger hidden">
					<?php _e( 'You need to accept the terms and conditions.', 'virtual-adoption' ); ?>
				</div>
				<div id="subscription-plan-error">
					<?php _e( 'You need to select a monthly donation amount', 'virtual-adoption' ); ?>
				</div>
				<div id="paypal-button-container"></div>
				<input type="hidden" id="plan-id">
				<button
					type="button"
					hidden
					name="virtual-checkout-submit"
					id="submit-sponsorship"
					value="Submit sponsorship"
				>
					Submit sponsorship <span></span>
				</button>
				<input type="hidden" id="animal-id" value="<?php echo $_GET['aid'] ?>">
				<?php wp_nonce_field( 'va-taina', 'turbo-security' ); ?>
			</div>
		</div>
	</div>
	<?php
}
