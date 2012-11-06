<?php /*
--------------------------------------------------------------------------------
Plugin Name: Commentpress Core
Plugin URI: http://www.futureofthebook.org/commentpress/
Description: Commentpress allows readers to comment paragraph by paragraph in the margins of a text. You can use it to annotate, gloss, workshop, debate and more!
Author: Institute for the Future of the Book
Version: 3.4
Author URI: http://www.futureofthebook.org
--------------------------------------------------------------------------------
Special thanks to:
Eddie Tejeda @ www.visudo.com for Commentpress 2.0
The developers of jQuery www.jquery.com
Mark James, for the icon http://www.famfamfam.com/lab/icons/silk/
--------------------------------------------------------------------------------
*/





// -----------------------------------------------------------------------------
// No need to edit below this line
// -----------------------------------------------------------------------------

// set version
define( 'CP_VERSION', '3.4' );

// store reference to this file
if ( !defined( 'CP_PLUGIN_FILE' ) ) {
	define( 'CP_PLUGIN_FILE', __FILE__ );
}

// store URL to this plugin's directory
if ( !defined( 'CP_PLUGIN_URL' ) ) {
	define( 'CP_PLUGIN_URL', plugin_dir_url( CP_PLUGIN_FILE ) );
}
// store PATH to this plugin's directory
if ( !defined( 'CP_PLUGIN_PATH' ) ) {
	define( 'CP_PLUGIN_PATH', plugin_dir_path( CP_PLUGIN_FILE ) );
}







/*
--------------------------------------------------------------------------------
Begin by establishing Plugin Context
--------------------------------------------------------------------------------
NOTE: force-activated context is now deprecated
--------------------------------------------------------------------------------
*/

// test for multisite location
if ( basename( dirname( CP_PLUGIN_FILE ) ) == 'mu-plugins' ) { 

	// directory-based forced activation
	if ( !defined( 'CP_PLUGIN_CONTEXT' ) ) {
		define( 'CP_PLUGIN_CONTEXT', 'mu_forced' );
	}
	
// test for multisite
} elseif ( is_multisite() ) {

	// check if our plugin is one of those activated sitewide
	$this_plugin = plugin_basename( CP_PLUGIN_FILE );
	
	// unfortunately, is_plugin_active_for_network() is not yet available so
	// we have to do this manually...
	
	// get sitewide plugins
	$active_plugins = (array) get_site_option( 'active_sitewide_plugins' );
	
	// is the plugin network activated?
	if ( isset( $active_plugins[ $this_plugin ] ) ) {
	
		// yes, network activated
		if ( !defined( 'CP_PLUGIN_CONTEXT' ) ) {
			define( 'CP_PLUGIN_CONTEXT', 'mu_sitewide' );
		}
		
	} else {

		// optional activation per blog in multisite
		if ( !defined( 'CP_PLUGIN_CONTEXT' ) ) {
			define( 'CP_PLUGIN_CONTEXT', 'mu_optional' );
		}
		
	}

} else {

	// single user install
	if ( !defined( 'CP_PLUGIN_CONTEXT' ) ) {
		define( 'CP_PLUGIN_CONTEXT', 'standard' );
	}
	
}

//print_r( CP_PLUGIN_CONTEXT ); die();






/*
--------------------------------------------------------------------------------
NOTE: in multisite, child themes are registered as broken if the plugin is not 
network-enabled. Make sure child themes have instructions.
--------------------------------------------------------------------------------
There are further complex issues when in Multisite:

First scenario:
* if the plugin is NOT initially network-enabled 
* but it IS enabled on one or more blogs on the network
* and the plugin in THEN network-enabled

Second scenario:
* if the plugin IS initially network-enabled 
* and it IS activated on one or more blogs on the network
* and the plugin in THEN network-disabled

If installs stick to one or the other, then all works as expected.
--------------------------------------------------------------------------------
*/

// register our themes directory
register_theme_directory( plugin_dir_path( CP_PLUGIN_FILE ) . 'themes' );







/*
--------------------------------------------------------------------------------
Include Standalone
--------------------------------------------------------------------------------
*/

commentpress_include_core();






/*
--------------------------------------------------------------------------------
Init Standalone
--------------------------------------------------------------------------------
*/

// only activate if in standard or mu_optional context
if ( CP_PLUGIN_CONTEXT == 'standard' OR CP_PLUGIN_CONTEXT == 'mu_optional' ) {

	// Commentpress Core
	commentpress_activate_core();
	
	// access global
	global $commentpress_core;
	
	// activation
	register_activation_hook( CP_PLUGIN_FILE, array( $commentpress_core, 'activate' ) );
	
	// deactivation
	register_deactivation_hook( CP_PLUGIN_FILE, array( $commentpress_core, 'deactivate' ) );
	
	// uninstall uses the 'uninstall.php' method
	// see: http://codex.wordpress.org/Function_Reference/register_uninstall_hook
	
	// AJAX Commenting
	commentpress_activate_ajax();
	
}





/*
--------------------------------------------------------------------------------
Init Multisite
--------------------------------------------------------------------------------
*/

// have we activated network-wide?
if ( CP_PLUGIN_CONTEXT == 'mu_sitewide' ) {

	// activate multisite plugin

	// define filename
	$_file = 'commentpress-multisite/commentpress-mu.php';

	// get path
	$_file_path = cp_file_is_present( $_file );
	
	// we're fine, include class definition
	require_once( $_file_path );

}





/*
--------------------------------------------------------------------------------
Misc Utility Functions
--------------------------------------------------------------------------------
*/

/** 
 * @description: utility to include the core plugin
 * @todo: 
 *
 */
function commentpress_include_core() {
	
	// do we have our class?
	if ( !class_exists( 'CommentPress' ) ) {
		
		// define filename
		$_file = 'commentpress-core/class_commentpress.php';
		
		// get path
		$_file_path = cp_file_is_present( $_file );
		
		// we're fine, include class definition
		require_once( $_file_path );
		
	}
	
}






/** 
 * @description: utility to activate the core plugin
 * @todo: 
 *
 */
function commentpress_activate_core() {
	
	// declare as global
	global $commentpress_core;
	
	// do we have it already?
	if ( is_null( $commentpress_core ) ) {
	
		// instantiate it
		$commentpress_core = new CommentPress;
	
	}

}






/** 
 * @description: utility to activate the ajax plugin
 * @todo: 
 *
 */
function commentpress_activate_ajax() {
	
	// define filename
	$_file = 'commentpress-ajax/cp-ajax-comments.php';

	// get path
	$_file_path = cp_file_is_present( $_file );
	
	// we're fine, include ajax file
	require_once( $_file_path );
		
}






/** 
 * @description: utility to add link to settings page
 * @todo: 
 *
 */
function commentpress_plugin_action_links( $links, $file ) {
	
	// add settings link
	if ( $file == plugin_basename( dirname(__FILE__).'/commentpress.php' ) ) {
		$links[] = '<a href="options-general.php?page=cp_admin_page">'.__( 'Settings', 'commentpress-plugin' ).'</a>';
	}
	
	// --<
	return $links;

}

add_filter( 'plugin_action_links', 'commentpress_plugin_action_links', 10, 2 );






/** 
 * @description: utility to check for presence of vital files
 * @param string $filename the name of the Commentpress Plugin file
 * @return string $filepath absolute path to file
 * @todo: 
 *
 */
function cp_file_is_present( $filename ) {

	// define path to our requested file
	$filepath = CP_PLUGIN_PATH . $filename;

	// is our class definition present?
	if ( !is_file( $filepath ) ) {
	
		// oh no!
		die( 'Commentpress Error: file "'.$filepath.'" is missing from the plugin directory.' );
	
	}
	
	
	
	// --<
	return $filepath;

}






/**
 * shortcut for debugging
 */
function _cpdie( $var ) {

	print '<pre>';
	print_r( $var ); 
	print '</pre>';
	die();
	
}




?>