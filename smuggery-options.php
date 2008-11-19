<?php
/*
file:	smuggery-options.php

desc:	Options page for Smuggery Wordpress Plugin	
*/
?>
<?php
// SmugMug API
$f = new phpSmug('APIKey=' . SMUGGERY_APIKEY, 'AppName=' . SMUGGERY_APPINFO);

// Validate nickname
if (isset($_POST ['clearnickname'])) {
	update_option('smuggery_smugmugNickname', NULL);
}
// Clear nickname
if (isset($_POST['validatenickname'])) {
	update_option ('smuggery_smugmugNickname', htmlspecialchars ( $_POST [ 'smuggery_smugmugNickname' ], ENT_QUOTES ) );
}
// If the form was just submitted (options updated), ...
if (isset($_POST ['updatedisplayoptions'])) {
	update_option ('smuggery_galleryTitle', htmlspecialchars ( $_POST [ 'smuggery_galleryTitle' ], ENT_QUOTES ) );
	update_option ('smuggery_imageSize', htmlspecialchars ( $_POST [ 'smuggery_imageSize' ], ENT_QUOTES ) );
	update_option ('smuggery_videoSize', htmlspecialchars ( $_POST [ 'smuggery_videoSize' ], ENT_QUOTES ) );
?>
	<div id="message" class="updated fade">
		<p>
			<strong>
				Options saved!
			</strong>
		</p>
	</div>
<?php
}
?>

<?php
// First thing's first, we need to find out if we have a valid SmugMug username to create our options later on this page.
$validatedNickname = false;
try {
	$f->login();
	$albums = $f->albums_get('NickName=' . get_option('smuggery_smugmugNickname'));
	$validatedNickname = true;
}
catch (Exception $e) {
	$validatedNickname = false;
	//echo "{$e->getMessage()} (Error Code: {$e->getCode()})";
} 
?>

<div class="wrap">
<form action="<?php echo $_SERVER ['REQUEST_URI']; ?>" method="post">

<h2>
	Smuggery Options
</h2>

<h3>
	Display Options
</h3>

<p>
	Title of gallery:
	<input type="text" name="smuggery_galleryTitle" size="50" value="<?php echo stripslashes ( get_option ( 'smuggery_galleryTitle' ) ); ?>" />
</p>
<p>
	Target Image Size:
	<select name=smuggery_imageSize>
	<?php
	global $SMUGGERY_SMUGMUGIMAGESIZES;
	global $SMUGGERY_SMUGMUGIMAGESIZESNAMES;
	for ( $thisSize = 1; $thisSize <= count($SMUGGERY_SMUGMUGIMAGESIZES); $thisSize ++ ) {
		echo '<option value="';
		echo $thisSize;
		// Do we select it?
		if ( $thisSize == get_option ( 'smuggery_imageSize' ) ) {
			echo '" selected="selected';
		}
		echo '">';
		echo $SMUGGERY_SMUGMUGIMAGESIZESNAMES[$thisSize];
		echo '</option>';
	}
	echo count($SMUGGERY_SMUGMUGIMAGESIZES);
	?>
	</select>
<p>
<p>
	Target Video Size:
	<select name=smuggery_videoSize>
	<?php
	global $SMUGGERY_SMUGMUGVIDEOSIZES;
	global $SMUGGERY_SMUGMUGVIDEOSIZESNAMES;
	for ( $thisSize = 1; $thisSize <= count($SMUGGERY_SMUGMUGVIDEOSIZES); $thisSize ++ ) {
		echo '<option value="';
		echo $thisSize;
		// Do we select it?
		if ( $thisSize == get_option ( 'smuggery_videoSize' ) ) {
			echo '" selected="selected';
		}
		echo '">';
		echo $SMUGGERY_SMUGMUGVIDEOSIZESNAMES[$thisSize];
		echo '</option>';
	}
	echo count($SMUGGERY_SMUGMUGVIDEOSIZES);
	?>
	</select>
</p>
<p>
	<input type="submit" name="updatedisplayoptions" value="Update Display Options" />
</p>

<h3>
	SmugMug Account Details
</h3>
<?php if (!$validatedNickname) { ?>
<p>
	SmugMug was unable to validate your nickname. You must supply a valid nickname to use this plugin.
	<br />
	SmugMug Nickname: 
	<input type="text" name="smuggery_smugmugNickname" value="<?php echo stripslashes ( get_option ( 'smuggery_smugmugNickname' ) ) ?>" />
	<input type="submit" name="validatenickname" value = "Use Nickname" />
</p>
<?php } else { ?>
<p>
	<?php
	$numAlbums = count($albums);
	echo "Getting info on $numAlbums albums...";
	/*
	$images = $f->images_get("AlbumID={$albums['0']['id']}", "AlbumKey={$albums['0']['Key']}", "Heavy=1");
	$images = ($f->APIVer == "1.2.2") ? $images['Images'] : $images;
	// Display the thumbnails and link to the medium image for each image
	foreach ($images as $image) {
		echo '<a href="'.$image['MediumURL'].'"><img src="'.$image['TinyURL'].'" title="'.$image['Caption'].'" alt="'.$image['id'].'" /></a>';
	}
	*/
	?>
	<br />
	<input type="submit" name="clearnickname" value = "Clear Nickname" />
</p>
<?php } ?>

<!-- End of Options Page -->
	</form>
</div>
<!-- End of End of Options Page -->
