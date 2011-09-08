<?php
/*
Plugin Name: Store Locator
Plugin URI: http://www.viadat.com/store-locator
Description: A store locator plugin that gives you the ability to effectively show important locations (stores, buildings, points of interest, etc.) in an easily searchable manner using Google Maps.
Version: 1.2.42.1
Author: Viadat Creations
Author URI: http://www.viadat.com
*/

$sl_version="1.2.42.1";
$sl_db_version=1.2;
include_once("variables.sl.php");
include_once("copyfolder.lib.php");
include_once("functions.sl.php");
include_once("via-latest.php");

register_activation_hook( __FILE__, 'install_table');

//add_action('wp_head', 'install_table');

add_action('wp_head', 'head_scripts');
// add_action('wp_footer', 'foot_scripts');

add_filter('the_content', 'ajax_map', 7);

add_action('admin_menu', 'sl_add_options_page');
add_action('admin_print_scripts', 'add_admin_javascript');
add_action('admin_print_styles','add_admin_stylesheet');

load_plugin_textdomain($text_domain, "/wp-content/uploads/sl-uploads/languages/");

add_filter('option_update_plugins', 'plugin_prevent_upgrade');
add_filter('transient_update_plugins', 'plugin_prevent_upgrade');

function plugin_prevent_upgrade($opt) {
	global $update_class;
	$plugin = plugin_basename(__FILE__);
	if ( $opt && isset($opt->response[$plugin]) ) {
		//Theres an update. Remove automatic upgrade:
		//$opt->response[$plugin]->package = '';
		//Show div update class
		$update_class="update-message";
		//Now we've prevented the upgrade taking place, It might be worth to give users a note that theres an update available:
		//add_action("after_plugin_row_$plugin", 'plugin_update_disabled_notice');
	}
	return $opt;
}

function plugin_update_disabled_notice() {
	global $sl_dir, $update_class;
	echo '<tr class="plugin-update-tr"><td class="plugin-update" colspan="5" style=""><a href="./admin.php?page='.$sl_dir.'/news-upgrades.php&upgrade=1&_wpnonce='.wp_create_nonce('my-nonce').'"><div class="'.$update_class.'"><b>Click Here to Upgrade Automatically</b></a> <span style="font-weight:normal">(preserves added themes, addons, images, icons, language files)</span> <!-- or <a href="http://www.viadat.com/vdl/store-locator.zip">Download the latest version</a--></div></td></tr>';
}
?>
