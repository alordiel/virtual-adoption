<?php
/**
 * Template name: ARS Donation Checkout
 */
get_header();
$ars_settings               = get_option( 'ars-settings' );
$manage_payments = get_permalink($ars_settings['my-subscriptions-page']);
?>
<h2>Thank you for your donation</h2>
<p>You can manage your payments from <a href="<?php echo $manage_payments; ?>">this page </a>.</p>
<?php
get_footer();
