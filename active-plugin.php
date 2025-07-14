<?php
/**
 * Plugin Name:       Active Plugin
 * Plugin URI:        https://example.com/
 * Description:       A simple, restructured plugin.
 * Version:           1.0.0
 * Author:            Your Name
 * Author URI:        https://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       active-plugin
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'ACTIVE_PLUGIN_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 */
function activate_active_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-active-plugin-activator.php';
	Active_Plugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_active_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-active-plugin-deactivator.php';
	Active_Plugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_active_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_active_plugin' );

/**
 * The core plugin class and logic handler.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-active-plugin.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-active-plugin-logic.php';

/**
 * Begins execution of the plugin.
 */
function run_active_plugin() {
	new Active_Plugin();
}
run_active_plugin(); 