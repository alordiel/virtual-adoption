<?php

/**
 * Adding the Virtual Adoption's settings menu to the Settings in the admin panel
 *
 * @return void
 */
function ee_add_settings_page() {
	add_submenu_page( 'options-general.php',
		__( 'Virtual Adoptions', 'virtual-adoption' ),
		__( 'Virtual Adoptions', 'virtual-adoption' ),
		'manage_options',
		'va_settings',
		'va_admin_settings_page' );
}

add_action( 'admin_menu', 'ee_add_settings_page' );

/**
 * HTML and settings form for the virtual adoption's settings and tweaks
 *
 * @return void
 */
function va_admin_settings_page() {
	?>

	<h1><?php _e( 'Virtual Adoptions settings', 'virtual-adoption' ) ?></h1>

	<?php

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'virtual-adoption' ) );
	}

	$va_settings    = get_option( 'va-settings' );
	$has_webhook_id = ! empty( $va_settings['payment-methods']['paypal']['webhook_id'] );

	// In case we don't have any settings saved, we will create this empty array
	if ( empty( $va_settings ) ) {
		$va_settings = [
			'general'         => [],
			'page'            => [],
			'payment-methods' => [
				'paypal' => []
			]
		];
	}
	if ( isset( $_POST['submit'] ) ) {


		$va_settings['page'] = [
			'checkout'         => (int) $_POST['checkout-donation'],
			'thank-you'        => (int) $_POST['thank-you-page'],
			'my-subscriptions' => (int) $_POST['my-subscriptions-page'],
			'login'            => (int) $_POST['login-page'],
			'terms'            => (int) $_POST['terms-page'],
		];

		$va_settings['payment-methods']['paypal'] = [
			'client_id'  => $_POST['paypal-client-id'],
			'secret'     => va_encrypt_data( $_POST['paypal-secret-key'] ),
			'test'       => isset( $_POST['paypal-test-env'] ) ? 'true' : '',
			'webhook_id' => $has_webhook_id ? $va_settings['payment-methods']['paypal']['webhook_id'] : 0,
		];

		$va_settings['general'] = [
			'enable-categories' => ! empty( $_POST['enable-categories'] ) ? 'on' : 'off',
			'all-animals-logo'  => ! empty( $_POST['featured-image-id'] ) ? $_POST['featured-image-id'] : '',
		];

		// check if we have the webhook ID for PayPal and if not - create a new one;
		if ( ! $has_webhook_id && ! empty( $_POST['paypal-client-id'] ) && ! empty( $_POST['paypal-secret-key'] ) ) {
			$VA_PayPal                                              = new VA_PayPal();
			$va_settings['payment-methods']['paypal']['webhook_id'] = $VA_PayPal->create_webhook_endpoint();
		}

		update_option( 'va-settings', $va_settings );

		echo '<div class="updated"><p><strong>' . __( 'Settings saved.', 'virtual-adoption' ) . '</strong></p></div>';
	}

	$pages = get_posts( [
		'post_type'      => 'page',
		'posts_per_page' => - 1,
	] );

	$enable_categories   = ( ! empty( $va_settings['general']['enable-categories'] ) && $va_settings['general']['enable-categories'] === 'on' ) ? 'checked' : '';
	$checkout_page_id    = ! empty( $va_settings['page']['checkout'] ) ? $va_settings['page']['checkout'] : 0;
	$thank_you_page_id   = ! empty( $va_settings['page']['thank-you'] ) ? $va_settings['page']['thank-you'] : 0;
	$my_subscriptions_id = ! empty( $va_settings['page']['my-subscriptions'] ) ? $va_settings['page']['my-subscriptions'] : 0;
	$login_page_id       = ! empty( $va_settings['page']['login'] ) ? $va_settings['page']['login'] : 0;
	$terms_page_id       = ! empty( $va_settings['page']['terms'] ) ? $va_settings['page']['terms'] : 0;
	$paypal_client_id    = ! empty( $va_settings['payment-methods']['paypal']['client_id'] ) ? $va_settings['payment-methods']['paypal']['client_id'] : '';
	$paypal_secret       = ! empty( $va_settings['payment-methods']['paypal']['secret'] ) ? va_decrypt_data( $va_settings['payment-methods']['paypal']['secret'] ) : '';
	$paypal_is_test      = ! empty( $va_settings['payment-methods']['paypal']['test'] );

	$checkout_page         = va_get_selected_options_for_the_admin_settings_by_page( $pages, $checkout_page_id );
	$thank_you_page        = va_get_selected_options_for_the_admin_settings_by_page( $pages, $thank_you_page_id );
	$my_subscriptions_page = va_get_selected_options_for_the_admin_settings_by_page( $pages, $my_subscriptions_id );
	$login_page            = va_get_selected_options_for_the_admin_settings_by_page( $pages, $login_page_id );
	$terms_page            = va_get_selected_options_for_the_admin_settings_by_page( $pages, $terms_page_id );

	// Image for the "All animals"
	$image_id  = ! empty( $va_settings['general']['all-animals-logo'] ) ? (int) $va_settings['general']['all-animals-logo'] : 0;
	$image_url = '';
	if ( ! empty( $image_id ) ) {
		$image_url = wp_get_attachment_image_url( $image_id, 'medium' );
	}
	?>
	<form name="form1" method="post" action="">
		<table class="form-table">
			<tbody>
			<tr>
				<th>
					<label for="enable-categories">
						<?php _e( "Enable donation taxonomies", "virtual-adoption" ); ?>
					</label>
				</th>
				<td>
					<input type="checkbox" name="enable-categories"
						   id="enable-categories" <?php echo $enable_categories; ?>><br>
					<small>
						<?php _e( 'Enable if you have different kind of animals (like cats, dogs, etc).', 'virtual-adoptions' ); ?>
					</small>
				</td>
			</tr>
			<?php if ( $enable_categories !== '' ): ?>
				<th scope="row">
					<label for="featured-image-id">
						<?php _e( 'Image for "All animals"', 'virtual-adoptions' ); ?>
						<input id="featured-image-id" name="featured-image-id" type="hidden"
							   value="<?php echo $image_id; ?>">
					</label>
				</th>
				<td>
					<div style="display: flex;align-items: center;">
						<div style="display: flex;flex-direction: column;text-align: center;">
							<a id="featured-image-button" href="#" class="button button-primary"
							   style="margin-bottom: 10px">
								<?php _e( 'Upload image', 'virtual-adoptions' ); ?>
							</a>
							<a id="remove-image-button" href="#" class="button button-secondary"
							   style="display:<?php echo empty( $image_url ) ? 'none' : 'block'; ?>">
								<?php _e( 'Remove image', 'virtual-adoptions' ); ?>
							</a>
						</div>
						<div style="margin-left: 20px">
							<img src="<?php echo $image_url; ?>" width="100" alt="featured-image-for-kind-of-animal"
								 id="featured-image-block"
								 style="display: <?php echo ! empty( $image_url ) ? 'block' : 'none'; ?>">
						</div>
					</div>
				</td>
			<?php endif; ?>
			</tbody>
		</table>

		<hr>

		<h4><?php _e( "Donation's pages", "virtual-adoption" ); ?></h4>

		<table class="form-table">
			<tbody>
			<!--Donation Checkout page-->
			<tr>
				<th>
					<label
						for="checkout-donation">
						<?php _e( "Checkout page", "virtual-adoption" ); ?>
					</label>
				</th>
				<td>
					<select name="checkout-donation" id="checkout-donation">
						<?php echo $checkout_page ?>
					</select>
				</td>
			</tr>
			<!-- "Thank you" Page -->
			<tr>
				<th>
					<label
						for="thank-you-page">
						<?php _e( "Thank you page", "virtual-adoption" ); ?>
					</label>
				</th>
				<td>
					<select name="thank-you-page" id="thank-you-page">
						<?php echo $thank_you_page ?>
					</select>
				</td>
			</tr>
			<!-- "My Subscriptions" Page -->
			<tr>
				<th>
					<label
						for="my-subscriptions-page">
						<?php _e( "Manage subscriptions page", "virtual-adoption" ); ?>
					</label>
				</th>
				<td>
					<select name="my-subscriptions-page" id="my-subscriptions-page">
						<?php echo $my_subscriptions_page ?>
					</select>
				</td>
			</tr>
			<!-- "Login" Page -->
			<tr>
				<th>
					<label
						for="login-page">
						<?php _e( "Login page", "virtual-adoption" ); ?>
					</label>
				</th>
				<td>
					<select name="login-page" id="login-page">
						<?php echo $login_page ?>
					</select>
				</td>
			</tr>
			<!-- "Login" Page -->
			<tr>
				<th>
					<label
						for="terms-page">
						<?php _e( "Terms & Conditions page", "virtual-adoption" ); ?>
					</label>
				</th>
				<td>
					<select name="terms-page" id="terms-page">
						<?php echo $terms_page ?>
					</select>
				</td>
			</tr>
			</tbody>
		</table>

		<hr>

		<h4><?php _e( "PayPal payment method", "virtual-adoption" ); ?></h4>

		<table class="form-table">
			<tbody>
			<tr>
				<th>
					<label for="paypal-client-id">
						<?php _e( "Client ID", "virtual-adoption" ); ?>
					</label>
				</th>
				<td>
					<input type="text" name="paypal-client-id" id="paypal-client-id"
						   style="max-width: 680px;width: 100%;"
						   value="<?php echo $paypal_client_id ?>">
				</td>
			</tr>
			<tr>
				<th>
					<label for="paypal-secret-key">
						<?php _e( "Secret key", "virtual-adoption" ); ?>
					</label>
				</th>
				<td>
					<input type="password" name="paypal-secret-key" id="paypal-secret-key"
						   style="max-width: 680px;width: 100%;"
						   value="<?php echo $paypal_secret ?>">
				</td>
			</tr>
			<tr>
				<th>
					<label for="paypal-test-env">
						<?php _e( "Activate test environment", "virtual-adoption" ); ?>
					</label>
				</th>
				<td>
					<input type="checkbox" name="paypal-test-env" id="paypal-test-env"
						<?php echo $paypal_is_test ? 'checked' : ''; ?>>
				</td>
			</tr>
			</tbody>
		</table>

		<p class="submit">
			<input type="submit" name="submit" class="button-primary"
				   value="<?php _e( 'Save Changes', 'virtual-adoption' ) ?>"/>

			<input type="button" name="test-api" class="button-secondary"
				   value="<?php _e( 'Test PayPal connection', 'virtual-adoption' ) ?>" id="test-api">
		</p>
		<?php wp_nonce_field( 'va-taina', 'va-security' ); ?>
	</form>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			document.getElementById('test-api').addEventListener('click', function () {
				const thisButton = this;
				const clientID = document.getElementById('paypal-client-id').value;
				const secretKey = document.getElementById('paypal-secret-key').value;
				const security = document.getElementById('va-security').value;

				if (clientID === '' || secretKey === '') {
					alert('<?php _e( 'The PayPal client ID and secret key can not be empty.', 'virtual-adoptions' ); ?>');
					return false;
				}

				thisButton.disabled = true;

				jQuery.ajax({
					url: '/wp-admin/admin-ajax.php',
					data: {
						action: 'va_test_paypal_api_connection',
						clientID: clientID,
						secretKey: secretKey,
						security: security,
					},
					method: 'POST',
					success: (response) => {
						alert(response);
						thisButton.disabled = false;
					},
					error: (error) => {
						alert(error.code + ' > ' + error.message);
						thisButton.disabled = false;
					}
				});
			});
		});
	</script>
	<?php
}


/**
 * Builds a string of HTML <option> tags from the list of pages (and marks the selected on if there is match between the page and selected one)
 *
 * @param array $pages
 * @param int $page_id
 *
 * @return string
 */
function va_get_selected_options_for_the_admin_settings_by_page( array $pages, int $page_id ): string {
	$options = "<option value='0'>- select -</option>";
	foreach ( $pages as $page ) {
		$selected = ( $page_id !== 0 && $page_id === $page->ID ) ? 'selected="selected"' : '';
		$options  .= "<option $selected value='$page->ID'>$page->post_title</option>";
	}

	return $options;
}
