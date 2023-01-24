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
