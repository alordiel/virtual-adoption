<?php
/**
 * @param WP_Post $post
 *
 * @return void
 */
function va_send_confirmation_email( WP_Post $post ) {
	$user    = get_user_by( 'ID', $post->post_author );
	$details = va_get_sponsored_animal_details_by_subscription( $post->ID );
	if ( $details === [] ) {
		$subject = __( 'Error with the current subscription', 'virtual-adoption' );
		$content = sprintf( __( "Hello, \n We had issues with finding entry with ID %d, please send this message to our admin. \n Thank you.", 'virtual-adoption' ), $post->ID );
	} else {
		$va_settings          = get_option( 'va-settings' );
		$manage_subscriptions = get_permalink( $va_settings['page']['my-subscriptions'] );
		$animal               = get_post( $details['sponsored_animal_id'] );
		$subject              = __( 'Successful virtual adoption', 'virtual-adoption' );
		$content              = sprintf( __( "Hello, \n You have successfully subscribed for virtual adoption of %s. \n You can manage your subscriptions from <a href='%s'>this</a> link. \n Thank you for your support. \n \n Kind regards, \n ARS team.", 'virtual-adoption' ), $animal->post_title, $manage_subscriptions );
	}

	$headers = '';
	wp_mail( $user->user_email, $subject, $content, $headers );
}

/**
 * Sends emails to the admin email which is used in Settings -> General
 *
 * @param string $content
 * @param string $subject
 *
 * @return void
 */
function va_send_admin_warning_email( string $content, string $subject ) {
	$admin_email = get_bloginfo( 'admin_email' );
	$headers     = '';
	wp_mail( $admin_email, $subject, $content, $headers );
}

