<?php
add_action( 'rest_api_init', function () {
	register_rest_route( 'virtual-donations/v1', '/subscription/', array(
		'methods'             => 'POST',
		'callback'            => 'va_handle_paypal_webhook_triggered_on_subscription_change',
		'args'                => array(
			'id' => array(
				'validate_callback' => function ( $param, $request, $key ) {
					return is_numeric( $param );
				}
			),
		),
		'permission_callback' => function () {
			return current_user_can( 'edit_others_posts' );
		}
	) );
} );

function va_handle_paypal_webhook_triggered_on_subscription_change() {

}


function va_create_paypal_authentication_token(): array {
	$va_settings        = get_option( 'va-settings' );
	$paypal_client_id   = ! empty( $va_settings['payment-methods']['paypal']['client_id'] ) ? $va_settings['payment-methods']['paypal']['client_id'] : '';
	$paypal_secret      = ! empty( $va_settings['payment-methods']['paypal']['secret'] ) ? va_decrypt_data( $va_settings['payment-methods']['paypal']['secret'] ) : '';
	$paypal_is_test_env = ! empty( $va_settings['payment-methods']['paypal']['test'] );
	$paypal_api_url     = $paypal_is_test_env ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

	$url  = $paypal_api_url . '/v1/oauth2/token';
	$curl = curl_init();

	$options = [
		CURLOPT_URL            => $url,
		CURLOPT_POST           => 1,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_POSTFIELDS     => "grant_type=client_credentials",
		CURLOPT_USERPWD        => $paypal_client_id . ':' . $paypal_secret,
		CURLOPT_HTTPHEADER     => array(
			'Content-Type:application/x-www-form-urlencoded',
		),
	];

	curl_setopt_array( $curl, $options );

	// execute the cURL
	$curl_response = curl_exec( $curl );
	if ( curl_errno( $curl ) ) {

		$info = curl_getinfo( $curl );
		curl_close( $curl );

		return [
			'status'  => 'error',
			'message' => 'ERROR: ' . $info['http_code']
		];
	}

	$result = json_decode( $curl_response, true );
	curl_close( $curl );

	if ( ! empty( $result['access_token'] ) ) {
		return [
			'status' => 'success',
			'data'   => $result
		];
	}


	return [
		'status'  => 'error',
		'message' => 'There was issue with the PayPal response'
	];
}

function va_build_a_subscription_plan( string $token ) {
	$data = array(
		'product_id'          => 'PROD-XXCD1234QWER65782',
		'name'                => 'Video Streaming Service Plan',
		'description'         => 'Video Streaming Service basic plan',
		'status'              => 'ACTIVE',
		'billing_cycles'      =>
			array(
				0 =>
					array(
						'frequency'      =>
							array(
								'interval_unit'  => 'MONTH',
								'interval_count' => 1,
							),
						'tenure_type'    => 'TRIAL',
						'sequence'       => 1,
						'total_cycles'   => 2,
						'pricing_scheme' =>
							array(
								'fixed_price' =>
									array(
										'value'         => '3',
										'currency_code' => 'USD',
									),
							),
					),
			),
		'payment_preferences' =>
			array(
				'auto_bill_outstanding'     => true,
				'setup_fee'                 =>
					array(
						'value'         => '10',
						'currency_code' => 'USD',
					),
				'setup_fee_failure_action'  => 'CONTINUE',
				'payment_failure_threshold' => 3,
			),
		'taxes'               =>
			array(
				'percentage' => '0',
			),
	);

	$ch = curl_init();

	curl_setopt( $ch, CURLOPT_URL, 'https://api-m.sandbox.paypal.com/v1/billing/plans' );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_POST, 1 );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $data, JSON_NUMERIC_CHECK ) );

	$headers   = array();
	$headers[] = 'Content-Type: application/json';
	$headers[] = 'Authorization: Bearer ' . $token;
	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

	$result = curl_exec( $ch );
	dbga( $result );
	if ( curl_errno( $ch ) ) {
		echo 'Error:' . curl_error( $ch );
	}
	curl_close( $ch );
}

function va_get_plans( string $token ) {
	$curl = curl_init();

	curl_setopt_array( $curl, array(
		CURLOPT_URL            => 'https://api-m.sandbox.paypal.com/v1/billing/plans',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING       => '',
		CURLOPT_MAXREDIRS      => 10,
		CURLOPT_TIMEOUT        => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST  => 'GET',
		CURLOPT_HTTPHEADER     => array(
			'Prefer: return=representation'
		),
	) );

	$headers   = array();
	$headers[] = 'Content-Type: application/json';
	$headers[] = 'Authorization: Bearer ' . $token;
	curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
	if ( curl_errno( $curl ) ) {
		echo 'Error:' . curl_error( $curl );
	}
	$response = curl_exec( $curl );
	$result = json_decode( $response, true );
	dbga( $result );
	if (!empty($result['plans'])) {
		dbga($result['plans']);
	} else {
		dbga (' EMPTY ');
	}

	curl_close( $curl );
	return $response;

}

function va_test_api() {
	$token = va_create_paypal_authentication_token();
	dbga($token);
	if ( $token['status'] === 'success' ) {
		va_get_plans( $token['data']['access_token'] );
	}
	wp_die( 'done' );
}

add_action( 'wp_ajax_va_test_api', 'va_test_api' );
