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

	private function get_curl_header( bool $add_paypal_unique_id = false, bool $add_purposes = false ): array {
		$headers = [
			'Content-Type: application/json',
			'Authorization: Bearer ' . $this->auth_token
		];

		if ( $add_paypal_unique_id ) {
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


	public function create_product() {
		$product_data = [
			"name"        => "Charity - donate 10 EUR per month",
			"type"        => "SERVICE",
			"id"          => "PRD-" . time(),
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

		$response = curl_exec( $curl );
		$data     = json_decode( $response );
		/*
		 (
		    [id] => PRD-1674576789
		    [name] => Charity - donate 10 EUR per month
		    [description] => For virtual adoption of poor animal from the shelter
		    [create_time] => 2023-01-24T16:13:10Z
		    [links] => Array
		        (
		            [0] => stdClass Object
		                (
		                    [href] => https://api.sandbox.paypal.com/v1/catalogs/products/PRD-1674576789
		                    [rel] => self
		                    [method] => GET
		                )

		            [1] => stdClass Object
		                (
		                    [href] => https://api.sandbox.paypal.com/v1/catalogs/products/PRD-1674576789
		                    [rel] => edit
		                    [method] => PATCH
		                )
		        )
		)
		*/

		curl_close( $curl );

		return $data;

	}

	public function create_subscription_plan() {
		$data = array(
			'product_id'          => 'PRD-1674576789',
			'name'                => 'Charity - donate 10 EUR per month',
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
							'total_cycles'   => 24,
							'pricing_scheme' =>
								array(
									'fixed_price' =>
										array(
											'value'         => '10',
											'currency_code' => 'EUR',
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
					'payment_failure_threshold' => 2,
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

		$headers = $this->get_curl_header( true, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

		$result = curl_exec( $ch );
		dbga( $result );
		if ( curl_errno( $ch ) ) {
			$this->error = 'Error:' . curl_error( $ch );
		}
		curl_close( $ch );

		/*
		 (
		    [id] => P-2B4635940K6518739MPIAP6I
		    [product_id] => PRD-1674576789
		    [name] => Charity - donate 10 EUR per month
		    [status] => ACTIVE
		    [description] => For virtual adoption of poor animal from the shelter
		    [usage_type] => LICENSED
		    [billing_cycles] => Array
		        (
		            [0] => stdClass Object
		                (
		                    [pricing_scheme] => stdClass Object
		                        (
		                            [version] => 1
		                            [fixed_price] => stdClass Object
		                                (
		                                    [currency_code] => EUR
		                                    [value] => 10.0
		                                )

		                            [create_time] => 2023-01-24T16:31:53Z
		                            [update_time] => 2023-01-24T16:31:53Z
		                        )

		                    [frequency] => stdClass Object
		                        (
		                            [interval_unit] => MONTH
		                            [interval_count] => 1
		                        )

		                    [tenure_type] => REGULAR
		                    [sequence] => 1
		                    [total_cycles] => 24
		                )

		        )

		    [payment_preferences] => stdClass Object
		        (
		            [service_type] => PREPAID
		            [auto_bill_outstanding] => 1
		            [setup_fee] => stdClass Object
		                (
		                    [currency_code] => EUR
		                    [value] => 0.0
		                )

		            [setup_fee_failure_action] => CANCEL
		            [payment_failure_threshold] => 2
		        )

		    [taxes] => stdClass Object
		        (
		            [percentage] => 0.0
		            [inclusive] => 1
		        )

		    [quantity_supported] =>
		    [create_time] => 2023-01-24T16:31:53Z
		    [update_time] => 2023-01-24T16:31:53Z
		    [links] => Array
		        (
		            [0] => stdClass Object
		                (
		                    [href] => https://api.sandbox.paypal.com/v1/billing/plans/P-2B4635940K6518739MPIAP6I
		                    [rel] => self
		                    [method] => GET
		                    [encType] => application/json
		                )

		            [1] => stdClass Object
		                (
		                    [href] => https://api.sandbox.paypal.com/v1/billing/plans/P-2B4635940K6518739MPIAP6I
		                    [rel] => edit
		                    [method] => PATCH
		                    [encType] => application/json
		                )

		            [2] => stdClass Object
		                (
		                    [href] => https://api.sandbox.paypal.com/v1/billing/plans/P-2B4635940K6518739MPIAP6I/deactivate
		                    [rel] => self
		                    [method] => POST
		                    [encType] => application/json
		                )

		        )

		)

		 * */

		return json_decode( $result );
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

}
