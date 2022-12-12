<?php
/** @noinspection ForgottenDebugOutputInspection */
function dbga( $arg ) {
	error_log( print_r( $arg, true ) );
}

function ars_get_the_current_selected_kind(): string {
	$term_slug = get_query_var( 'term' );

	return 'dogs';
}


function ars_is_wpml_activated(): bool {
	return class_exists( 'SitePress' ) && ! function_exists( 'wpml_is_setup_complete' ) && wpml_is_setup_complete();
}
