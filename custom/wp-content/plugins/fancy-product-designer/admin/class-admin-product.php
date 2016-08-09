<?php

if( !class_exists('FPD_Admin_Product') ) {

	class FPD_Admin_Product {

		public function __construct() {

			add_filter( 'product_type_options', array( &$this, 'add_product_type_option' ) );
			add_filter( 'woocommerce_product_data_tabs', array( &$this, 'add_product_data_tab' ) );
			add_action( 'woocommerce_product_data_panels', array( &$this, 'add_product_data_panel' ) );
			add_action( 'woocommerce_process_product_meta', array( &$this, 'save_custom_fields' ), 10, 2 );
			add_action( 'woocommerce_duplicate_product', array( &$this, 'duplicate_fancy_product' ), 10, 2 );
			//when a product gets deleted, delete also the asscociated views in the database
			if ( current_user_can( 'delete_posts' ) ) {
				add_action( 'delete_post', array( &$this, 'delete_fancy_product' ), 10 );
			}

		}

		//add checkbox to enable fancy product for a product
		public function add_product_type_option( $types ) {

			$types['fancy_product'] = array(
				'id' => '_fancy_product',
				'wrapper_class' => 'show_if_fancy_product',
				'label' => __( 'Fancy Product', 'radykal' ),
				'description' => __( 'A product for the Fancy Product Designer?', 'radykal' )
			);

			return $types;

		}

		//the tab in the data panel
		public function add_product_data_tab( $tabs ) {

			$tabs['fancy_product'] = array(
				'label'  => __( 'Fancy Product', 'radykal' ),
				'target' => 'fancy_product_data',
				'class'  => array( 'hide_if_fancy_product' ),
			);

			return $tabs;

		}

		//custom panel in the product post to add/edit/remove views
		public function add_product_data_panel() {

			global $wpdb, $post;

			$custom_fields = get_post_custom($post->ID);

			require_once(FPD_PLUGIN_ADMIN_DIR.'/views/html-admin-meta-box.php');

		}

		//be sure to save the checkbox value (product post)
		public function save_custom_fields( $post_id, $post ) {

			update_post_meta( $post_id, 'fpd_product_settings', htmlentities($_POST['fpd_product_settings']) );
			update_post_meta( $post_id, '_fancy_product', isset( $_POST['_fancy_product'] ) ? 'yes' : 'no' );

		}

		//duplicate fancy products, all views will be available in the duplicated product
		public function duplicate_fancy_product( $new_id, $post ) {

			if( is_fancy_product($post->ID) ) {

				$fp = new Fancy_Product($post->ID);
				$fp->duplicate($new_id);

			}

		}

		//delete also the views from db when associated post is deleted
		public function delete_fancy_product( $pid ) {

			$fp = new Fancy_Product($pid);
			$fp->delete();

		}
	}
}

new FPD_Admin_Product();

?>