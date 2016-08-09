<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


function fpd_admin_get_view_list_item($id, $title, $thumbnail) {

	return '<li data-id="'.$id.'"><h3 class="fpd-clearfix"><img src="'.$thumbnail.'" /><span>'.$title.'</span><a href="#" class="button fpd-remove-view">'. __( 'Remove', 'radykal' ).'</a><a href="#" class="button fpd-duplicate-view">'. __( 'Duplicate', 'radykal' ).'</a><a href="#" class="button fpd-edit-view">'.__( 'Edit', 'radykal' ).'</a><a href="'.(admin_url().'edit.php?post_type=product&page=fancy_products&view_id='.$id.'').'" class="button">'.__( 'Edit Elements', 'radykal' ).'</a></h3></li>';

}

function fpd_admin_display_version_info() {

		echo '<p class="fpd-version description" style="float: right; margin-top: 8px;">';

		if( false === ( $fpd_version = get_transient( 'fpd_version' ) )) {

			$version_str = fpd_admin_get_file_content("http://assets.radykal.de/fpd/version.json");
			if($version_str !== false) {
				$json = json_decode($version_str, true);
				$current_version = $json['version'];

				set_transient('fpd_version', $current_version, HOUR_IN_SECONDS);
			}

		}
		else {

			$current_version = $fpd_version;
			delete_transient('fpd_version');

		}

		if(Fancy_Product_Designer::VERSION < $current_version) {

			_e('You are not using the <a href="http://fancyproductdesigner.com/woocommerce-plugin/documentation/changelog/" target="_blank">latest version</a> of Fancy Product Designer. Please go to your <a href="http://codecanyon.net/downloads" target="_blank">downloads tab on codecanyon</a> and download it again. Read also the <a href="http://fancyproductdesigner.com/woocommerce-plugin/documentation/upgrading/" target="_blank">upgrading documentation</a> how to install a new version.', 'radykal');

		}
		else {

			_e('You are using the latest version of Fancy Product Designer: ', 'radykal');
			echo '<strong>'.$current_version.'</strong>';

		}

		echo '</p><div class="clear"></div>';
}


function fpd_admin_get_file_content( $file ) {

	$result = false;
	if( function_exists('curl_exec') ) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $file);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
		$result = curl_exec($ch);
		curl_close($ch);
	}

	//if curl does not work, use file_get_contents
	if( $result == false && function_exists('file_get_contents') ) {
		$result = @file_get_contents($file);
	}

	if($result !== false) {
		return $result;
	}
	else {
		return false;
	}

}

function fpd_admin_upload_image_to_wp( $name, $base64_image, $add_to_library = true ) {

	//upload to wordpress
	$upload = wp_upload_bits( $name, null, base64_decode($base64_image) );
	//add to media library
	if($add_to_library) {
		media_sideload_image( $upload['url'], 0 );
	}

	return $upload['error'] === false ? $upload['url'] : false;

}

?>