<?php
/*
 * Adds the styles and script files
 * */
function va_sheltered_animals_styles_and_scripts() {
	$template_page     = get_page_template_slug();
	$list_of_va_pages = [ 'va-donation-checkout.php', 'va-thank-you-donation.php', 'va-my-subscriptions.php', 'va-login-page.php' ];

	$is_va_page = in_array( $template_page, $list_of_va_pages, true );
	$is_va_post = ! is_singular( 'sheltered-animal' ) && ! is_post_type_archive( 'sheltered-animal' ) && ! is_tax( 'kind-of-animal' );

	if ( ! $is_va_page && $is_va_post ) {
		return;
	}

	wp_enqueue_script(
		'sheltered-animal',
		VA_URL . '/assets/build/js/index.js',
		'jquery',
		filemtime( VA_ABS . '/assets/build/js/index.js' ),
		true,
	);

	wp_enqueue_style(
		'sheltered-animal',
		VA_URL . '/assets/build/css/sheltered-animals.css',
		'',
		filemtime( VA_ABS . '/assets/build/css/sheltered-animals.css' )
	);

	if ( $template_page === 'va-my-subscriptions.php' ) {
		wp_enqueue_script(
			'va-vue-js',
			VA_URL . '/assets/inc/vue.min.js',
			'jquery',
			filemtime( VA_ABS . '/assets/inc/vue.min.js' ),
			true,
		);
	}

}

add_action( 'wp_enqueue_scripts', 'va_sheltered_animals_styles_and_scripts' );
