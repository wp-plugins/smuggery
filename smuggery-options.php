<?php
/*
file:	smuggery-options.php

desc:	Options page for Smuggery Wordpress Plugin	
*/
?>
<?php
// If the form was just submitted (options updated), ...
if ( isset ( $_POST [ 'submitted' ] ) ) {
	update_option ( 'smuggery_galleryTitle', htmlspecialchars ( $_POST [ 'smuggery_galleryTitle' ], ENT_QUOTES ) );
	update_option ( 'smuggery_smugmugUsername', htmlspecialchars ( $_POST [ 'smuggery_smugmugUsername' ], ENT_QUOTES ) );
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
	<br />
	<input type="text" name="smuggery_galleryTitle" size="100" value="<?php echo stripslashes ( get_option ( 'smuggery_galleryTitle' ) ); ?>" />
</p>

<h3>
	SmugMug Account Details
</h3>
<p>
	SmugMug Username: 
	<input type="text" name="smuggery_smugmugUsername" maxlength="100" size="100" value="<?php echo stripslashes ( get_option ( 'smuggery_smugmugUsername' ) ) ?>" />
</p>

<input type="submit" name="submitted" value="Update Options &raquo;" />

<!-- End of Options Page -->
	</form>
</div>
<!-- End of End of Options Page -->
