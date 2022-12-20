<?php
/** @noinspection ForgottenDebugOutputInspection */
function dbga( $arg ) {
	error_log( print_r( $arg, true ) );
}


/**
 * Checks which is the current page and returns a code string for marking which is the selected category
 * Used from the "select-kind-of-animal.php" template
 *
 * @param $settings
 *
 * @return string
 */
function ars_get_the_current_selected_kind( $settings ): string {

	if ( is_post_type_archive( 'sheltered-animal' ) ) {
		return 'all';
	}

	if ( empty( $settings ) ) {
		$settings = get_option( 'ars-settings' );
	}

	// If we don't have the saved settings we can do much about this
	if ( empty( $settings['animal-terms'] ) ) {
		return '';
	}
	$term_slug = get_query_var( 'term' );
	$term      = get_term_by( 'slug', $term_slug, 'kind-of-animal' );

	/** @noinspection NotOptimalIfConditionsInspection */
	if ( ars_is_wpml_activated() && ICL_LANGUAGE_CODE !== 'en' ) {
		$term_id = apply_filters( 'wpml_object_id', $term->term_id, 'kind-of-animal', false, 'en' );
		if ( ! empty( $term_id ) ) {
			global $sitepress;
			$current_language = $sitepress->get_current_language();
			$sitepress->switch_lang('en');
			$term = get_term(  $term_id, 'kind-of-animal');
			$sitepress->switch_lang($current_language);
		}
	}
	return $term->slug;
}

/**
 * Returns true if WPML is active
 *
 * @return bool
 */
function ars_is_wpml_activated(): bool {
	return class_exists( 'SitePress' ) && function_exists( 'wpml_is_setup_complete' ) && wpml_is_setup_complete();
}

/**
 * an obfuscation of the id
 * @param int $id
 *
 * @return string
 */
function ars_encode_id(int $id):string {
	return 's_' .  (($id * 3 ) + 13);
}


/**
 * Deobfuscation of the id
 *
 * @param string $string
 *
 * @return int
 */
function ars_decode_id(string $string): int {
	$int = (int) $string;
	return ($int - 13) / 3;
}
