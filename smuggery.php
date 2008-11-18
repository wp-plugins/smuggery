<?php
/*
Plugin Name: Smuggery

Plugin URI: http://www.freerobby.com/smuggery

Description: Smuggery pulls all of your public photos from your SmugMug.com account and generates dynamic Wordpress photo galleries from them to be displayed on your blog!

Author: Robby Grossman

Version: 0.1

Author URI: http://www.freerobby.com
*/	
?>
<?php
////////////////////
// Plugin options //
////////////////////
// Option keys
global $ds_ap_options_names;
$ds_ap_options_names = array (
	'smuggery_galleryTitle',
	'smuggery_smugmugUsername'
	);

global $ds_ap_options_vals;
$ds_ap_options_vals = array (
	"My Gallery",
	NULL
);

//////////////
// Includes //
//////////////

// Wordpress Administrative Functions
require_once ( ABSPATH . '/wp-admin/admin-functions.php' );

/////////////////////////////////
// Wordpress Hook Declarations //
/////////////////////////////////

// Plugin activation
register_activation_hook ( __FILE__ , 'smuggery_install' );
// Plugin deactivation
register_deactivation_hook ( __FILE__ , 'smuggery_uninstall' );
// Add our page to the administration menu
add_action('admin_menu', 'smuggery_addpages');
// Before content is displayed, let us look through it and make changes as necessary.
add_filter ('the_content', 'smuggery_parsecontent');

////////////////////////////////
// Wordpress Hook Invocations //
////////////////////////////////
// Define our configuration pages
function smuggery_addpages () {
	// Add our menu under "options"
	add_options_page ( 'Smuggery', 'Smuggery', 'edit_plugins', __FILE__, 'smuggery_optionspage');
}
// Content interrupt entry point!
function smuggery_parsecontent ( $content ) {
	return $content;
}
function smuggery_install () {
	// Add our options with their default values as defined.
	global $ds_ap_options_names;
	global $ds_ap_options_vals;
	$num_opts = count($ds_ap_options_names);
	for ($i = 0; $i < $num_opts; $i++) {
		add_option($ds_ap_options_names[$i], $ds_ap_options_vals[$i]);
	}
}
function smuggery_uninstall () {
	// Remove options from database on uninstall.
	//global $ds_ap_options_names;
	//$num_opts = count($ds_ap_options_names);
	//for ($i = 0; $i < $num_opts; $i++) {
	//	delete_option($ds_ap_options_names[$i], $ds_ap_options_vals[$i]);
	//}
}

///////////////////
// Options pages //
///////////////////
// Define our options page
function smuggery_optionspage () {
	include  ( 'smuggery-options.php' );
}
?>