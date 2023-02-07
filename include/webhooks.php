<?php
/**
 * Registering a WP endpoint for PayPal webhook
 *
 * @return void
 */
function register_paypal_webhook() {

	register_rest_route( 'virtual-donations/v1', '/subscription/', array(
		'methods'             => 'POST',
		'callback'            => 'va_handle_paypal_webhook_triggered_on_subscription_change',
		'args'                => [],
		'permission_callback' => "__return_true",
	) );
}

add_action( 'rest_api_init', 'register_paypal_webhook' );

/**
 * Callback of the PayPal webhook, will check first if the request is verified by PayPal and then proceed
 * with the actual event
 */
function va_handle_paypal_webhook_triggered_on_subscription_change( WP_REST_Request $request ) {
	$entityBody = file_get_contents( 'php://input' );
	if ( ! validate_paypal_request( $request, $entityBody ) ) {
		return new WP_Error( '401', esc_html__( 'Not Authorized', 'virtual-adoptions' ), array( 'status' => 401 ) );
	}
	$data = json_decode( $entityBody, ARRAY_A );
	if ( $data['event_type'] === 'BILLING.SUBSCRIPTION.ACTIVATED' ) {
		dbga( $data['resource']['id'] );
		dbga( $data['resource']['plan_id'] );
		dbga( $data['resource']['status'] );
		dbga( $data['resource']['billing_info']['cycle_executions'][0]['cycles_completed'] );
		dbga( $data['resource']['billing_info']['next_billing_time'] );
	}

	if ( $data['event_type'] === 'BILLING.SUBSCRIPTION.CANCELLED' ) {
		dbga( $data );
		dbga( $data['resource']['id'] );
		dbga( $data['resource']['plan_id'] );
		dbga( $data['resource']['status'] );
		dbga( $data['resource']['agreement_details']['id'] );
		dbga( $data['resource']['billing_info']['next_billing_time'] );
	}
	dbga( $data['event_type']);

	return new WP_REST_Response( [ 'status' => 'Success' ], 200 );
}


function validate_paypal_request( WP_REST_Request $request, string $data ) {
	$headers = $request->get_headers();
	// Check if we have the needed headers
	$headers_list = [
		'paypal_transmission_id',
		'paypal_transmission_time',
		'paypal_auth_algo',
		'paypal_cert_url',
		'paypal_transmission_sig'
	];

	// Validates if we have the needed request headers
	foreach ( $headers_list as $header ) {
		if ( empty( $headers[ $header ][0] ) ) {
			return false;
		}
	}

	// Build the details of the data for verification
	$va_settings = get_option( 'va-settings' );
	$details     = [
		"webhook_id"        => $va_settings['payment-methods']['paypal']['webhook_id'],
		"transmission_id"   => $headers['paypal_transmission_id'][0],
		"transmission_time" => $headers['paypal_transmission_time'][0],
		"cert_url"          => $headers['paypal_cert_url'][0],
		"auth_algo"         => $headers['paypal_auth_algo'][0],
		"transmission_sig"  => $headers['paypal_transmission_sig'][0],
		"body"              => $data,
	];

	$VA_paypal = new VA_PayPal();

	return $VA_paypal->manual_verification( $details );
}
