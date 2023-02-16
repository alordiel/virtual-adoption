<?php
// It is possible for another of my plugins to be activated and this function to be already declared
if ( ! function_exists( 'dbga' ) ) {
	/**
	 * @noinspection ForgottenDebugOutputInspection
	 * Internal use only, for debugging purposes
	 */
	function dbga( $arg ) {
		error_log( print_r( $arg, true ) );
	}
}


/**
 * an obfuscation of the id
 *
 * @param int $id
 *
 * @return string
 */
function va_encode_id( int $id ): string {
	return 's_' . ( ( $id * 3 ) + 13 );
}


/**
 * Deobfuscation of the id
 *
 * @param string $string
 *
 * @return int
 */
function va_decode_id( string $string ): int {
	$int = (int) str_replace( 's_', '', $string );

	return ( $int - 13 ) / 3;
}


/**
 * Create
 *
 * @param string $page_type
 *
 * @return void
 */
function va_create_template_page( string $page_type ) {
	$templates = [
		'checkout'         => [
			'title'    => __( 'Donation checkout', 'virtual-adoptions' ),
			'template' => 'va-donation-checkout.php',
			'slug'     => 'donations-checkout',
		],
		'thank-you'        => [
			'title'    => __( 'Virtual adopt - Thank you', 'virtual-adoptions' ),
			'template' => 'va-thank-you-donation.php',
			'slug'     => 'va-thank-you',
		],
		'my-subscriptions' => [
			'title'    => __( 'Manage my adopted animals', 'virtual-adoptions' ),
			'template' => 'va-my-subscriptions.php',
			'slug'     => 'va-my-donations',
		],
		'login'            => [
			'title'    => __( 'Login', 'virtual-adoptions' ),
			'template' => 'va-login-page.php',
			'slug'     => 'vd-login',
		],
	];

	$user_id      = get_current_user_id();
	$current_page = $templates[ $page_type ];
	$page_id      = wp_insert_post( [
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'post_title'     => $current_page['title'],
		'post_author'    => $user_id,
		'comment_status' => 'closed',
		'page_template'  => $current_page['template'],
		'post_name'      => $current_page['slug'],
	] );

	$va_settings                       = get_option( 'va-settings' );
	$va_settings['page'][ $page_type ] = $page_id;
	update_option( 'va-settings', $va_settings );
}

/**
 * General function that echos the response status and message from an ajax query and terminates the call
 *
 * @param int $code
 * @param string $message
 * @param array $some_data
 *
 * @return void
 */
function va_json_response( int $code, string $message = '', array $some_data = [] ) {
	$data = [ 'status' => $code ];
	if ( $message !== '' ) {
		$data['message'] = $message;
	}

	if ( $some_data !== [] ) {
		$data['data'] = $some_data;
	}

	echo json_encode( $data, JSON_NUMERIC_CHECK );
	wp_die();
}

/**
 * Function will create new WP_User and will automatically log the user in.
 *
 * @param array $user_data
 *
 * @return string
 */
function va_create_new_user_and_login( array $user_data ): string {
	if ( empty( $user_data['email'] ) || ! is_email( $user_data['email'] ) ) {
		return __( 'Not valid email', 'virtual-adoptions' );
	}
	if ( email_exists( $user_data['email'] ) || username_exists( $user_data['email'] ) ) {
		return __( 'There is already an user with that email', 'virtual-adoptions' );
	}

	$user_id = wp_insert_user( [
		'user_login' => $user_data['email'],
		'user_pass'  => $user_data['user_pass'],
		'user_email' => $user_data['email'],
		'first_name' => $user_data['first_name'],
		'last_name'  => $user_data['last_name'],
		'role'       => 'virtual-adopter'
	] );

	if ( is_wp_error( $user_id ) ) {
		return $user_id->get_error_message();
	}

	$credentials = array(
		'user_login'    => $user_data['email'],
		'user_password' => $user_data['user_pass'],
		'remember'      => true
	);

	$result = wp_signon( $credentials, false );
	if ( is_wp_error( $result ) ) {
		return $result->get_error_message();
	}

	wp_set_current_user( $user_id );

	return '';
}

/**
 * Converts the status code to readable text. Used to display te subscriptions' status codes on front-end
 *
 * @param string $status_code
 *
 * @return string
 */
function va_get_verbose_subscription_status( string $status_code ): string {
	switch ( $status_code ) {
		case 'va-pending':
			return __( 'Pending', 'virtual-adoptions' );
		case 'va-active':
			return __( 'Active', 'virtual-adoptions' );
		case 'va-cancelled':
			return __( 'Cancelled', 'virtual-adoptions' );
		default:
			return 'n/a';
	}
}

/**
 * Returns the subscription's details from va_subscriptions table based on the post_id
 *
 * @param int $post_id
 *
 * @return array if nothing is found will return empty array
 */
function va_get_subscription_by_post_id( int $post_id ): array {
	global $wpdb;
	$subscription = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}va_subscriptions WHERE post_id = $post_id", ARRAY_A );
	if ( empty( $subscription ) ) {
		return [];
	}

	return $subscription;
}


/**
 * Creates a 'virtual-adoptions' folder in the wp-content/uploads directory, and adds 2 files - error.log and success.log
 *
 * @return void
 */
function va_create_log_files() {
	$log_directory = VA_UPLOADS_ABS . '/virtual-adoptions';
	if ( ! mkdir( $log_directory, 755 ) && ! is_dir( $log_directory ) ) {
		throw new RuntimeException( sprintf( 'Directory "%s" was not created', $log_directory ) );
	}

	$error_file = fopen( VA_UPLOADS_ABS . '/virtual-adoptions/errors.log', 'wb' );
	fclose( $error_file );

	$success_message = fopen( VA_UPLOADS_ABS . '/virtual-adoptions/success.log', 'wb' );
	fclose( $success_message );
}


/**
 * Writes message into a given file. Used for logging events
 *
 * @param string $file_name
 * @param string $message
 *
 * @return void
 */
function va_log_report( string $file_name, string $message ): void {
	$file         = VA_UPLOADS_ABS . '/virtual-adoptions/' . $file_name;
	$file_handler = fopen( $file, 'ab' );
	fwrite( $file_handler, "\n" . $message . "\n" );
	fclose( $file_handler );
}

/**
 * Used to store an error in the error.log and to notify with email the admin
 * Used when the creation of the wp_post entry for a new subscription fails
 * At this point we have the PayPal subscription ID but no WP entry, which must be created manually
 *
 * @param array $data
 * @param string $subject
 *
 * @return void
 */
function va_record_error_with_creating_wp_post_( array $data, string $subject ) {
	$message = json_encode( $data, JSON_NUMERIC_CHECK );
	$message .= " \n\r You will need to add this data manually into the DB";
	va_send_admin_warning_email( $message, $subject );
	va_log_report( 'error.log', $subject . "\n\r" . $message );
}
