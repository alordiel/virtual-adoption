<?php
/**
 * Template name: VirtualAdopt - Login Page
 */
$error = '';
if ( ! empty( $_POST['login-submit'] ) && ! is_user_logged_in() ) {
	if ( ! empty( $_POST['email'] ) && ! is_email( $_POST['email'] ) ) {
		$error = __( 'Invalid email', 'virtual-adoptions' );
	}
	if ( ! empty( $_POST['email'] ) && ! empty( $_POST['password'] ) ) {
		$credentials = array(
			'user_login'    => $_POST['email'],
			'user_password' => $_POST['password'],
			'remember'      => ! empty( $_POST['remember-me'] )
		);

		$user = wp_signon( $credentials, false );
		if ( ! is_wp_error( $user ) ) {
			wp_set_current_user( $user->ID );
		} else {
			$error .= $user->get_error_message();
		}

	} else {
		$error = __( 'Please enter email and password', 'virtual-adoptions' );
	}
}


if ( is_user_logged_in() ) {
	wp_redirect( get_post_type_archive_link( 'sheltered-animal' ) );
}

get_header();
?>
<div class="va-container">
	<div class="vir-adopt-login">
		<form method="post">
			<p>
				<label for="email"><?php _e( 'Email', 'virtual-adoptions' ); ?></label><br>
				<input type="email" name="email" id="email"
					   value="<?php echo ! empty( $_POST['email'] ) ? $_POST['email'] : '' ?>" autofocus>
			</p>

			<div>
				<label for="user_pass"><?php _e( 'Password', 'virtual-adoptions' ); ?></label><br>
				<input type="password" name="password" id="user_pass" value="">
			</div>
			<p>
				<input name="remember-me" type="checkbox" id="remember-me" value="forever">
				<label for="remember-me"><?php _e( 'Remember Me', 'virtual-adoptions' ); ?></label>
			</p>

			<?php if ( $error !== '' ): ?>
				<div class="error-box">
					<p>
						<?php echo $error; ?>
					</p>
				</div>
			<?php endif; ?>

			<p class="submit">
				<input type="submit" name="login-submit" id="login-submit" class="button button-primary"
					   value="<?php _e( 'Log in', 'virtual-adoptions' ); ?>">
			</p>
		</form>
	</div>
</div>
<?php
get_footer();

