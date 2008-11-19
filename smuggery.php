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
///////////////
// Constants //
///////////////
define( 'SMUGGERY_APIKEY', 'f83PiPGvYhcek14Zu8Og4rLPDYd2wsxH'); // Smuggery API key
define( 'SMUGGERY_APPINFO', 'Smuggery/0.1 (http://www.freerobby.com/smuggery)');
////////////////////
// Plugin options //
////////////////////
// Option keys
global $smuggery_optionnames;
$smuggery_optionnames = array (
	'smuggery_galleryTitle',
	'smuggery_smugmugNickname'
	);

global $smuggery_optionvals;
$smuggery_optionvals = array (
	"My Gallery",
	NULL
);

//////////////
// Includes //
//////////////

// PHPSmug
require_once ('phpSmug/phpSmug.php');
// Wordpress Administrative Functions
require_once (ABSPATH . '/wp-admin/admin-functions.php');

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
	global $smuggery_optionnames;
	global $smuggery_optionvals;
	$num_opts = count($smuggery_optionnames);
	for ($i = 0; $i < $num_opts; $i++) {
		add_option($smuggery_optionnames[$i], $smuggery_optionvals[$i]);
	}
}
function smuggery_uninstall () {
	// Remove options from database on uninstall.
	global $smuggery_optionnames;
	global $smuggery_optionvals;
	$num_opts = count($smuggery_optionnames);
	for ($i = 0; $i < $num_opts; $i++) {
		delete_option($smuggery_optionnames[$i], $smuggery_optionvals[$i]);
	}
}

///////////////////
// Options pages //
///////////////////
// Define our options page
function smuggery_optionspage () {
	include  ( 'smuggery-options.php' );
}
?>