<?php
// loads templates name in page attributes
function va_register_template_pages( $templates ) {
	$templates['va-donation-checkout.php']  = 'VirtualAdoption - Donation Checkout page';
	$templates['va-thank-you-donation.php'] = 'VirtualAdoption - Thank you page';
	$templates['va-my-subscriptions.php']   = 'VirtualAdoption - My subscriptions page';
	$templates['va-login-page.php']         = 'VirtualAdoption - Login page';

	return $templates;
}

add_filter( 'theme_page_templates', 'va_register_template_pages' );

// loads page templates
function va_add_templates_pages( $template ) {
	$template_slug = get_page_template_slug();
	if ( $template_slug === 'va-donation-checkout.php' ) {
		$template = VA_ABS . '/templates/pages/donation-checkout.php';
	} elseif ( $template_slug === 'va-thank-you-donation.php' ) {
		$template = VA_ABS . '/templates/pages/thank-you-donation.php';
	} elseif ( $template_slug === 'va-my-subscriptions.php' ) {
		$template = VA_ABS . '/templates/pages/my-subscriptions.php';
	} elseif ( $template_slug === 'va-login-page.php' ) {
		$template = VA_ABS . '/templates/pages/login-page.php';
	}

	return $template;
}

add_filter( 'page_template', 'va_add_templates_pages' );
