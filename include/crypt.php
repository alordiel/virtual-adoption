<?php

/**
 * Crypt function that uses the salt to get the actual decrypted value of the PayPal secret key
 *
 * @param string $paypal_secret
 *
 * @return string
 */
function va_encrypt_data( string $paypal_secret ): string {
	$key = va_get_salt_key();
	// Remove the base64 encoding from our key
	$encryption_key = base64_decode( $key );
	// Generate an initialization vector
	$iv = openssl_random_pseudo_bytes( openssl_cipher_iv_length( 'aes-256-cbc' ) );
	// Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
	$encrypted = openssl_encrypt( $paypal_secret, 'aes-256-cbc', $encryption_key, 0, $iv );

	// The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)
	return base64_encode( $encrypted . '::' . $iv );
}


/**
 * Decrypting function that uses the salt to get the actual decrypted value of the PayPal secret key
 *
 * @param string $paypal_secret
 *
 * @return false|string
 */
function va_decrypt_data( string $paypal_secret ) {
	$key = va_get_salt_key();
	// Remove the base64 encoding from our key
	$encryption_key = base64_decode( $key );
	// To decrypt, split the encrypted data from our IV - our unique separator used was "::"
	[ $encrypted_data, $iv ] = explode( '::', base64_decode( $paypal_secret ), 2 );

	return openssl_decrypt( $encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv );
}


/**
 * Generates a salt or retrieve an already created salt
 *
 * @return string
 */
function va_get_salt_key(): string {
	$salt              = get_option( 'va-salt-phrase' );
	$secret_phrase_key = ! empty( $salt ) ? $salt : '';
	if ( $secret_phrase_key === '' ) {
		$salt = base64_encode( openssl_random_pseudo_bytes( 32 ) );
		update_option( 'va-salt-phrase', $salt );
	}

	return $salt;
}

