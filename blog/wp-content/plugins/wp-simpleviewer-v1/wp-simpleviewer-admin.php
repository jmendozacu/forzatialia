<?php
////////////////////////////////////////////////////////////////
// This file contains all functions used in the admin backend //
////////////////////////////////////////////////////////////////
function wp_simpleviewer_get_form_option() {
	global $wp_simpleviewer_textdomain;
	if ( isset($_POST['wp_simpleviewer_form_option']) ) {
		$wp_simpleviewer_form_option = attribute_escape($_POST['wp_simpleviewer_form_option']);
	} elseif ( isset($_GET['wp_simpleviewer_form_option']) ) {
		$wp_simpleviewer_form_option = attribute_escape($_GET['wp_simpleviewer_form_option']);
	} else {
		$wp_simpleviewer_form_option = NULL;
	}
	if ( $wp_simpleviewer_form_option == __('Create gallery files for this folder', $wp_simpleviewer_textdomain) ) {
		$wp_simpleviewer_form_option = "create";
	} elseif ( $wp_simpleviewer_form_option == __('Edit captions and settings for this gallery', $wp_simpleviewer_textdomain) ) {
		$wp_simpleviewer_form_option = "writenewxml";
	} elseif ( $wp_simpleviewer_form_option == __('Back to the gallery list without editing the captions', $wp_simpleviewer_textdomain) ) {
		$wp_simpleviewer_form_option = NULL;
	} elseif ( $wp_simpleviewer_form_option == __('Save Changes') ) {
		$wp_simpleviewer_form_option = "update";
	} elseif ( $wp_simpleviewer_form_option == __('Reset WP-SimpleViewer plugin', $wp_simpleviewer_textdomain) ) {
		$wp_simpleviewer_form_option = "reset";
	}
	return $wp_simpleviewer_form_option;
} //end function wp_simpleviewer_get_form_option

function wp_simpleviewer_manage_page() {
	global $wp_simpleviewer_textdomain;
	global $wp_simpleviewer_nonce;
	
	if (!get_option('wp_simpleviewer_version_installed')) {	//The plugin has not been installed yet, ask admin to install it
		$plugin_file = attribute_escape($_GET['page']);
		echo '</a> <a href="options-general.php?page='.$plugin_file.'">';
		?>
		<div id="message" class="updated fade"><p><a href="options-general.php?page=<?php echo $plugin_file; ?>"><?php _e('The WP-SimpleViewer plugin has not been configured yet. Click here to configure it on the plugin\'s option page.', $wp_simpleviewer_textdomain); ?></a></p></div>
		<?php
	} else {
		$wp_simpleviewer_options = wp_simpleviewer_get_options();
		$wp_simpleviewer_form_option = wp_simpleviewer_get_form_option();
		
		// Check if a directory has been selected to create a new gallery
		if ( $wp_simpleviewer_form_option == 'create' && !isset($_POST['wp_simpleviewer_gallery']) ) {
			echo '<div id="message" class="updated fade"><p><strong>';
			_e('You have not selected a folder that you want to use as a gallery!', $wp_simpleviewer_textdomain);
			echo '</strong></p></div>';
		} elseif ( $wp_simpleviewer_form_option == 'create' ) {
			check_admin_referer($wp_simpleviewer_nonce.'create');
			
			$wp_simpleviewer_gallery = attribute_escape($_POST['wp_simpleviewer_gallery']);
			$wp_simpleviewer_gallery_dir = $wp_simpleviewer_options['images_dir'].$wp_simpleviewer_gallery.'/';
			$wp_simpleviewer_gallery_dir_regular = $wp_simpleviewer_gallery_dir.$wp_simpleviewer_options['folder_regular'];
			$wp_simpleviewer_gallery_dir_thumbnails = $wp_simpleviewer_gallery_dir.$wp_simpleviewer_options['folder_thumbnails'];
			$wp_simpleviewer_gallery_url_regular = $wp_simpleviewer_options['images_url'].$wp_simpleviewer_gallery.'/'.$wp_simpleviewer_options['folder_regular'].'/';
			$wp_simpleviewer_gallery_url_thumbnails = $wp_simpleviewer_options['images_url'].$wp_simpleviewer_gallery.'/'.$wp_simpleviewer_options['folder_thumbnails'].'/';
			$wp_simpleviewer_xmlfile_dir = $wp_simpleviewer_gallery_dir.'gallery.xml';
			
			$wp_simpleviewer_gallery_array = wp_simpleviewer_gallery_array_from_options( $wp_simpleviewer_options, $wp_simpleviewer_gallery_url_regular, $wp_simpleviewer_gallery_url_thumbnails );
			
			cleanup_old_files($wp_simpleviewer_xmlfile_dir, $wp_simpleviewer_gallery_dir_regular, $wp_simpleviewer_gallery_dir_thumbnails);
			
			if ( mkdir ($wp_simpleviewer_gallery_dir_regular, 0777) && mkdir ($wp_simpleviewer_gallery_dir_thumbnails, 0777) ) {	//create new directories
				chmod ($wp_simpleviewer_gallery_dir_regular, 0777);
				chmod ($wp_simpleviewer_gallery_dir_thumbnails, 0777);
				
				//start update box for WordPress
				echo '<div id="message" class="updated fade"><p>';
				//show GD imaging library version
				$gdInfo    = gd_info(); 
				_e('GD library version 2 or later is required. You are running version:', $wp_simpleviewer_textdomain);
				echo ' '.$gdInfo["GD Version"].'<br /><br />';
				if( ini_get('safe_mode') ){
					_e('Safe_mode is enabled in PHP. You will most probably run into problems with the gallery creation.', $wp_simpleviewer_textdomain);
					echo '<br /><br />';
				}
				_e('Now the images and XML file for the selected folder will be created:', $wp_simpleviewer_textdomain);
				echo '<br /><br />'; 
				
				//Open directory and add all image filenames to an array sorted by name
				$directory_handle = opendir($wp_simpleviewer_gallery_dir);
				$files_array = array();
				while($file = readdir($directory_handle)) {	
					if ($file[0] != "." && $file[0] != ".." && !is_dir($wp_simpleviewer_gallery_dir.$file) ) {
						if ( getExtension($file) == "jpg" || getExtension($file) == "png" || getExtension($file) == "gif" ){
							array_push($files_array, array('name' => $file, 'date' => filemtime($wp_simpleviewer_gallery_dir.'/'.$file)));
						} else {
							echo $file.': ';
							_e('This file will not be included in the gallery - it is no jpg, png or gif file'); 
							echo '<br />';
						}
					}		
				}	//end while readdir(...)
				
				if ( !count($files_array) ) {
					_e('The directory you selected does not contain any jpg, png or gif files. Please upload them first (and ignore the rest of the page)!', $wp_simpleviewer_textdomain);
					echo '<br /><br />';
				} else {
					usort($files_array, "wp_simpleviewer_image_sort");
					/*If the plugin should hang up while it is creating the thumbs (last line on the admin is: "Now I am working on file xzy.jpg:") you
					can try to uncomment the following line (remove // ) to define a new memory limit (40MB in this case). More info on the plugins FAQ.*/
					//ini_set("memory_limit","40M");
					foreach($files_array as $key => $file_data_array) {
						//the script should not work on one image longer than 30s 
						@set_time_limit(30);
						
						_e('Now I am working on file', $wp_simpleviewer_textdomain);
						echo ' '.$file_data_array['name'].': <br />';
						$filename_original  = $wp_simpleviewer_gallery_dir.$file_data_array['name'];
						$filename_regular   = $wp_simpleviewer_gallery_dir_regular."/".$file_data_array['name'];
						$filename_thumbnail = $wp_simpleviewer_gallery_dir_thumbnails."/".$file_data_array['name'];
						
						//create regular image and thumbnail
						if (!file_exists($filename_regular) && !file_exists($filename_thumbnail) ){					
							if (create_resized_image($filename_original, $filename_regular, false, $wp_simpleviewer_options['max_image_size'], $wp_simpleviewer_options['resizequality'])){
								chmod($filename_regular,0777);
								_e('I have created the regular image', $wp_simpleviewer_textdomain);
								echo ' ';
								if (create_resized_image($filename_original, $filename_thumbnail, true, 65, $wp_simpleviewer_options['resizequality'])){	//thumbnails are suared with 65px width/height
									chmod($filename_thumbnail,0777);
									_e('and the thumbnail', $wp_simpleviewer_textdomain);
									echo ' ';
									//and add the image to the gallery array
									$wp_simpleviewer_new_image = array( "_c" =>  array( 
										"filename" => array( "_v" => $file_data_array['name'] ),
										"caption" => array( "_v" => $wp_simpleviewer_options['default_download_link_text'] )
									));
									if ( !strcmp($wp_simpleviewer_options['default_download_link_text'], __('Filenames should be used for the captions', $wp_simpleviewer_textdomain)) ){
										$wp_simpleviewer_new_image["_c"]["caption"]["_v"] = substr($file_data_array['name'], 0, strpos($file_data_array['name'], '.'));
									}
									wp_simpleviewer_ins2ary( $wp_simpleviewer_gallery_array["simpleviewerGallery"]["_c"]["image"], $wp_simpleviewer_new_image, $key );
									
									_e('and now it has been added to the XML file!', $wp_simpleviewer_textdomain);
									echo '<br />';
								}			
							}									
						}
						
					}	//end for each
				}	//end if isset files_array
				closedir($directory_handle);
				wp_simpleviewer_save_gallery($wp_simpleviewer_gallery_array, $wp_simpleviewer_options, $wp_simpleviewer_gallery, $wp_simpleviewer_xmlfile_dir);

				echo '</p></div>'; //end update box for WordPress (fading div box)
			} else {
				echo '<div id="message" class="updated fade"><p>';
				_e('Sorry, but I could not create the subfolders for the thumbnails.', $wp_simpleviewer_textdomain);
				echo '</p></div>';
			}
		}
		
		if ( $wp_simpleviewer_form_option == 'editxml' || ( $wp_simpleviewer_form_option == 'create' && isset($_POST['wp_simpleviewer_gallery']) ) ) {	//show xml file's content to edit it
			if ($wp_simpleviewer_form_option == 'editxml') {
				check_admin_referer($wp_simpleviewer_nonce.'editxml');
				$wp_simpleviewer_gallery = attribute_escape($_GET['wp_simpleviewer_gallery']);
			} else {
				check_admin_referer($wp_simpleviewer_nonce.'create');
				$wp_simpleviewer_gallery = attribute_escape($_POST['wp_simpleviewer_gallery']);
			}
			
			?>
			<div class="wrap">
			<h2>
			<?php _e('Edit captions for gallery:', $wp_simpleviewer_textdomain); echo ' '.$wp_simpleviewer_gallery; ?>
			</h2>
			<?php
			$wp_simpleviewer_gallery_dir = $wp_simpleviewer_options['images_dir'].$wp_simpleviewer_gallery.'/';
			$wp_simpleviewer_xmlfile_dir = $wp_simpleviewer_gallery_dir.'gallery.xml';
			//check if xml file exists and get variables
			if ( !is_readable($wp_simpleviewer_xmlfile_dir) ) {
				_e('I cannot find the gallery\'s xml file, please check that the gallery\'s files have been created on the admin pages:', $wp_simpleviewer_textdomain);
				echo ' '.$wp_simpleviewer_xmlfile_dir.'<br />';
			} else {
				$wp_simpleviewer_gallery_array = wp_simpleviewer_gallery_from_xml($wp_simpleviewer_xmlfile_dir);
				$wp_simpleviewer_options = wp_simpleviewer_merge_options_and_array($wp_simpleviewer_options, $wp_simpleviewer_gallery_array);
				$wp_simpleviewer_gallery_url_regular = $wp_simpleviewer_gallery_array["simpleviewerGallery"]["_a"]["imagePath"];
				$wp_simpleviewer_gallery_url_thumbnails = $wp_simpleviewer_gallery_array["simpleviewerGallery"]["_a"]["thumbPath"];

				if ( count($wp_simpleviewer_gallery_array["simpleviewerGallery"]["_c"]["image"]) == 0) {	//no images found in XML file
					_e('No images found in the xml file. Please check that there are image files in the directory!', $wp_simpleviewer_textdomain);
					echo '<br />';
				} else { //there are images in the xml file, show the form to edit them
					?>
					<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo( attribute_escape($_GET['page']) ); ?>">
					<?php wp_nonce_field($wp_simpleviewer_nonce.'writenewxml'); ?>
					<div class="tablenav">
						<div class="alignleft">
							<input type="submit" class="button-secondary" value="<?php _e('Edit captions and settings for this gallery', $wp_simpleviewer_textdomain) ?>" />
							<br class="clear" />
						</div>
					</div>
					<br class="clear" />
					
					<table id="sortable_table" class="widefat"> 
						<thead>
						<tr>
							<th scope="col" style="text-align: center"><?php //_e('Image', $wp_simpleviewer_textdomain) ?></th>
							<th scope="col"><?php _e('Name', $wp_simpleviewer_textdomain) ?></th>
							<th scope="col"><?php _e('Date', $wp_simpleviewer_textdomain) ?></th>
							<th scope="col"><?php _e('Caption', $wp_simpleviewer_textdomain) ?></th>
							<th scope="col"><?php _e('Order', $wp_simpleviewer_textdomain) ?></th>
						</tr>
						</thead>
						<tbody>
							<?php 
							foreach ( $wp_simpleviewer_gallery_array["simpleviewerGallery"]["_c"]["image"] as $key => $wp_simpleviewer_image ) {
								/*if ( $key%2 ) {
									echo '<tr id="directory-'.$key.'" class="alternate" >'."\n";
								} else {
									echo '<tr id="directory-'.$key.'" >'."\n";
								}*/
								if ( $key%2 ) {
									echo '<tr id="sortable_row_'.$key.'" class="sortable_row alternate" >'."\n";
								} else {
									echo '<tr id="sortable_row_'.$key.'" class="sortable_row">'."\n";
								}
								echo '<td><img src="'.$wp_simpleviewer_gallery_url_thumbnails.$wp_simpleviewer_image["_c"]["filename"]["_v"].'" alt="'.$wp_simpleviewer_image["_c"]["filename"]["_v"].'" /></td>'."\n";
								echo '<td><input name="wp_simpleviewer_images['.$key.'][filename]" type="hidden" id="wp_simpleviewer_images['.$key.'][filename]" value="'.$wp_simpleviewer_image["_c"]["filename"]["_v"].'" />'.$wp_simpleviewer_image["_c"]["filename"]["_v"].'</td>'."\n";
								echo '<td>'.date(__("Y/m/d"), filemtime($wp_simpleviewer_gallery_dir.$wp_simpleviewer_image["_c"]["filename"]["_v"])).'</td>'."\n";
								echo '<td><input name="wp_simpleviewer_images['.$key.'][caption]"  type="text"   id="wp_simpleviewer_images['.$key.'][caption]"  value="'.$wp_simpleviewer_image["_c"]["caption"]["_v"].'" size="50" /> </td>'."\n";
								echo '<td><input name="wp_simpleviewer_images['.$key.'][order]"    type="text"   id="wp_simpleviewer_images['.$key.'][order]"    value="'.($key+1).'" size="2" class="wp_simpleviewer_order" /> </td>'."\n";
								echo '</tr>'."\n";
							}	//end foreach 
							?>
						</tbody>
					</table>
					<div class="tablenav">
						<input type="submit" class="button-secondary" value="<?php _e('Edit captions and settings for this gallery', $wp_simpleviewer_textdomain) ?>" />
						<br class="clear" />
					</div>
					<br class="clear" />
					
					<?php wp_simpleviewer_settings_form( $wp_simpleviewer_options, 'editxml' ); ?>
					<p class="submit">
						<input name="wp_simpleviewer_gallery"        			type="hidden" id="wp_simpleviewer_gallery"        			value="<?php echo $wp_simpleviewer_gallery; ?>" />
						<input name="wp_simpleviewer_max_image_size" 			type="hidden" id="wp_simpleviewer_max_image_size" 			value="<?php echo $wp_simpleviewer_options['max_image_size']; ?>" />
						<input name="wp_simpleviewer_gallery_url_regular"      	type="hidden" id="wp_simpleviewer_gallery_url_regular"      value="<?php echo $wp_simpleviewer_gallery_url_regular; ?>" />
						<input name="wp_simpleviewer_gallery_url_thumbnails"	type="hidden" id="wp_simpleviewer_gallery_url_thumbnails"	value="<?php echo $wp_simpleviewer_gallery_url_thumbnails; ?>" />
						<input name="wp_simpleviewer_form_option" 				type="hidden" id="wp_simpleviewer_form_option"				value="writenewxml" />
						<input 									 				type="submit"												value="<?php _e('Edit captions and settings for this gallery', $wp_simpleviewer_textdomain) ?>" />
					</p>
					</form>
					
					<?php					
				}	//end if count > 0
			}	//end if file is_readable
			?>
			<div class="tool-box">
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo( attribute_escape($_GET['page']) ); ?>">&lt;&lt; <?php _e('Back to the gallery list without editing the captions', $wp_simpleviewer_textdomain) ?></a>
			</div>
			<?php
		} elseif ( $wp_simpleviewer_form_option == 'writenewxml' ) {	//write new xml file
			check_admin_referer($wp_simpleviewer_nonce.'writenewxml');
			$wp_simpleviewer_gallery = attribute_escape($_POST['wp_simpleviewer_gallery']);
			$wp_simpleviewer_gallery_dir = $wp_simpleviewer_options['images_dir'].$wp_simpleviewer_gallery.'/';
			$wp_simpleviewer_xmlfile_dir = $wp_simpleviewer_gallery_dir.'gallery.xml';
			$wp_simpleviewer_gallery_url_regular = attribute_escape( $_POST['wp_simpleviewer_gallery_url_regular'] );
			$wp_simpleviewer_gallery_url_thumbnails = attribute_escape( $_POST['wp_simpleviewer_gallery_url_thumbnails'] );
			
			$wp_simpleviewer_gallery_array = wp_simpleviewer_gallery_array_from_options( $wp_simpleviewer_options, $wp_simpleviewer_gallery_url_regular, $wp_simpleviewer_gallery_url_thumbnails );
			
			foreach ( $_POST[wp_simpleviewer_images] as $key => $wp_simpleviewer_image ) {
				$wp_simpleviewer_image['caption'] = wp_specialchars( stripslashes($wp_simpleviewer_image['caption']), 1);
				$wp_simpleviewer_new_image = array( "_c" =>  array( 
					"filename" => array( "_v" => attribute_escape( $wp_simpleviewer_image['filename']) ),
					"caption" => array( "_v" => attribute_escape( $wp_simpleviewer_image['caption'] ) )
				));
				wp_simpleviewer_ins2ary( $wp_simpleviewer_gallery_array["simpleviewerGallery"]["_c"]["image"], $wp_simpleviewer_new_image, $wp_simpleviewer_image['order']-1 );
			}	//end foreach
			
			echo '<div id="message" class="updated fade"><p>';
				_e('Now the XML file will be recreated...', $wp_simpleviewer_textdomain); 
				wp_simpleviewer_save_gallery($wp_simpleviewer_gallery_array, $wp_simpleviewer_options, $wp_simpleviewer_gallery, $wp_simpleviewer_xmlfile_dir);
			echo '</p></div>'; //end update box for WordPress (fading div box)
		} elseif ( $wp_simpleviewer_form_option == 'addimages' ) {	//show xml file's content to edit it
			check_admin_referer($wp_simpleviewer_nonce.'addimages');
			_e('This function is not implemented yet!', $wp_simpleviewer_textdomain);
		}	//end if option == create ||editxml || addimages
		
		if ( !isset($wp_simpleviewer_form_option) || $wp_simpleviewer_form_option == 'writenewxml' || ( $wp_simpleviewer_form_option == 'create' && !isset($_POST['wp_simpleviewer_gallery']) ) ) {	//show next part only on startpage or after writing new xml file
			?>
			<div class="wrap">
			<?php
			if ($wp_simpleviewer_options['howto']=="on") {
				?>
				<h2><?php _e('How to use the WP-SimpleViewer plugin', $wp_simpleviewer_textdomain) ?></h2> 
				<?php 
				//Show usage text:
				_e('Use an FTP tool to upload your images to a subdirectory to:', $wp_simpleviewer_textdomain);
				echo '<br />'.$wp_simpleviewer_options['images_dir'].' <br />';
				_e('Make the folder writeable for everyone (chmod 777) and then select it in the list below to generate the thumbnails and the XML file. More information about this is available on the plugin\'s homepage:', $wp_simpleviewer_textdomain);
				echo ' <a href="http://wp-simpleviewer.fuggi82.de" target="_blank">';
				_e('Click here', $wp_simpleviewer_textdomain);
				echo '</a><br /><br />';
				_e('To show the SimpleViewer gallery on a post or a page just include the foldername in its content like this, you can also use the quicktag button in the code view of the editor for that:', $wp_simpleviewer_textdomain);
				echo '<br /><br />Lorem ipsum dolor sit ame: <br />[svgallery name="';
				_e('your_foldername', $wp_simpleviewer_textdomain);
				echo'"]<br />Consectetuer adipiscing elit.<br /><br />'; ?>
			<?php
			}	//end if ($wp_simpleviewer_options['howto']=="on")
			?>
			<h2><?php _e('Manage WP-SimpleViewer galleries', $wp_simpleviewer_textdomain); ?></h2> 
			<?php 
			if ( !is_dir($wp_simpleviewer_options['images_dir']) ) {
				echo '<p>';
				_e('The following directory does not exist - please create it and upload your images to subdirectories of it:');
				echo ' '.$wp_simpleviewer_options['images_dir'].'</p>';
			} else {	// directory exists
				$directory_handle = opendir($wp_simpleviewer_options['images_dir']);
				$directory_array = array();
				while($file = readdir($directory_handle)) {	
					if ($file[0] != "." && $file[0] != ".." && !strpos($file,".") && strcmp($file, "themes") && strcmp($file, "plugins") && is_dir($wp_simpleviewer_options['images_dir'].$file.'/') ) {
						array_push($directory_array, array('name' => $file, 'date' => filemtime($wp_simpleviewer_options['images_dir'].$file.'/')));
					}		
				}
				closedir($directory_handle);
				
				if ( !count($directory_array) ) {
					_e('There are no directories I can use as a gallery. Please create some!', $wp_simpleviewer_textdomain);
				} else {
					usort($directory_array, "wp_simpleviewer_directory_sort");	
					?>
					<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo( attribute_escape($_GET['page']) ); ?>">
					<?php wp_nonce_field($wp_simpleviewer_nonce.'create'); ?>
					<div class="tablenav">
							<?php 
							if ( !isset( $_GET['paged'] ) )
								$_GET['paged'] = 1;
							$page_links = paginate_links( array(
								'base' => add_query_arg( 'paged', '%#%' ),
								'format' => '',
								'total' => ceil( count($directory_array)/10),
								'current' => $_GET['paged']
							));
							
							if ( $page_links )
								echo "<div class='tablenav-pages'>$page_links</div>";
							?>
						<input type="submit" class="button-secondary" value="<?php _e('Create gallery files for this folder', $wp_simpleviewer_textdomain) ?>" />
						<br class="clear" />
					</div>
					<br class="clear" />
					<table class="widefat"> 
						<thead>
						<tr>
							<th scope="col" style="text-align: center"><?php //_e('Select Directory', $wp_simpleviewer_textdomain) ?></th>
							<th scope="col">
								<a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo( attribute_escape($_GET['page']) ); ?>&wp_simpleviewer_gallerysort=<?php if ($wp_simpleviewer_options['gallerysort']=="nameascending") echo "namedecending"; else echo "nameascending"; ?>">
									<?php _e('Directory', $wp_simpleviewer_textdomain) ?>
								</a>
							</th>
							<th scope="col">
								<a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo( attribute_escape($_GET['page']) ); ?>&wp_simpleviewer_gallerysort=<?php if ($wp_simpleviewer_options['gallerysort']=="datedescending") echo "dateascending"; else echo "datedescending"; ?>">
									<?php _e('Date', $wp_simpleviewer_textdomain) ?>
								</a>
							</th>
							<th scope="col"><?php _e('Status', $wp_simpleviewer_textdomain) ?></th>
							<th scope="col" style="text-align: center" colspan="1"><?php _e('Action', $wp_simpleviewer_textdomain); ?></th>
						</tr>
						</thead>
						
						<tbody>
							<?php
							$directory_id=($_GET['paged']-1)*10;
							while( $directory_id < $_GET['paged']*10 && $directory_id < count($directory_array) ) {
								if ( $directory_id%2 ) {
									echo '<tr id="directory-'.$directory_id.'" class="alternate" >';
								} else {
									echo '<tr id="directory-'.$directory_id.'" >';
								}
								$directory_name = $directory_array[$directory_id]['name'];
								$directory_date = date(__('Y/m/d'), $directory_array[$directory_id]['date']);
								if ( is_writable($wp_simpleviewer_options['images_dir'].$directory_name.'/') AND (!is_file($wp_simpleviewer_options['images_dir'].$directory_name.'/gallery.xml')) OR (current_user_can('edit_others_posts'))   )
									echo '<td> <input type="radio" name="wp_simpleviewer_gallery" id="directory_radio_'.$directory_name.'" value="'.$directory_name.'" /> </td>';
								else {	//directory not writable
									echo '<td> </td>';
								}
								
								echo '<td>'.$directory_name.'</td>';
								echo '<td>'.$directory_date.'</td>';
								//date(get_option('date_format'), filemtime($wp_simpleviewer_options['images_dir'].$file.'/') ).date(get_option('time_format'), filemtime($wp_simpleviewer_options['images_dir'].$file.'/') ) ));
								
								$plugin_file = attribute_escape($_GET['page']); 
								$page_name = basename($_SERVER['PHP_SELF']);
								$editxml_url = $page_name.'?page='.$plugin_file.'&wp_simpleviewer_gallery='.$directory_name.'&wp_simpleviewer_form_option=editxml';
								$addimages_url = $page_name.'?page='.$plugin_file.'&wp_simpleviewer_gallery='.$directory_name.'&wp_simpleviewer_form_option=addimages';
								
								$editxml_url = wp_nonce_url($editxml_url, $wp_simpleviewer_nonce.'editxml');
								$addimages_url = wp_nonce_url($addimages_url, $wp_simpleviewer_nonce.'addimages');
								
								if ( !is_writable($wp_simpleviewer_options['images_dir'].$directory_name.'/') ) {	//Directory not writable
									echo '<td>';
									_e('This directory is not writable. You have to make it writable (chmod 777) if you want to use it for a gallery.', $wp_simpleviewer_textdomain);
									echo '</td>';
									echo '<td>&nbsp;</td>';
								} elseif ( is_file($wp_simpleviewer_options['images_dir'].$directory_name.'/gallery.xml') && is_writable($wp_simpleviewer_options['images_dir'].$directory_name.'/gallery.xml') ) { //directory is writable and xmlfile exists
									if ( current_user_can('edit_others_posts') ) {
										echo '<td>';
										_e('XML file found, you can recreate or edit the gallery if you like', $wp_simpleviewer_textdomain);
										echo '</td>';
										echo '<td><a href="'.$editxml_url.'">';
										_e('Edit captions and settings', $wp_simpleviewer_textdomain);
										echo '</a></td>';
									} else {
										echo '<td>';
										_e('XML file found but you do not have the permission to recreate or edit existing galleries', $wp_simpleviewer_textdomain);
										echo '</td>';
										echo '<td>&nbsp;</td>';
									}
								} elseif ( is_file($wp_simpleviewer_options['images_dir'].$directory_name.'/gallery.xml') && !is_writable($wp_simpleviewer_options['images_dir'].$directory_name.'/gallery.xml') ) { //xmlfile exists but not writable
									echo '<td>';
									_e('XML file found, however it is not writable. You have to make it writable (chmod 777) if you want to recreate or edit the gallery', $wp_simpleviewer_textdomain);
									echo '</td>';
									echo '<td>&nbsp;</td>';
								} else {	//directory is writable and no xmlfile exists
									echo '<td>';
									_e('You can create gallery files for this folder if you like', $wp_simpleviewer_textdomain);
									echo '</td>';
									echo '<td>&nbsp;</td>';
								}
								//echo '<td><a href="'.$addimages_url.'">Add images</a></td>';
								echo '</tr>';
								$directory_id++;
							} //end while $directory_id
							?>
						</tbody>
					</table>
					<div class="tablenav">
						<?php if ( $page_links )
							echo "<div class='tablenav-pages'>$page_links</div>";
						?>
						<input type="submit" class="button-secondary" value="<?php _e('Create gallery files for this folder', $wp_simpleviewer_textdomain) ?>" />
						<br class="clear" />
					</div>
					<br class="clear" />
					<?php wp_simpleviewer_settings_form( $wp_simpleviewer_options, 'create' ); ?>
					<p class="submit">
						<input name="wp_simpleviewer_form_option" type="hidden" id="wp_simpleviewer_form_option" value="create" />
						<input type="submit" class="button-secondary" value="<?php _e('Create gallery files for this folder', $wp_simpleviewer_textdomain) ?>" />
					</p>
					</form> 
					<?php
				}	//end if !isset($directory_array)
			}	//end directory exists
		}	//end if plugin is not installed
		?>
		</div> 
		<?php
	}	//end if !isset option ...
}

function wp_simpleviewer_options_page() {
	global $wp_simpleviewer_textdomain;
	global $wp_simpleviewer_version;
	global $wp_simpleviewer_nonce;
	
	if (!get_option('wp_simpleviewer_version_installed')) {	//The plugin has not been installed yet, default parameters will be added to the DB
		$wp_simpleviewer_options = wp_simpleviewer_add_options();
	} else {
		$wp_simpleviewer_options = wp_simpleviewer_get_options();
	}
	$wp_simpleviewer_form_option = wp_simpleviewer_get_form_option();
		
	if ($wp_simpleviewer_form_option == 'update') {	// Check if form data has been sent to update settings
		check_admin_referer($wp_simpleviewer_nonce.'settings');
		wp_simpleviewer_update_options( $wp_simpleviewer_options );

	} elseif ($wp_simpleviewer_form_option == 'reset') {	//plugin should be reset
		check_admin_referer($wp_simpleviewer_nonce.'reset');
		wp_simpleviewer_reset_options();
	} // end if $wp_simpleviewer_form_option	

	if ( $wp_simpleviewer_form_option != 'reset' || !isset($wp_simpleviewer_form_option) )	//show the main page only if plugin should not be reset
	{
		?>
		<div class="wrap">
			<h2><?php _e('WP-SimpleViewer Settings', $wp_simpleviewer_textdomain); ?></h2>
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo( attribute_escape($_GET['page']) ); ?>">
				<?php 
				wp_nonce_field($wp_simpleviewer_nonce.'settings');
				wp_simpleviewer_settings_form( $wp_simpleviewer_options, 'settings' );
				?>
				<p class="submit">
					<input name="wp_simpleviewer_form_option" type="hidden" id="wp_simpleviewer_form_option" value="update" />
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
			</form> 
		</div>
		<?php 
		wp_simpleviewer_reset_form();
	}	
}	//end function wp_simpleviewer_options_page()

function wp_simpleviewer_reset_form() {
	global $wp_simpleviewer_textdomain;
	global $wp_simpleviewer_nonce;
	?>
	<br class="clear" />
	<div class="wrap"> 
		<h2><?php _e('Reset WP-SimpleViewer plugin', $wp_simpleviewer_textdomain) ?></h2> 
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo( attribute_escape($_GET['page']) ); ?>">
		<?php _e('Pressing the uninstall button does not delete any files. It just removes all settings from the database and asks you if you want to deactivate the plugin or start from scratch.', $wp_simpleviewer_textdomain); ?>
		<p class="submit">
			<?php 
			wp_nonce_field($wp_simpleviewer_nonce.'reset'); 
			?>
			<input name="wp_simpleviewer_form_option" type="hidden" id="wp_simpleviewer_form_option" value="reset" />
			<input type="submit" class="button-primary" value="<?php _e('Reset WP-SimpleViewer plugin', $wp_simpleviewer_textdomain) ?>" />
		</p>
		</form>
	</div>
	<?php
}	//end function wp_simpleviewer_reset_form

function wp_simpleviewer_settings_form( $wp_simpleviewer_options, $wp_simpleviewer_settings_form_purpose='settings' ) {
	global $wp_simpleviewer_textdomain;
	global $wp_simpleviewer_nonce;
	?>
	<h3><?php _e('Size and colors', $wp_simpleviewer_textdomain); ?></h3>
	<table class="form-table"> 
		<tr valign="top"> 
			<th scope="row"><?php _e('Width:', $wp_simpleviewer_textdomain); ?></th> 
			<td>
				<span class="wp_simpleviewer_tips" title="<?php _e('Default width of the SimpleViewer gallery as it should be shown on the page, e.g. 500 for 500 pixels or 90%.', $wp_simpleviewer_textdomain); ?>">
					<input name="wp_simpleviewer_width" type="text" id="wp_simpleviewer_width" value="<?php echo $wp_simpleviewer_options['width']; ?>" size="10" />
				</span>
			</td>
		</tr> 
		<tr valign="top">
			<th scope="row"><?php _e('Height:', $wp_simpleviewer_textdomain); ?></th> 
			<td>
				<span class="wp_simpleviewer_tips" title="<?php _e('Default height of the SimpleViewer gallery as it should be shown on the page, e.g. 500 for 500 pixels or 90%.', $wp_simpleviewer_textdomain); ?>">
					<input name="wp_simpleviewer_height" type="text" id="wp_simpleviewer_height" value="<?php echo $wp_simpleviewer_options['height']; ?>" size="10" />
				</span>
			</td>
		</tr> 
		<tr valign="top">
			<th scope="row"><?php _e('Textcolor:', $wp_simpleviewer_textdomain); ?></th> 
			<td>
				<span class="wp_simpleviewer_tips" title="<?php _e('Color of title and caption text (hexidecimal color value)', $wp_simpleviewer_textdomain); ?>">
					<input name="wp_simpleviewer_textcolor" type="text" id="wp_simpleviewer_textcolor" value="#<?php echo $wp_simpleviewer_options['textcolor']; ?>" size="10" maxlength="7" />
				</span>
				<div id="wp_simpleviewer_textcolor_picker"></div>
			</td>
		</tr> 
		<tr valign="top">
			<th scope="row"><?php _e('Framecolor:', $wp_simpleviewer_textdomain); ?></th> 
			<td>
				<span class="wp_simpleviewer_tips" title="<?php _e('Color of image frame, navigation buttons and thumbnail frame (hexidecimal color value)', $wp_simpleviewer_textdomain); ?>">
					<input name="wp_simpleviewer_framecolor" type="text" id="wp_simpleviewer_framecolor" value="#<?php echo $wp_simpleviewer_options['framecolor']; ?>" size="10" maxlength="7" />
				</span>
				<div id="wp_simpleviewer_framecolor_picker"></div>
			</td>
		</tr> 
		<tr valign="top">
			<th scope="row"><?php _e('Backgroundcolor:', $wp_simpleviewer_textdomain); ?></th> 
			<td>
				<span class="wp_simpleviewer_tips" title="<?php _e('Color of gallery background (hexidecimal color value)', $wp_simpleviewer_textdomain); ?>">
					<input name="wp_simpleviewer_bgcolor" type="text" id="wp_simpleviewer_bgcolor" value="#<?php echo $wp_simpleviewer_options['bgcolor']; ?>" size="10" maxlength="7" />
				</span>
				<div id="wp_simpleviewer_bgcolor_picker"></div>
			</td>
		</tr> 
	</table> 
	<br class="clear" />
	<h3><?php _e('Gallery creation and appearance', $wp_simpleviewer_textdomain); ?></h3>
	<table class="form-table">
		<tr valign="top"> 
			<th scope="row"><?php _e('Framewidth:', $wp_simpleviewer_textdomain); ?></th> 
			<td>
				<span class="wp_simpleviewer_tips" title="<?php _e('Width of image frame', $wp_simpleviewer_textdomain); ?>">
					<input name="wp_simpleviewer_framewidth" type="text" id="wp_simpleviewer_framewidth" value="<?php echo $wp_simpleviewer_options['framewidth']; ?>" size="10" /> <?php _e('pixels', $wp_simpleviewer_textdomain); ?>
				</span>
			</td>
		</tr> 
		<tr valign="top">
			<th scope="row"><?php _e('Stagepadding:', $wp_simpleviewer_textdomain); ?></th> 
			<td>
				<span class="wp_simpleviewer_tips" title="<?php _e('Width of padding around gallery edge. To have the image flush to the edge of the swf, set this to 0.', $wp_simpleviewer_textdomain); ?>">
					<input name="wp_simpleviewer_stagepadding" type="text" id="wp_simpleviewer_stagepadding" value="<?php echo $wp_simpleviewer_options['stagepadding']; ?>" size="10" /> <?php _e('pixels', $wp_simpleviewer_textdomain); ?>
				</span>
			</td>
		</tr> 
		<tr valign="top">
			<th scope="row"><?php _e('Navigationpadding:', $wp_simpleviewer_textdomain); ?></th> 
			<td>
				<span class="wp_simpleviewer_tips" title="<?php _e('Distance between image and thumbnails', $wp_simpleviewer_textdomain); ?>">
					<input name="wp_simpleviewer_navpadding" type="text" id="wp_simpleviewer_navpadding" value="<?php echo $wp_simpleviewer_options['navpadding']; ?>" size="10" /> <?php _e('pixels', $wp_simpleviewer_textdomain); ?>
				</span>
			</td>
		</tr> 
		<tr valign="top">
			<th scope="row"><?php _e('Number of thumbnails in one row:', $wp_simpleviewer_textdomain); ?></th> 
			<td>
				<span class="wp_simpleviewer_tips" title="<?php _e('Number of thumbnail columns. To disable thumbnails completely set this value to 0', $wp_simpleviewer_textdomain); ?>">
					<input name="wp_simpleviewer_thumbnailcolumns" type="text" id="wp_simpleviewer_thumbnailcolumns" value="<?php echo $wp_simpleviewer_options['thumbnailcolumns']; ?>" size="10" />
				</span>
			</td>
		</tr> 
		<tr valign="top">
			<th scope="row"><?php _e('Number of thumbnail rows:', $wp_simpleviewer_textdomain); ?></th> 
			<td>
				<span class="wp_simpleviewer_tips" title="<?php _e('Number of thumbnail rows. To disable thumbnails completely set this value to 0.', $wp_simpleviewer_textdomain); ?>">
					<input name="wp_simpleviewer_thumbnailrows" type="text" id="wp_simpleviewer_thumbnailrows" value="<?php echo $wp_simpleviewer_options['thumbnailrows']; ?>" size="10" />
				</span>
			</td>
		</tr> 
		<tr valign="top">
			<th scope="row"><?php _e('Position of navigation:', $wp_simpleviewer_textdomain); ?></th> 
			<td>
				<span class="wp_simpleviewer_tips" title="<?php _e('Position of thumbnails relative to image', $wp_simpleviewer_textdomain); ?>">
					<label><input type='radio' name='wp_simpleviewer_navposition' value="top"    <?php if ($wp_simpleviewer_options['navposition']=="top") echo 'checked="checked"'; ?>>    <?php _e('top', $wp_simpleviewer_textdomain); ?></label><br />
					<label><input type='radio' name='wp_simpleviewer_navposition' value="bottom" <?php if ($wp_simpleviewer_options['navposition']=="bottom") echo 'checked="checked"'; ?>> <?php _e('bottom', $wp_simpleviewer_textdomain); ?></label><br />
					<label><input type='radio' name='wp_simpleviewer_navposition' value="left"   <?php if ($wp_simpleviewer_options['navposition']=="left") echo 'checked="checked"'; ?>>   <?php _e('left', $wp_simpleviewer_textdomain); ?></label><br />
					<label><input type='radio' name='wp_simpleviewer_navposition' value="right"  <?php if ($wp_simpleviewer_options['navposition']=="right") echo 'checked="checked"'; ?>>  <?php _e('right', $wp_simpleviewer_textdomain); ?></label><br />
				</span>
			</td>
		</tr> 
		<tr valign="top">
			<th scope="row"><?php _e('Vertical placement of the image and thumbnails:', $wp_simpleviewer_textdomain); ?></th> 
			<td>
				<span class="wp_simpleviewer_tips" title="<?php _e('In most cases this is best set to center. For small format galleries setting this to top or bottom can help get the image flush to the edge of the swf.', $wp_simpleviewer_textdomain); ?>">
					<label><input type='radio' name='wp_simpleviewer_valign' value="top"    <?php if ($wp_simpleviewer_options['valign']=="top") echo 'checked="checked"'; ?>>    <?php _e('top', $wp_simpleviewer_textdomain); ?></label><br />
					<label><input type='radio' name='wp_simpleviewer_valign' value="center" <?php if ($wp_simpleviewer_options['valign']=="center") echo 'checked="checked"'; ?>> <?php _e('center', $wp_simpleviewer_textdomain); ?></label><br />
					<label><input type='radio' name='wp_simpleviewer_valign' value="bottom" <?php if ($wp_simpleviewer_options['valign']=="bottom") echo 'checked="checked"'; ?>> <?php _e('bottom', $wp_simpleviewer_textdomain); ?></label><br />
				</span>
			</td>
		</tr> 
		<tr valign="top">
			<th scope="row"><?php _e('Horizontal placement of the image and thumbnails:', $wp_simpleviewer_textdomain); ?></th> 
			<td>
				<span class="wp_simpleviewer_tips" title="<?php _e('In most cases this is best set to center. For small format galleries setting this to left or right can help get the image flush to the edge of the swf.', $wp_simpleviewer_textdomain); ?>">
					<label><input type='radio' name='wp_simpleviewer_halign' value="left"   <?php if ($wp_simpleviewer_options['halign']=="left") echo 'checked="checked"'; ?>>   <?php _e('left', $wp_simpleviewer_textdomain); ?></label><br />
					<label><input type='radio' name='wp_simpleviewer_halign' value="center" <?php if ($wp_simpleviewer_options['halign']=="center") echo 'checked="checked"'; ?>> <?php _e('center', $wp_simpleviewer_textdomain); ?></label><br />
					<label><input type='radio' name='wp_simpleviewer_halign' value="right"  <?php if ($wp_simpleviewer_options['halign']=="right") echo 'checked="checked"'; ?>>  <?php _e('right', $wp_simpleviewer_textdomain); ?></label><br />
				</span>
			</td>
		</tr>
		<?php 
		if ( $wp_simpleviewer_settings_form_purpose != 'editxml' ) {
		?> 
			<tr valign="top">
				<th scope="row"><?php _e('Max. image dimension:', $wp_simpleviewer_textdomain); ?></th> 
				<td>
					<span class="wp_simpleviewer_tips" title="<?php _e('All images in the gallery will be resized to this dimension', $wp_simpleviewer_textdomain); ?>">
						<input name="wp_simpleviewer_max_image_size" type="text" id="wp_simpleviewer_max_image_size" value="<?php echo $wp_simpleviewer_options['max_image_size']; ?>" size="4" /> <?php _e('pixels', $wp_simpleviewer_textdomain); ?>
					</span>
				</td>
			</tr> 
			<tr valign="top">
				<th scope="row"><?php _e('Image resize quality:', $wp_simpleviewer_textdomain); ?></th> 
				<td>
					<span class="wp_simpleviewer_tips" title="<?php _e('Resampling quality used when creating thumbnails and resizing images.', $wp_simpleviewer_textdomain); ?>">
						<input name="wp_simpleviewer_resizequality" type="text" id="wp_simpleviewer_resizequality" value="<?php echo $wp_simpleviewer_options['resizequality']; ?>" size="4" /> %
					</span>
				</td>
			</tr> 
		<?php 
		}
		?> 
		<tr valign="top">
			<th scope="row"><?php _e('Show download link or caption:', $wp_simpleviewer_textdomain); ?></th> 
			<td>
				<span class="wp_simpleviewer_tips" title="<?php _e('Should the caption link to the original image, open it in a Thichbox or should it be text only? You can also hide captions completely. ', $wp_simpleviewer_textdomain); ?>">
					<?php if ( $wp_simpleviewer_options['jsbox'] != "off" ) { ?>
					<label><input type='radio' name='wp_simpleviewer_show_download_link' value="jsbox"         <?php if ($wp_simpleviewer_options['show_download_link']=="jsbox") echo 'checked="checked"'; ?>>        <?php _e('Show link to Thickbox', $wp_simpleviewer_textdomain); ?></label><br />
					<?php } ?>
					<label><input type='radio' name='wp_simpleviewer_show_download_link' value="true"          <?php if ($wp_simpleviewer_options['show_download_link']=="true") echo 'checked="checked"'; ?>>         <?php _e('Show download link', $wp_simpleviewer_textdomain); ?></label><br />
					<label><input type='radio' name='wp_simpleviewer_show_download_link' value="captions only" <?php if ($wp_simpleviewer_options['show_download_link']=="captions only") echo 'checked="checked"'; ?>><?php _e('Show caption only', $wp_simpleviewer_textdomain); ?></label><br />
					<label><input type='radio' name='wp_simpleviewer_show_download_link' value="false"         <?php if ($wp_simpleviewer_options['show_download_link']=="false") echo 'checked="checked"'; ?>>        <?php _e('Hide caption', $wp_simpleviewer_textdomain); ?></label><br />
				</span>
			</td>
		</tr> 
		<?php 
		if ( $wp_simpleviewer_settings_form_purpose=='settings' ) {
		?>
			<tr valign="top">
				<th scope="row"><?php _e('Standard download link text/caption:', $wp_simpleviewer_textdomain); ?></th> 
				<td>
					<span class="wp_simpleviewer_tips" title="<?php _e('This text will be proposed as caption text when creating a new gallery.', $wp_simpleviewer_textdomain); ?>">
						<?php if ( strcmp($wp_simpleviewer_options['default_download_link_text'], __('Filenames should be used for the captions', $wp_simpleviewer_textdomain)) ){ ?>
							<input name="wp_simpleviewer_download_link_use_filename" type="checkbox" id="wp_simpleviewer_download_link_use_filename" /><?php _e('Use filename as caption', $wp_simpleviewer_textdomain); ?><br />
							<input name="wp_simpleviewer_default_download_link_text" type="text" id="wp_simpleviewer_default_download_link_text" value="<?php echo $wp_simpleviewer_options['default_download_link_text']; ?>" size="80" />
						<?php } else { ?>
							<input name="wp_simpleviewer_download_link_use_filename" type="checkbox" id="wp_simpleviewer_download_link_use_filename" checked="checked" /><?php _e('Use filename as caption', $wp_simpleviewer_textdomain); ?><br />
							<input name="wp_simpleviewer_default_download_link_text" type="text" id="wp_simpleviewer_default_download_link_text" value="<?php echo $wp_simpleviewer_options['default_download_link_text']; ?>" size="80" style="display:none" />
						<?php } ?>
					</span>
				</td>
			</tr> 
			<tr valign="top">
				<th scope="row"><?php _e('Standard gallery title:', $wp_simpleviewer_textdomain); ?></th> 
				<td>
					<span class="wp_simpleviewer_tips" title="<?php _e('This text will be proposed as gallery title when creating a new gallery. If you do not want to have a default title leave it empty.', $wp_simpleviewer_textdomain); ?>">
						<input name="wp_simpleviewer_standard_title" type="text" id="wp_simpleviewer_standard_title" value="<?php echo $wp_simpleviewer_options['standard_title']; ?>" size="80" />
					</span>
				</td>
			</tr>
		<?php
		} elseif ( $wp_simpleviewer_settings_form_purpose=='create' ) {
		?>
			<tr valign="top">
				<th scope="row"><?php _e('Download link text/caption:', $wp_simpleviewer_textdomain); ?></th> 
				<td>
					<span class="wp_simpleviewer_tips" title="<?php _e('This text will be used as caption text for the images in this gallery.', $wp_simpleviewer_textdomain); ?>">
						<?php if ( strcmp($wp_simpleviewer_options['default_download_link_text'], __('Filenames should be used for the captions', $wp_simpleviewer_textdomain)) ){ ?>
							<input name="wp_simpleviewer_download_link_use_filename" type="checkbox" id="wp_simpleviewer_download_link_use_filename" /><?php _e('Use filename as caption', $wp_simpleviewer_textdomain); ?><br />
							<input name="wp_simpleviewer_default_download_link_text" type="text" id="wp_simpleviewer_default_download_link_text" value="<?php echo $wp_simpleviewer_options['default_download_link_text']; ?>" size="80" />
						<?php } else { ?>
							<input name="wp_simpleviewer_download_link_use_filename" type="checkbox" id="wp_simpleviewer_download_link_use_filename" checked="checked" /><?php _e('Use filename as caption', $wp_simpleviewer_textdomain); ?><br />
							<input name="wp_simpleviewer_default_download_link_text" type="text" id="wp_simpleviewer_default_download_link_text" value="<?php echo $wp_simpleviewer_options['default_download_link_text']; ?>" size="80" style="display:none" />
						<?php } ?>
					</span>
				</td>
			</tr> 
		<?php
		}
		if ( $wp_simpleviewer_settings_form_purpose!='settings' ) {
		?>
			<tr valign="top">
				<th scope="row"><?php _e('Gallery title:', $wp_simpleviewer_textdomain); ?></th> 
				<td>
					<span class="wp_simpleviewer_tips" title="<?php _e('If the gallery should have a title write it down here, otherwise just leave it empty.', $wp_simpleviewer_textdomain); ?>">
						<input name="wp_simpleviewer_standard_title" type="text" id="wp_simpleviewer_standard_title" value="<?php echo $wp_simpleviewer_options['standard_title']; ?>" size="40" />
					</span>
				</td>
			</tr> 
		<?php
		}
		if ( $wp_simpleviewer_settings_form_purpose != 'editxml' ) {
		?> 
		<tr valign="top">
			<th scope="row"><?php _e('Sort images by:', $wp_simpleviewer_textdomain); ?></th> 
			<td>
				<span class="wp_simpleviewer_tips" title="<?php _e('Select how the images in the gallery should be sorted during gallerycreation.', $wp_simpleviewer_textdomain); ?>">
					<label><input type='radio' name='wp_simpleviewer_imagesort' value="nameascending"  <?php if ($wp_simpleviewer_options['imagesort']=="nameascending")  echo 'checked="checked"'; ?>><?php _e('Name, ascending', $wp_simpleviewer_textdomain); ?></label><br />
					<label><input type='radio' name='wp_simpleviewer_imagesort' value="namedescending" <?php if ($wp_simpleviewer_options['imagesort']=="namedescending") echo 'checked="checked"'; ?>><?php _e('Name, descending', $wp_simpleviewer_textdomain); ?></label><br />
					<label><input type='radio' name='wp_simpleviewer_imagesort' value="dateascending"  <?php if ($wp_simpleviewer_options['imagesort']=="dateascending")  echo 'checked="checked"'; ?>><?php _e('Date, ascending', $wp_simpleviewer_textdomain); ?></label><br />
					<label><input type='radio' name='wp_simpleviewer_imagesort' value="datedescending" <?php if ($wp_simpleviewer_options['imagesort']=="datedescending") echo 'checked="checked"'; ?>><?php _e('Date, descending', $wp_simpleviewer_textdomain); ?></label><br />
				</span>
			</td>
		</tr> 
		<?php
		}
		?> 
	</table> 
	<?php 
	if ( $wp_simpleviewer_settings_form_purpose=='settings' ) {
	?>
		<br class="clear" />
		<h3><?php _e('Other settings', $wp_simpleviewer_textdomain); ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Enable Thickbox:', $wp_simpleviewer_textdomain); ?></th> 
				<td>
					<span class="wp_simpleviewer_tips" title="<?php _e('You can show your full size images in a Thickbox, a fancy javascript tool.', $wp_simpleviewer_textdomain); ?>">
						<label><input type='radio' name='wp_simpleviewer_jsbox' value="thickbox" <?php if ($wp_simpleviewer_options['jsbox']=="thickbox") echo 'checked="checked"'; ?>><?php _e('Yes, enable Thickbox', $wp_simpleviewer_textdomain); ?></label><br />
						<label><input type='radio' name='wp_simpleviewer_jsbox' value="off"      <?php if ($wp_simpleviewer_options['jsbox']=="off")      echo 'checked="checked"'; ?>><?php _e('No, I only want to link to the image files', $wp_simpleviewer_textdomain); ?></label><br />
					</span>
				</td>
			</tr> 
			<tr valign="top">
				<th scope="row"><?php _e('Text to be displayed in feeds:', $wp_simpleviewer_textdomain); ?></th> 
				<td>
					<span class="wp_simpleviewer_tips" title="<?php _e('Most feed readers cannot display the Flash gallery. In your Feeds this text will be shown where the gallery is supposed to be.', $wp_simpleviewer_textdomain); ?>">
						<textarea name="wp_simpleviewer_feed_text" id="wp_simpleviewer_feed_text" cols="80" rows="3"><?php echo $wp_simpleviewer_options['feed_text']; ?></textarea>
					</span>
				</td>
			</tr> 
			<tr valign="top">
				<th scope="row"><?php _e('Show howto text?', $wp_simpleviewer_textdomain); ?></th> 
				<td>
					<span class="wp_simpleviewer_tips" title="<?php _e('Uncheck this box if you want to hide the howto text on the manage page.', $wp_simpleviewer_textdomain); ?>">
						<input name="wp_simpleviewer_howto" type="checkbox" id="wp_simpleviewer_howto" value="1" <?php if ($wp_simpleviewer_options['howto']=="on") echo 'checked="checked"'; ?>/>
					</span>
				</td>
			</tr> 
			<tr valign="top">
				<th scope="row"><?php _e('Sort list of galleries by:', $wp_simpleviewer_textdomain); ?></th> 
				<td>
					<span class="wp_simpleviewer_tips" title="<?php _e('Select how the list of galleries on the manage page should be sorted.', $wp_simpleviewer_textdomain); ?>">
						<label><input type='radio' name='wp_simpleviewer_gallerysort' value="nameascending"  <?php if ($wp_simpleviewer_options['gallerysort']=="nameascending")  echo 'checked="checked"'; ?>><?php _e('Name, ascending', $wp_simpleviewer_textdomain); ?></label><br />
						<label><input type='radio' name='wp_simpleviewer_gallerysort' value="namedescending" <?php if ($wp_simpleviewer_options['gallerysort']=="namedescending") echo 'checked="checked"'; ?>><?php _e('Name, descending', $wp_simpleviewer_textdomain); ?></label><br />
						<label><input type='radio' name='wp_simpleviewer_gallerysort' value="dateascending"  <?php if ($wp_simpleviewer_options['gallerysort']=="dateascending")  echo 'checked="checked"'; ?>><?php _e('Date, ascending', $wp_simpleviewer_textdomain); ?></label><br />
						<label><input type='radio' name='wp_simpleviewer_gallerysort' value="datedescending" <?php if ($wp_simpleviewer_options['gallerysort']=="datedescending") echo 'checked="checked"'; ?>><?php _e('Date, descending', $wp_simpleviewer_textdomain); ?></label><br />
					</span>
				</td>
			</tr> 
		</table> 
		<br class="clear" />
		<h3><?php _e('Directory settings', $wp_simpleviewer_textdomain); ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('URL to images directory:', $wp_simpleviewer_textdomain); ?></th> 
				<td>
					<span class="wp_simpleviewer_tips" title="<?php _e('The directory where your images are located has to be accessible under this URL', $wp_simpleviewer_textdomain); ?>">
						<input name="wp_simpleviewer_images_url" type="text" id="wp_simpleviewer_images_url" value="<?php echo $wp_simpleviewer_options['images_url']; ?>" size="80" />
					</span>
				</td>
			</tr> 
			<tr valign="top">
				<th scope="row"><?php _e('Path to images directory:', $wp_simpleviewer_textdomain); ?></th> 
				<td>
					<span class="wp_simpleviewer_tips" title="<?php _e('The full path to your directory where your images are located on your server', $wp_simpleviewer_textdomain); ?>">
						<input name="wp_simpleviewer_images_dir" type="text" id="wp_simpleviewer_images_dir" value="<?php echo $wp_simpleviewer_options['images_dir']; ?>" size="80" />
					</span>
				</td>
			</tr> 
			<tr valign="top">
				<th scope="row"><?php _e('Regular images subdirectory:', $wp_simpleviewer_textdomain); ?></th> 
				<td>
					<span class="wp_simpleviewer_tips" title="<?php _e('When you create a new gallery the resized images will be put into a subfolder with this name', $wp_simpleviewer_textdomain); ?>">
						<input name="wp_simpleviewer_folder_regular" type="text" id="wp_simpleviewer_folder_regular" value="<?php echo $wp_simpleviewer_options['folder_regular']; ?>" size="10" />
					</span>
				</td>
			</tr> 
			<tr valign="top">
				<th scope="row"><?php _e('Thumbnails subdirectory:', $wp_simpleviewer_textdomain); ?></th> 
				<td>
					<span class="wp_simpleviewer_tips" title="<?php _e('When you create a new gallery the thumbnails will be put into a subfolder with this name', $wp_simpleviewer_textdomain); ?>">
						<input name="wp_simpleviewer_folder_thumbnails" type="text" id="wp_simpleviewer_folder_thumbnails" value="<?php echo $wp_simpleviewer_options['folder_thumbnails']; ?>" size="10" />
					</span>
				</td>
			</tr> 
		</table> 
	<?php
	}
}	//end function wp_simpleviewer_settings_form

//This function verifies that the new directory has a "/" at the end
function verify_directory_name($wp_simpleviewer_folder_to_verify) {
	$wp_simpleviewer_folder_to_verify = trim($wp_simpleviewer_folder_to_verify);
	if ( $wp_simpleviewer_folder_to_verify[strlen($wp_simpleviewer_folder_to_verify)-1] != '/')
		return $wp_simpleviewer_folder_to_verify.'/';
	else
		return $wp_simpleviewer_folder_to_verify;
}

// Function to delete all old image & xml files if already existing, will not return anything or handle errors (maybe this will follow soon)
function cleanup_old_files($wp_simpleviewer_xmlfile_dir, $wp_simpleviewer_gallery_url_regular, $wp_simpleviewer_gallery_url_thumbnails) {
		//Remove old xmlfile
		if (is_writable($wp_simpleviewer_xmlfile_dir)){
			unlink($wp_simpleviewer_xmlfile_dir);
		}
		
		//Remove old regular images and thumbnails
		if ( is_dir($wp_simpleviewer_gallery_url_regular) ){
					$directory_handle = opendir($wp_simpleviewer_gallery_url_regular);
					 while($file = readdir($directory_handle)) {	
						if ($file[0] != "." && $file[0] != ".." ){
							unlink($wp_simpleviewer_gallery_url_regular.'/'.$file);
						}		
					 }	//end loop thru files
					 rmdir($wp_simpleviewer_gallery_url_regular);
		}
		
		if ( is_dir($wp_simpleviewer_gallery_url_thumbnails) ){
					$directory_handle = opendir($wp_simpleviewer_gallery_url_thumbnails);
					 while($file = readdir($directory_handle)) {	
						if ($file[0] != "." && $file[0] != ".." ){
							unlink($wp_simpleviewer_gallery_url_thumbnails.'/'.$file);
						}		
					 }	//end loop thru files		
					 rmdir($wp_simpleviewer_gallery_url_thumbnails);
		}
}	//end function cleanup_old_files()

// Function to create resized images
// To learn more about this function check out this tutorial: http://www.reconn.us/content/view/32/43/
function create_resized_image($src_filepath, $dst_filepath, $new_image_squared, $max_size_dest, $wp_simpleviewer_resizequality) {
	global $wp_simpleviewer_textdomain;
	//get image extension.
	$ext=getExtension($src_filepath);
	//creates the new image using the appropriate function from gd library
	if(!strcmp("jpg",$ext) || !strcmp("jpeg",$ext) && function_exists("imagecreatefromjpeg") )
		$src_img = @imagecreatefromjpeg($src_filepath);
	elseif(!strcmp("png",$ext) && function_exists("imagecreatefrompng") )
		$src_img = @imagecreatefrompng($src_filepath);
	elseif(!strcmp("gif",$ext) && function_exists("imagecreatefromgif") )
		$src_img = @imagecreatefromgif($src_filepath);
	else {
		_e('Skipping thumb creation for this file, file type is invalid:', $wp_simpleviewer_textdomain);
		echo ' '.$src_filepath.'<br />';
		return 0;
	}
	
	if (!$src_img) {
		_e('This image file does not have a correct image format so I am skipping thumb creation for this file - please upload it again:', $wp_simpleviewer_textdomain);
		echo ' '.$src_filepath.'<br />';
		return 0;
	} else {
		//gets the dimmensions of the image
		$old_x=imageSX($src_img);
		$old_y=imageSY($src_img);
	
		//set default size values
		$new_x       = $max_size_dest;
		$new_y       = $max_size_dest;
		$distance_x  = 0;
		$distance_y  = 0;
		$px_to_use_x = $old_x;
		$px_to_use_y = $old_y;
		//calculate ratio
		$ratio = $old_x/$old_y;
		
		if ($new_image_squared==true){		//generate squared new image (with cutting - used for thumbs)
			// Check image shape 
			if ($ratio > 1) {			//horizontal
				$distance_x  = ($old_x - $old_y) / 2;
				//$distance_y= 0;
				$px_to_use_x = $old_y;
				$px_to_use_y = $old_y;
	
			} elseif ($ratio < 1) {	//vertical
				//$distance_x= 0;
				$distance_y  = ($old_y - $old_x) / 2;
				$px_to_use_x = $old_x;
				$px_to_use_y = $old_x;
			}
		} else {	//resize original image and keep ratio (used for all other images)
			// Check image shape 
			if ($ratio > 1) {			//horizontal
				$new_y = $max_size_dest/$ratio;
			
			} elseif ($ratio < 1) {	//vertical
				$new_x = $max_size_dest*$ratio;
			} 
		}		//end resize original image
		
		// we create a new image with the new dimmensions
		$dst_img = imagecreatetruecolor($new_x, $new_y);
			
		// resize the big image to the new created one
		// parameter used:
		// $distance_x  - distance from left image corner to start with thumb in x direction (left to right)
		// $distance_y  - distance from left image corner to start with thumb in y direction (up to down)
		// $new_x   - x-size of destination image
		// $new_y   - y-size of destination image
		// $px_to_use_x - how many px of the old image should be used in x direction
		// $px_to_use_y - how many px of the old image should be used in y direction
		imagecopyresampled($dst_img, $src_img, 0, 0, $distance_x, $distance_y, $new_x, $new_y, $px_to_use_x, $px_to_use_y);
		
		// output the created image to the file.
		if(!strcmp("png",$ext)) {
			if (!@imagepng($dst_img,$dst_filepath) ){
				_e('The new image could not be created - skipping thumb creation for this file:', $wp_simpleviewer_textdomain);
				echo ' '.$dst_filepath.' <br />';
				imagedestroy($dst_img); 
				imagedestroy($src_img); 
				return 0;
			}
		} elseif(!strcmp("gif",$ext)) {
			if (!@imagegif($dst_img,$dst_filepath) ) {
				_e('The new image could not be created - skipping thumb creation for this file:', $wp_simpleviewer_textdomain);
				echo ' '.$dst_filepath.' <br />';
				imagedestroy($dst_img); 
				imagedestroy($src_img); 
				return 0; 
			}
		} elseif (!@imagejpeg($dst_img,$dst_filepath, $wp_simpleviewer_resizequality) ) {
			_e('The new image could not be created - skipping thumb creation for this file:', $wp_simpleviewer_textdomain);
			echo ' '.$dst_filepath.' <br />';
			imagedestroy($dst_img); 
			imagedestroy($src_img); 
			return 0; 
		} 
		
		//destroys source and destination images. 
		imagedestroy($dst_img); 
		imagedestroy($src_img); 
		
		return 1;
	}	//end if !srv_img
} //end function create_resized_image

// This function reads the extension of the file. 
// It is used to determine if the file is an image by checking the extension. 
function getExtension($str) {
	$i = strrpos($str,".");
	if (!$i) { 
		return ""; 
	}
	$l = strlen($str) - $i;
	$ext = substr($str,$i+1,$l);
	return strtolower($ext);
}	//end function getExtension

function wp_simpleviewer_gallery_from_xml($xmlfile) {
	$wp_simpleviewer_gallery_array = wp_simpleviewer_xml2ary( file_get_contents($xmlfile) );
	//Now the gallery is in the array, only cleanup of captions is necessary
	//If caption is a download or jsbox link only the text is needed
	foreach ($wp_simpleviewer_gallery_array["simpleviewerGallery"]["_c"]["image"] as $key => $wp_simpleviewer_image) {
		if ( preg_match('/<A.*><U>(.*)<\/U><\/A>/i', $wp_simpleviewer_image["_c"]["caption"]["_v"], $wp_simpleviewer_caption) ) {
			$wp_simpleviewer_gallery_array["simpleviewerGallery"]["_c"]["image"][$key]["_c"]["caption"]["_v"] = $wp_simpleviewer_caption[1];
		}
	}	//end foreach 
	return $wp_simpleviewer_gallery_array;
}	//end function wp_simpleviewer_gallery_from_xml

function wp_simpleviewer_save_gallery( $wp_simpleviewer_gallery_array, &$wp_simpleviewer_options, $wp_simpleviewer_gallery, $wp_simpleviewer_xmlfile_dir ) {
	global $wp_simpleviewer_textdomain;
	//If caption is a download or jsbox link the text has to be changed
	foreach ($wp_simpleviewer_gallery_array["simpleviewerGallery"]["_c"]["image"] as $key => $wp_simpleviewer_image) {
		if ($wp_simpleviewer_options['show_download_link'] == "jsbox"){		
			$wp_simpleviewer_gallery_array["simpleviewerGallery"]["_c"]["image"][$key]["_c"]["caption"]["_v"] = '<![CDATA[<A href="javascript:jsbx(\''.$wp_simpleviewer_options['images_url'].$wp_simpleviewer_gallery.'/'.$wp_simpleviewer_image["_c"]["filename"]["_v"].'\',\'\',\''.$wp_simpleviewer_gallery.'\');"><U>'.$wp_simpleviewer_image["_c"]["caption"]["_v"].'</U></A>]]>';
		} elseif ($wp_simpleviewer_options['show_download_link'] == "true"){		
			$wp_simpleviewer_gallery_array["simpleviewerGallery"]["_c"]["image"][$key]["_c"]["caption"]["_v"] = '<![CDATA[<A href="'.$wp_simpleviewer_options['images_url'].$wp_simpleviewer_gallery.'/'.$wp_simpleviewer_image["_c"]["filename"]["_v"].'" target="_blank"><U>'.$wp_simpleviewer_image["_c"]["caption"]["_v"].'</U></A>]]>';
		} elseif ($wp_simpleviewer_options['show_download_link'] == "captions only") {
			$wp_simpleviewer_gallery_array["simpleviewerGallery"]["_c"]["image"][$key]["_c"]["caption"]["_v"] = '<![CDATA['.$wp_simpleviewer_image["_c"]["caption"]["_v"].']]>';
		} else {
			$wp_simpleviewer_gallery_array["simpleviewerGallery"]["_c"]["image"][$key]["_c"]["caption"]["_v"] = '<![CDATA[]]>';
		}
	}	//end foreach */
	$wp_simpleviewer_gallery_xml  = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
	$wp_simpleviewer_gallery_xml .= wp_simpleviewer_ary2xml($wp_simpleviewer_gallery_array);
	
	//Now the array will be written to the file:
	if ( !$wp_simpleviewer_xmlfile_handle = @fopen($wp_simpleviewer_xmlfile_dir,"w") ) { 
		_e('I cannot open the XML file, please check that write permissions of the folder are correct. Filename is:', $wp_simpleviewer_textdomain);
		echo ' '.$wp_simpleviewer_xmlfile_dir.' <br /><br />';
	} elseif ( !fwrite($wp_simpleviewer_xmlfile_handle, $wp_simpleviewer_gallery_xml) ) { 
		_e('I have opened the XML file, but I was not able to write into it. Please check that write permissions of the folder are correct! Filename is:', $wp_simpleviewer_textdomain);
		echo ' '.$wp_simpleviewer_xmlfile_dir.'<br /><br />';   
	} else {
		echo '<br />';
		_e('Yeah, we are done!', $wp_simpleviewer_textdomain); 
	}
	fclose($wp_simpleviewer_xmlfile_handle);
	chmod($wp_simpleviewer_xmlfile_dir,0777); 		
}	//end function wp_simpleviewer_save_gallery

function wp_simpleviewer_directory_sort($a, $b){
	if ( isset($_GET['wp_simpleviewer_gallerysort']) ) {
		$wp_simpleviewer_options['gallerysort'] = attribute_escape($_GET['wp_simpleviewer_gallerysort']);
	} else {
		$wp_simpleviewer_options['gallerysort'] = get_option("wp_simpleviewer_gallerysort");
	}
	if ($wp_simpleviewer_options['gallerysort']=="datedescending") {
		return -strnatcasecmp($a["date"], $b["date"]);
	} elseif ($wp_simpleviewer_options['gallerysort']=="dateascending") {
		return strnatcasecmp($a["date"], $b["date"]);
	} elseif ($wp_simpleviewer_options['gallerysort']=="namedescending") {
		return -strnatcasecmp($a["name"], $b["name"]);
	} elseif ($wp_simpleviewer_options['gallerysort']=="nameascending") {
		return strnatcasecmp($a["name"], $b["name"]);
	}
}	//end function wp_simpleviewer_directory_sort

function wp_simpleviewer_image_sort($a, $b){
	if ( isset($_POST['wp_simpleviewer_imagesort']) ) {
		$wp_simpleviewer_options['imagesort'] = attribute_escape($_POST['wp_simpleviewer_imagesort']);
	} else {
		$wp_simpleviewer_options['imagesort'] = get_option("wp_simpleviewer_imagesort");
	}
	if ($wp_simpleviewer_options['imagesort']=="datedescending") {
		return -strnatcasecmp($a["date"], $b["date"]);
	} elseif ($wp_simpleviewer_options['imagesort']=="dateascending") {
		return strnatcasecmp($a["date"], $b["date"]);
	} elseif ($wp_simpleviewer_options['imagesort']=="namedescending") {
		return -strnatcasecmp($a["name"], $b["name"]);
	} elseif ($wp_simpleviewer_options['imagesort']=="nameascending") {
		return strnatcasecmp($a["name"], $b["name"]);
	}
}	//end function wp_simpleviewer_image_sort

// XML to Array - taken from http://mysrc.blogspot.com/2007/02/php-xml-to-array-and-backwards.html 
function wp_simpleviewer_xml2ary(&$string) {
    $parser = xml_parser_create();
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parse_into_struct($parser, $string, $vals, $index);
    xml_parser_free($parser);

    $mnary=array();
    $ary=&$mnary;
    foreach ($vals as $r) {
        $t=$r['tag'];
        if ($r['type']=='open') {
            if (isset($ary[$t])) {
                if (isset($ary[$t][0])) $ary[$t][]=array(); else $ary[$t]=array($ary[$t], array());
                $cv=&$ary[$t][count($ary[$t])-1];
            } else $cv=&$ary[$t];
            if (isset($r['attributes'])) {foreach ($r['attributes'] as $k=>$v) $cv['_a'][$k]=$v;}
            $cv['_c']=array();
            $cv['_c']['_p']=&$ary;
            $ary=&$cv['_c'];

        } elseif ($r['type']=='complete') {
            if (isset($ary[$t])) { // same as open
                if (isset($ary[$t][0])) $ary[$t][]=array(); else $ary[$t]=array($ary[$t], array());
                $cv=&$ary[$t][count($ary[$t])-1];
            } else $cv=&$ary[$t];
            if (isset($r['attributes'])) {foreach ($r['attributes'] as $k=>$v) $cv['_a'][$k]=$v;}
            $cv['_v']=(isset($r['value']) ? $r['value'] : '');

        } elseif ($r['type']=='close') {
            $ary=&$ary['_p'];
        }
    }    
    
    _del_p($mnary);
    return $mnary;
}	//end function wp_simpleviewer_xml2ary

// _Internal: Remove recursion in result array
function _del_p(&$ary) {
    foreach ($ary as $k=>$v) {
        if ($k==='_p') unset($ary[$k]);
        elseif (is_array($ary[$k])) _del_p($ary[$k]);
    }
}	//end function _del_p

// Array to XML
function wp_simpleviewer_ary2xml($cary, $d=0, $forcetag='') {
    $res=array();
    foreach ($cary as $tag=>$r) {
        if (isset($r[0])) {
            $res[]=wp_simpleviewer_ary2xml($r, $d, $tag);
        } else {
            if ($forcetag) $tag=$forcetag;
            $sp=str_repeat("\t", $d);
            $res[]="$sp<$tag";
            if (isset($r['_a'])) {foreach ($r['_a'] as $at=>$av) $res[]=" $at=\"$av\"";}
            $res[]=">".((isset($r['_c'])) ? "\n" : '');
            if (isset($r['_c'])) $res[]=wp_simpleviewer_ary2xml($r['_c'], $d+1);
            elseif (isset($r['_v'])) $res[]=$r['_v'];
            $res[]=(isset($r['_c']) ? $sp : '')."</$tag>\n";
        }
        
    }
    return implode('', $res);
}	//end function wp_simpleviewer_ary2xml

// Insert element into array
function wp_simpleviewer_ins2ary(&$ary, $element, $pos) {
    $ar1=array_slice($ary, 0, $pos); $ar1[]=$element;
    $ary=array_merge($ar1, array_slice($ary, $pos));
}	//function wp_simpleviewer_ins2ary

function wp_simpleviewer_quicktag() {
?>
	<script type="text/javascript">
	if(wp_simpleviewer_toolbar = document.getElementById("ed_toolbar")){
		var wp_simpleviewer_button_nr = edButtons.length;
		edButtons[wp_simpleviewer_button_nr] = new edButton('wp_simpleviewer_button','WP-SimpleViewer','[svgallery name=""]', '','', -1);
		var wp_simpleviewer_button = wp_simpleviewer_toolbar.lastChild;
		while (wp_simpleviewer_button.nodeType != 1){
			wp_simpleviewer_button = wp_simpleviewer_button.previousSibling;
		}
		wp_simpleviewer_button = wp_simpleviewer_button.cloneNode(true);
		wp_simpleviewer_toolbar.appendChild(wp_simpleviewer_button);
		wp_simpleviewer_button.value = 'WP-SimpleViewer';
		wp_simpleviewer_button.title = wp_simpleviewer_button_nr;
		wp_simpleviewer_button.onclick = function () {edInsertTag(edCanvas, parseInt(this.title));}
		wp_simpleviewer_button.id = "wp_simpleviewer_button";
	}
	</script>
<?php
}	//end function wp_simpleviewer_quicktag()

function wp_simpleviewer_add_options() {
	global $wp_simpleviewer_version;
	global $wp_simpleviewer_textdomain;
	
	$wp_simpleviewer_options['width'] = "100%";
	add_option("wp_simpleviewer_width", $wp_simpleviewer_options['width'], "Width of simpleviewer gallery", no);
	
	$wp_simpleviewer_options['height'] = "650";
	add_option("wp_simpleviewer_height", $wp_simpleviewer_options['height'], "Height of simpleviewer gallery", no);
	
	$wp_simpleviewer_options['textcolor'] = "f8f8f8";
	add_option("wp_simpleviewer_textcolor", $wp_simpleviewer_options['textcolor'], "Textcolor of simpleviewer gallery", no);
	
	$wp_simpleviewer_options['framecolor'] = "f8f8f8";
	add_option("wp_simpleviewer_framecolor", $wp_simpleviewer_options['framecolor'], "Framecolor of simpleviewer gallery", no);
	
	$wp_simpleviewer_options['bgcolor'] = "343434";
	add_option("wp_simpleviewer_bgcolor", $wp_simpleviewer_options['bgcolor'], "Backgroundcolor of simpleviewer gallery", no);
	
	$wp_simpleviewer_options['framewidth'] = "15";
	add_option("wp_simpleviewer_framewidth", $wp_simpleviewer_options['framewidth'], "Framewidth of simpleviewer gallery", no);
	
	$wp_simpleviewer_options['stagepadding'] = "20";
	add_option("wp_simpleviewer_stagepadding", $wp_simpleviewer_options['stagepadding'], "Stagepadding of simpleviewer gallery", no);
	
	$wp_simpleviewer_options['navpadding'] = "40";
	add_option("wp_simpleviewer_navpadding", $wp_simpleviewer_options['navpadding'], "Navigationpadding of simpleviewer gallery", no);
	
	$wp_simpleviewer_options['thumbnailcolumns'] = "4";
	add_option("wp_simpleviewer_thumbnailcolumns", $wp_simpleviewer_options['thumbnailcolumns'], "Thumbnailcolumns of simpleviewer gallery", no);
	
	$wp_simpleviewer_options['thumbnailrows'] = "2";
	add_option("wp_simpleviewer_thumbnailrows", $wp_simpleviewer_options['thumbnailrows'], "Thumbnailrows of simpleviewer gallery", no);
	
	$wp_simpleviewer_options['navposition'] = "bottom"; 
	add_option("wp_simpleviewer_navposition", $wp_simpleviewer_options['navposition'], "Position of navigation of simpleviewer gallery", no);
	
	$wp_simpleviewer_options['valign'] = "center"; 
	add_option("wp_simpleviewer_valign", $wp_simpleviewer_options['valign'], "Vertical placement of the image and thumbnails of simpleviewer gallery", no);
	
	$wp_simpleviewer_options['halign'] = "center"; 
	add_option("wp_simpleviewer_halign", $wp_simpleviewer_options['halign'], "Horizontal placement of the image and thumbnails of simpleviewer gallery", no);
	
	$wp_simpleviewer_options['standard_title'] = "";
	add_option("wp_simpleviewer_standard_title", $wp_simpleviewer_options['standard_title'], "Title of simpleviewer gallery", no);

	$wp_simpleviewer_options['feed_text'] =__('Here a SimpleViewer Flash gallery should be displayed. Click here to open the post in your browser to see the gallery.', $wp_simpleviewer_textdomain);
	add_option("wp_simpleviewer_feed_text", $wp_simpleviewer_options['feed_text'], "Text to be displayed in feeds for simpleviewer gallery", no);
	
	$wp_simpleviewer_options['images_url'] = WP_CONTENT_URL."/photos/";
	add_option("wp_simpleviewer_images_url", $wp_simpleviewer_options['images_url'], "URL to the images directory for simpleviewer gallery", no);
	
	$wp_simpleviewer_options['images_dir'] = WP_CONTENT_DIR."/photos/";
	add_option("wp_simpleviewer_images_dir", $wp_simpleviewer_options['images_dir'], "Path to the images directory for simpleviewer gallery", no);
	
	$wp_simpleviewer_options['folder_regular'] = "reg";
	add_option("wp_simpleviewer_folder_regular", $wp_simpleviewer_options['folder_regular'], "Prefix for regular images for simpleviewer gallery", no);
	
	$wp_simpleviewer_options['folder_thumbnails'] = "tn";
	add_option("wp_simpleviewer_folder_thumbnails", $wp_simpleviewer_options['folder_thumbnails'], "Prefix for thumbnails for simpleviewer gallery", no);
	
	$wp_simpleviewer_options['max_image_size'] = "300";
	add_option("wp_simpleviewer_max_image_size", $wp_simpleviewer_options['max_image_size'], "Maximum image dimension for simpleviewer gallery", no);
	
	$wp_simpleviewer_options['resizequality'] = "85";
	add_option("wp_simpleviewer_resizequality", $wp_simpleviewer_options['resizequality'], "Image quality when resizing images for simpleviewer gallery", no);
	
	$wp_simpleviewer_options['jsbox'] = "thickbox";
	add_option("wp_simpleviewer_jsbox", $wp_simpleviewer_options['jsbox'], "Usage of Javascript image box for simpleviewer gallery", no);
	
	$wp_simpleviewer_options['show_download_link'] = "jsbox";
	add_option("wp_simpleviewer_show_download_link", $wp_simpleviewer_options['show_download_link'], "Show download links in simpleviewer gallery", no);
	
	$wp_simpleviewer_options['default_download_link_text'] = __('Show image in full size', $wp_simpleviewer_textdomain);
	add_option("wp_simpleviewer_default_download_link_text", $wp_simpleviewer_options['default_download_link_text'], "Default download link text for simpleviewer gallery", no);
	
	$wp_simpleviewer_options['howto'] = "on";
	add_option("wp_simpleviewer_howto", $wp_simpleviewer_options['howto'], "Show howto text on manage page of simpleviewer gallery", no);

	$wp_simpleviewer_options['gallerysort'] = "datedescending";
	add_option("wp_simpleviewer_gallerysort", $wp_simpleviewer_options['gallerysort'], "Show howto text on manage page of simpleviewer gallery", no);

	$wp_simpleviewer_options['imagesort'] = "nameascending";
	add_option("wp_simpleviewer_imagesort", $wp_simpleviewer_options['imagesort'], "Show howto text on manage page of simpleviewer gallery", no);

	//Finally add the version parameter
	add_option("wp_simpleviewer_version_installed", $wp_simpleviewer_version, "Is the WP-SimpleViewer plugin already installed?", no);
	?>
	<div id="message" class="updated fade"><p><strong><?php _e('Plugin has been configured!', $wp_simpleviewer_textdomain) ?></strong></p></div>
	<?php
	return $wp_simpleviewer_options;
} //end function wp_simpleviewer_add_options

function wp_simpleviewer_update_options( $wp_simpleviewer_options ) {
	global $wp_simpleviewer_textdomain;
	update_option('wp_simpleviewer_width', $wp_simpleviewer_options['width']);
	update_option('wp_simpleviewer_height', $wp_simpleviewer_options['height']);
	update_option('wp_simpleviewer_textcolor', $wp_simpleviewer_options['textcolor']);
	update_option('wp_simpleviewer_framecolor', $wp_simpleviewer_options['framecolor']);
	update_option('wp_simpleviewer_bgcolor', $wp_simpleviewer_options['bgcolor']);
	update_option('wp_simpleviewer_framewidth', $wp_simpleviewer_options['framewidth']);
	update_option('wp_simpleviewer_stagepadding', $wp_simpleviewer_options['stagepadding']);
	update_option('wp_simpleviewer_navpadding', $wp_simpleviewer_options['navpadding']);
	update_option('wp_simpleviewer_thumbnailcolumns', $wp_simpleviewer_options['thumbnailcolumns']);
	update_option('wp_simpleviewer_thumbnailrows', $wp_simpleviewer_options['thumbnailrows']);
	update_option('wp_simpleviewer_navposition', $wp_simpleviewer_options['navposition']);
	update_option('wp_simpleviewer_valign', $wp_simpleviewer_options['valign']);
	update_option('wp_simpleviewer_halign', $wp_simpleviewer_options['halign']);
	update_option('wp_simpleviewer_images_url', $wp_simpleviewer_options['images_url']);
	update_option('wp_simpleviewer_images_dir', $wp_simpleviewer_options['images_dir']);
	update_option('wp_simpleviewer_folder_regular', $wp_simpleviewer_options['folder_regular']);
	update_option('wp_simpleviewer_folder_thumbnails', $wp_simpleviewer_options['folder_thumbnails']);
	update_option('wp_simpleviewer_max_image_size', $wp_simpleviewer_options['max_image_size']);
	update_option('wp_simpleviewer_resizequality', $wp_simpleviewer_options['resizequality']);
	update_option('wp_simpleviewer_jsbox', $wp_simpleviewer_options['jsbox']);
	update_option('wp_simpleviewer_show_download_link', $wp_simpleviewer_options['show_download_link']);
	update_option('wp_simpleviewer_default_download_link_text', $wp_simpleviewer_options['default_download_link_text']);
	update_option('wp_simpleviewer_standard_title', $wp_simpleviewer_options['standard_title']);
	update_option('wp_simpleviewer_feed_text', $wp_simpleviewer_options['feed_text']);
	update_option('wp_simpleviewer_howto', $wp_simpleviewer_options['howto']);
	update_option('wp_simpleviewer_gallerysort', $wp_simpleviewer_options['gallerysort']);
	update_option('wp_simpleviewer_imagesort', $wp_simpleviewer_options['imagesort']);
	?>
	<div id="message" class="updated fade"><p><strong><?php _e('Settings saved.') ?></strong></p></div>
	<?php
}	//end function wp_simpleviewer_update_options

function wp_simpleviewer_reset_options() {
	global $wp_simpleviewer_textdomain;
	//ok, so we delete all settings from the DB:
	require(WP_SIMPLEVIEWER_DIR.'/uninstall.php');
	wp_simpleviewer_delete_options();
	?>
	<div id="message" class="updated fade"><p><strong><?php _e('Plugin has been reset!', $wp_simpleviewer_textdomain) ?></strong></p></div>
	
	<div class="wrap">
		<h2><?php _e('Now you can...', $wp_simpleviewer_textdomain) ?></h2> 
		<br class="clear" />
			<?php 
			$plugin_file = attribute_escape($_GET['page']);
			$wp_simpleviewer_plugin_basedir = basename(dirname(__FILE__)); 
			$deactivate_url = "plugins.php?action=deactivate&amp;plugin=".$wp_simpleviewer_plugin_basedir."%2F".$plugin_file;
			$deactivate_url = wp_nonce_url( $deactivate_url , 'deactivate-plugin_'.$wp_simpleviewer_plugin_basedir.'/'.$plugin_file);
			
			echo '<a href="'.$deactivate_url.'">...';
			_e('deactivate the plugin', $wp_simpleviewer_textdomain);
			echo '</a> <a href="options-general.php?page='.$plugin_file.'">';
			_e('or start from scratch.', $wp_simpleviewer_textdomain);
			echo '</a><br /><br />'; ?>
	</div>
	<?php
}	//end function wp_simpleviewer_reset_options

function wp_simpleviewer_get_options() {
	global $wp_simpleviewer_textdomain;
	if ( isset($_POST['wp_simpleviewer_width']) ) {
		$wp_simpleviewer_options['width'] = attribute_escape($_POST['wp_simpleviewer_width']);
	} else {
		$wp_simpleviewer_options['width'] = get_option("wp_simpleviewer_width");
	}
	if ( isset($_POST['wp_simpleviewer_height']) ) {
		$wp_simpleviewer_options['height'] = attribute_escape($_POST['wp_simpleviewer_height']);
	}  else {
		$wp_simpleviewer_options['height'] = get_option("wp_simpleviewer_height");
	}
	if ( isset($_POST['wp_simpleviewer_textcolor']) ) {
		$wp_simpleviewer_options['textcolor'] = substr( attribute_escape($_POST['wp_simpleviewer_textcolor']), 1);
	} else {
		$wp_simpleviewer_options['textcolor'] = get_option("wp_simpleviewer_textcolor");
	}
	if ( isset($_POST['wp_simpleviewer_framecolor']) ) {
		$wp_simpleviewer_options['framecolor'] = substr( attribute_escape($_POST['wp_simpleviewer_framecolor']), 1);
	} else {
		$wp_simpleviewer_options['framecolor'] = get_option("wp_simpleviewer_framecolor");
	}
	if ( isset($_POST['wp_simpleviewer_bgcolor']) ) {
		$wp_simpleviewer_options['bgcolor'] = substr( attribute_escape($_POST['wp_simpleviewer_bgcolor']), 1);
	} else {
		$wp_simpleviewer_options['bgcolor'] = get_option("wp_simpleviewer_bgcolor");
	}
	if ( isset($_POST['wp_simpleviewer_framewidth']) ) {
		$wp_simpleviewer_options['framewidth'] = attribute_escape($_POST['wp_simpleviewer_framewidth']);
	} else {
		$wp_simpleviewer_options['framewidth'] = get_option("wp_simpleviewer_framewidth");
	}
	if ( isset($_POST['wp_simpleviewer_stagepadding']) ) {
		$wp_simpleviewer_options['stagepadding'] = attribute_escape($_POST['wp_simpleviewer_stagepadding']);
	} else {
		$wp_simpleviewer_options['stagepadding'] = get_option("wp_simpleviewer_stagepadding");
	}
	if ( isset($_POST['wp_simpleviewer_navpadding']) ) {
		$wp_simpleviewer_options['navpadding'] = attribute_escape($_POST['wp_simpleviewer_navpadding']);
	} else {
		$wp_simpleviewer_options['navpadding'] = get_option("wp_simpleviewer_navpadding");
	}
	if ( isset($_POST['wp_simpleviewer_thumbnailcolumns']) ) {
		$wp_simpleviewer_options['thumbnailcolumns'] = attribute_escape($_POST['wp_simpleviewer_thumbnailcolumns']);
	} else {
		$wp_simpleviewer_options['thumbnailcolumns'] = get_option("wp_simpleviewer_thumbnailcolumns");
	}
	if ( isset($_POST['wp_simpleviewer_thumbnailrows']) ) {
		$wp_simpleviewer_options['thumbnailrows'] = attribute_escape($_POST['wp_simpleviewer_thumbnailrows']);
	} else {
		$wp_simpleviewer_options['thumbnailrows'] = get_option("wp_simpleviewer_thumbnailrows");
	}
	if ( isset($_POST['wp_simpleviewer_navposition']) ) {
		$wp_simpleviewer_options['navposition'] = attribute_escape($_POST['wp_simpleviewer_navposition']);
	} else {
		$wp_simpleviewer_options['navposition'] = get_option("wp_simpleviewer_navposition");
	}
	if ( isset($_POST['wp_simpleviewer_valign']) ) {
		$wp_simpleviewer_options['valign'] = attribute_escape($_POST['wp_simpleviewer_valign']);
	} else {
		$wp_simpleviewer_options['valign'] = get_option("wp_simpleviewer_valign");
	}
	if ( isset($_POST['wp_simpleviewer_halign']) ) {
		$wp_simpleviewer_options['halign'] = attribute_escape($_POST['wp_simpleviewer_halign']);
	} else {
		$wp_simpleviewer_options['halign'] = get_option("wp_simpleviewer_halign");
	}
	if ( isset($_POST['wp_simpleviewer_standard_title']) ) {
		$wp_simpleviewer_options['standard_title'] = attribute_escape( wp_specialchars( stripslashes($_POST['wp_simpleviewer_standard_title']), 1 ));
	}  else {
		$wp_simpleviewer_options['standard_title'] = get_option("wp_simpleviewer_standard_title");
	}
	if ( isset($_POST['wp_simpleviewer_feed_text']) ) {
		$wp_simpleviewer_options['feed_text'] = attribute_escape($_POST['wp_simpleviewer_feed_text']);
	}  else {
		$wp_simpleviewer_options['feed_text'] = get_option("wp_simpleviewer_feed_text");
	}
	if ( isset($_POST['wp_simpleviewer_max_image_size']) ) {
		$wp_simpleviewer_options['max_image_size'] = attribute_escape($_POST['wp_simpleviewer_max_image_size']);
	} else {
		$wp_simpleviewer_options['max_image_size'] = get_option("wp_simpleviewer_max_image_size");
	}
	if ( isset($_POST['wp_simpleviewer_resizequality']) ) {
		$wp_simpleviewer_options['resizequality'] = attribute_escape($_POST['wp_simpleviewer_resizequality']);
	} else {
		$wp_simpleviewer_options['resizequality'] = get_option("wp_simpleviewer_resizequality");
	}
	if ( isset($_POST['wp_simpleviewer_jsbox']) ) {
		$wp_simpleviewer_options['jsbox'] = attribute_escape($_POST['wp_simpleviewer_jsbox']);
	} else {
		$wp_simpleviewer_options['jsbox'] = get_option("wp_simpleviewer_jsbox");
	}
	if ( isset($_POST['wp_simpleviewer_show_download_link']) ) {
		$wp_simpleviewer_options['show_download_link'] = attribute_escape($_POST['wp_simpleviewer_show_download_link']);
			if ( $wp_simpleviewer_options['show_download_link'] == 'jsbox' && $wp_simpleviewer_options['jsbox'] == 'off' ) {
			$wp_simpleviewer_options['show_download_link'] = 'true';
			?>
			<div id="message" class="updated fade"><p><strong><?php _e('You can only use Thickbox links if Thickbox is enabled.', $wp_simpleviewer_textdomain) ?></strong></p></div>
			<?php
		}
	} else {
		$wp_simpleviewer_options['show_download_link'] = get_option("wp_simpleviewer_show_download_link");
	}
	if ( isset($_POST['wp_simpleviewer_default_download_link_text']) ) {
		$wp_simpleviewer_options['default_download_link_text'] = attribute_escape( wp_specialchars( stripslashes($_POST['wp_simpleviewer_default_download_link_text']), 1));
	} else {
		$wp_simpleviewer_options['default_download_link_text'] = get_option("wp_simpleviewer_default_download_link_text");
	}
	if ( isset($_POST['wp_simpleviewer_folder_regular']) ) {
		$wp_simpleviewer_options['folder_regular'] = attribute_escape($_POST['wp_simpleviewer_folder_regular']);
	} else {
		$wp_simpleviewer_options['folder_regular'] = get_option("wp_simpleviewer_folder_regular");
	}
	if ( isset($_POST['wp_simpleviewer_folder_thumbnails']) ) {
		$wp_simpleviewer_options['folder_thumbnails'] = attribute_escape($_POST['wp_simpleviewer_folder_thumbnails']);
	} else {
		$wp_simpleviewer_options['folder_thumbnails'] = get_option("wp_simpleviewer_folder_thumbnails");
	}
	if ( isset($_POST['wp_simpleviewer_images_url']) ) {
		$wp_simpleviewer_options['images_url'] = attribute_escape($_POST['wp_simpleviewer_images_url']);
		$wp_simpleviewer_options['images_url'] = verify_directory_name($wp_simpleviewer_options['images_url']);
	} else {
		$wp_simpleviewer_options['images_url'] = get_option("wp_simpleviewer_images_url");
	}
	if ( isset($_POST['wp_simpleviewer_images_dir']) ) {
		$wp_simpleviewer_options['images_dir'] = attribute_escape($_POST['wp_simpleviewer_images_dir']);
		$wp_simpleviewer_options['images_dir'] = verify_directory_name($wp_simpleviewer_options['images_dir']);
	} else {
		$wp_simpleviewer_options['images_dir'] = get_option("wp_simpleviewer_images_dir");
	}
	if ( isset($_POST['wp_simpleviewer_howto']) ) {
		$wp_simpleviewer_options['howto'] = "on";
	} elseif (isset($_POST['wp_simpleviewer_form_option'])) {
		$wp_simpleviewer_options['howto'] = "off";
	} else {
		$wp_simpleviewer_options['howto'] = get_option("wp_simpleviewer_howto");
	}
	if ( isset($_POST['wp_simpleviewer_gallerysort']) ) {
		$wp_simpleviewer_options['gallerysort'] = attribute_escape($_POST['wp_simpleviewer_gallerysort']);
	} elseif ( isset($_GET['wp_simpleviewer_gallerysort']) ) {
		$wp_simpleviewer_options['gallerysort'] = attribute_escape($_GET['wp_simpleviewer_gallerysort']);
	} else {
		$wp_simpleviewer_options['gallerysort'] = get_option("wp_simpleviewer_gallerysort");
	}
	if ( isset($_POST['wp_simpleviewer_imagesort']) ) {
		$wp_simpleviewer_options['imagesort'] = attribute_escape($_POST['wp_simpleviewer_imagesort']);
	} else {
		$wp_simpleviewer_options['imagesort'] = get_option("wp_simpleviewer_imagesort");
	}

	return $wp_simpleviewer_options;
} // end function wp_simpleviewer_get_options

function wp_simpleviewer_merge_options_and_array($wp_simpleviewer_options, $wp_simpleviewer_gallery_array) {
	$wp_simpleviewer_options['max_image_size'] = $wp_simpleviewer_gallery_array["simpleviewerGallery"]["_a"]["maxImageWidth"];
	$wp_simpleviewer_options['textcolor'] = substr($wp_simpleviewer_gallery_array["simpleviewerGallery"]["_a"]["textColor"], 2);
	$wp_simpleviewer_options['framecolor'] = substr($wp_simpleviewer_gallery_array["simpleviewerGallery"]["_a"]["frameColor"], 2);
	$wp_simpleviewer_options['framewidth'] = $wp_simpleviewer_gallery_array["simpleviewerGallery"]["_a"]["frameWidth"];
	$wp_simpleviewer_options['stagepadding'] = $wp_simpleviewer_gallery_array["simpleviewerGallery"]["_a"]["stagePadding"];
	$wp_simpleviewer_options['navpadding'] = $wp_simpleviewer_gallery_array["simpleviewerGallery"]["_a"]["navPadding"];
	$wp_simpleviewer_options['thumbnailcolumns'] = $wp_simpleviewer_gallery_array["simpleviewerGallery"]["_a"]["thumbnailColumns"];
	$wp_simpleviewer_options['thumbnailrows'] = $wp_simpleviewer_gallery_array["simpleviewerGallery"]["_a"]["thumbnailRows"];
	$wp_simpleviewer_options['navposition'] = $wp_simpleviewer_gallery_array["simpleviewerGallery"]["_a"]["navPosition"];
	$wp_simpleviewer_options['valign'] = $wp_simpleviewer_gallery_array["simpleviewerGallery"]["_a"]["vAlign"];
	$wp_simpleviewer_options['halign'] = $wp_simpleviewer_gallery_array["simpleviewerGallery"]["_a"]["hAlign"];
	$wp_simpleviewer_options['standard_title'] = wp_specialchars($wp_simpleviewer_gallery_array["simpleviewerGallery"]["_a"]["title"], 1);
	$wp_simpleviewer_options['width'] = $wp_simpleviewer_gallery_array["simpleviewerGallery"]["_a"]["wpSimpleviewerWidth"];
	$wp_simpleviewer_options['height'] = $wp_simpleviewer_gallery_array["simpleviewerGallery"]["_a"]["wpSimpleviewerHeight"];
	$wp_simpleviewer_options['bgcolor'] = $wp_simpleviewer_gallery_array["simpleviewerGallery"]["_a"]["wpSimpleviewerBackgroundColor"];
	return $wp_simpleviewer_options;
}	//end function wp_simpleviewer_merge_options_and_array

function wp_simpleviewer_gallery_array_from_options( &$wp_simpleviewer_options, $wp_simpleviewer_gallery_url_regular, $wp_simpleviewer_gallery_url_thumbnails ){
	if ($wp_simpleviewer_options['jsbox']=="off" && $wp_simpleviewer_options['show_download_link']=="true")
		$wp_simpleviewer_options['enableRightClickOpen'] = "true"; 
	else
		$wp_simpleviewer_options['enableRightClickOpen'] = "false"; 
	return array( "simpleviewerGallery" => array( 
		"_a" => array(
			"maxImageWidth" => $wp_simpleviewer_options['max_image_size'],
			"maxImageHeight" => $wp_simpleviewer_options['max_image_size'],
			"textColor" => "0x".$wp_simpleviewer_options['textcolor'],
			"frameColor" => "0x".$wp_simpleviewer_options['framecolor'],
			"frameWidth" => $wp_simpleviewer_options['framewidth'],
			"stagePadding" => $wp_simpleviewer_options['stagepadding'],
			"navPadding" => $wp_simpleviewer_options['navpadding'],
			"thumbnailColumns" => $wp_simpleviewer_options['thumbnailcolumns'],
			"thumbnailRows" => $wp_simpleviewer_options['thumbnailrows'],
			"navPosition" => $wp_simpleviewer_options['navposition'],
			"vAlign" => $wp_simpleviewer_options['valign'],
			"hAlign" => $wp_simpleviewer_options['halign'],
			"enableRightClickOpen" => $wp_simpleviewer_options['enableRightClickOpen'],
			"title" => $wp_simpleviewer_options['standard_title'],
			"imagePath" =>  $wp_simpleviewer_gallery_url_regular,
			"thumbPath" => $wp_simpleviewer_gallery_url_thumbnails,
			"wpSimpleviewerWidth" => $wp_simpleviewer_options['width'],
			"wpSimpleviewerHeight" => $wp_simpleviewer_options['height'],
			"wpSimpleviewerBackgroundColor" => $wp_simpleviewer_options['bgcolor']
		), "_c" => array(
			"image" => array()
		)
	));	
}	//end function wp_simpleviewer_gallery_array_from_options

function wp_simpleviewer_update_check() {
	global $wp_simpleviewer_version;
	$wp_simpleviewer_version_installed = get_option('wp_simpleviewer_version_installed');
	if ( $wp_simpleviewer_version_installed && $wp_simpleviewer_version_installed != $wp_simpleviewer_version ) {	//check if update of DB is needed
		wp_simpleviewer_update_db( $wp_simpleviewer_version_installed );
	} 
}	//end function wp_simpleviewer_update_check

function wp_simpleviewer_update_db( $wp_simpleviewer_db_version ) {
	global $wp_simpleviewer_textdomain;
	global $wp_simpleviewer_version;
	
	if ( $wp_simpleviewer_db_version == '0.4' ) {
		delete_option("wp_simpleviewer_align");
		wp_simpleviewer_update_db( '0.5' );
	} elseif ( $wp_simpleviewer_db_version == '0.5' ) {
		delete_option("wp_simpleviewer_thumbnailsize");
		wp_simpleviewer_update_db( '1.0' );
	} elseif ( $wp_simpleviewer_db_version == '1.0' ) {
		delete_option("wp_simpleviewer_plugin_basedir");
		wp_simpleviewer_update_db( '1.1' );
	} elseif ( $wp_simpleviewer_db_version == '1.1' ) {
		//New thickbox settings
		$wp_simpleviewer_options['show_download_link'] = get_option("wp_simpleviewer_show_download_link");
		if ( $wp_simpleviewer_options['show_download_link'] == "true" ) {
			$wp_simpleviewer_options['show_download_link'] = "jsbox";
			update_option('wp_simpleviewer_show_download_link', $wp_simpleviewer_options['show_download_link']);
			$wp_simpleviewer_options['jsbox'] = "thickbox";
		} else {
			$wp_simpleviewer_options['jsbox'] = "off";
		}
		add_option("wp_simpleviewer_jsbox", $wp_simpleviewer_options['jsbox'], "Usage of Javascript image box for simpleviewer gallery", no);
		delete_option("wp_simpleviewer_right_click_menu");
		wp_simpleviewer_update_db( '1.2' );
	} elseif ( $wp_simpleviewer_db_version == '1.2' ) {
		//New URL & path to image folder 
		$wp_simpleviewer_options['images_basedir'] = get_option("wp_simpleviewer_images_basedir");
		add_option("wp_simpleviewer_images_url", get_option('siteurl')."/".$wp_simpleviewer_options['images_basedir'], "URL to the images directory for simpleviewer gallery", no);
		add_option("wp_simpleviewer_images_dir", ABSPATH.$wp_simpleviewer_options['images_basedir'], "Path to the images directory for simpleviewer gallery", no);
		delete_option("wp_simpleviewer_images_basedir");
		//SimpleViewer 1.8.5 features
		add_option("wp_simpleviewer_navpadding", "40", "Navigationpadding of simpleviewer gallery", no);
		add_option("wp_simpleviewer_valign", "center", "Vertical placement of the image and thumbnails of simpleviewer gallery", no);
		add_option("wp_simpleviewer_halign", "center", "Horizontal placement of the image and thumbnails of simpleviewer gallery", no);
		//Feed text & howto
		add_option("wp_simpleviewer_feed_text", __('Here a SimpleViewer Flash gallery should be displayed. Click here to open the post in your browser to see the gallery.', $wp_simpleviewer_textdomain), "Text to be displayed in feeds for simpleviewer gallery", no);
		add_option("wp_simpleviewer_howto", "on", "Show howto text on manage page of simpleviewer gallery", no);
		wp_simpleviewer_update_db( '1.3' );
	} elseif ( $wp_simpleviewer_db_version == '1.3' ) {
		//Gallery and image sort options 
		add_option("wp_simpleviewer_gallerysort", "datedescending", "Show howto text on manage page of simpleviewer gallery", no);
		add_option("wp_simpleviewer_imagesort", "nameascending", "Show howto text on manage page of simpleviewer gallery", no);
		wp_simpleviewer_update_db( '1.4' );
	} elseif ( $wp_simpleviewer_db_version == $wp_simpleviewer_version ) {	//End of recursion
		update_option('wp_simpleviewer_version_installed', $wp_simpleviewer_version);
		?>
		<div id="message" class="updated fade"><p><strong><?php _e('Database has been updated for the new plugin version!', $wp_simpleviewer_textdomain) ?></strong></p></div>
		<?php
	} 
}	//end function wp_simpleviewer_update_db

?>