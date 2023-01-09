<?php
/**
 * Plugin Name:       ARS Virtual Donations
 * Plugin URI:        https://github.com/alordiel/ars-virtual-donations
 * Description:       This plugin creates the options add animals from the shelter for virtual donations
 * Version:           1.0.0
 * Requires at least: 5.9.1
 * Requires PHP:      7.4.0
 * Author:            Alexander Vasilev
 * Author URI:        https://timelinedev.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ars-virtual-donations
 * Domain Path:       /languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ARSVD_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
const ARSVD_ABS = __DIR__;

require_once( ARSVD_ABS . '/include/cpt-animals.php' );
require_once( ARSVD_ABS . '/include/cpt-ars-subscriptions.php' );
require_once( ARSVD_ABS . '/include/template-loader.php' );
require_once( ARSVD_ABS . '/include/functions.php' );
require_once( ARSVD_ABS . '/include/wordpress.php' );
require_once( ARSVD_ABS . '/include/enqueue-scripts-and-styles.php' );
require_once( ARSVD_ABS . '/include/admin-settings.php' );
require_once( ARSVD_ABS . '/include/ajax.php' );
require_once( ARSVD_ABS . '/include/database-tables.php' );
require_once( ARSVD_ABS . '/include/subscriptions.php' );


/**
 * Activate the plugin.
 */
function ars_plugin_activated() {
	// Trigger our function that registers the custom post type plugin.
	if ( ! post_type_exists( 'sheltered-animal' ) ) {
		ars_sheltered_animals();
		sheltered_animal_taxonomy();
		ars_register_meta_boxes();
	}
	$ars_settings = get_option( 'ars-settings' );
	if(empty($ars_settings['checkout-page'])){
		ars_create_template_page('checkout-page');
	}
	if(empty($ars_settings['thank-you-page'])){
		ars_create_template_page('thank-you-page');
	}
	if(empty($ars_settings['my-subscriptions-page'])){
		ars_create_template_page('my-subscriptions-page');
	}
	// Clear the permalinks after the post type has been registered.
	flush_rewrite_rules();

	ars_create_subscription_tables();
}

register_activation_hook( __FILE__, 'ars_plugin_activated' );
