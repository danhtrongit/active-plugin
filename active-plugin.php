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

require_once plugin_dir_path( __FILE__ ) . 'includes/class-active-plugin.php';

function run_active_plugin() {
    return Active_Plugin::get_instance();
}

run_active_plugin(); 