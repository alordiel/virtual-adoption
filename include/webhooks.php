<?php
add_action( 'rest_api_init', function () {
	register_rest_route( 'virtual-donations/v1', '/subscription/', array(
		'methods'             => 'POST',
		'callback'            => 'va_handle_paypal_webhook_triggered_on_subscription_change',
		'args'                => array(
			'id' => array(
				'validate_callback' => function ( $param, $request, $key ) {
					$headers      = $request->get_headers();
					$webhook_data = $request->get_json_params();

					if (empty($headers['paypal_transmission_sig'][0])) {
						return false;
					}
					$details   = [
						"webhook_id"        => $webhook_data['id'][0],
						"transmission_id"   => $headers['paypal_transmission_id'][0],
						"transmission_time" => $headers['paypal_transmission_time'][0],
						"cert_url"          => $headers['paypal_cert_url'][0],
						"auth_algo"         => $headers['paypal_auth_algo'][0],
						"transmission_sig"  => $headers['paypal_transmission_sig'][0],
						"webhook_event"     => $request->get_params(),
					];

					$VA_paypal    = new VA_PayPal();
					return $VA_paypal->verify_webhook_signature( $details );
				}
			),
		),
		'permission_callback' => function () {
			return current_user_can( 'edit_others_posts' );
		}
	) );
} );

function va_handle_paypal_webhook_triggered_on_subscription_change() {
	dbga( 'HITR' );
}
