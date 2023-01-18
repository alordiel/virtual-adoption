<?php
/**
 * Ajax callback function for creating the subscriptions.
 * It will add two DB entries, one in wp_posts and one in the va_subscriptions
 *
 * @return void
 */
function va_create_new_donation_subscription_ajax() {
	check_ajax_referer( 'va-taina', 'security' );

	if ( empty( $_POST['animalID'] ) || empty( $_POST['donationAmount'] ) ) {
		va_json_response( 0, __( 'Missing some data.', 'virtual-adoption' ) );
	}

	if ( $_POST['acceptedTerms'] !== 'true' ) {
		va_json_response( 0, __( 'You need to accept our terms and conditions.', 'virtual-adoption' ) );
	}

	$donation_amount = (float) $_POST['donationAmount'];
	if ( $donation_amount < 5.00 ) {
		va_json_response( 0, __( 'There is problem with the donation amount.', 'virtual-adoption' ) );
	}

	// Check if animal exists
	$animal_id  = va_decode_id( $_POST['animalID'] );
	$the_animal = get_post( $animal_id );

	// Check if the animal's ID is not faked
	if ( empty( $the_animal ) || $the_animal->post_type !== 'sheltered-animal' || $the_animal->post_status !== 'publish' ) {
		va_json_response( 0, __( 'We do not have record of that animal', 'virtual-adoption' ) );
	}

	// Checks if this is a new user if it needs updating
	if ( ! is_user_logged_in() ) {
		$result_message = va_create_new_user( $_POST );
		if ( $result_message !== '' ) {
			va_json_response( 0, $result_message );
		}
	}
	// Checks the Gift email if given and if valid
	$gift_email = '';
	if ( ! empty( $_POST['giftEmail'] ) ) {
		if ( is_email( $_POST['giftEmail'] ) ) {
			$gift_email = $_POST['giftEmail'];
		} else {
			va_json_response( 0, __( 'The gift email is not valid.', 'virtual-adoption' ) );
		}
	}

	// Creating of the wp_post entry and subscription entry. At this point the both will be with inactive status
	$results = va_create_new_donation_subscription( $animal_id, $donation_amount, $gift_email );
	if ( $results['status'] === 'error' ) {
		va_json_response( 0, $results['message'] );
	}

	// get redirection link for "Thank you" page
	$options = get_option( 'va-settings' );
	$ids     = [
		'post_id'         => $results['post_id'],
		'subscription_id' => $results['subscription_id'],
		'redirect_to'     => get_permalink( $options['page']['thank-you'] ),
	];
	va_json_response( 1, '', $ids );
}

add_action( 'wp_ajax_va_create_new_donation_subscription', 'va_create_new_donation_subscription_ajax' );
add_action( 'wp_ajax_nopriv_va_create_new_donation_subscription', 'va_create_new_donation_subscription_ajax' );


/**
 * Function to cancel a given subscription. The ajax can be called by the user only.
 *
 * @return void
 */
function va_cancel_subscription_ajax() {
	check_ajax_referer( 'va-taina', 'security' );

	if ( empty( $_POST['post_id'] ) ) {
		va_json_response( 0, __( 'No subscription ID.', 'virtual-adoption' ) );
	}

	// Check if the current user is the author (creator) of the requested for cancellation subscription
	global $wpdb;
	$post_exist = "SELECT ID FROM {$wpdb->prefix}posts WHERE ID = %d AND post_author = %d";
	$user_id    = get_current_user_id();
	if ( empty( $wpdb->get_var( $wpdb->prepare( $post_exist, $_POST['post_id'], $user_id ) ) ) ) {
		va_json_response( 0, __( 'This subscription does not belong to you!', 'virtual-adoption' ) );
	}

	$result = va_cancel_va_subscription_entry( $_POST['post_id'] );
	if ( $result !== 'success' ) {
		va_json_response( 0, $result );
	}

	va_json_response( 1, '', [
		'message' => __( 'Successfully cancelled.', 'virtual-adoption' ),
		'status'  => __( 'Cancelled', 'virtual-adoption' )
	] );
}

add_action( 'wp_ajax_va_cancel_subscription_ajax', 'va_cancel_subscription_ajax' );
