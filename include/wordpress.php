<?php
// load templates name in page attributes
function vive_ship_add_page_template( $templates ) {
	$templates['donation-checkout.php'] = 'ARS Donation Checkout';

	return $templates;
}

add_filter( 'theme_page_templates', 'vive_ship_add_page_template' );

// load page templates
function vive_ship_load_plugin_template( $template ) {

	if ( get_page_template_slug() === 'donation-checkout.php' ) {
		$template = ARSVD_ABS . '/templates/donation-checkout.php';
	}

	return $template;
}

add_filter( 'page_template', 'vive_ship_load_plugin_template' );
