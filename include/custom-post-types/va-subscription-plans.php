<?php

function va_subscriptions_plans_post_type() {
	$labels = array(
		'name'               => _x( 'Subscription plans', 'post type general name', 'virtual-donations' ),
		'singular_name'      => _x( 'Subscription plan', 'post type singular name', 'virtual-donations' ),
		'menu_name'          => _x( 'Subscription plans', 'admin menu', 'virtual-donations' ),
		'name_admin_bar'     => _x( 'Subscription plan', 'add new on admin bar', 'virtual-donations' ),
		'add_new'            => _x( 'Add New Subscription plan', 'Subscription plan', 'virtual-donations' ),
		'add_new_item'       => __( 'Add New Subscription plan', 'virtual-donations' ),
		'new_item'           => __( 'New subscription plan', 'virtual-donations' ),
		'edit_item'          => __( 'Edit subscription plan', 'virtual-donations' ),
		'view_item'          => __( 'View subscription plan', 'virtual-donations' ),
		'all_items'          => __( 'All subscription plans', 'virtual-donations' ),
		'search_items'       => __( 'Search subscription plans', 'virtual-donations' ),
		'parent_item_colon'  => __( 'Parent subscription plans:', 'virtual-donations' ),
		'not_found'          => __( 'No subscription plan found.', 'virtual-donations' ),
		'not_found_in_trash' => __( 'No subscription plan found in Trash.', 'virtual-donations' )
	);

	$args = array(
		'labels'              => $labels,
		'description'         => __( 'Description.', 'virtual-donations' ),
		'public'              => false,
		'publicly_queryable'  => false,
		'show_ui'             => true,
		'show_in_nav_menus'   => false,
		'show_in_menu'        => true,
		'query_var'           => false,
		'rewrite'             => array( 'slug' => 'va-subscription-plan' ),
		'capability_type'     => 'post',
		'exclude_from_search' => true,
		'has_archive'         => false,
		'hierarchical'        => false,
		'menu_position'       => null,
		'supports'            => array( 'title' )
	);
	register_post_type( 'va-subscription-plan', $args );
}

add_action( 'init', 'va_subscriptions_plans_post_type' );


/**
 * Register meta box for subscription's details. There is no metadata that is being saved with this meta box.
 */
function va_meta_info_about_subscription_plans() {
	add_meta_box(
		'subscription-plan',
		__( 'Subscription plan details', 'virtual-adoptions' ),
		'va_subscription_plan_details',
		'va-subscription-plan',
		'normal'
	);
}

add_action( 'add_meta_boxes', 'va_meta_info_about_subscription_plans' );


/**
 * Function to display the details of the subscription in the admin
 *
 * @param WP_Post $post
 *
 * @return void
 */
function va_subscription_plan_details( WP_Post $post ) {
	$plan_meta         = get_post_meta( $post->ID );
	$va_settings       = get_option( 'va-settings' );
	$paypal_test       = ! empty( $va_settings['payment-methods']['paypal']['test'] );
	$plan_costs        = ! empty( $plan_meta['cost'] ) ? (float) $plan_meta['cost'][0] : '';
	$currency          = ! empty( $plan_meta['currency'] ) ? $plan_meta['currency'][0] : '';
	$paypal_product_id = ! empty( $plan_meta['paypal_product_id'] ) ? $plan_meta['paypal_product_id'][0] : '';
	$paypal_plan_id    = ! empty( $plan_meta['paypal_plan_id'] ) ? $plan_meta['paypal_plan_id'][0] : '';
	?>
	<div>
		<?php if ( $paypal_test ) : ?>
			<div style="background-color: #fd5f5f;border: 2px solid #790707; padding: 10px 20px">
				<p><?php _e( 'PayPal is set in test mode. Check the plugin\'s settings, as well as your paypal developer\'s account. Creating a new subscription in sandbox mode will reflect only the test business account and not the live account.', 'virtual-adoptions' ); ?></p>
			</div>
		<?php endif; ?>
		<p>
			<label><?php _e( 'Plan cost', 'virtual-adoptions' ) ?>:
				<input type="number" value="<?php echo $plan_costs; ?>" name="plan-cost" style="width: 150px;">
			</label> <br>
			<label><?php _e( 'Currency', 'virtual-adoptions' ) ?>:
				<select name="plan-currency">
					<option><?php _e( '-- Select currency --', 'virtual-adoptions' ) ?></option>
					<option <?php echo $currency === 'eur' ? 'selected' : '' ?> value="eur">EUR</option>
					<option <?php echo $currency === 'usd' ? 'selected' : '' ?> value="usd">USD</option>
					<option <?php echo $currency === 'gbp' ? 'selected' : '' ?> value="gbp">GBP</option>
				</select>
			</label>
		</p>
		<br>
		<p>
			<label><?php _e( 'PayPal product ID', 'virtual-adoptions' ); ?>:<br>
				<input type="text" readonly value="<?php echo $paypal_product_id; ?>" style="width: 300px;">
			</label>
		</p>
		<p>
			<label><?php _e( 'PayPal subscription plan ID', 'virtual-adoptions' ); ?>:<br>
				<input type="text" readonly value="<?php echo $paypal_plan_id; ?>" style="width: 300px;">
			</label>
		</p>
		<div style="background-color: #d7cca1;border: 2px solid gray; padding: 10px 20px">
			<p><?php _e( 'Once you have created a plan, and it got its PayPal product and subscription ID it is not possible to change the amount. You may update the amount here, but this will not update the PayPal subscription plan and you will have to do this manually. Moving the current plan to trash will deactivate the plan on PayPal and will also cancel the subscriptions that were made with it. You can restore it later from the the trash, which will reactivate the PayPal plan, but will NOT resubscribe the old subscribers to it.', 'virtual-adoptions' ) ?></p>
		</div>
	</div>
	<?php wp_nonce_field( 'va-subscription-plan-meta', 'va-power-dog' ); ?>
	<?php
}


function va_save_subscription_plan_meta( int $post_id, WP_Post $post ) {
	$nonce_name = $_POST['va-power-dog'] ?? '';
	if ( ! wp_verify_nonce( $nonce_name, 'va-subscription-plan-meta' ) ) {
		return;
	}

	$currency = '';
	if ( ! empty( $_POST['plan-currency'] ) && in_array( $_POST['plan-currency'], [ 'gbp', 'eur', 'usd' ] ) ) {
		$currency = $_POST['plan-currency'];
		update_post_meta( $post_id, 'currency', $currency );
	} else {
		delete_post_meta( $post_id, 'currency' );
	}

	$price = 0;
	if ( ! empty( $_POST['plan-cost'] ) ) {
		$price = (float) $_POST['plan-cost'];
		update_post_meta( $post_id, 'cost', (float) $_POST['plan-cost'] );
	} else {
		delete_post_meta( $post_id, 'cost' );
	}

	if ( $post->post_status === 'publish' && $price !== 0 && $currency !== '' ) {

		$paypal_product_id = get_post_meta( $post_id, 'paypal_product_id', true );
		$paypal_plan_id    = get_post_meta( $post_id, 'paypal_plan_id', true );

		// Check if we are missing any of the two PayPal IDs, so we will create them
		if ( empty( $paypal_product_id ) || empty( $paypal_plan_id ) ) {
			$VA_paypal = new VA_PayPal();

			// checks for error during initialization (authentication of PayPal)
			if ( $VA_paypal->get_error() !== '' ) {
				$message = $VA_paypal->get_error() . "\n\r";
				va_log_report( 'error.log', $message );
				return;
			}

			// Adds product in PayPal if we don't have one
			if ( empty( $paypal_product_id ) ) {
				$paypal_product_id = uniqid( 'product-', true );
				$product           = $VA_paypal->create_product( $post->post_title, $paypal_product_id );
				if ( ! empty( $product ) ) {
					update_post_meta( $post_id, 'paypal_product_id', $paypal_product_id );
				} else {
					$message = $VA_paypal->get_error() . "\n\r";
					va_log_report( 'error.log', $message );
					return;
				}
			}

			// Creates the subscription
			if ( empty( $paypal_plan_id ) ) {
				$subscription_plan = $VA_paypal->create_subscription_plan( $paypal_product_id, $post->post_title, $price, $currency );
				if ( ! empty( $subscription_plan ) ) {
					update_post_meta( $post_id, 'paypal_plan_id', $subscription_plan['id'] );
				} else {
					$message = $VA_paypal->get_error() . "\n\r";
					va_log_report( 'error.log', $message );
				}
			}
		}
	}
}

add_action( 'save_post_va-subscription-plan', 'va_save_subscription_plan_meta', 10, 2 );


/**
 * Hook on moving a post subscription's entry into the trash. Updates the status of the va-subscriptions entry to "cancelled".
 *
 * @param int $post_id
 *
 * @return void
 */
function va_deactivate_subscription_plan_on_trashing_plan( int $post_id ) {
	$post = get_post( $post_id );
	if ( $post->post_status !== 'va-subscription-plan' ) {
		return;
	}
	// check if there are any subscriptions attached to this plan that are active
	global $wpdb;
	$sql = "SELECT ID, post_id FROM {$wpdb->prefix}va_subscriptions
			WHERE subscription_plan_id = $post_id AND status = 'va-active'";

	$subscriptions = $wpdb->get_results( $sql );

	if ( ! empty( $subscriptions ) ) {
		// update the post status of the WP posts entries. This will trigger the post-status change hook
		// and eventually will cancel the PayPal subscriptions as well as update our database
		foreach ( $subscriptions as $subscription ) {
			$wpdb->update(
				$wpdb->prefix . 'posts',
				[ 'post_status' => 'va-cancelled' ],
				[ 'post_id' => $subscription->post_id ],
				[ '%s' ],
				[ '%d' ]
			);
		}
	}

	// deactivate plan on PayPal
	$paypal_plan_id = get_post_meta( $post_id, 'paypal_plan_id', true );
	if ( empty( $paypal_plan_id ) ) {
		return;
	}

	$VA_paypal = new VA_PayPal();
	$VA_paypal->change_active_state_of_subscription_plan( $paypal_plan_id, 'deactivate' );
	if ( $VA_paypal->get_error() ) {
		$message = $VA_paypal->get_error() . "\n\r";
		va_log_report( 'error.log', $message );
	}
	va_log_report( 'success.log', "Successfully deactivated PayPal plan with ID: $paypal_plan_id \n\r" );
}

add_action( 'wp_trash_post', 'va_deactivate_subscription_plan_on_trashing_plan' );


/**
 * When subscription plan is restored from trash reactivate the PayPal plan.
 *
 * @param int $post_id
 * @param string $previous_status Old post status before being sent to trash
 */
function va_reactivate_plan_when_post_restored_from_trash( int $post_id, string $previous_status ) {
	$post = get_post( $post_id );
	if ( $post->post_status !== 'va-subscription-plan' ) {
		return;
	}
	if ( $previous_status !== 'publish' ) {
		return;
	}

	// check if we have connected PayPal's plan to our post
	$paypal_plan_id = get_post_meta( $post_id, 'paypal_plan_id', true );
	if ( empty( $paypal_plan_id ) ) {
		return;
	}

	// reactivate the PayPal subscription plan
	$VA_paypal = new VA_PayPal();
	$VA_paypal->change_active_state_of_subscription_plan( $paypal_plan_id, 'activate' );
	va_log_report( 'success.log', "Successfully activated PayPal plan with ID: $paypal_plan_id \n\r" );
}

add_action( 'untrash_post', 'va_reactivate_plan_when_post_restored_from_trash', 10, 2 );
