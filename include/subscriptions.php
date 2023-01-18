<?php

/**
 * Creates wp_post and va_subscriptions entry in the database
 * On error it will return the error message
 *
 * @param int $animal_id
 * @param float $amount
 * @param string $email
 *
 * @return array
 */
function va_create_new_donation_subscription( int $animal_id, float $amount, string $email = '' ): array {
	$user = wp_get_current_user();
	if ( $user === null ) {
		return [
			'status'  => 'error',
			'message' => __( 'It seems that you are not logged in. Please log in first.', 'virtual-adoption' )
		];
	}
	$animal  = get_post( $animal_id );
	$title   = $user->first_name . ' ' . $user->last_name . ' - ' . $animal->post_title;
	$post_id = wp_insert_post( [
		'status'      => 'ars-pending-payment',
		'post_type'   => 'ars-subscription',
		'post_title'  => $title,
		'post_author' => $user->ID,
		'post_status' => 'ars-pending',
	], true );

	if ( $post_id === 0 ) {
		return [
			'status'  => 'error',
			'message' => __( 'Creating the subscription entry failed', 'virtual-adoption' )
		];
	}

	if ( is_wp_error( $post_id ) ) {
		return [
			'status'  => 'error',
			'message' => $post_id->get_error_message()
		];
	}

	if ( $email === '' ) {
		$email = $user->user_email;
	}

	$currency = 'BGN';
	if ( defined( ICL_LANGUAGE_CODE ) && ICL_LANGUAGE_CODE === 'en' ) {
		$currency = 'EUR';
	}

	global $wpdb;
	$insert_status = $wpdb->insert(
		$wpdb->prefix . 'va_subscriptions',
		[
			'user_id'             => $user->ID,
			'sponsored_animal_id' => $animal_id,
			'amount'              => $amount,
			'status'              => 'ars-pending',
			'period_type'         => 'monthly',
			'currency'            => $currency,
			'completed_cycles'    => 0,
			'next_due'            => date( "Y-m-d", strtotime( "+1 month" ) ),
			'post_id'             => $post_id,
			'email_for_updates'   => $email,
		],
		[ '%d', '%d', '%f', '%s', '%s', '%s', '%d', '%s', '%d' ],
	);

	if ( $insert_status === false ) {
		wp_delete_post( $post_id );

		return [
			'status'  => 'error',
			'message' => __( 'Creating the subscription entry failed', 'virtual-adoption' )
		];
	}

	$subscription_id = $wpdb->insert_id;
	update_post_meta( $post_id, 'subscription_id', $subscription_id );

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
				WHERE user_id = $user_id AND sponsored_animal_id = $animal_id AND status != 'cancelled'";
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
				WHERE user_id = $user_id AND status != 'ars-cancelled'";
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

	$post_update = wp_update_post( [
		'ID'          => $post_id,
		'post_status' => 'ars-cancelled',
	] );

	if ( $post_update === 0 || is_wp_error( $post_update ) ) {
		return __( 'The cancellation failed, please try again', 'ars-virtual-donation' );
	}

	global $wpdb;
	$wpdb->update(
		$wpdb->prefix . 'va_subscriptions',
		[ 'status' => 'ars-cancelled' ],
		[ 'post_id' => $post_id ],
		[ '%d' ],
		[ '%d' ]
	);

	return 'success';
}


function va_paypal_cancel_subscription( array $subscription ) {

}
