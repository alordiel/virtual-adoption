<?php
function ars_sheltered_animals_template_loader( $template ) {

	if ( is_singular( 'sheltered-animal' ) ) {
		return require( ARSVD_ABS . '/templates/pages/single-sheltered-animal.php');
	}

	if ( is_archive() && is_post_type_archive( 'sheltered-animal' ) ) {
		return require( ARSVD_ABS . '/templates/pages/archive-sheltered-animal.php');
	}

	if( is_tax( 'kind-of-animal' ) ) {
		return require( ARSVD_ABS . '/templates/pages/taxonomy-kind-of-animal.php');
	}

	return $template;
}

add_filter( 'template_include', 'ars_sheltered_animals_template_loader' );
