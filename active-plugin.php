<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://hi@active-plugin.com
 * @since             1.0.0
 * @package           Active_Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Active Plugin
 * Plugin URI:        https://active-plugin.com
 * Description:       Active Plugin
 * Version:           1.0.0
 * Author:            Active Plugin
 * Author URI:        https://hi@active-plugin.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       active-plugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
	die;
	}


define( 'ACTIVE_PLUGIN_VERSION', '1.0.0' );


function activate_active_plugin()
	{
	require_once plugin_dir_path( __FILE__ ) . 'src/class-active-plugin-activator.php';
	Active_Plugin_Activator::activate();
	}


function deactivate_active_plugin()
	{
	require_once plugin_dir_path( __FILE__ ) . 'src/class-active-plugin-deactivator.php';

	Active_Plugin_Deactivator::deactivate();
	}

register_activation_hook( __FILE__, 'activate_active_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_active_plugin' );

require plugin_dir_path( __FILE__ ) . 'src/class-active-plugin.php';

function run_active_plugin()
	{

	$plugin = new Active_Plugin();
	$plugin->run();

	}
run_active_plugin();
