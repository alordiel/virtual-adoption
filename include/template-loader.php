<?php

/**
 * Matches the page templates to their origins (or replacing the original template with the one from the plugin)
 *
 * @param $template
 *
 * @return mixed
 */
function va_sheltered_animals_template_loader( $template ) {
	if ( is_singular( 'sheltered-animal' ) ) {
		return  VA_ABS . '/templates/pages/single-sheltered-animal.php';
	}

	if ( is_archive() && is_post_type_archive( 'sheltered-animal' ) ) {
		return  VA_ABS . '/templates/pages/archive-sheltered-animal.php';
	}

	if( is_tax( 'kind-of-animal' ) ) {
		return  VA_ABS . '/templates/pages/taxonomy-kind-of-animal.php';
	}

	return $template;
}

add_filter( 'template_include', 'va_sheltered_animals_template_loader' );
