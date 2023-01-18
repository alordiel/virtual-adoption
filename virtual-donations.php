<?php
/**
 * Plugin Name:       Virtual Adoption
 * Plugin URI:        https://github.com/alordiel/virtual-adiption
 * Description:       This plugin creates the options add animals from your shelter for virtual adoption. The payment is done on monthly bases by PayPal.
 * Version:           1.1.0
 * Requires at least: 5.9.1
 * Requires PHP:      7.4.0
 * Author:            Alexander Vasilev
 * Author URI:        https://timelinedev.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       virtual-adoption
 * Domain Path:       /languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}




define( 'VA_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
const VA_ABS = __DIR__;

require_once( VA_ABS . '/include/cpt-animals.php' );
require_once( VA_ABS . '/include/cpt-ars-subscriptions.php' );
require_once( VA_ABS . '/include/template-loader.php' );
require_once( VA_ABS . '/include/functions.php' );
require_once( VA_ABS . '/include/wordpress.php' );
require_once( VA_ABS . '/include/enqueue-scripts-and-styles.php' );
require_once( VA_ABS . '/include/admin-settings.php' );
require_once( VA_ABS . '/include/ajax.php' );
require_once( VA_ABS . '/include/database-tables.php' );
require_once( VA_ABS . '/include/subscriptions.php' );
require_once( VA_ABS . '/include/mails.php' );
require_once( VA_ABS . '/include/api.php' );
require_once( VA_ABS . '/include/crypt.php' );


/**
 * Activate the plugin.
 */
function va_plugin_activated() {

	// Trigger our function that registers the custom post type plugin.
	if ( ! post_type_exists( 'sheltered-animal' ) ) {
		va_sheltered_animals();
		sheltered_animal_taxonomy();
		va_register_meta_boxes();
	}
	$va_settings = get_option( 'va-settings' );
	if(empty($va_settings['checkout-page'])){
		va_create_template_page('checkout-page');
	}
	if(empty($va_settings['thank-you-page'])){
		va_create_template_page('thank-you-page');
	}
	if(empty($va_settings['my-subscriptions-page'])){
		va_create_template_page('my-subscriptions-page');
	}
	if(empty($va_settings['login-page'])){
		va_create_template_page('my-subscriptions-page');
	}
	// Clear the permalinks after the post type has been registered.
	flush_rewrite_rules();

	va_create_subscription_tables();
	va_custom_post_status_for_subscriptions();
}

register_activation_hook( __FILE__, 'va_plugin_activated' );
