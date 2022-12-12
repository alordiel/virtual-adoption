<?php
/** @noinspection ForgottenDebugOutputInspection */
function dbga( $arg ) {
	error_log( print_r( $arg, true ) );
}
