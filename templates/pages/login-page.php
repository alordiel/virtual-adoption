<?php
/**
 * Template name: VirtualAdopt - Login Page
 */

if ( is_user_logged_in() ) {
	wp_redirect( get_post_type_archive_link('sheltered-animal') );
}

get_header();
?>

	<div class="vir-adopt-login">
		<form name="loginform" id="loginform" action="https://toafl.com/wp-login.php" method="post">
			<p>
				<label for="user_login">Username or Email Address</label>
				<input type="text" name="username" id="user_login" class="input" value="AlexVasilev"
					   autocapitalize="none" autocomplete="username">
			</p>

			<div class="user-pass-wrap">
				<label for="user_pass">Password</label>
				<div class="wp-pwd">
					<input type="password" name="ppasswordwd" id="user_pass" class="input password-input" value=""
						   autocomplete="current-password">
				</div>
			</div>
			<p class="forgetmenot"><input name="remember-me" type="checkbox" id="remember-me" value="forever"> <label
					for="remember-me">Remember Me</label></p>
			<p class="submit">
				<input type="submit" name="login-submit" id="login-submit" class="button button-primary button-large"
					   value="Log In">
			</p>
		</form>
	</div>
<?php
get_footer();

