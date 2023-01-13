<?php
// load templates name in page attributes
function ars_register_template_pages( $templates ) {
	$templates['ars-donation-checkout.php'] = 'ARS Donation Checkout page';
	$templates['ars-thank-you-donation.php'] = 'ARS Thank you page';
	$templates['ars-my-subscriptions.php'] = 'ARS My subscriptions page';

	return $templates;
}

add_filter( 'theme_page_templates', 'ars_register_template_pages' );

// load page templates
function ars_add_templates_pages( $template ) {
	$template_slug = get_page_template_slug();
	if ( $template_slug === 'ars-donation-checkout.php' ) {
		$template = ARSVD_ABS . '/templates/pages/donation-checkout.php';
	} elseif ( $template_slug === 'ars-thank-you-donation.php' ) {
		$template = ARSVD_ABS . '/templates/pages/thank-you-donation.php';
	} elseif ( $template_slug === 'ars-my-subscriptions.php' ) {
		$template = ARSVD_ABS . '/templates/pages/my-subscriptions.php';
	}

	return $template;
}

add_filter( 'page_template', 'ars_add_templates_pages' );
