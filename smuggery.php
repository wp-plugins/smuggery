<?php
/*
Plugin Name: Smuggery

Plugin URI: http://www.freerobby.com/smuggery

Description: Smuggery pulls all of your public photos from your SmugMug.com account and generates dynamic Wordpress photo galleries from them to be displayed on your blog!

Author: Robby Grossman

Version: 0.2

Author URI: http://www.freerobby.com
*/	
?>
<?php

///////////////
// Constants //
///////////////
// Plugin metadata
define('SMUGGERY_APIKEY', 'f83PiPGvYhcek14Zu8Og4rLPDYd2wsxH'); // Smuggery API key
define('SMUGGERY_APPINFO', 'Smuggery/0.1 (http://www.freerobby.com/smuggery)');
// Tags
define('SMUGGERY_TAGGALLERY', '[smuggery=gallery]');
// Post meta
define('SMUGGERY_POSTMETAGALLERYHASH', 'smuggery_galleryhash');
// SmugMug data
global $SMUGGERY_SMUGMUGIMAGESIZES;
$SMUGGERY_SMUGMUGIMAGESIZES = array(
	'1' => 'Ti',
	'2' => 'Th',
	'3' => 'S',
	'4' => 'M',
	'5' => 'L',
	'6' => 'XL',
	'7' => 'X2',
	'8' => 'X3',
	'9' => 'O'
);
global $SMUGGERY_SMUGMUGIMAGESIZESNAMES;
$SMUGGERY_SMUGMUGIMAGESIZESNAMES = array(
	'1' => 'Tiny',
	'2' => 'Thumbnail',
	'3' => 'Small',
	'4' => 'Medium',
	'5' => 'Large',
	'6' => 'X Large',
	'7' => 'XX Large',
	'8' => 'XXX Large',
	'9' => 'Full Res'
);
global $SMUGGERY_SMUGMUGIMAGESIZESURLTOKENS;
$SMUGGERY_SMUGMUGIMAGESIZESURLTOKENS = array(
	'1' => 'TinyURL',
	'2' => 'ThumbURL',
	'3' => 'SmallURL',
	'4' => 'MediumURL',
	'5' => 'LargeURL',
	'6' => 'XLargeURL',
	'7' => 'X2LargeURL',
	'8' => 'X3LargeURL',
	'9' => 'OriginalURL'
);

global $SMUGGERY_SMUGMUGVIDEOSIZES;
$SMUGGERY_SMUGMUGVIDEOSIZES = array(
	'1' => '320',
	'2' => '640',
	'3' => '960',
	'4' => '1280',
);
global $SMUGGERY_SMUGMUGVIDEOSIZESNAMES;
$SMUGGERY_SMUGMUGVIDEOSIZESNAMES = array(
	'1' => '320 x 240',
	'2' => '640 x 480',
	'3' => '960 x 720',
	'4' => '1280 x 1024',
);


////////////////////
// Plugin options //
////////////////////
// Option keys
global $smuggery_optionnames;
$smuggery_optionnames = array (
	'smuggery_galleryTitle',
	'smuggery_smugmugNickname',
	'smuggery_imageSize',
	'smuggery_videoSize'
	);

global $smuggery_optionvals;
$smuggery_optionvals = array (
	"My Gallery",
	NULL,
	'4',
	'640'
);

//////////////
// Includes //
//////////////

// PHPSmug
require_once ('phpSmug/phpSmug.php');
// Wordpress Administrative Functions
require_once (ABSPATH . 'wp-admin/admin-functions.php');

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
function smuggery_parsecontent ($content) {
	return smuggery_substitutetags($content);
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

/////////////////////
// Content parsing //
/////////////////////
// Replace a tag with its content
function smuggery_mergeparsedtagcontent ($content, $tag, $replacement) {
	$start_pos = smuggery_findtagstart($content, $tag);
	$end_pos = smuggery_findtagend($content, $tag);
	$pre = substr($content, 0, $start_pos);
	$post = substr($content, $end_pos, strlen($content) - $end_pos);
	return $pre . $replacement . $post;		
}
// Find the starting position of first occurrance of $tag in $content. -1 on fail.
function smuggery_findtagstart($content, $tag) {
	$starting_pos = strpos($content, $tag);
	if ($starting_pos === false)
		return -1;
	else
		return $starting_pos;
}
// Find the ending position of first occurance of $tag in $content. -1 on fail.
function smuggery_findtagend($content, $tag) {
	$starting_pos = strpos($content, $tag);
	if ($starting_pos === false)
		return -1;
	else
		return $starting_pos + strlen ($tag);
}
function smuggery_substitutetags($content) {
	// Galleries
	$content = smuggery_substitutegallerytags($content);
	return $content;
}
function smuggery_substitutegallerytags($content) {
	global $id;
	global $SMUGGERY_SMUGMUGIMAGESIZESURLTOKENS;
	
	$start_pos = smuggery_findtagstart($content, SMUGGERY_TAGGALLERY);
	while ($start_pos >= 0) {
		$end_pos = smuggery_findtagend($content, SMUGGERY_TAGGALLERY);
		
		// Generate replacement
		$hash = get_post_meta($id, SMUGGERY_POSTMETAGALLERYHASH, $single=true);
		$underscorepos=strpos($hash, '_');
		$album_id = substr($hash, 0, $underscorepos);
		$album_key = substr($hash, $underscorepos + 1, strlen($hash)-strlen($album_id));
		$f = new phpSmug('APIKey=' . SMUGGERY_APIKEY, 'AppName=' . SMUGGERY_APPINFO);
		$f->login();
		$images = $f->images_get("AlbumID=$album_id", "AlbumKey=$album_key", "Heavy=1");
		$image_token = $SMUGGERY_SMUGMUGIMAGESIZESURLTOKENS[get_option('smuggery_imageSize')];
		foreach ($images as $image) {
			$rcontent .= '<a href="'.$image[$image_token].'" rel="lightbox['.$id.']"><img src="'.$image['TinyURL'].'" title="'.$image['Caption'].'" alt="'.$image['id'].'" /></a>';
	}
		
		$content = smuggery_mergeparsedtagcontent($content, SMUGGERY_TAGGALLERY, $rcontent);
		$start_pos = smuggery_findtagstart($content, SMUGGERY_TAGGALLERY);
	}
	return $content;
}

?>