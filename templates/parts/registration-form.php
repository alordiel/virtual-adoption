<?php
/**
 * @var $terms_page string
 */
?>
<p><?php _e( "First we need to create an account for you, so you can manage your monthly support. <br> Please fill in the details below.", 'virtual-adoption' ); ?></p>
<p>
	<label for="first-name">
		<input id="first-name" name="first-name" type="text"
			   placeholder="<?php _e( 'First name', 'virtual-adoption' ); ?>">
		<small></small>
	</label>
</p>
<p>
	<label for="last-name">
		<input id="last-name" name="last-name" type="text"
			   placeholder="<?php _e( 'Last name', 'virtual-adoption' ); ?>">
		<small></small>
	</label>
</p>
<p>
	<label for="email">
		<input id="email" name="email" type="email"
			   placeholder="<?php _e( 'Email', 'virtual-adoption' ); ?>">
		<small></small>
	</label>
</p>
<p>
	<label for="password">
		<input id="password" name="password" type="password"
			   placeholder="<?php _e( 'Password', 'virtual-adoption' ); ?>">
		<small></small>
	</label>
</p>
<p>
	<label for="re-password">
		<input id="re-password" name="re-password"
			   type="password" placeholder="<?php _e( 'Confirm Password', 'virtual-adoption' ); ?>">
		<small></small>
	</label>
</p>
<p class="form-row">
	<label for="terms">
		<input type="checkbox" name="terms" id="terms">
		<span>
			<?php echo sprintf( __( 'I have read and agree to the website <a href="%s" target="_blank" rel="noopener noreferrer">terms and conditions</a>', 'virtual-adoptions' ), $terms_page ); ?>
		</span>
	</label>
	<input type="hidden" name="terms-field" value="1">
</p>

<?php wp_nonce_field( 'va-taina', 'turbo-security' ); ?>

<div class="blue-button-wrap" style="text-align: left">
	<button type="button" id="register-user" class="blue-button"><?php _e('Sign up', 'virtual-adoptions') ?></button>
</div>
