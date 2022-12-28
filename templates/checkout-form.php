<?php
/**
 * @var int $post_id
 */
$sheltered_animal = get_post( $post_id );
if ( empty( $sheltered_animal ) ) {
	include_once 'no-animals-found.php';
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
					<span><strong><?php _e( 'Age', 'ars-virtual-donation' ) ?>:</strong> <?php echo $age ?></span>
				</div>
			</div>
		</div>
		<div class="checkout-fields">
			<h4><strong><?php _e( 'Sponsorship amount per month', 'ars-virtual-donation' ) ?></strong></h4>
			<div class="donation-amounts">
				<?php if ( ars_is_wpml_activated() && ICL_LANGUAGE_CODE === 'en' ) { ?>
					<label for="eur5"><input id="eur5" value="5" type="radio" name="selected-amount">€ 5.00</label>
					<label for="eur10" class="selected-donation-amount"><input id="eur10" checked value="10" type="radio" name="selected-amount">€ 10.00</label>
					<label for="eur20"><input id="eur20" value="20" type="radio" name="selected-amount">€ 20.00</label>
					<label for="custom-amount">
						<input id="custom-amount" value="custom" type="radio" name="selected-amount">Custom
					</label>
					<label class="part-element" for="selected-custom-amount">€
						<input id="selected-custom-amount" class="custom-amount" value="5" type="number"
							   name="selected-custom-amount" min="5">
					</label>
				<?php } else { ?>
					<label for="lv5"><input id="lv5" value="5" type="radio" name="selected-amount">5.00 лв.</label>
					<label for="lv10" class="selected-donation-amount"><input id="lv10" checked value="10" type="radio" name="selected-amount">10.00 лв.</label>
					<label for="lv20"><input id="lv20" value="20" type="radio" name="selected-amount">20.00 лв.</label>
					<label for="custom-amount">
						<input id=custom-amount"" value="custom" type="radio" name="selected-amount">Посочена сума
					</label>
					<label class="part-element" for="selected-custom-amount">
						<input id="selected-custom-amount" class="custom-amount" value="5" type="number"
							   name="selected-custom-amount" min="5">лв.
					</label>
				<?php } ?>
			</div>
			<h4><strong><?php _e( 'Account Details', 'ars-virtual-donation' ) ?></strong></h4>
			<div class="contact-details">
				<?php if ( $user->ID === 0 ) { ?>
					<p><?php _e( 'We need to create an account for you so you can manage your monthly support. Please fill in the details below.', 'ars-virtual-donation' ); ?></p>
					<p>
						<label for="first-name">
							<input id="first-name" name="first-name" type="text"
								   placeholder="<?php _e( 'First name', 'ars-virtual-donations' ); ?>">
						</label>
					</p>
					<p>
						<label for="last-name">
							<input id="last-name" name="last-name" type="text"
								   placeholder="<?php _e( 'Last name', 'ars-virtual-donations' ); ?>">
						</label>
					</p>
					<p>
						<label for="email">
							<input id="email" name="email" type="email"
								   placeholder="<?php _e( 'Email', 'ars-virtual-donations' ); ?>">
						</label>
					</p>
					<p>
						<label for="contact-phone">
							<input id="contact-phone" name="contact-phone" type="text"
								   placeholder="<?php _e( 'Phone (optional)', 'ars-virtual-donations' ); ?>">
						</label>
					</p>
					<p>
						<label for="password">
							<input id="password" name="password"
								   type="text" placeholder="<?php _e( 'Password', 'ars-virtual-donations' ); ?>">
						</label>
					</p>
				<?php } ?>
				<p>
					<label for="gift-donation">
						<input name="gift-donation" id="gift-donation" type="checkbox">
						<?php _e( 'This will be a gift', 'ars-virtual-donations' ) ?>
					</label>
				</p>
				<p class="email-gift">
					<label for="email-gift">
						<input id="email-gift" name="email-gift" type="email"
							   placeholder="<?php _e( "Gift receiver's email", 'ars-virtual-donations' ); ?>">
					</label><br>
					<small><?php _e( 'The updates about the selected animal will be sent to that email.' ) ?></small>
				</p>
			</div>
			<div class="donation-payment-methods">
				<h4><strong><?php _e( 'Payment methods', 'ars-virtual-donation' ) ?></strong>:</h4>
				<ul class="payment-methods">
					<li class="payment-method payment-method-mypos">
						<input id="payment-method-mypos" type="radio" class="input-radio" name="payment-method"
							   value="stripe" checked="checked">
						<label for="payment-method-mypos"> Credit/Debit Card (myPos)
							<img src="<?php echo ARSVD_URL ?>/assets/images/payments/myPos.png" alt="myPos logo">
						</label>
						<div class="payment-box payment-method-mypos">
							<span class="box-arrow"></span>
								<p>Pay with your Credit Card</p>
								<p>myPOS Checkout simplifies your online payments by managing the entire payment process
									from the time your customers wish to make a purchase to the completed purchase.</p>
						</div>
					</li>
					<li class="payment-method payment-method-paypal">
						<input id="payment-method-paypal" type="radio" class="input-radio" name="payment-method"
							   value="paypal">
						<label for="payment-method-paypal">
							PayPal <img src="<?php echo ARSVD_URL ?>/assets/images/payments/paypal.png"
										alt="PayPal logo">
						</label>
						<div class="payment-box payment-method-paypal hidden">
							<p>Pay via PayPal; you can pay with your credit card if you don’t have a PayPal account.</p>
						</div>
					</li>
				</ul>
				<p class="form-row validate-required">
					<label for="terms">
						<input type="checkbox" name="terms" id="terms">
						<span class="woocommerce-terms-and-conditions-checkbox-text">
							I have read and agree to the website
							<a href="https://toafl.com/terms-conditions" target="_blank">terms and conditions</a>
						</span>&nbsp;<abbr class="required" title="required">*</abbr>
					</label>
					<input type="hidden" name="terms-field" value="1">
				</p>
				<button type="button" name="virtual-checkout-submit"
						id="submit-sponsorship" value="Submit sponsorship">Submit sponsorship
				</button>
			</div>
		</div>
	</div>
	<?php
}
