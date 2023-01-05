<?php
function ars_create_new_donation_subscription() {
	check_ajax_referer('ars-taina','security');
	wp_die(1);
}
add_action('wp_ajax_ars_create_new_donation_subscription','ars_create_new_donation_subscription');
add_action('wp_ajax_nopriv_ars_create_new_donation_subscription','ars_create_new_donation_subscription');
