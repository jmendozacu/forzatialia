<?php

//checks if a product has fancy product enabled
function is_fancy_product( $product_id ) {

	//check if wpml plugin is activated
	global $sitepress;
	if($sitepress && method_exists($sitepress, 'get_original_element_id')) {
		$product_id = $sitepress->get_original_element_id($product_id, 'post_product');
	}

	return get_post_meta( $product_id, '_fancy_product', true ) == 'yes' && get_post_type($product_id) == 'product';

}

//get the master id of a post in wpml-enabled site
function fpd_get_master_id( $id ) {

	global $sitepress;

	$master_id = $id;
	if($sitepress && method_exists($sitepress, 'get_original_element_id')) {
		$master_id = $sitepress->get_original_element_id($id, 'post_product');
	}

	return $master_id;

}

//checks if the views table in the database exists
function fpd_views_table_exist() {

	global $wpdb;

	return (bool) $wpdb->query("SHOW TABLES LIKE '".FPD_VIEWS_TABLE."'");

}

function fpd_not_empty($value) {

	$value = trim($value);
	return $value == '0' || !empty($value);

}

function fpd_convert_string_value_to_int($value) {

	if($value == 'yes') { return 1; }
	else if($value == 'no') { return 0; }
	else { return $value; }

}

function fpd_start_customizing_button_used( $product_id ) {

	$fancy_product = new Fancy_Product($product_id);
	return trim($fancy_product->get_option('start_customizing_button')) != '' && !isset($_GET['start_customizing']) && !isset($_GET['cart_item_key']);
}

?>