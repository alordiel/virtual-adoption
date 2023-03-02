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
		$subject = __( 'Error with the current subscription', 'virtual-adoptions' );
		$content = sprintf( __( "Hello, \n We had issues with finding entry with ID %d, please send this message to our admin. \n Thank you.", 'virtual-adoptions' ), $post->ID );
	} else {
		$va_settings          = get_option( 'va-settings' );
		$manage_subscriptions = get_permalink( $va_settings['page']['my-subscriptions'] );
		$animal               = get_post( $details['sponsored_animal_id'] );
		$subject              = __( 'Successful virtual adoption', 'virtual-adoptions' );
		$content              = sprintf( __( "Hello, \n You have successfully subscribed for virtual adoption of %s. \n You can manage your subscriptions from <a href='%s'>this</a> link. \n Thank you for your support. \n \n Kind regards, \n", 'virtual-adoptions' ), $animal->post_title, $manage_subscriptions ) . get_option( 'blogname' )   ;
	}

	$headers = va_get_email_headers();
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
	$headers     = va_get_email_headers();
	wp_mail( $admin_email, $subject, $content, $headers );
}

/**
 * Sends email with link for resetting user's password;
 *
 * @param WP_User $user
 *
 * @return void
 */
function va_send_reset_password_mail( WP_User $user ) {
	$user_id   = $user->ID;
	$site_name = get_option( 'blogname' );
	$user_info = get_userdata( $user_id );
	$unique    = get_password_reset_key( $user_info );
	$link      = network_site_url( "wp-login.php?action=rp&key=$unique&login=" . rawurlencode( $user_info->user_login ), 'login' );
	$subject   = "[$site_name] " . __( "Reset Password Link", 'virtual-adoptions' );
	$message   = '<p>' . sprintf( __( 'Dear %s,', 'virtual-adoptions' ), ucfirst( $user_info->first_name ) ) . '</p>';
	$message   .= '<p>' . __( 'Someone requested password reset for your account.', 'virtual-adoptions' ) . '<br>';
	$message   .= __( 'If this was a mistake, just ignore this email and nothing will happen.', 'virtual-adoptions' ) . '<br>';
	$message   .= __( 'To reset your password, visit the following address:', 'virtual-adoptions' ) . '<br></p>';
	$message   .= "<p><a href='$link'>" . $link . '</a></p>';
	$message   .= '<p>Kind Regards</p>';
	$headers   = va_get_email_headers();
	wp_mail( $user->user_email, $subject, $message, $headers );
}

/**
 * generates an array with e-mail headers for content-type (html) and  "From" header
 *
 * @return string[]
 */
function va_get_email_headers(): array {

	return [
		'Content-Type: text/html; charset=UTF-8',
		'From: ' . get_bloginfo( 'name' ) . ' <' . get_bloginfo( 'admin_email' ) . '>'
	];

}
