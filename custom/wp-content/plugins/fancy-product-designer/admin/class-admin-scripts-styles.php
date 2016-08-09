<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( !class_exists('FPD_Admin_Scripts_Styles') ) {

	class FPD_Admin_Scripts_Styles {

		public function __construct() {

			add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_styles_scripts' ), 20 );

		}

		public function enqueue_styles_scripts( $hook ) {

			if( version_compare(WC_VERSION, '2.3.0', '<') ) {
				wp_register_style( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.css', false, '3.5.2' );
				wp_register_script( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.js', array( 'jquery' ), '3.5.2' );
			}

			wp_register_style( 'fpd-admin', plugins_url('/css/admin.css', __FILE__) );
			wp_register_script( 'fpd-admin', plugins_url('/js/admin.js', __FILE__), false, Fancy_Product_Designer::VERSION );

			global $post, $woocommerce;

			$wc_settings_page = 'wc-settings';

			//woocommerce settings
			if( $hook == 'woocommerce_page_'.$wc_settings_page.'' ) {

				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_style( 'select2' );
				wp_enqueue_style( 'fpd-admin' );
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_script( 'select2' );
				wp_enqueue_script( 'fpd-admin' );

			}

			//woocommerce post types
		    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {

		    	//product
		        if ( 'product' === $post->post_type ) {

		        	wp_enqueue_style( 'wp-color-picker' );
		        	wp_enqueue_style( 'select2' );
					wp_enqueue_style( 'fpd-admin' );
					wp_enqueue_script( 'wp-color-picker' );
					wp_enqueue_script( 'select2' );
					wp_enqueue_script( 'fpd-admin' );

		        }
		        //order
		        else if( 'shop_order' === $post->post_type ) {

		        	FPD_Fonts::output_webfont_links();

					wp_enqueue_style('fpd-semantic-layout');
					wp_enqueue_style( 'fpd-plugins' );
					wp_enqueue_style( 'fpd-admin' );
					wp_enqueue_script( 'fpd-jspdf' );
					wp_enqueue_script( 'jquery-fpd' );
					wp_enqueue_script( 'fpd-admin' );

		        }
		    }

			//edit fancy products
		    if( $hook == 'product_page_fancy_products') {

		    	wp_enqueue_media();

		    	wp_enqueue_style( 'jquery-fpd' );
		    	wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css' );
		    	wp_enqueue_style( 'fpd-tags-manager', plugins_url('/css/tagmanager.css', __FILE__) );
		    	wp_enqueue_style( 'font-awesome-4.1' );
		    	wp_enqueue_style( 'fpd-fonts' );
		    	wp_enqueue_style( 'fpd-admin' );


		    	FPD_Fonts::output_webfont_links();

				wp_enqueue_script( 'jquery-tiptip', $woocommerce->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js' );
				wp_enqueue_script( 'fpd-tags-manager', plugins_url('/js/tagmanager.js', __FILE__) );
				wp_register_script( 'fpd-product-builder', plugins_url('/js/product-builder.js', __FILE__), array(
					'jquery-ui-core',
					'jquery-ui-mouse',
					'jquery-ui-sortable',
					'jquery-ui-spinner',
					'jquery-ui-widget',
					'jquery-fpd'
				) );
				wp_localize_script( 'fpd-product-builder', 'fpd_product_builder_opts', array(
						'adminUrl' => admin_url(),
						'originX' => FPD_Admin_Settings::get_option('fpd_common_parameter_originx'),
						'originY' => FPD_Admin_Settings::get_option('fpd_common_parameter_originy'),
						'paddingControl' => FPD_Admin_Settings::get_option('fpd_padding_controls'),
						'defaultFont' => get_option('fpd_default_font') ? get_option('fpd_default_font') : 'Arial',
						'enterTitlePrompt' => __('Enter a title for the element', 'radykal'),
						'chooseElementImageTitle' => __( 'Choose an element image', 'radykal' ),
						'set' => __( 'Set', 'radykal' ),
						'enterYourText' => __( 'Enter your text.', 'radykal' ),
						'removeElement' => __('Remove element?', 'radykal'),
						'notChanged' => __('You have not save your changes!', 'radykal')
					)
				);
				wp_enqueue_script( 'fpd-product-builder' );
				wp_enqueue_script( 'fpd-admin' );

		    }

			//manage designs
		    if( $hook == 'product_page_manage_designs') {

		    	wp_enqueue_media();
		    	wp_enqueue_style( 'font-awesome-4.1' );
		    	wp_enqueue_style( 'select2' );
		    	wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
		    	wp_enqueue_style( 'fpd-admin' );
		    	wp_enqueue_script( 'select2' );
		    	wp_enqueue_script( 'fpd-admin' );

		    }

		}
	}
}

new FPD_Admin_Scripts_Styles();

?>