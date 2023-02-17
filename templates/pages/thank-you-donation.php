<?php
/**
 * Template name: VirtualAdopt - Donation Checkout
 */
get_header();
$va_settings     = get_option( 'va-settings' );
$manage_payments = get_permalink( $va_settings['page']['my-subscriptions'] );
?>
	<div class="va-container">
		<h2><?php echo get_the_title() ?></h2>
		<div class="entry-content">
			<?php the_content(); ?>
		</div>
		<p><?php echo sprintf( __( 'You can manage your payments from <a href="%s">this page.</a>', 'virtual-donations' ), $manage_payments ); ?></p>
	</div>
<?php
get_footer();
