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
 *
 * https://developer.paypal.com/api/rest/webhooks/event-names/#subscriptions
 */
function va_handle_paypal_webhook_triggered_on_subscription_change( WP_REST_Request $request ) {
	$entityBody = file_get_contents( 'php://input' );
	$data = json_decode( $entityBody, ARRAY_A );
	dbga( $data );
	if ( ! validate_paypal_request( $request, $entityBody ) ) {
		return new WP_Error( '401', esc_html__( 'Not Authorized', 'virtual-adoptions' ), array( 'status' => 401 ) );
	}

	$data = json_decode( $entityBody, ARRAY_A );
	dbga( $data );
	switch ( $data['event_type'] ) {
		case  'BILLING.SUBSCRIPTION.CANCELLED':
		case  'BILLING.SUBSCRIPTION.EXPIRED':
		case  'BILLING.SUBSCRIPTION.SUSPENDED':
			if ( ! empty( $data['resource']['plan_id'] ) ) {
				va_change_subscription_status_from_paypal( $data['resource']['id'], 'va-cancelled' );
			} else {
				// TODO LOG the event
				dbga( $data );
			}
			break;

		case  'BILLING.SUBSCRIPTION.RE-ACTIVATED':
		case  'BILLING.SUBSCRIPTION.ACTIVATED':
			if ( ! empty( $data['resource']['plan_id'] ) ) {
				va_change_subscription_status_from_paypal( $data['resource']['id'], 'va-active' );
			} else {
				// TODO LOG the event
				dbga( $data );
			}
			break;

		case  'PAYMENT.SALE.COMPLETED':
			if ( ! empty( $data['resource'] ) ) {
				$subscriptions_new_data = va_check_if_payment_is_for_subscription( $data['resource'] );
				if ( $subscriptions_new_data === [] ) {
					break;
				}
				global $wpdb;
				$wpdb->update(
					$wpdb->prefix . 'va_subscriptions',
					[ 'completed_cycles' => $subscriptions_new_data['number_of_cycle'] ],
					[ 'paypal_id' => $subscriptions_new_data['subscription_id'] ],
					[ '%d' ],
					[ '%s' ]
				);
			} else {
				// TODO LOG the event
				dbga( $data );
			}
			break;

		default:
			// strange case where another event has hit our webhook
			dbga( $data );
	}

	return new WP_REST_Response( [ 'status' => 'Success' ], 200 );
}


function validate_paypal_request( WP_REST_Request $request, string $data ): bool {
	$headers = $request->get_headers();
	// List of the needed headers
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

	return ( new VA_PayPal() )->manual_verification( $details );
}
