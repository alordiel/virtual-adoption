<?php
function ars_create_new_donation_subscription() {
	check_ajax_referer( 'ars-taina', 'security' );
	if ( empty( $_POST['animalID'] ) || empty( $_POST['donationAmount'] ) ) {
		ars_json_response( 0, __( 'Missing some data.', 'ars-virtual-donations' ) );
	}

	if ( $_POST['acceptedTerms'] !== 'true' ) {
		ars_json_response( 0, __( 'You need to accept our terms and conditions.', 'ars-virtual-donations' ) );
	}

	$donation_amount = (float) $_POST['donationAmount'];
	if ($donation_amount < 5.00) {
		ars_json_response( 0, __( 'There is problem with the donation amount.', 'ars-virtual-donations' ) );
	}
	// Check if animal exists
	$animal_id  = ars_decode_id( $_POST['animalID'] );
	$the_animal = get_post( $animal_id );

	// Check if the animal's ID is not faked
	if ( empty( $the_animal ) || $the_animal->post_type !== 'sheltered-animal' || $the_animal->post_status !== 'publish' ) {
		ars_json_response( 0, __( 'We do not have record of that animal', 'ars-virtual-donations' ) );
	}
	if ( ! is_user_logged_in() ) {
		$result_message = ars_create_new_user( $_POST );
		if ( $result_message !== '' ) {
			ars_json_response( 0, $result_message );
		}
	}

	ars_json_response(1);
}

add_action( 'wp_ajax_ars_create_new_donation_subscription', 'ars_create_new_donation_subscription' );
add_action( 'wp_ajax_nopriv_ars_create_new_donation_subscription', 'ars_create_new_donation_subscription' );
