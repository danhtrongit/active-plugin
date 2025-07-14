<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *	
 * @link              https://danhtrong.com
 * @since             1.0.0
 * @package           Active_Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Mã kích hoạt
 * Plugin URI:        https://danhtrong.com
 * Description:       Quản lý mã kích hoạt
 * Version:           1.0.0
 * Author:            Trần Danh Trọng
 * Author URI:        https://danhtrong.com
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
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-active-plugin-activator.php';
	Active_Plugin_Activator::activate();
	}


function deactivate_active_plugin()
	{
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-active-plugin-deactivator.php';

	Active_Plugin_Deactivator::deactivate();
	}

register_activation_hook( __FILE__, 'activate_active_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_active_plugin' );

require plugin_dir_path( __FILE__ ) . 'includes/class-active-plugin.php';

function run_active_plugin()
	{

	$plugin = new Active_Plugin();
	$plugin->run();

	}
run_active_plugin();






//////////////
//
//
//
//
//
//
//
//
//
//
//




// Register REST API route
add_action('rest_api_init', function () {
	register_rest_route('cdvn/v1', '/check_combo_offer/', array(
		'methods' => 'GET',
		'callback' => 'get_combo_offers',
	));
});

function get_combo_offers($request)
{
	return array(
		"success" => "true",
		"name" => "Combo Offers Plugin",
		"slug" => "isures-combo-offers",
		"slug" => "isures-combo-offers",
		"download_url" => "https://svplugin.isures.com/wp-admin/admin-ajax.php/?action=download_link&token=a363d018e028c6df5bfdfc61e575f6d3&file=isures-combo-offers-12znssx.zip&sl=isures-combo-offers&vs=2.1.1",
		"version" => "2.1.1",
		"requires" => "4.0",
		"tested" => "6.2.2",
		"last_updated" => "2023-07-02",
		"upgrade_notice" => "Update was successful",
		"author" => "XXXX",
		"author_homepage" => "XXXX",
		"sections" => array(
			"changelog" => "xx",
			"description" => "xx",
			"installation" => "xx"
		),
		"banner" => array(
			"low" => "xx",
			"high" => "xx"
		),
		"one_year" => false,
		"dm" => "xxx",
		"date_end" => "01-01-2043"
	);
}
