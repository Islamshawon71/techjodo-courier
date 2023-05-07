<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://devshawon.com
 * @since             1.0.0
 * @package           Techjodo_Courier
 *
 * @wordpress-plugin
 * Plugin Name:       TechJodo Courier
 * Plugin URI:        https://techjodo.com
 * Description:       This plugin connect multiple courier like ecourier, pathao, steadfast, redx and upload order from woocommerce.  
 * Version:           1.0.0
 * Author:            DevShawon
 * Author URI:        https://devshawon.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       techjodo-courier
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TECHJODO_COURIER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-techjodo-courier-activator.php
 */
function activate_techjodo_courier() {
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-techjodo-courier-deactivator.php
 */
function deactivate_techjodo_courier() {
}

register_activation_hook( __FILE__, 'activate_techjodo_courier' );
register_deactivation_hook( __FILE__, 'deactivate_techjodo_courier' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-techjodo-courier.php';