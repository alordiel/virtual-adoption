<?php
/*
 * Adds the styles and script files
 * */
function ars_sheltered_animals_styles_and_scripts() {
	$is_ars_page = ( get_page_template_slug() === 'donation-checkout.php' ) ;
	if ( ! $is_ars_page && ! is_singular( 'sheltered-animal' ) && ! is_post_type_archive( 'sheltered-animal' ) && ! is_tax( 'kind-of-animal' ) ) {
		return;
	}

	wp_enqueue_style(
		'sheltered-animal',
		ARSVD_URL . '/assets/build/css/sheltered-animals.css',
		'',
		filemtime( ARSVD_ABS . '/assets/build/css/sheltered-animals.css' )
	);

}

add_action( 'wp_enqueue_scripts', 'ars_sheltered_animals_styles_and_scripts' );
