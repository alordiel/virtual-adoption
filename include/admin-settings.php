<?php

add_action( 'admin_menu', 'ee_add_settings_page' );
function ee_add_settings_page() {
	add_submenu_page( 'options-general.php',
		__( 'ARS Settings', 'virtual-adoption' ),
		__( 'ARS Settings', 'virtual-adoption' ),
		'manage_options',
		'va_settings',
		'va_admin_settings_page' );
}

function va_admin_settings_page() {
	?>

	<h1><?php _e( 'ARS sheltered animals settings', 'virtual-adoption' ) ?></h1>

	<?php

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'virtual-adoption' ) );
	}

	$va_settings = get_option( 'va-settings' );

	if ( isset( $_POST['dogs-term-id'] ) ) {
		if ( empty( $va_settings ) ) {
			$va_settings = [ 'animal-terms' ];
		}

		$va_settings['animal-terms']['dogs']                   = (int) $_POST['dogs-term-id'];
		$va_settings['animal-terms']['cats']                   = (int) $_POST['cats-term-id'];
		$va_settings['animal-terms']['horses']                 = (int) $_POST['horses-term-id'];
		$va_settings['animal-terms']['other']                  = (int) $_POST['farm-animals-term-id'];
		$va_settings['page']['checkout']                       = (int) $_POST['checkout-donation'];
		$va_settings['page']['thank-you']                      = (int) $_POST['thank-you-page'];
		$va_settings['page']['my-subscriptions']               = (int) $_POST['my-subscriptions-page'];
		$va_settings['page']['login']                          = (int) $_POST['login-page'];
		$va_settings['payment-methods']['paypal']['client_id'] = $_POST['paypal-client-id'];
		$va_settings['payment-methods']['paypal']['secret']    = va_encrypt_data( $_POST['paypal-secret-key'] );
		$va_settings['payment-methods']['paypal']['test']      = isset( $_POST['paypal-test-env'] ) ? 'true' : '';

		update_option( 'va-settings', $va_settings );
		echo '<div class="updated"><p><strong>' . __( 'Settings saved.', 'virtual-adoption' ) . '</strong></p></div>';
	}

	$terms = get_terms( [
		'taxonomy'   => 'kind-of-animal',
		'hide_empty' => false,
	] );

	$pages = get_posts( [
		'post_type'      => 'page',
		'posts_per_page' => - 1,
	] );

	$dogs_tax_id         = ! empty( $va_settings['animal-terms']['dogs'] ) ? (int) $va_settings['animal-terms']['dogs'] : 0;
	$cats_tax_id         = ! empty( $va_settings['animal-terms']['cats'] ) ? (int) $va_settings['animal-terms']['cats'] : 0;
	$horses_tax_id       = ! empty( $va_settings['animal-terms']['horses'] ) ? (int) $va_settings['animal-terms']['horses'] : 0;
	$other_tax_id        = ! empty( $va_settings['animal-terms']['other'] ) ? (int) $va_settings['animal-terms']['other'] : 0;
	$checkout_page_id    = ! empty( $va_settings['page']['checkout'] ) ? $va_settings['page']['checkout'] : 0;
	$thank_you_page_id   = ! empty( $va_settings['page']['thank-you'] ) ? $va_settings['page']['thank-you'] : 0;
	$my_subscriptions_id = ! empty( $va_settings['page']['my-subscriptions'] ) ? $va_settings['page']['my-subscriptions'] : 0;
	$login_page_id       = ! empty( $va_settings['page']['login'] ) ? $va_settings['page']['login'] : 0;
	$paypal_client_id    = ! empty( $va_settings['payment-methods']['paypal']['client_id'] ) ? $va_settings['payment-methods']['paypal']['client_id'] : '';
	$paypal_secret       = ! empty( $va_settings['payment-methods']['paypal']['secret'] ) ? va_decrypt_data( $va_settings['payment-methods']['paypal']['secret'] ) : '';
	$paypal_is_test      = ! empty( $va_settings['payment-methods']['paypal']['test'] );

	$dogs_options          = va_get_selected_options_for_the_admin_settings( $dogs_tax_id, $terms );
	$cats_options          = va_get_selected_options_for_the_admin_settings( $cats_tax_id, $terms );
	$horses_options        = va_get_selected_options_for_the_admin_settings( $horses_tax_id, $terms );
	$farm_options          = va_get_selected_options_for_the_admin_settings( $other_tax_id, $terms );
	$checkout_page         = va_get_selected_options_for_the_admin_settings_by_page( $pages, $checkout_page_id );
	$thank_you_page        = va_get_selected_options_for_the_admin_settings_by_page( $pages, $thank_you_page_id );
	$my_subscriptions_page = va_get_selected_options_for_the_admin_settings_by_page( $pages, $my_subscriptions_id );
	$login_page            = va_get_selected_options_for_the_admin_settings_by_page( $pages, $login_page_id );
	?>
	<form name="form1" method="post" action="">
		<p><?php _e( 'Set the categories for the animals', 'virtual-adoption' ) ?></p>
		<table class="form-table">
			<tbody>
			<!--Dogs categories-->
			<tr>
				<th>
					<label for="dogs-term-id">
						<?php _e( "Dogs Category", "virtual-adoption" ); ?>
					</label>
				</th>
				<td>
					<select name="dogs-term-id" id="dogs-term-id">
						<?php echo $dogs_options ?>
					</select>
				</td>
			</tr>
			<!--Cats categories-->
			<tr>
				<th>
					<label for="cats-term-id">
						<?php _e( "Cats Category", "virtual-adoption" ); ?>
					</label>
				</th>
				<td>
					<select name="cats-term-id" id="cats-term-id">
						<?php echo $cats_options ?>
					</select>
				</td>
			</tr>
			<!--Horses Category-->
			<tr>
				<th>
					<label for="horses-term-id">
						<?php _e( "Horses Category", "virtual-adoption" ); ?>
					</label>
				</th>
				<td>
					<select name="horses-term-id" id="horses-term-id">
						<?php echo $horses_options ?>
					</select>
				</td>
			</tr>
			<!--Farm animals category-->
			<tr>
				<th>
					<label
						for="farm-animals-term-id">
						<?php _e( "Farm animals Category", "virtual-adoption" ); ?>
					</label>
				</th>
				<td>
					<select name="farm-animals-term-id" id="farm-animals-term-id">
						<?php echo $farm_options ?>
					</select>
				</td>
			</tr>
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
			<input type="submit" name="Submit" class="button-primary"
				   value="<?php esc_attr_e( 'Save Changes', 'virtual-adoption' ) ?>"/>

			<input type="button" name="test-api" class="button-secondary" value="test-api" id="test-api">
		</p>
	</form>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			document.getElementById('test-api').addEventListener('click', function () {
				jQuery.ajax({
					url: '/wp-admin/admin-ajax.php',
					data: {
						action: 'va_test_api'
					},
					method: 'POST',
					success: (response) => {
						alert(response)
					},
					error: (error) => {
						alert(error.code + ' > ' + error.message);
					}
				});
			})
		})
	</script>
	<?php
}


function va_get_selected_options_for_the_admin_settings( $search_term_id, $terms ): string {
	// build the select options for the categories
	$options = "<option value='0'>- select -</option>";
	foreach ( $terms as $term ) {
		$selected = ( $search_term_id !== 0 && $search_term_id === $term->term_id ) ? 'selected="selected"' : '';
		$options  .= "<option $selected value='$term->term_id'>$term->name</option>";
	}

	return $options;
}

function va_get_selected_options_for_the_admin_settings_by_page( $pages, $page_id ): string {
	$options = "<option value='0'>- select -</option>";
	foreach ( $pages as $page ) {
		$selected = ( $page_id !== 0 && $page_id === $page->ID ) ? 'selected="selected"' : '';
		$options  .= "<option $selected value='$page->ID'>$page->post_title</option>";
	}

	return $options;
}
