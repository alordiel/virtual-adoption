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
		va_json_response( 0, __( 'Missing some data.', 'virtual-adoptions' ) );
	}

	if ( $_POST['acceptedTerms'] !== 'true' ) {
		va_json_response( 0, __( 'You need to accept our terms and conditions.', 'virtual-adoptions' ) );
	}

	$donation_amount = (float) $_POST['donationAmount'];
	if ( $donation_amount <= 0.00 ) {
		va_json_response( 0, __( 'There is problem with the donation amount.', 'virtual-adoptions' ) );
	}

	// Check if animal exists
	$animal_id  = va_decode_id( $_POST['animalID'] );
	$the_animal = get_post( $animal_id );

	// Check if the animal's ID is not faked
	if ( empty( $the_animal ) || $the_animal->post_type !== 'sheltered-animal' || $the_animal->post_status !== 'publish' ) {
		va_json_response( 0, __( 'We do not have record of that animal', 'virtual-adoptions' ) );
	}

	// Checks the Gift email if given and if valid
	$gift_email = '';
	if ( ! empty( $_POST['giftEmail'] ) && is_email( $_POST['giftEmail'] ) ) {
		$gift_email = $_POST['giftEmail'];
	}

	// Creating of the wp_post entry and subscription entry
	$paypal = [
		'amount'          => $donation_amount,
		'subscription_id' => ! empty( $_POST['subscriptionID'] ) ? $_POST['subscriptionID'] : 0,
		'plan_id'         => ! empty( $_POST['subscriptionPlanID'] ) ? $_POST['subscriptionPlanID'] : 0
	];

	$results = va_create_new_donation_subscription( $animal_id, $paypal, $gift_email );
	if ( $results['status'] === 'error' ) {
		va_json_response( 0, $results['message'] );
	}

	// get redirection link for "Thank you" page
	$options = get_option( 'va-settings' );
	$data    = [ 'redirect_to' => get_permalink( $options['page']['thank-you'] ), ];
	va_json_response( 1, '', $data );
}

add_action( 'wp_ajax_va_create_new_donation_subscription', 'va_create_new_donation_subscription_ajax' );


/**
 * Ajax callback function for registration
 *
 * @return void
 */
function va_register_new_user() {
	check_ajax_referer( 'va-taina', 'security' );

	if ( empty( $_POST['pass'] ) || empty( $_POST['email'] ) || empty( $_POST['firstName'] ) || empty( $_POST['lastName'] ) ) {
		va_json_response( 0, __( 'There is an empty field from your form. Please check all the fields', 'virtual-adoptions' ) );
	}

	if (  empty( $_POST['terms'] ) || $_POST['terms'] !== 'true' ) {
		va_json_response( 0, __( 'You need to accept the terms in order to continue.', 'virtual-adoptions' ) );
	}

	$user_data = [
		'user_pass'  => $_POST['pass'],
		'email'      => $_POST['email'],
		'first_name' => $_POST['firstName'],
		'last_name'  => $_POST['lastName'],
	];

	$error_message = va_create_new_user_and_login( $user_data );

	if ( $error_message !== '' ) {
		va_json_response( 0, $error_message );
	}
	va_json_response( 1 );
}

add_action( 'wp_ajax_nopriv_va_register_new_user', 'va_register_new_user' );


/**
 * Function to cancel a given subscription. The ajax can be called by the user only.
 *
 * @return void
 */
function va_cancel_subscription_ajax() {
	check_ajax_referer( 'va-taina', 'security' );

	if ( empty( $_POST['post_id'] ) ) {
		va_json_response( 0, __( 'No subscription ID.', 'virtual-adoptions' ) );
	}

	// Check if the current user is the author (creator) of the requested for cancellation subscription
	global $wpdb;
	$post_exist = "SELECT ID FROM {$wpdb->prefix}posts WHERE ID = %d AND post_author = %d";
	$user_id    = get_current_user_id();
	if ( empty( $wpdb->get_var( $wpdb->prepare( $post_exist, $_POST['post_id'], $user_id ) ) ) ) {
		va_json_response( 0, __( 'This subscription does not belong to you!', 'virtual-adoptions' ) );
	}

	$result = va_cancel_va_subscription_entry( $_POST['post_id'] );
	if ( $result !== 'success' ) {
		va_json_response( 0, $result );
	}

	va_log_report('success.log', "Successfully cancelled subscription ({$_POST['post_id']}) by owner ( User: $user_id)");

	va_json_response( 1, '', [
		'message' => __( 'Successfully cancelled.', 'virtual-adoptions' ),
		'status'  => __( 'Cancelled', 'virtual-adoptions' )
	] );
}

add_action( 'wp_ajax_va_cancel_subscription_ajax', 'va_cancel_subscription_ajax' );


/**
 * Ajax callback used to generate the CSV file with monthly report
 * It fetches all subscriptions with related information (for all statuses).
 *
 * @return void
 */
function va_generate_monthly_report() {

	$uploads_directory = wp_upload_dir();
	$report_link_abs   = $uploads_directory['basedir'] . '/virtual-adoptions/';
	$report_link_url   = $uploads_directory['baseurl'] . '/virtual-adoptions/monthly-report.csv';

	// checks if the /virtual-adoptions/ folder exists and creates it if not
	if ( ! file_exists( $report_link_abs ) && ! mkdir( $report_link_abs, 0755 ) && ! is_dir( $report_link_abs ) ) {
		echo json_encode( [
			'status'  => 0,
			'message' => sprintf( __( 'Directory "%s" was not created', 'virtual-donations' ), $report_link_abs ),
		], JSON_NUMERIC_CHECK );
		wp_die();
	}

	$report_link_abs .= 'monthly-report.csv'; // adds the actual file

	global $wpdb;
	$sql           = "SELECT subs.amount, subs.currency, subs.status, subs.start_date, subs.completed_cycles, animals.post_title animal_name,
       			      umeta1.meta_value first_name, umeta2.meta_value last_name, users.user_email owner_email,
       			      subs.email_for_updates gift_email
						FROM {$wpdb->prefix}va_subscriptions subs
						LEFT JOIN {$wpdb->prefix}posts as subs_post ON subs_post.ID = subs.post_id
						LEFT JOIN {$wpdb->prefix}posts as animals ON subs.sponsored_animal_id = animals.ID
						LEFT JOIN {$wpdb->prefix}usermeta as umeta1 ON umeta1.user_id = subs_post.post_author AND umeta1.meta_key = 'first_name'
						LEFT JOIN {$wpdb->prefix}usermeta as umeta2 ON umeta2.user_id = subs_post.post_author AND umeta2.meta_key = 'last_name'
						LEFT JOIN {$wpdb->prefix}users as users ON users.ID  = subs_post.post_author";
	$subscriptions = $wpdb->get_results( $sql );

	if ( empty( $subscriptions ) ) {
		echo json_encode( [
			'status'  => 0,
			'message' => __( 'No subscriptions found', 'virtual-donations' ),
		], JSON_NUMERIC_CHECK );
		wp_die();
	}

	// check if the old report file exists and delete it
	if ( file_exists( $report_link_abs ) ) {
		unlink( $report_link_abs );
	}

	// Start writing in a csv file
	$output  = fopen( $report_link_abs, 'wb' );
	$headers = [
		'First Name',
		'Last Name',
		'Email',
		'Gifted Email',
		'Animal Name',
		'Donated Amount',
		'Currency',
		'Status',
		'Completed cycles',
		'Start date'
	];
	fputcsv( $output, $headers );
	foreach ( $subscriptions as $subscription ) {
		$row = [
			$subscription->first_name,
			$subscription->last_name,
			$subscription->owner_email,
			$subscription->gift_email,
			$subscription->animal_name,
			$subscription->amount,
			$subscription->currency,
			$subscription->status,
			$subscription->completed_cycles,
			$subscription->start_date
		];
		fputcsv( $output, $row );
	}

	fclose( $output );

	echo json_encode( [
		'status'  => 1,
		'message' => __( 'Report successfully created. Please download it from the link.', 'virtual-donations' ),
		'url'     => $report_link_url,
	], JSON_NUMERIC_CHECK );
	wp_die();
}

add_action( 'wp_ajax_va_get_donations_report', 'va_generate_monthly_report' );


/**
 * Ajax Callback for connecting to the PayPal API.
 *
 * @return void
 */
function va_test_paypal_api_connection() {
	check_ajax_referer( 'va-taina', 'security' );

	if ( empty( $_POST['clientID'] ) || empty( $_POST['secretKey'] ) ) {
		wp_die( __( 'The PayPal client ID and secret key can not be empty.', 'virtual-adoptions' ) );
	}

	// We will check if we have the credentials stored in the DB and if not we will save them (most probably the user forgot to click 'Save changes' first)
	$va_settings      = get_option( 'va-settings' );
	$paypal_client_id = ! empty( $va_settings['payment-methods']['paypal']['client_id'] ) ? $va_settings['payment-methods']['paypal']['client_id'] : '';
	$paypal_secret    = ! empty( $va_settings['payment-methods']['paypal']['secret'] ) ? va_decrypt_data( $va_settings['payment-methods']['paypal']['secret'] ) : '';

	if ( $paypal_client_id === '' || $paypal_secret === '' ) {
		$va_settings['payment-methods']['paypal']['client_id'] = $_POST['clientID'];
		$va_settings['payment-methods']['paypal']['secret']    = $_POST['secretKey'];
		update_option( 'va-settings', $va_settings );
	}


	$VA_paypal = new VA_PayPal();
	if ( $VA_paypal->get_error() === '' ) {
		wp_die( 'PayPal REST API connection - successfully' );
	}

	wp_die( 'ERROR: ' . $VA_paypal->get_error() );
}

add_action( 'wp_ajax_va_test_paypal_api_connection', 'va_test_paypal_api_connection' );
