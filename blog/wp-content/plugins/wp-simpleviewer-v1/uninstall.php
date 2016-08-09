<?php
//////////////////////////////////////////////////////////////////////
// This file contains all functions used to uninstall the plugin    //
//////////////////////////////////////////////////////////////////////

function wp_simpleviewer_delete_options(){
	delete_option("wp_simpleviewer_width");
	delete_option("wp_simpleviewer_height");
	delete_option("wp_simpleviewer_textcolor");
	delete_option("wp_simpleviewer_framecolor");
	delete_option("wp_simpleviewer_bgcolor");
	delete_option("wp_simpleviewer_framewidth");
	delete_option("wp_simpleviewer_stagepadding");
	delete_option("wp_simpleviewer_navpadding");
	delete_option("wp_simpleviewer_thumbnailcolumns");
	delete_option("wp_simpleviewer_thumbnailrows");
	delete_option("wp_simpleviewer_navposition");
	delete_option("wp_simpleviewer_valign");
	delete_option("wp_simpleviewer_halign");
	delete_option("wp_simpleviewer_standard_title");
	delete_option("wp_simpleviewer_feed_text");
	delete_option("wp_simpleviewer_images_url");
	delete_option("wp_simpleviewer_images_dir");
	delete_option("wp_simpleviewer_folder_regular");
	delete_option("wp_simpleviewer_folder_thumbnails");
	delete_option("wp_simpleviewer_max_image_size");
	delete_option("wp_simpleviewer_resizequality");
	delete_option("wp_simpleviewer_jsbox");
	delete_option("wp_simpleviewer_show_download_link");
	delete_option("wp_simpleviewer_default_download_link_text");
	delete_option("wp_simpleviewer_version_installed");
	delete_option("wp_simpleviewer_howto");
}

if( !defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') ) {
	exit();
} else {
	wp_simpleviewer_delete_options();
}
?>