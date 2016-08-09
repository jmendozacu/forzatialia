<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('Fancy_Product')) {

	class Fancy_Product {

		public $id;
		public $individual_settings;

		public function __construct( $id ) {

			$this->id = $id;

			$product_settings_array = array();
			$product_settings = get_post_meta( fpd_get_master_id($id), 'fpd_product_settings', true );
			if( !empty($product_settings) ) {

				$product_settings_array = json_decode(html_entity_decode($product_settings), true);
				//remove elements with empty value
				if( is_array($product_settings_array) ) {
					$product_settings_array = array_filter($product_settings_array, array( &$this, 'remove_empty_values'));
				}

			}

			$this->individual_settings = $product_settings_array;

		}

		public function get_views() {

			global $wpdb;

			return $wpdb->get_results("SELECT * FROM ".FPD_VIEWS_TABLE." WHERE product_id=".$this->id." ORDER BY view_order ASC");

		}

		public function get_data() {

			global $wpdb;

			$product_array = array();
			$views = $wpdb->get_results("SELECT * FROM ".FPD_VIEWS_TABLE." WHERE product_id=".$this->id." ORDER BY view_order ASC");
			foreach($views as $view) {

				$view_array = array(
					'title' => $view->title,
					'thumbnail' => $view->thumbnail,
					'elements' => $view->elements
				);

				$product_array[] = $view_array;

			}

			return $product_array;

		}

		public function get_option( $name ) {

			if( isset($this->individual_settings[$name]) ) {
				$value = fpd_convert_string_value_to_int($this->individual_settings[$name]);
			}
			else {
				$value = FPD_Admin_Settings::get_option( 'fpd_'.$name );
			}

			return $value;

		}

		public function get_individual_option( $name ) {

			return isset($this->individual_settings[$name]) ?  $this->individual_settings[$name] : false;

		}

		public function add_view( $title, $elements = '', $thumbnail = '', $order = NULL ) {

			global $wpdb;

			//check if an order value is set
			if($order === NULL) {
				//count views of a fancy product
				$count = $wpdb->get_var("SELECT COUNT(*) FROM ".FPD_VIEWS_TABLE." WHERE product_id=".$this->id."");
				//count is the order value
				$order = intval($count);
			}

			$inserted = $wpdb->insert(
				FPD_VIEWS_TABLE,
				array(
					'product_id' => $this->id,
					'title' => $title,
					'elements' => $elements ? $elements : '',
					'thumbnail' => $thumbnail ? $thumbnail : '',
					'view_order' => $order
				),
				array( '%d', '%s', '%s', '%s', '%d')
			);

			return $inserted ? $wpdb->insert_id : false;

		}

		public function duplicate( $new_product_id ) {

			$new_fp = new Fancy_Product( $new_product_id );

			foreach( $this->get_views() as $view ) {

				$new_fp->add_view($view->title, $view->elements, $view->thumbnail, $view->view_order);

			}

		}

		public function delete() {

			global $wpdb;

			$wpdb->query( $wpdb->prepare("DELETE FROM ".FPD_VIEWS_TABLE." WHERE product_id=%d", $this->id) );

		}

		private function remove_empty_values($var){

			return ($var !== NULL && $var !== FALSE && $var !== '');

		}

	}

}

?>