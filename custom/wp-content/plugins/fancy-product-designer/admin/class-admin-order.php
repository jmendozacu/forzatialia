<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if(!class_exists('FPD_Admin_Order')) {

	class FPD_Admin_Order {

		public function __construct() {

			add_action( 'add_meta_boxes', array( &$this, 'add_meta_boxes' ) );
			add_action( 'woocommerce_admin_order_item_headers', array( &$this, 'add_order_item_header' ) );
			add_action( 'woocommerce_admin_order_item_values', array( &$this, 'admin_order_item_values' ), 10, 3 );

		}

		public function add_meta_boxes() {

			add_meta_box( 'fpd-order', __( 'Fancy Product - Order Viewer', 'radykal' ), array( &$this, 'fancy_product_order'), 'shop_order', 'normal', 'default' );

		}

		public function add_order_item_header() {

			?>
			<th class="fancy-product"><?php _e( 'Fancy Product', 'radykal' ); ?></th>
			<?php

		}

		//add a button to the ordered fancy product
		public function admin_order_item_values( $_product, $item, $item_id ) {

			if( is_object($_product) ) {

				$fancy_product = new Fancy_Product( fpd_get_master_id($_product->post->ID) );
				$stage_width = $fancy_product->get_option('stage_width');
				$stage_height = $fancy_product->get_option('stage_height');

				$product = unserialize($item['fpd_data']);
				?>
				<td class="fancy-product" width="100px">
					<?php if( isset($item['fpd_data']) ) : ?>
						<button class='button button-secondary fpd-show-order-item' data-stagewidth='<?php echo $stage_width; ?>' data-stageheight='<?php echo $stage_height;?>' data-order='<?php echo str_replace("'", "%27", $product['fpd_product']); ?>' id='<?php echo $item_id; ?>'><?php _e( 'Open', 'radykal' ); ?></button>
					<?php endif; ?>
				</td>
				<?php

			}

		}

		//add fancy product panel to order post
		public function fancy_product_order( $post ) {

			global $post, $woocommerce, $thepostid;

			include_once( FPD_PLUGIN_ADMIN_DIR.'/views/html-order-viewer.php' );

		}

	}

}

new FPD_Admin_Order();

?>