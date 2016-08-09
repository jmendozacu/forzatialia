<?php

if(!class_exists('FPD_Admin_Menus')) {

	class FPD_Admin_Menus {

		public function __construct() {

			//add menu pages - Fancy Products, Fancy Designs
			add_action( 'admin_menu', array( &$this, 'add_menu_pages' ) );
			//add action links to plugins page
			add_filter( 'plugin_action_links_' . plugin_basename( FPD_PLUGIN_DIR ).'/fancy-product-designer.php', array( &$this, 'action_links' ) );

		}

		public function add_menu_pages() {

			//add fancy products sub menu page to products menu
			add_submenu_page(
				'edit.php?post_type=product',
				 __('Fancy Products | Product Builder', 'radykal'),
				 __('Fancy Products', 'radykal'),
				 Fancy_Product_Designer::CAPABILITY,
				 'fancy_products',
				 array( $this, 'product_builder_page' )
			);

			//add fancy designs sub menu page to products menu
			add_submenu_page(
				'edit.php?post_type=product',
				__('Manage Fancy Designs', 'radykal'),
				__('Fancy Designs', 'radykal'),
				Fancy_Product_Designer::CAPABILITY,
				'manage_designs',
				array( $this, 'designs_page' )
			);

		}

		public function action_links( $links ) {

			return array_merge( array(
				'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=fancy_product_designer' ) . '">' . __( 'Settings', 'radykal' ) . '</a>',
				'<a href="' . esc_url( 'http://fancyproductdesigner.com/woocommerce-plugin/documentation/' ) . '" target="_blank">' . __( 'Documentation', 'radykal' ) . '</a>',
			), $links );

		}

		public function product_builder_page() {

			$page = require_once( FPD_PLUGIN_ADMIN_DIR.'/class-admin-product-builder.php' );
			$page->output();

		}

		public function designs_page() {

			if( class_exists('FPD_Admin_Fancy_Designs') ) {

				$page = new FPD_Admin_Fancy_Designs();
				$page->output();

			}

		}
	}
}

new FPD_Admin_Menus();

?>