<?php
/**
 * Template name: VirtualAdopt - Donation Checkout
 */
get_header();
$va_settings               = get_option( 'va-settings' );
$manage_payments = get_permalink($va_settings['page']['my-subscriptions']);
?>
<h2>Thank you for your donation</h2>
<p>You can manage your payments from <a href="<?php echo $manage_payments; ?>">this page </a>.</p>
<?php
get_footer();
