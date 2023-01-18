<?php

function va_encrypt_data(string $data) {
	$key = va_get_salt_key();
    // Remove the base64 encoding from our key
    $encryption_key = base64_decode($key);
    // Generate an initialization vector
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    // Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
    // The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)
    return base64_encode($encrypted . '::' . $iv);
}

function va_decrypt_data(string $data) {
	$key = va_get_salt_key();
    // Remove the base64 encoding from our key
    $encryption_key = base64_decode($key);
    // To decrypt, split the encrypted data from our IV - our unique separator used was "::"
    list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
    return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
}

function va_get_salt_key(): string{
	$va_settings = get_option( 'va-settings' );
	$secret_phrase_key   = ! empty( $va_settings['phrase-key'] ) ? $va_settings['phrase-key'] : '';
	if ($secret_phrase_key === '') {
		$va_settings['phrase-key'] = base64_encode(openssl_random_pseudo_bytes(32));
		update_option( 'va-settings', $va_settings );
	}
	return $va_settings['phrase-key'];
}
