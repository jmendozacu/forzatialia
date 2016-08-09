<?php
////////////////////////////////////////////////////////////////
// This file contains all functions used for the media tab    //
////////////////////////////////////////////////////////////////
function wp_simpleviewer_media_button() {
	$context = __('Add media: %s');
	$wp_simpleviewer_media_button_image = WP_SIMPLEVIEWER_URL.'/images/wp_simpleviewer_media_button.gif';
	$wp_simpleviewer_media_button = '<a href="media-upload.php?type=wp_simpleviewer&amp;TB_iframe=true" class="thickbox" '."title='".__('Add a WP-SimpleViewer gallery', $wp_simpleviewer_textdomain)."'><img src='".$wp_simpleviewer_media_button_image."' alt='".__('Add a WP-SimpleViewer gallery', $wp_simpleviewer_textdomain)."' /></a>".' %s';
	return sprintf($context, $wp_simpleviewer_media_button);
}
add_filter('media_buttons_context', 'wp_simpleviewer_media_button');

function wp_simpleviewer_add_tab( $media_tabs ) {
	$wp_simpleviewer_media_tab = array( 'wp_simpleviewer' => __('WP-SimpleViewer', $wp_simpleviewer_textdomain));
	if ( isset( $_GET['type'] ) && $_GET['type'] == 'wp_simpleviewer')
		return $wp_simpleviewer_media_tab;
	else if ( isset($_GET['type']) && $_GET['type'] == 'image' || !isset($_GET['type']) )
		return array_merge( $wp_simpleviewer_media_tab, $media_tabs );
	else
		return $media_tabs;
}
add_filter('media_upload_tabs','wp_simpleviewer_add_tab');

function media_upload_wp_simpleviewer_form() {
	global $wp_simpleviewer_textdomain;
	media_upload_header();
	$wp_simpleviewer_options['images_dir'] = get_option('wp_simpleviewer_images_dir');
	
	if ( !is_dir($wp_simpleviewer_options['images_dir']) ) {
		echo '<p>';
		_e('The following directory does not exist - please create it and upload your photos to subdirectories of it:');
		echo ' '.$wp_simpleviewer_options['images_dir'].'</p>';
	} else {	// directory exists
	?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?type=<?php echo( attribute_escape($_GET['type']) ); ?>">
		<p><?php _e('Select the gallery you want to add to your post:', $wp_simpleviewer_textdomain); ?></p>
	  	<div id="media-items">
		<?php 
		wp_nonce_field($wp_simpleviewer_nonce.'sent_to_editor');
		$directory_handle = opendir($wp_simpleviewer_options['images_dir']);
		
		while($file = readdir($directory_handle)) {	
			if ($file[0] != "." && $file[0] != ".." && !strpos($file,".") && strcmp($file, "themes") && strcmp($file, "plugins") && is_dir($wp_simpleviewer_options['images_dir'].$file.'/') ) {
				$directory_array[$file] = $file;
			}		
		}	//end while readdir(...)
		if ( !isset($directory_array) ) {
			_e('There are no directories I can use as a gallery. Please create some!', $wp_simpleviewer_textdomain);
		} else {
			sort($directory_array);					
			$directory_id = 1;
			foreach($directory_array as $directory_name) {
				if ( is_file($wp_simpleviewer_options['images_dir'].$directory_name.'/gallery.xml') ){
					echo '
					<div id="media-item-'.$directory_id.'" class="media-item">
						<div class="filename"><input type="radio" name="wp_simpleviewer_gallery" value="'.$directory_name.'"> '.$directory_name.'</div>
					</div>';
					$directory_id++;
				}
			} //end foreach directory_array
		} //end if ( !isset($directory_array) )
	  	?>
	  	</div>
		<br class="clear" />
		<div id="media-items">
		<div class="media-item media-blank">
		<table class="describe"><tbody>
			<tr><td>
			<input type="radio"  name="wp_simpleviewer_shortcode_type" value="gallery" checked="checked" /><?php _e("Show selected gallery", $wp_simpleviewer_textdomain); ?><br />
	    	<input type="radio"  name="wp_simpleviewer_shortcode_type" value="link" /><?php _e("Show only a link to the selected gallery with this linktext:", $wp_simpleviewer_textdomain); ?> <br />
			<input type="text"   name="wp_simpleviewer_link_text" />
	  		</td></tr>
		</table>
		</div>
		</div>
		<br class="clear" />
		<input type="submit" name="wp_simpleviewer_submit" class="button savebutton" value="<?php _e("Insert into post"); ?>" />
	</form>
	</body></html>
	<?php
	}	//end if ( !is_dir($wp_simpleviewer_options['images_dir']) )
}

function media_upload_wp_simpleviewer() {
	//should we sent something to the editor? 
	if ( isset($_POST['wp_simpleviewer_submit']) ) {
		check_admin_referer($wp_simpleviewer_nonce.'sent_to_editor');
		$wp_simpleviewer_gallery = attribute_escape( $_POST["wp_simpleviewer_gallery"] );
		$wp_simpleviewer_shortcode_type = attribute_escape( $_POST["wp_simpleviewer_shortcode_type"] );
		if ( $wp_simpleviewer_shortcode_type == 'gallery' ) {
			return media_send_to_editor('[svgallery name="'.$wp_simpleviewer_gallery.'"]');
		} elseif ( $wp_simpleviewer_shortcode_type == 'link' ) {
			$wp_simpleviewer_link_text = attribute_escape( $_POST["wp_simpleviewer_link_text"] );
			return media_send_to_editor('[svgallery name="'.$wp_simpleviewer_gallery.'" link="'.$wp_simpleviewer_link_text.'"]');
		}
	} else {
		return wp_iframe( 'media_upload_wp_simpleviewer_form' );
	}
}
add_action('media_upload_wp_simpleviewer', 'media_upload_wp_simpleviewer');

function wp_simpleviewer_default_tab( $default_tab ){
	if ( isset( $_GET['type'] )&& $_GET['type'] == 'wp_simpleviewer' )
		return 'wp_simpleviewer';
	else
		return $default_tab;
}
add_filter('media_upload_default_tab', 'wp_simpleviewer_default_tab');
?>