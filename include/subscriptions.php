<?php

/**
 * Creates wp_post and va_subscriptions entry in the database
 * On error it will return the error message
 *
 * @param int $animal_id
 * @param array $paypal
 * @param string $email
 *
 * @return array
 */
function va_create_new_donation_subscription( int $animal_id, array $paypal, string $email = '' ): array {
	$user = wp_get_current_user();
	if ( $user === null ) {
		return [
			'status'  => 'error',
			'message' => __( 'It seems that you are not logged in. Please log in first.', 'virtual-adoptions' )
		];
	}
	$VA_paypal    = new VA_PayPal();
	$plan_details = $VA_paypal->get_subscription_plan_details( $paypal['plan_id'] );
	$animal       = get_post( $animal_id );
	$title        = $user->first_name . ' ' . $user->last_name . ' - ' . $animal->post_title;
	$post_id      = wp_insert_post( [
		'status'      => 'va-active',
		'post_type'   => 'va-subscription',
		'post_title'  => $title,
		'post_author' => $user->ID,
		'post_status' => 'va-active',
	], true );

	if ( $post_id === 0 || is_wp_error( $post_id ) ) {
		$message = is_wp_error( $post_id ) ? $post_id->get_error_message() : __( 'Creating the subscription entry failed', 'virtual-adoptions' );
		$data    = [
			'error_message'        => $message,
			'user_id'              => $user->ID,
			'sponsored_animal_id'  => $animal_id,
			'amount'               => $plan_details['amount'],
			'next_due'             => date( "Y-m-d", strtotime( "+1 month" ) ),
			'email_for_updates'    => $email,
			'paypal_id'            => $paypal['subscription_id'],
			'subscription_plan_id' => $paypal['plan_id'],
		];
		va_record_error_with_creating_wp_post( $data, 'wp_post entry not created for a new subscription' );

		return [
			'status'  => 'error',
			'message' => $message
		];
	}

	if ( $email === '' ) {
		$email = $user->user_email;
	}

	$subscription_data = [
		'user_id'              => $user->ID,
		'sponsored_animal_id'  => $animal_id,
		'amount'               => $plan_details['amount'],
		'status'               => 'va-active',
		'period_type'          => 'monthly',
		'currency'             => $plan_details['currency'],
		'completed_cycles'     => 1,
		'next_due'             => date( "Y-m-d", strtotime( "+1 month" ) ),
		'post_id'              => $post_id,
		'email_for_updates'    => $email,
		'paypal_id'            => $paypal['subscription_id'],
		'subscription_plan_id' => $paypal['plan_id'],
	];
	global $wpdb;
	$insert_status = $wpdb->insert(
		$wpdb->prefix . 'va_subscriptions',
		$subscription_data,
		[ '%d', '%d', '%f', '%s', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%s' ],
	);

	if ( $insert_status === false ) {
		$subject = "wp_va_subscriptions entry wasn't created";
		va_record_error_with_creating_wp_post( $subscription_data, $subject );

		return [
			'status'  => 'error',
			'message' => __( 'Creating the subscription entry failed', 'virtual-adoptions' )
		];
	}

	$subscription_id = $wpdb->insert_id;
	update_post_meta( $post_id, 'subscription_id', $subscription_id );
	va_log_report( 'success.log', "New subscription created: \n\r" . json_encode( $subscription_data ) );

	return [
		'status'          => 'success',
		'subscription_id' => $subscription_id,
		'post_id'         => $post_id
	];
}


/**
 * Check is there is a record in va_subscriptions for the current user and animal
 * Also checks if the status is different from 'cancelled'
 *
 * @param int $user_id
 * @param int $animal_id
 *
 * @return bool
 */
function va_is_animal_adopted_by_user( int $user_id, int $animal_id ): bool {
	global $wpdb;
	$sql    = "SELECT ID
				FROM {$wpdb->prefix}va_subscriptions
				WHERE user_id = $user_id AND sponsored_animal_id = $animal_id AND status != 'va-cancelled'";
	$result = $wpdb->get_var( $sql );

	return ! empty( $result );
}


/**
 * Gets all animals that are adopted by the current user and are not with 'cancelled' status
 *
 * @return array returns an array of animal's IDs or empty array if nothing found
 */
function va_get_list_of_adopted_animals(): array {
	$user_id = get_current_user_id();
	if ( $user_id === 0 ) {
		return [];
	}

	global $wpdb;
	$sql     = "SELECT sponsored_animal_id
				FROM {$wpdb->prefix}va_subscriptions
				WHERE user_id = $user_id AND status != 'va-cancelled'";
	$animals = $wpdb->get_col( $sql );
	if ( empty( $animals ) ) {
		return [];
	}

	return $animals;
}

/**
 * Get the details of a subscription by the wp_post subscription ID
 *
 * @param int $subscription_post_id
 *
 * @return array
 */
function va_get_sponsored_animal_details_by_subscription( int $subscription_post_id ): array {
	global $wpdb;
	$sql  = "SELECT * FROM {$wpdb->prefix}va_subscriptions WHERE post_id = $subscription_post_id";
	$data = $wpdb->get_row( $sql, ARRAY_A );
	if ( empty( $data ) ) {
		return [];
	}

	return $data;
}


/**
 * Changes the status of the post and the va_subscriptions entry to 'cancelled'
 *
 * @param int $post_id
 *
 * @return string
 */
function va_cancel_va_subscription_entry( int $post_id ): string {
	$subscription = va_get_subscription_by_post_id( $post_id );
	$result       = va_paypal_cancel_subscription( $subscription, 'Subscription was cancelled by the user from the web site.' );

	if ($result === false) {
		return __('Failed to cancel from PayPal.', 'virtual-adoptions');
	}

	$post_update = wp_update_post( [
		'ID'          => $post_id,
		'post_status' => 'va-cancelled',
	] );

	if ( $post_update === 0 || is_wp_error( $post_update ) ) {
		return __( 'The cancellation failed, please try again', 'va-virtual-donation' );
	}

	global $wpdb;
	$wpdb->update(
		$wpdb->prefix . 'va_subscriptions',
		[ 'status' => 'va-cancelled' ],
		[ 'post_id' => $post_id ],
		[ '%s' ],
		[ '%d' ]
	);

	return 'success';
}

/**
 *
 * This function is called from hooks when subscription post is deleted or post status is changed
 *
 * @param array $subscription
 * @param string $reason
 *
 * @return bool
 */
function va_paypal_cancel_subscription( array $subscription, string $reason ): bool {
	return ( new VA_PayPal() )->cancel_subscription( $subscription['paypal_id'], $reason );
}


/**
 * Used from the webhook. If the subscription status changes on the PayPal it will hit the webhook and will call this function to update the DB with the made changes
 *
 * @param string $paypal_subscription_id
 * @param string $status
 *
 * @return void
 */
function va_change_subscription_status_from_paypal( string $paypal_subscription_id, string $status ) {
	global $wpdb;
	$sql     = "SELECT post_id, ID FROM {$wpdb->prefix}va_subscriptions WHERE paypal_id = %s";
	$details = $wpdb->get_row( $wpdb->prepare( $sql, $paypal_subscription_id ) );
	if ( empty( $details ) ) {
		$message = 'Not found subscription with ID: ' . $paypal_subscription_id . "\n\r";
		va_log_report( 'error.log', $message );

		return;
	}

	// Update the wp_post related to that subscription
	$wpdb->update(
		$wpdb->prefix . 'posts',
		[ 'post_status' => $status ],
		[ 'ID' => $details->post_id ],
		[ '%s' ],
		[ '%d' ]
	);
	// Update the wp_va_subscriptions table with the details
	$wpdb->update(
		$wpdb->prefix . 'va_subscriptions',
		[ 'status' => $status ],
		[ 'ID' => $details->ID ],
		[ '%s' ],
		[ '%d' ]
	);
}


/**
 * Checks the data received from the PayPal Payment.Sale.Completed event and checks if there is a subscription with that ID
 * If it there is a subscription it will return an array with the subscription's ID and the number of cycles
 *
 * @param array $payment_data
 *
 * @return array returns an empty array if nothing found, else - an associative array with 'subscription_id' and 'number_of_cycles'
 */
function va_check_if_payment_is_for_subscription( array $payment_data ): array {
	$subscription_id = ! empty( $payment_data['billing_agreement_id'] ) ? $payment_data['billing_agreement_id'] : '';
	if ( $subscription_id === '' ) {
		return [];
	}

	// check if we have such a subscription in our DB
	global $wpdb;
	$sql          = "SELECT ID FROM {$wpdb->prefix}va_subscriptions WHERE paypal_id = %s";
	$subscription = $wpdb->get_var( $wpdb->prepare( $sql, $subscription_id ) );
	if ( empty( $subscription ) ) {
		return [];
	}

	// get details from PayPal for the current subscriptions and check if we have the number of completed cycles
	$subscription_details = ( new VA_PayPal() )->get_subscription_details( $subscription_id );
	if ( $subscription_details === [] || empty( $subscription_details['billing_info']['cycle_executions'][0]['cycles_completed'] ) ) {
		return [];
	}

	return [
		'subscription_id'  => $payment_data['billing_agreement_id'],
		'number_of_cycles' => (int) $subscription_details['billing_info']['cycle_executions'][0]['cycles_completed'],
	];
}
