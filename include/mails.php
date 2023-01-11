<?php
function ars_send_confirmation_email( WP_Post $post ) {
	$user    = get_user_by( 'ID', $post->post_author );
	$details = ars_get_sponsored_animal_details_by_subscription( $post->ID );
	if ( $details === [] ) {
		$subject = __( 'Error with the current subscription', 'ars-virtual-donations' );
		$content = sprintf( __( "Hello, \n We had issues with finding entry with ID %d, please send this message to our admin. \n Thank you.", 'ars-virtual-donations' ), $post->ID );
	} else {
		$ars_settings         = get_option( 'ars-settings' );
		$manage_subscriptions = get_permalink( $ars_settings['my-subscriptions-page'] );
		$animal               = get_post( $details['sponsored_animal_id'] );
		$subject              = __( 'Successful virtual adoption', 'ars-virtual-donations' );
		$content              = sprintf( __( "Hello, \n You have successfully subscribed for virtual adoption of %s. \n You can manage your subscriptions from <a href='%s'>this</a> link. \n Thank you for your support. \n \n Kind regards, \n ARS team.", 'ars-virtual-donations' ), $animal->post_title, $manage_subscriptions );
	}

	$headers = '';
	wp_mail( $user->user_email, $subject, $content, $headers );
}
