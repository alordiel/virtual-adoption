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

require_once (ARSVD_ABS . '/include/cpt-animals.php');
require_once (ARSVD_ABS . '/include/template-loader.php');
require_once (ARSVD_ABS . '/include/functions.php');
require_once (ARSVD_ABS . '/include/enqueue-scripts-and-styles.php');
require_once (ARSVD_ABS . '/include/admin-settings.php');
