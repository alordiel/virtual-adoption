<?php
/*
 * Adds the styles and script files
 * */
function ars_sheltered_animals_styles_and_scripts() {
	$template_page     = get_page_template_slug();
	$list_of_ars_pages = [ 'ars-donation-checkout.php', 'ars-thank-you-donation.php', 'ars-my-subscriptions.php' ];

	$is_ars_page = ( in_array( $template_page, $list_of_ars_pages ) );
	$is_ars_post = ! is_singular( 'sheltered-animal' ) && ! is_post_type_archive( 'sheltered-animal' ) && ! is_tax( 'kind-of-animal' );

	if ( ! $is_ars_page && $is_ars_post ) {
		return;
	}

	wp_enqueue_script(
		'sheltered-animal',
		ARSVD_URL . '/assets/build/js/index.js',
		'jquery',
		filemtime( ARSVD_ABS . '/assets/build/js/index.js' ),
		true,
	);

	wp_enqueue_style(
		'sheltered-animal',
		ARSVD_URL . '/assets/build/css/sheltered-animals.css',
		'',
		filemtime( ARSVD_ABS . '/assets/build/css/sheltered-animals.css' )
	);

	if ( $template_page === 'ars-my-subscriptions.php' ) {
		wp_enqueue_script(
			'ars-vue-js',
			ARSVD_URL . '/assets/inc/vue.min.js',
			'jquery',
			filemtime( ARSVD_ABS . '/assets/inc/vue.min.js' ),
			true,
		);
	}

}

add_action( 'wp_enqueue_scripts', 'ars_sheltered_animals_styles_and_scripts' );
