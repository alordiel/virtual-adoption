<?php
/*
 * Adds the styles and script files
 * */
function ars_sheltered_animals_styles_and_scripts() {
	$template_page = get_page_template_slug();
	$is_ars_page = ( in_array($template_page,['donation-checkout.php', 'thank-you-donation.php'] ) ) ;
	if ( ! $is_ars_page && ! is_singular( 'sheltered-animal' ) && ! is_post_type_archive( 'sheltered-animal' ) && ! is_tax( 'kind-of-animal' ) ) {
		return;
	}

	wp_enqueue_script(
		'sheltered-animal',
		ARSVD_URL . '/assets/build/js/index.js',
		'',
		filemtime( ARSVD_ABS . '/assets/build/js/index.js' ),
		true,
	);

	wp_enqueue_style(
		'sheltered-animal',
		ARSVD_URL . '/assets/build/css/sheltered-animals.css',
		'',
		filemtime( ARSVD_ABS . '/assets/build/css/sheltered-animals.css' )
	);

}

add_action( 'wp_enqueue_scripts', 'ars_sheltered_animals_styles_and_scripts' );
