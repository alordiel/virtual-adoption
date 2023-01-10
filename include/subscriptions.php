<?php

/**
 * Creates wp_post and ars_subscriptions entry in the database
 * On error it will return the error message
 *
 * @param int $animal_id
 * @param float $amount
 * @param string $email
 *
 * @return array
 */
function ars_create_new_donation_subscription( int $animal_id, float $amount, string $email = '' ) {
	$user    = wp_get_current_user();
	$title   = $user->first_name . ' ' . $user->last_name . ' - ' . $animal_id;
	$post_id = wp_insert_post( [
		'status'      => 'ars-pending-payment',
		'post_type'   => 'ars-subscription',
		'post_title'  => $title,
		'post_author' => $user->ID,
	], true );

	if ( $post_id === 0 ) {
		return [
			'status'  => 'error',
			'message' => __( 'Creating the subscription entry failed', 'ars-virtual-donations' )
		];
	}

	if ( is_wp_error( $post_id ) ) {
		return [
			'status'  => 'error',
			'message' => $post_id->get_error_message()
		];
	}

	if ($email === '') {
		$email = $user->user_email;
	}

	global $wpdb;
	$insert_status = $wpdb->insert(
		$wpdb->prefix . 'ars_subscriptions',
		[
			'user_id'             => $user->ID,
			'sponsored_animal_id' => $animal_id,
			'amount'              => $amount,
			'status'              => 'hold',
			'period_type'         => 'monthly',
			'completed_cycles'    => 0,
			'next_due'            => date( "Y-m-d", strtotime( "+1 month", time() ) ),
			'post_id'             => $post_id,
			'email_for_updates'   => $email
		],
		[ '%d', '%d', '%f', '%s', '%s', '%d', '%s', '%d' ],
	);

	if ( $insert_status === false ) {
		wp_delete_post( $post_id );

		return [
			'status'  => 'error',
			'message' => __( 'Creating the subscription entry failed', 'ars-virtual-donations' )
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
