<?php
/**
 * @var $terms_page string
 * @var $login_page string
 */

$http_protocol = isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
$url           = $login_page . '?redirect-to=' . $http_protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$escaped_url   = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
?>
<p>
	<?php _e( "We need to create an account for you, so you can manage your monthly support.", 'virtual-adoptions' ); ?>
	<br>
	<?php echo sprintf( __( 'If you already have an account please log in from <a href="%s">here</a>', 'virtual-adoptions' ), $escaped_url ); ?>
	<br>
</p>
<p>
	<label for="first-name">
		<input id="first-name" name="first-name" type="text"
			   placeholder="<?php _e( 'First name', 'virtual-adoptions' ); ?>">
		<small></small>
	</label>
</p>
<p>
	<label for="last-name">
		<input id="last-name" name="last-name" type="text"
			   placeholder="<?php _e( 'Last name', 'virtual-adoptions' ); ?>">
		<small></small>
	</label>
</p>
<p>
	<label for="email">
		<input id="email" name="email" type="email"
			   placeholder="<?php _e( 'Email', 'virtual-adoptions' ); ?>">
		<small></small>
	</label>
</p>
<p>
	<label for="password">
		<input id="password" name="password" type="password"
			   placeholder="<?php _e( 'Password', 'virtual-adoptions' ); ?>">
		<small></small>
	</label>
</p>
<p>
	<label for="re-password">
		<input id="re-password" name="re-password"
			   type="password" placeholder="<?php _e( 'Confirm Password', 'virtual-adoptions' ); ?>">
		<small></small>
	</label>
</p>
<p class="form-row">
	<label for="terms">
		<input type="checkbox" name="terms" id="terms">
		<span>
			<?php echo sprintf( __( 'I have read and agree to the website <a href="%s" target="_blank" rel="noopener noreferrer">terms and conditions</a>', 'virtual-adoptions' ), $terms_page ); ?>
		</span>
		<small id="terms-error" class="alert-danger hidden">
			<?php _e( 'You need to accept the terms and conditions.', 'virtual-adoptions' ); ?>
		</small>
	</label>
	<input type="hidden" name="terms-field" value="1"><br>

</p>

<?php wp_nonce_field( 'va-taina', 'turbo-security' ); ?>

<div class="blue-button-wrap" style="text-align: left">
	<button type="button" id="register-user" class="blue-button"><?php _e( 'Sign up', 'virtual-adoptions' ) ?></button>
</div>

<small>
	<?php _e( "After the registration you will be automatically logged in and you can proceed with the virtual adoption.", 'virtual-adoptions' ) ?>
</small>
