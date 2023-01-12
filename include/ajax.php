<?php
function ars_create_new_donation_subscription_ajax() {
	check_ajax_referer( 'ars-taina', 'security' );

	if ( empty( $_POST['animalID'] ) || empty( $_POST['donationAmount'] ) ) {
		ars_json_response( 0, __( 'Missing some data.', 'ars-virtual-donations' ) );
	}

	if ( $_POST['acceptedTerms'] !== 'true' ) {
		ars_json_response( 0, __( 'You need to accept our terms and conditions.', 'ars-virtual-donations' ) );
	}

	$donation_amount = (float) $_POST['donationAmount'];
	if ( $donation_amount < 5.00 ) {
		ars_json_response( 0, __( 'There is problem with the donation amount.', 'ars-virtual-donations' ) );
	}

	// Check if animal exists
	$animal_id  = ars_decode_id( $_POST['animalID'] );
	$the_animal = get_post( $animal_id );

	// Check if the animal's ID is not faked
	if ( empty( $the_animal ) || $the_animal->post_type !== 'sheltered-animal' || $the_animal->post_status !== 'publish' ) {
		ars_json_response( 0, __( 'We do not have record of that animal', 'ars-virtual-donations' ) );
	}

	// Checks if this is a new user if it needs updating
	if ( ! is_user_logged_in() ) {
		$result_message = ars_create_new_user( $_POST );
		if ( $result_message !== '' ) {
			ars_json_response( 0, $result_message );
		}
	}
	// Checks the Gift email if given and if valid
	$gift_email = '';
	if ( ! empty( $_POST['giftEmail'] ) ) {
		if ( is_email( $_POST['giftEmail'] ) ) {
			$gift_email = $_POST['giftEmail'];
		} else {
			ars_json_response( 0, __( 'The gift email is not valid.', 'ars-virtual-donations' ) );
		}
	}

	// Creating of the wp_post entry and subscription entry. At this point the both will be with inactive status
	$results = ars_create_new_donation_subscription( $animal_id, $donation_amount, $gift_email );
	if ( $results['status'] === 'error' ) {
		ars_json_response( 0, $results['message'] );
	}

	// get redirection link for "Thank you" page
	$options = get_option( 'ars-settings' );
	$ids     = [
		'post_id'         => $results['post_id'],
		'subscription_id' => $results['subscription_id'],
		'redirect_to'     => get_permalink( $options['thank-you-page'] ),
	];
	ars_json_response( 1, '', $ids );
}

add_action( 'wp_ajax_ars_create_new_donation_subscription', 'ars_create_new_donation_subscription_ajax' );
add_action( 'wp_ajax_nopriv_ars_create_new_donation_subscription', 'ars_create_new_donation_subscription_ajax' );

/**
 * Function to cancel a given subscription. The ajax can be called by the user only.
 *
 * @return void
 */
function ars_cancel_subscription_ajax() {
	check_ajax_referer( 'ars-taina', 'security' );

	if ( empty( $_POST['post_id'] ) || empty( $_POST['post_id'] ) ) {
		ars_json_response( 0, __( 'No subscription ID.', 'ars-virtual-donations' ) );
	}

	$result  = ars_cancel_ars_subscription_entry( $_POST['post_id'] );
	if ( $result !== 'success' ) {
		ars_json_response( 0, $result );
	}

	ars_json_response( 1, __( 'Successfully cancelled.', 'ars-virtual-donations' ) );
}

add_action( 'wp_ajax_ars_cancel_subscription', 'ars_cancel_subscription_ajax' );
