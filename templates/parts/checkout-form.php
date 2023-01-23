<?php
/**
 * @var int $post_id
 */
$sheltered_animal = get_post( $post_id );
if ( empty( $sheltered_animal ) ) {
	include_once( VA_ABS . '/templates/parts/no-animals-found.php' );
} else {
	$age   = get_post_meta( $post_id, 'animals-age', true );
	$image = get_the_post_thumbnail_url( $post_id, 'small' );
	$name  = $sheltered_animal->post_title;
	$user  = wp_get_current_user();
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
		<div class="checkout-fields">
			<h4><strong><?php _e( 'Sponsorship amount per month', 'virtual-adoption' ) ?></strong></h4>
			<div class="donation-amounts">
				<label for="eur5">
					<input id="eur5" value="5" type="radio" name="selected-amount">
					€ 5.00
				</label>
				<label for="eur10" class="selected-donation-amount">
					<input id="eur10" checked value="10" type="radio" name="selected-amount">
					€ 10.00
				</label>
				<label for="eur15" class="selected-donation-amount">
					<input id="eur15" checked value="15" type="radio" name="selected-amount">
					€ 15.00
				</label>
				<label for="eur20">
					<input id="eur20" value="20" type="radio" name="selected-amount">
					€ 20.00
				</label>
				<label for="eur30">
					<input id="eur30" value="30" type="radio" name="selected-amount">
					€ 30.00
				</label>
				<label for="eur50">
					<input id="eur50" value="50" type="radio" name="selected-amount">
					€ 50.00
				</label>
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
				<h4><strong><?php _e( 'Payment methods', 'virtual-adoption' ) ?></strong>:</h4>
				<ul class="payment-methods">
					<li class="payment-method payment-method-paypal">
						<input id="payment-method-paypal" type="radio" class="input-radio" name="payment-method"
							   value="paypal" checked>
						<label for="payment-method-paypal">
							PayPal <img src="<?php echo VA_URL ?>/assets/images/payments/paypal.png"
										alt="PayPal logo">
						</label>
					</li>
				</ul>
				<p class="form-row validate-required">
					<label for="terms">
						<input type="checkbox" name="terms" id="terms">
						<span>
							I have read and agree to the website
							<a href="https://toafl.com/terms-conditions" target="_blank">terms and conditions</a>
						</span>&nbsp;<abbr class="required" title="required">*</abbr>
					</label>
					<input type="hidden" name="terms-field" value="1">
				</p>
				<button
					type="button"
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
