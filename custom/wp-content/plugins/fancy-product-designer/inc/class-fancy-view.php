<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('Fancy_View') ) {

	class Fancy_View {

		public $id;

		public function __construct( $id ) {

			$this->id = $id;

		}

		public function get_elements() {

			global $wpdb;

			return unserialize($wpdb->get_var("SELECT elements FROM ".FPD_VIEWS_TABLE." WHERE ID=".$this->id.""));

		}

		public function get_data() {

			global $wpdb;

			return $wpdb->get_row("SELECT * FROM ".FPD_VIEWS_TABLE." WHERE ID=".$this->id."");

		}

		public function get_product_id() {

			global $wpdb;

			return $wpdb->get_var("SELECT product_id FROM ".FPD_VIEWS_TABLE." WHERE ID=".$this->id."");

		}

		public function update( $data_array = array() ) {

			global $wpdb;

			//all available columns with format that can be updated
			$data_keys = array(
				'product_id' => '%d',
				'title' => '%s',
				'thumbnail' => '%s',
				'elements' => '%s',
				'view_order' => '%d'
			);

			//the data and formats arrays that will be used in the sql
			$data = array();
			$formats = array();

			//loop through all available keys and check if the key exist in the passed data_array
			foreach( $data_keys as $key => $value ) {

				if( array_key_exists( $key, $data_array ) ) {
					$data[$key] = $data_array[$key];
					$formats[] = $data_keys[$key];
				}
			}

			//update view with the passed data and return number of updated columns
			return $wpdb->update(
				FPD_VIEWS_TABLE,
				$data,
				array('ID' => $this->id),
				$formats,
				'%d'
			);

		}

		public function duplicate( $new_title ) {

			global $wpdb;

			$data = $this->get_data();
			$count = $wpdb->get_var("SELECT COUNT(*) FROM ".FPD_VIEWS_TABLE." WHERE product_id=".$data->product_id."");

			$inserted = $wpdb->insert(
				FPD_VIEWS_TABLE,
				array(
					'product_id' => $data->product_id,
					'title' => $new_title,
					'thumbnail' => $data->thumbnail,
					'elements' => $data->elements,
					'view_order' => intval($count)
				),
				array( '%d', '%s', '%s', '%s', '%d')
			);

			return $inserted ? $wpdb->get_row("SELECT * FROM ".FPD_VIEWS_TABLE." WHERE ID=".$wpdb->insert_id."") : false;

		}

		public function delete() {

			global $wpdb;

			try {
				$wpdb->query( $wpdb->prepare("DELETE FROM ".FPD_VIEWS_TABLE." WHERE ID=%d", $this->id) );
				return 1;
			}
			catch(Exception $e) {
				return 0;
			}

		}

	}

}

?>