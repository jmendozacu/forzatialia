<?php
/*
Plugin Name: WP-SimpleViewer-v1
Plugin URI: http://wp-simpleviewer.fuggi82.de
Description: Include the <a href="http://www.airtightinteractive.com/simpleviewer" target="_blank">SimpleViewer</a> Flash gallery into WordPress.
Version: 1.5.4
Author: fuggi
Author URI: http://wp-simpleviewer.fuggi82.de
*/

/*  Copyright 2007  Markus F. ( wp-simpleviewer@fuggi82.de )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307USA
*/

/*  Permission handling or which user is allowed to do what?

	Capability          Role                        ...is allowed to?
	manage_options      admin                       Manage the default parameters (the options page)
	edit_posts          contributor and all above   Create new XML files
	edit_others_posts   editor & admin              Create new and recreate existing XML files

*/
// Set global variables
global $wp_simpleviewer_version;
       $wp_simpleviewer_version='1.4';
global $wp_simpleviewer_nonce;
       $wp_simpleviewer_nonce='wp-simpleviewer-option_';
global $wp_simpleviewer_textdomain;
       $wp_simpleviewer_textdomain='wp-simpleviewer';

//Define plugin directories
define( 'WP_SIMPLEVIEWER_URL', WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)) );
define( 'WP_SIMPLEVIEWER_DIR', WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)) );

// Apply actions to filters and hooks
if ( !is_admin() ){	//Frontend
	//Works with WP 2.8 & lower, to be replaced with next line in future versions: XXX
	add_action('wp_print_scripts', 'wp_simpleviewer_enqueue_scripts_frontend');	// Java scripts
	//Works in WP 2.8 only: 
	//add_action('wp_enqueue_scripts', 'wp_simpleviewer_enqueue_scripts_frontend');	// Java scripts
	add_action('wp_footer', 'wp_simpleviewer_footer_frontend');					// JSBox Javascript & CSS
	add_shortcode('svgallery', 'svgallery_shortcode');					// The plugins shortcode
	add_filter('the_content', 'wp_simpleviewer_shortcode_replacer', 9);	// Replaces deprecated svgallery= with shortcode
	if (isset($_GET['FullScreenGallery'])) {									// If a full page gallery should be shown
		add_action('init', 'wp_simpleviewer_fullpage_gallery');
	}
} else {	//Backend
	global $wp_simpleviewer_textdomain;
	wp_simpleviewer_load_textdomain();
	
	require(WP_SIMPLEVIEWER_DIR.'/wp-simpleviewer-mediatab.php');	// Load code that adds the media tab
	
	add_action('admin_menu',  'wp_simpleviewer_add_admin_pages');	// Admin backend pages
	add_filter('admin_footer', 'wp_simpleviewer_quicktag_loader');	// Text Editor Button
	add_filter('favorite_actions', 'wp_simpleviewer_favorite_action');	//Favorite actions shortcut
	//Works in WP 2.8 only: 
	//add_action('admin_enqueue_scripts', 'wp_simpleviewer_enqueue_scripts_admin'); // Loads TableDnD & Farbtastic
}

function wp_simpleviewer_add_admin_pages() {
	//WP 2.8 only: 
	//add_options_page(__('WP-SimpleViewer', $wp_simpleviewer_textdomain), __('WP-SimpleViewer', $wp_simpleviewer_textdomain), 'manage_options', basename(__FILE__), 'wp_simpleviewer_show_options_page');
	//add_media_page(__('WP-SimpleViewer Galleries', $wp_simpleviewer_textdomain), __('WP-SV Galleries', $wp_simpleviewer_textdomain), 'edit_posts', basename(__FILE__), 'wp_simpleviewer_show_manage_page');
	//WP <=2.8
	$wp_simpleviewer_options_page = add_options_page(__('WP-SimpleViewer', $wp_simpleviewer_textdomain), __('WP-SimpleViewer', $wp_simpleviewer_textdomain), 'manage_options', basename(__FILE__), 'wp_simpleviewer_show_options_page');
	$wp_simpleviewer_management_page = add_media_page(__('WP-SimpleViewer Galleries', $wp_simpleviewer_textdomain), __('WP-SV Galleries', $wp_simpleviewer_textdomain), 'edit_posts', basename(__FILE__), 'wp_simpleviewer_show_manage_page');
	add_action('admin_print_scripts-'.$wp_simpleviewer_options_page, 'wp_simpleviewer_print_admin_scripts' );	// Loads TableDnD & Farbtastic
	add_action('admin_print_scripts-'.$wp_simpleviewer_management_page, 'wp_simpleviewer_print_admin_scripts' );	// Loads TableDnD & Farbtastic
	add_action('admin_head-'.$wp_simpleviewer_options_page, 'wp_simpleviewer_print_admin_css' );	// Loads Farbtastic CSS
	add_action('admin_head-'.$wp_simpleviewer_management_page, 'wp_simpleviewer_print_admin_css' );	// Loads Farbtastic CSS
	//End WP <=2.8
}

// To keep this file small the admin functions will only be loaded on demand
function wp_simpleviewer_admin_init() {
	require(WP_SIMPLEVIEWER_DIR.'/wp-simpleviewer-admin.php');
	wp_simpleviewer_update_check();
}
// These functions will load the admincode and show the admin pages
function wp_simpleviewer_show_options_page() {
	wp_simpleviewer_admin_init();
	wp_simpleviewer_options_page();
}
function wp_simpleviewer_show_manage_page() {
	wp_simpleviewer_admin_init();
	wp_simpleviewer_manage_page();
}
function wp_simpleviewer_quicktag_loader() {
	if (strpos($_SERVER['REQUEST_URI'], 'post.php') || strpos($_SERVER['REQUEST_URI'], 'page.php') || strpos($_SERVER['REQUEST_URI'], 'post-new.php') || strpos($_SERVER['REQUEST_URI'], 'page-new.php') ) {
		wp_simpleviewer_admin_init();
		wp_simpleviewer_quicktag();
	}
}
function wp_simpleviewer_favorite_action($actions){
	global $wp_simpleviewer_textdomain;
	$actions['upload.php?page=wp-simpleviewer.php'] = array(__('WP-SV Galleries', $wp_simpleviewer_textdomain), 'edit_posts');
	return $actions;
}
// End admin loader functions...the next function might already be required in the frontend:
function wp_simpleviewer_load_textdomain(){
	global $wp_simpleviewer_textdomain;
	load_plugin_textdomain($wp_simpleviewer_textdomain, false, dirname(plugin_basename(__FILE__)) . '/languages' );
}

/* XXX WP 2.8 only:
function wp_simpleviewer_enqueue_scripts_admin($hook_suffix) {
	global $wp_simpleviewer_version;
	global $wp_simpleviewer_textdomain;
	if (strpos($hook_suffix, 'wp-simpleviewer')) {
		wp_register_script('jquery-tablednd', WP_SIMPLEVIEWER_URL.'/js/jquery.tablednd.js', array('jquery'), '0.5');
		wp_register_script('farbtastic', WP_SIMPLEVIEWER_URL.'/js/farbtastic/farbtastic.js', array('jquery'), '1.2');
		wp_register_script('wp_simpleviewer_admin', WP_SIMPLEVIEWER_URL.'/js/wp_simpleviewer_admin.js', array('jquery-tablednd', 'farbtastic'), $wp_simpleviewer_version);
		wp_enqueue_script('wp_simpleviewer_admin');
		
		wp_register_style('farbtastic', WP_SIMPLEVIEWER_URL.'/js/farbtastic/farbtastic.css', false, '1.2');
		wp_enqueue_style('farbtastic');
		
		echo "
		<script type='text/javascript'> 
			var wp_simpleviewer_default_download_link_text='".__('Show image in full size', $wp_simpleviewer_textdomain)."'; 
			var wp_simpleviewer_download_link_use_filename='".__('Filenames should be used for the captions', $wp_simpleviewer_textdomain)."'; 
		</script>\n";
	}
}	//end function wp_simpleviewer_enqueue_scripts_admin
END WP 2.8 only */ 

// WP <= 2.8
function wp_simpleviewer_print_admin_scripts() {
	global $wp_simpleviewer_version;
	wp_register_script('jquery-tablednd', WP_SIMPLEVIEWER_URL.'/js/jquery.tablednd.js', array('jquery'), '0.5');
	wp_register_script('farbtastic', WP_SIMPLEVIEWER_URL.'/js/farbtastic/farbtastic.js', array('jquery'), '1.2');
	wp_register_script('wp_simpleviewer_admin', WP_SIMPLEVIEWER_URL.'/js/wp_simpleviewer_admin.js', array('jquery-tablednd', 'farbtastic'), $wp_simpleviewer_version);
	wp_enqueue_script('wp_simpleviewer_admin');	
}	//end function wp_simpleviewer_print_admin_scripts
function wp_simpleviewer_print_admin_css() {
	global $wp_simpleviewer_textdomain;
	echo "
	<style type='text/css' media='all'>
		@import '".WP_SIMPLEVIEWER_URL."/js/farbtastic/farbtastic.css';
	</style>
	<script type='text/javascript'> 
		var wp_simpleviewer_default_download_link_text='".__('Show image in full size', $wp_simpleviewer_textdomain)."'; 
		var wp_simpleviewer_download_link_use_filename='".__('Filenames should be used for the captions', $wp_simpleviewer_textdomain)."'; 
	</script>\n";
}
//END WP <= 2.8

//Now the template functions start
function wp_simpleviewer_enqueue_scripts_frontend() {
	wp_register_script('swfobject', WP_SIMPLEVIEWER_URL.'/js/swfobject/swfobject.js', false, '2.1');
	wp_enqueue_script('swfobject');
	
	// Load jsbox script if it is enabled
	$wp_simpleviewer_options['jsbox'] = get_option("wp_simpleviewer_jsbox");
	if ($wp_simpleviewer_options['jsbox'] == 'thickbox') {
		add_thickbox();
	}
}	//end function wp_simpleviewer_enqueue_scripts_frontend

function wp_simpleviewer_footer_frontend(){
	// Load jsbox script if it is enabled
	$wp_simpleviewer_options['jsbox'] = get_option("wp_simpleviewer_jsbox");
	
	if ($wp_simpleviewer_options['jsbox'] == 'thickbox') {
		if ( !function_exists('adjacent_post_rel_link') ) {		//XXX WP < 2.8, can be removed when plugin is only WP 2.8 compatible
			echo "
			<style type='text/css' media='all'>
				@import '".get_option('siteurl')."/wp-includes/js/thickbox/thickbox.css?1'; 
				object { outline:none; }
			</style>
			<script type='text/javascript'> 
				var tb_pathToImage='".get_option('siteurl')."/wp-includes/js/thickbox/loadingAnimation.gif'; 
				var tb_closeImage ='".get_option('siteurl')."/wp-includes/js/thickbox/tb-close.png';
				function jsbx(url,caption,imageGroup) { tb_show(caption,url,imageGroup); }
			</script>\n";
		} else {	//XXX WP 2.8
			echo "
			<style type='text/css' media='all'>
				object { outline:none; }
			</style>
			<script type='text/javascript'> 
				var tb_pathToImage='".get_option('siteurl')."/wp-includes/js/thickbox/loadingAnimation.gif'; 
				var tb_closeImage ='".get_option('siteurl')."/wp-includes/js/thickbox/tb-close.png';
				function jsbx(url,caption,imageGroup) { tb_show(caption,url,imageGroup); }
			</script>\n";
		}
	}
}	//end function wp_simpleviewer_footer_frontend

function wp_simpleviewer_fullpage_gallery() {
	$galleryname = attribute_escape($_GET['FullScreenGallery']); 	
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
	<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<title><?php bloginfo('name'); echo ' - Gallery:'; echo ' '.$galleryname; ?></title>
	<?php do_action('admin_print_scripts'); ?>
	<style type="text/css">	
	/* hide from ie on mac \*/
	html {
		height: 100%;
		overflow: hidden;
	}
	div {
		height: 100%;
	}
	/* end hide */
	body {
		height: 100%;
		margin: 0;
		padding: 0;
		background-color: #181818;
		color:#ffffff;
	}
	</style>
	</head>
	<body>
	<?php echo wp_simpleviewer($galleryname, true); ?>
	</body>
	</html>
	<?php
	//Stop doing anything within Wordpress:
	exit();
}	//end function wp_simpleviewer_fullpage_gallery()

function svgallery_shortcode($options){
	if ( isset($options['name']) && isset($options['link']) ){
		return '<a href="'.get_option('siteurl').'/?FullScreenGallery='.attribute_escape($options['name']).'&keepThis=true&TB_iframe=true&height=700&width=900" class="thickbox wp-simpleviewer_gallery_link">'.attribute_escape($options['link']).'</a>';
	} elseif ( isset($options['name']) ){
		return wp_simpleviewer($options['name']);
	} else {
		return __('The svgallery shortcode has an invalid format. It has to look like this:', $wp_simpleviewer_textdomain).' [svgallery name="galleryname"]';
	}
}	//end function svgallery_shortcode

function wp_simpleviewer($wp_simpleviewer_gallery_name, $wp_simpleviewer_fullscreen=false ) {
	global $wp_simpleviewer_textdomain;
	if (!is_feed()){
		//get wp-simpleviewer parameters:
		$wp_simpleviewer_options['images_url'] = get_option("wp_simpleviewer_images_url");
		$wp_simpleviewer_options['images_dir'] = get_option("wp_simpleviewer_images_dir");
		$wp_simpleviewer_options['jsbox'] = get_option("wp_simpleviewer_jsbox");
		$wp_simpleviewer_xmlfile_url =  $wp_simpleviewer_options['images_url'].$wp_simpleviewer_gallery_name.'/gallery.xml';
		$wp_simpleviewer_xmlfile_dir =  $wp_simpleviewer_options['images_dir'].$wp_simpleviewer_gallery_name.'/gallery.xml';
		
		//read the xml file and get the variables, if this fails wp_simpleviewer_xml_file_data is a boolean:
		$wp_simpleviewer_xml_file_data = @file_get_contents( $wp_simpleviewer_xmlfile_dir );
		if ( is_bool($wp_simpleviewer_xml_file_data) ) {	
			wp_simpleviewer_load_textdomain();
			$wp_simpleviewer_gallery_output = __('I cannot read the gallery\'s xml file: ', $wp_simpleviewer_textdomain).$wp_simpleviewer_xmlfile_dir.' <br />'.__('Please check that the gallery\'s files have been created on the admin pages!', $wp_simpleviewer_textdomain).'<br />';
		} else {
			if ( preg_match('/wpSimpleviewerWidth="([01-9\%]*)" wpSimpleviewerHeight="([01-9\%]*)" wpSimpleviewerBackgroundColor="([01-9a-fA-F]*)"/is', $wp_simpleviewer_xml_file_data, $wp_simpleviewer_gallerydata) ){
				$wp_simpleviewer_options['width'] = $wp_simpleviewer_gallerydata[1];
				$wp_simpleviewer_options['height'] = $wp_simpleviewer_gallerydata[2];
				$wp_simpleviewer_options['bgcolor'] = $wp_simpleviewer_gallerydata[3];
			} else {
				$wp_simpleviewer_options['width'] = get_option("wp_simpleviewer_width");
				$wp_simpleviewer_options['height'] = get_option("wp_simpleviewer_height");
				$wp_simpleviewer_options['bgcolor'] = get_option("wp_simpleviewer_bgcolor");
			}
			if ( $wp_simpleviewer_fullscreen ) {
				$wp_simpleviewer_options['width'] = '100%';
				$wp_simpleviewer_options['height'] = '100%';
			}
			//this variable will be used for the name of the <div>
			$flashcontent = 'fc_id_'.mt_rand(0,1000);
			
			$wp_simpleviewer_gallery_output = '
			<div id="'.$flashcontent.'">
			This SimpleViewer gallery requires Macromedia Flash. Please open this post in your browser or get Macromedia Flash <a href="http://www.macromedia.com/go/getflashplayer/">here</a>.
			<br />
			This is a <a href="http://wp-simpleviewer.fuggi82.de">WPSimpleViewerGallery</a>
			</div>
			<script type="text/javascript">
				var flashvars = {};
				flashvars.preloaderColor = "0xffffff";
				flashvars.xmlDataPath = "'.$wp_simpleviewer_xmlfile_url.'?for='.$flashcontent.'";
				var params = {};
				params.bgcolor = "#'.$wp_simpleviewer_options['bgcolor'].'";'; 
			if ($wp_simpleviewer_options['jsbox'] != 'off') {
				$wp_simpleviewer_gallery_output .= '
				params.allowscriptaccess = "samedomain";
				params.wmode = "opaque";'; 
			}
			$wp_simpleviewer_gallery_output .= '
				var attributes = {};
				swfobject.embedSWF("'.WP_SIMPLEVIEWER_URL.'/viewer.swf", "'.$flashcontent.'", "'.$wp_simpleviewer_options['width'].'", "'.$wp_simpleviewer_options['height'].'", "8", "'.WP_SIMPLEVIEWER_URL.'/js/swfobject/expressInstall.swf", flashvars, params, attributes);
			</script>';
		}	//end if $wp_simpleviewer_xml_file_data == ''
	} else {	//if (!is_feed())
		$wp_simpleviewer_gallery_output = '<p><a href="'.get_permalink().'" title="Permanent Link to '.get_the_title_rss().'">'.get_option("wp_simpleviewer_feed_text").'</a></p>';
	}	//end if (!is_feed())
	return $wp_simpleviewer_gallery_output;
}

function wp_simpleviewer_shortcode_replacer($content) {
	$count = preg_match_all('/svgallery\=([-_a-zA-Z01-9]*)/i', $content, $matches);
	if ($count > 0) {	//There should be a gallery on this page
		for ($i = 0; $i < $count; $i++) {
			$galleryname = attribute_escape($matches[1][$i]);
			$wp_simpleviewer_shortcode = '[svgallery name="'.$galleryname.'"]';
			$content = preg_replace( '/svgallery\='.$matches[1][$i].'/i', $wp_simpleviewer_shortcode, $content, 1);	
		}	//end for i
	}	//end if count > 0
	return $content;
}
?>