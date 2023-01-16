<?php

add_action( 'admin_menu', 'ee_add_settings_page' );
function ee_add_settings_page() {
	add_submenu_page( 'options-general.php',
		__( 'ARS Settings', 'ears-virtual-donations' ),
		__( 'ARS Settings', 'ears-virtual-donations' ),
		'manage_options',
		'ars_settings',
		'ars_admin_settings_page' );
}

function ars_admin_settings_page() {
	?>

	<h1><?php _e( 'ARS sheltered animals settings', 'ars-virtual-donations' ) ?></h1>

	<?php

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'ears-virtual-donations' ) );
	}

	$ars_settings = get_option( 'ars-settings' );

	if ( isset( $_POST['dogs-term-id'] ) ) {
		if ( empty( $ars_settings ) ) {
			$ars_settings = [ 'animal-terms' ];
		}

		$ars_settings['animal-terms']['dogs']                   = (int) $_POST['dogs-term-id'];
		$ars_settings['animal-terms']['cats']                   = (int) $_POST['cats-term-id'];
		$ars_settings['animal-terms']['horses']                 = (int) $_POST['horses-term-id'];
		$ars_settings['animal-terms']['other']                  = (int) $_POST['farm-animals-term-id'];
		$ars_settings['checkout-page']                          = (int) $_POST['checkout-donation'];
		$ars_settings['thank-you-page']                         = (int) $_POST['thank-you-page'];
		$ars_settings['my-subscriptions-page']                  = (int) $_POST['my-subscriptions-page'];
		$ars_settings['payment-methods']['paypal']['client_id'] = $_POST['paypal-client-id'];
		$ars_settings['payment-methods']['paypal']['secret']    = ars_encrypt_data( $_POST['paypal-secret-key'] );
		$ars_settings['payment-methods']['paypal']['test']      = isset($_POST['paypal-test-env']) ? 'true' : '';

		update_option( 'ars-settings', $ars_settings );
		echo '<div class="updated"><p><strong>' . __( 'Settings saved.', 'ears-virtual-donations' ) . '</strong></p></div>';
	}

	$terms = get_terms( [
		'taxonomy'   => 'kind-of-animal',
		'hide_empty' => false,
	] );

	$pages = get_posts( [
		'post_type'      => 'page',
		'posts_per_page' => - 1,
	] );

	$dogs_tax_id         = ! empty( $ars_settings['animal-terms']['dogs'] ) ? (int) $ars_settings['animal-terms']['dogs'] : 0;
	$cats_tax_id         = ! empty( $ars_settings['animal-terms']['cats'] ) ? (int) $ars_settings['animal-terms']['cats'] : 0;
	$horses_tax_id       = ! empty( $ars_settings['animal-terms']['horses'] ) ? (int) $ars_settings['animal-terms']['horses'] : 0;
	$other_tax_id        = ! empty( $ars_settings['animal-terms']['other'] ) ? (int) $ars_settings['animal-terms']['other'] : 0;
	$checkout_page_id    = ! empty( $ars_settings['checkout-page'] ) ? $ars_settings['checkout-page'] : 0;
	$thank_you_page_id   = ! empty( $ars_settings['thank-you-page'] ) ? $ars_settings['thank-you-page'] : 0;
	$my_subscriptions_id = ! empty( $ars_settings['my-subscriptions-page'] ) ? $ars_settings['my-subscriptions-page'] : 0;
	$paypal_client_id    = ! empty( $ars_settings['payment-methods']['paypal']['client_id'] ) ? $ars_settings['payment-methods']['paypal']['client_id'] : '';
	$paypal_secret       = ! empty( $ars_settings['payment-methods']['paypal']['secret'] ) ? ars_decrypt_data( $ars_settings['payment-methods']['paypal']['secret'] ) : '';
	$paypal_is_test      = ! empty( $ars_settings['payment-methods']['paypal']['test'] );

	$dogs_options          = ars_get_selected_options_for_the_admin_settings( $dogs_tax_id, $terms );
	$cats_options          = ars_get_selected_options_for_the_admin_settings( $cats_tax_id, $terms );
	$horses_options        = ars_get_selected_options_for_the_admin_settings( $horses_tax_id, $terms );
	$farm_options          = ars_get_selected_options_for_the_admin_settings( $other_tax_id, $terms );
	$checkout_page         = ars_get_selected_options_for_the_admin_settings_by_page( $pages, $checkout_page_id );
	$thank_you_page        = ars_get_selected_options_for_the_admin_settings_by_page( $pages, $thank_you_page_id );
	$my_subscriptions_page = ars_get_selected_options_for_the_admin_settings_by_page( $pages, $my_subscriptions_id );
	?>
	<form name="form1" method="post" action="">
		<p><?php _e( 'Set the categories for the animals', 'ars-virtual-donations' ) ?></p>
		<table class="form-table">
			<tbody>
			<!--Dogs categories-->
			<tr>
				<th>
					<label for="dogs-term-id">
						<?php _e( "Dogs Category", "ars-virtual-donations" ); ?>
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
						<?php _e( "Cats Category", "ars-virtual-donations" ); ?>
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
						<?php _e( "Horses Category", "ars-virtual-donations" ); ?>
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
						<?php _e( "Farm animals Category", "ars-virtual-donations" ); ?>
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
						<?php _e( "Checkout page", "ars-virtual-donations" ); ?>
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
						<?php _e( "Thank you page", "ars-virtual-donations" ); ?>
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
						<?php _e( "Manage subscriptions page", "ars-virtual-donations" ); ?>
					</label>
				</th>
				<td>
					<select name="my-subscriptions-page" id="my-subscriptions-page">
						<?php echo $my_subscriptions_page ?>
					</select>
				</td>
			</tr>
			</tbody>
		</table>

		<hr>

		<h4><?php _e( "PayPal payment method", "ars-virtual-donations" ); ?></h4>

		<table class="form-table">
			<tbody>
			<tr>
				<th>
					<label for="paypal-client-id">
						<?php _e( "Client ID", "ars-virtual-donations" ); ?>
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
						<?php _e( "Client ID", "ars-virtual-donations" ); ?>
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
						<?php _e( "Activate test environment", "ars-virtual-donations" ); ?>
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
				   value="<?php esc_attr_e( 'Save Changes', 'ars-virtual-donations' ) ?>"/>
		</p>
	</form>

	<?php
}


function ars_get_selected_options_for_the_admin_settings( $search_term_id, $terms ): string {
	// build the select options for the categories
	$options = "<option value='0'>- select -</option>";
	foreach ( $terms as $term ) {
		$selected = ( $search_term_id !== 0 && $search_term_id === $term->term_id ) ? 'selected="selected"' : '';
		$options  .= "<option $selected value='$term->term_id'>$term->name</option>";
	}

	return $options;
}

function ars_get_selected_options_for_the_admin_settings_by_page( $pages, $page_id ): string {
	$options = "<option value='0'>- select -</option>";
	foreach ( $pages as $page ) {
		$selected = ( $page_id !== 0 && $page_id === $page->ID ) ? 'selected="selected"' : '';
		$options  .= "<option $selected value='$page->ID'>$page->post_title</option>";
	}

	return $options;
}
