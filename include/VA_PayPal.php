<?php

class VA_PayPal {
	private string $auth_token = '';
	private string $client_id;
	private string $secret_key;
	private string $paypal_url;
	private string $oauth_url = '/v1/oauth2/token';
	private string $plans_url = '/v1/billing/plans';
	private string $product_url = '/v1/catalogs/products';
	private string $error = '';

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


	/**
	 * Generates the CURL headers by requested parameters
	 *
	 * @param bool $add_unique_id adds a PayPal specific unique ID
	 * @param bool $add_purposes preferred size and details of the returned results
	 * @param bool $type_json Content type
	 *
	 * @return string[]
	 */
	private function get_curl_header( bool $add_unique_id = false, bool $add_purposes = false, bool $type_json = true ): array {
		$headers = [
			'Authorization: Bearer ' . $this->auth_token
		];

		if ( $type_json ) {
			$headers[] = 'Content-Type: application/json';
		}

		if ( $add_unique_id ) {
			$headers[] = 'PayPal-Request-Id: ' . uniqid( 'PP-', true );
		}

		if ( $add_purposes ) {
			$headers[] = 'Prefer: return=representation';
		}

		return $headers;
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

		if ( ! empty( $result['access_token'] ) ) {
			$this->auth_token = $result['access_token'];

			return;
		}

		$this->error = 'There was issue with the PayPal response';
	}


	/**
	 * Used to create a PayPal product. The product is used then for creation of subscription plan.
	 * Full documentation PayPal: https://developer.paypal.com/docs/api/catalog-products/v1/#products_create
	 *
	 * @param string $product_name
	 * @param string $product_id
	 *
	 * @return array
	 */
	public function create_product( string $product_name, string $product_id ): array {
		$product_data = [
			"name"        => $product_name,
			"type"        => "SERVICE",
			"id"          => $product_id,
			"description" => "For virtual adoption of poor animal from the shelter",
			"category"    => "CHARITY",
		];
		$curl         = curl_init();

		curl_setopt_array( $curl, array(
			CURLOPT_URL            => $this->paypal_url . $this->product_url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => '',
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => 'POST',
			CURLOPT_POSTFIELDS     => json_encode( $product_data, JSON_NUMERIC_CHECK ),
			CURLOPT_HTTPHEADER     => $this->get_curl_header( true ),
		) );

		$response  = curl_exec( $curl );
		$http_code = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
		// Checks for general error with the execution of the curl
		if ( curl_errno( $curl ) ) {
			$this->error = 'ERROR: ' . $http_code;
			curl_close( $curl );

			return [];
		}

		// Check for code 201 - created successfully
		if ( $http_code !== 201 ) {
			$response    = json_decode( $response );
			$this->error = 'ERROR: ' . $http_code . ' ' . $response->message;

			return [];
		}

		$data = json_decode( $response, true );
		curl_close( $curl );
		// Checks if the response was parsed correctly
		if ( $data === null ) {
			$this->error = __( 'ERROR: Could not parse PayPal response. Check error.log for response.', 'virtual-donations' );
			dbga( $response );

			return [];
		}

		return $data;
	}


	/**
	 * Will create a subscription plan in PayPal
	 * REST API documentation : https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_create
	 *
	 * @param string $product_id
	 * @param string $name
	 * @param float $price
	 * @param string $currency
	 *
	 * @return array|mixed
	 */
	public function create_subscription_plan( string $product_id, string $name, float $price, string $currency ) {
		$data = array(
			'product_id'          => $product_id,
			'name'                => $name,
			'description'         => 'For virtual adoption of poor animal from the shelter',
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
							'tenure_type'    => 'REGULAR',
							'sequence'       => 1,
							'total_cycles'   => 24, // this is how long it will bill the user
							'pricing_scheme' =>
								array(
									'fixed_price' =>
										array(
											'value'         => $price,
											'currency_code' => strtoupper( $currency ),
										),
								),
						),
				),
			'payment_preferences' =>
				array(
					'auto_bill_outstanding'     => true,
					'setup_fee'                 =>
						array(
							'value'         => '0',
							'currency_code' => 'EUR',
						),
					'setup_fee_failure_action'  => 'CANCEL',
					'payment_failure_threshold' => 1, // after 1 unsuccessful payment the plan will be cancelled
				),
			'taxes'               =>
				array(
					'percentage' => '0',
				),
		);
		$url  = $this->paypal_url . $this->plans_url;
		$curl = curl_init();

		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $curl, CURLOPT_POST, 1 );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, json_encode( $data, JSON_NUMERIC_CHECK ) );

		$headers = $this->get_curl_header( true, true );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );

		$response = curl_exec( $curl );

		$http_code = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
		if ( curl_errno( $curl ) ) {
			curl_close( $curl );
			$this->error = 'ERROR: ' . $http_code;

			return [];
		}
		curl_close( $curl );
		// Check for code 201 - created successfully
		if ( $http_code !== 201 ) {
			$response    = json_decode( $response );
			$this->error = 'ERROR: ' . $http_code . ' ' . $response->message;

			return [];
		}
		$data = json_decode( $response, true );
		// Checks if the response was parsed correctly
		if ( $data === null ) {
			$this->error = __( 'ERROR: Could not parse PayPal response. Check error.log for response.', 'virtual-donations' );
			dbga( $response );

			return [];
		}

		return $data;
	}


	/**
	 * Deactivates given PayPal plan by PayPal plan ID
	 * Documentation: https://developer.paypal.com/docs/api/subscriptions/v1/#plans_deactivate
	 *
	 * @param string $plan_id
	 * @param string $action can be either "activate" or "deactivated"
	 *
	 * @return bool
	 */
	public function change_active_state_of_subscription_plan( string $plan_id, string $action ): bool {
		$url = $this->paypal_url . $this->plans_url . "/:$plan_id/$action";

		$curl = curl_init();

		curl_setopt_array( $curl, array(
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => '',
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => 'POST',
			CURLOPT_HTTPHEADER     => $this->get_curl_header( true, true, false ),
		) );

		curl_exec( $curl );
		$http_code = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
		if ( $http_code !== 204 ) {
			$this->error = 'ERROR: ' . $http_code;

			return false;
		}

		return true;
	}


	public function get_subscription_plans(): array {
		$curl = curl_init();
		$url  = $this->paypal_url . $this->plans_url . '?page_size=10&page=1&total_required=true';

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
				'Prefer: return=representation',
				'Authorization: Bearer ' . $this->auth_token
			),
		) );


		$response = curl_exec( $curl );

		if ( curl_errno( $curl ) ) {
			$this->error = 'Error:' . curl_error( $curl );
			curl_close( $curl );

			return [];
		}

		$result = json_decode( $response, true );


		curl_close( $curl );

		return $result;
	}


	private function curl_executor( array $data, int $options, int $expected_code ) {

	}
}
