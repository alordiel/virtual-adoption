<?php

class VA_PayPal {
	private string $auth_token = '';
	private string $client_id;
	private string $secret_key;
	private string $paypal_url;
	private string $oauth_url = '/v1/oauth2/token';
	private string $plans_url = '/v1/billing/plans';
	private string $error;

	public function __construct() {
		$va_settings        = get_option( 'va-settings' );
		$this->client_id    = ! empty( $va_settings['payment-methods']['paypal']['client_id'] ) ? $va_settings['payment-methods']['paypal']['client_id'] : '';
		$this->secret_key   = ! empty( $va_settings['payment-methods']['paypal']['secret'] ) ? va_decrypt_data( $va_settings['payment-methods']['paypal']['secret'] ) : '';
		$paypal_is_test_env = ! empty( $va_settings['payment-methods']['paypal']['test'] );
		$this->paypal_url   = $paypal_is_test_env ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';
		$this->authenticate();
	}

	public function get_error(): string {
		return $this->error;
	}

	private function get_curl_header(): array {
		return [
			'Content-Type: application/json',
			'Authorization: Bearer ' . $this->auth_token
		];
	}

	private function authenticate(): void {

		$url  = $this->paypal_url . $this->oauth_url;
		$curl = curl_init();

		$options = [
			CURLOPT_URL            => $url,
			CURLOPT_POST           => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POSTFIELDS     => "grant_type=client_credentials",
			CURLOPT_USERPWD        => $this->client_id . ':' . $this->secret_key,
			CURLOPT_HTTPHEADER     => array(
				'Content-Type:application/x-www-form-urlencoded',
			),
		];

		curl_setopt_array( $curl, $options );
		$curl_response = curl_exec( $curl );
		if ( curl_errno( $curl ) ) {
			$info = curl_getinfo( $curl );
			curl_close( $curl );
			$this->error = 'ERROR: ' . $info['http_code'];

			return;
		}

		$result = json_decode( $curl_response, true );
		curl_close( $curl );

		dbga( $result );
		if ( ! empty( $result['access_token'] ) ) {
			$this->auth_token = $result['access_token'];

			return;
		}

		$this->error = 'There was issue with the PayPal response';
	}

	public function va_build_a_subscription_plan() {
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
		$url  = $this->paypal_url . $this->plans_url;
		$ch   = curl_init();

		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $data, JSON_NUMERIC_CHECK ) );

		$headers   = $this->get_curl_header();
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

		$result = curl_exec( $ch );
		dbga( $result );
		if ( curl_errno( $ch ) ) {
			$this->error = 'Error:' . curl_error( $ch );
		}
		curl_close( $ch );
	}

	public function va_get_plans(): array {
		$curl = curl_init();
		$url  = $this->paypal_url . $this->plans_url;

		curl_setopt_array( $curl, array(
			CURLOPT_URL            => $url,
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

		$headers   = $this->get_curl_header();
		curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );

		if ( curl_errno( $curl ) ) {
			$this->error = 'Error:' . curl_error( $curl );
			curl_close( $curl );

			return [];
		}

		$response = curl_exec( $curl );
		$result   = json_decode( $response, true );

		curl_close( $curl );

		return $result;
	}

}
