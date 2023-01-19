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


/**
 * Ajax callback function used to generate the CSV file with monthly report
 * It fetches all subscriptions with related information (for all statuses).
 *
 * @return void
 */
function va_generate_monthly_report() {

	$uploads_directory = wp_upload_dir();
	$report_link_abs   = $uploads_directory['basedir'] . '/virtual-adoptions/';
	$report_link_url   = $uploads_directory['baseurl'] . '/virtual-adoptions/monthly-report.csv';

	// checks if the /virtual-adoptions/ folder exists and creates it if not
	if ( ! is_dir( $report_link_abs ) || ! file_exists( $report_link_abs ) ) {
		mkdir( $report_link_abs, 0755 );
	}
	$report_link_abs .= 'monthly-report.csv';

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
	$output  = fopen($report_link_abs, 'w');
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
	fputcsv($output, $headers);
	foreach ($subscriptions as $subscription) {
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
		fputcsv($output, $row);
	}

	fclose($output);

	echo json_encode( [
		'status'  => 1,
		'message' => __( 'Report successfully created. Please download it from the link.', 'virtual-donations' ),
		'url'     => $report_link_url,
	], JSON_NUMERIC_CHECK );
	wp_die();
}

add_action( 'wp_ajax_va_get_donations_report', 'va_generate_monthly_report' );
