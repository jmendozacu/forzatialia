<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if(!class_exists('FPD_Scripts_Styles')) {

	class FPD_Scripts_Styles {

		public static $add_script = false;

		public function __construct() {


			add_action( 'init', array( &$this, 'register') );
			add_action( 'wp_enqueue_scripts',array( &$this,'enqueue_styles' ) );
			add_action( 'wp_footer', array(&$this, 'footer_handler') );

		}

		public function register() {

			wp_register_style( 'semantic-ui', plugins_url('/semantic/css/semantic.min.css', FPD_PLUGIN_ROOT_PHP), false, '0.17.0' );
			wp_register_style( 'fpd-flat-layout', plugins_url('/css/jquery.fancyProductDesigner.css', FPD_PLUGIN_ROOT_PHP), false, Fancy_Product_Designer::FPD_VERSION );
			wp_register_style( 'fpd-semantic-layout', plugins_url('/css/jquery.fancyProductDesigner-semantic.css', FPD_PLUGIN_ROOT_PHP), array( 'semantic-ui' ), Fancy_Product_Designer::FPD_VERSION );
			wp_register_style( 'font-awesome-4.1', plugins_url('/font-awesome/css/font-awesome.min.css', FPD_PLUGIN_ROOT_PHP), false, '4.1.0' );
			wp_register_style( 'fpd-chosen', plugins_url('/css/plugins/chosen.css', FPD_PLUGIN_ROOT_PHP), false, '1.0.0' );
			wp_register_style( 'fpd-fonts', plugins_url('/css/jquery.fancyProductDesigner-fonts.css', FPD_PLUGIN_ROOT_PHP), false, Fancy_Product_Designer::FPD_VERSION );
			wp_register_style( 'fpd-plugins', plugins_url('/css/plugins.min.css', FPD_PLUGIN_ROOT_PHP), array(
				'font-awesome-4.1',
				'fpd-fonts'
			), Fancy_Product_Designer::FPD_VERSION );

			$fpd_js_url = get_option('fpd_debug_mode') == 'yes' ? '/js/jquery.fancyProductDesigner.js' : '/js/jquery.fancyProductDesigner.min.js';
			wp_register_script( 'fabric', plugins_url('/js/fabric.js', FPD_PLUGIN_ROOT_PHP), false, '1.4.3' );
			wp_register_script( 'fpd-plugins', plugins_url('/js/plugins.min.js', FPD_PLUGIN_ROOT_PHP), false, Fancy_Product_Designer::FPD_VERSION );
			wp_register_script( 'fpd-jspdf', plugins_url('/jspdf/jspdf.min.js', FPD_PLUGIN_ROOT_PHP), false, Fancy_Product_Designer::FPD_VERSION );
			wp_register_script( 'fpd-jquery-form', plugins_url('/js/jquery.form.min.js', FPD_PLUGIN_ROOT_PHP) );
			wp_register_script( 'fpd-chosen', plugins_url('/js/plugins/chosen.jquery.min.js', FPD_PLUGIN_ROOT_PHP), false, '1.0.0' );
			wp_register_script( 'jquery-fpd', plugins_url($fpd_js_url, FPD_PLUGIN_ROOT_PHP), array(
				'jquery',
				'fabric',
				'fpd-plugins'
			), Fancy_Product_Designer::FPD_VERSION );

		}

		//includes scripts and styles in the frontend
		public function enqueue_styles() {

			global $post;

			wp_enqueue_style( 'fpd-single-product', plugins_url('/css/fancy-product.css', FPD_PLUGIN_ROOT_PHP), array('fpd-plugins'), Fancy_Product_Designer::VERSION );

			if( is_fancy_product($post->ID) ) {
				?>
				<style type="text/css">
					.fancy-product .fpd-start-customizing-button,
					.fancy-product .fpd-modal-button {
						background-color: <?php echo get_option('fpd_frontend_primary'); ?>;
						color: <?php echo get_option('fpd_frontend_secondary'); ?>;
					}
				</style>
				<?php
			}

			//only enqueue css and js files when necessary
			$fancy_product = new Fancy_Product($post->ID);
			if( is_fancy_product($post->ID) ) {

				//get individual product settings
				$layout = $fancy_product->get_option('layout');

				if($layout  == 'semantic') {
					wp_enqueue_style( 'fpd-semantic-layout' );
				}
				else {
					wp_enqueue_style( 'fpd-flat-layout' );

					?>
					<style type="text/css">

						/* Styling */

						.fancy-product .fpd-primary-bg-color {
							background-color: <?php echo get_option('fpd_frontend_primary'); ?>;
						}

						.fancy-product .fpd-primary-text-color,
						.fancy-product .fpd-primary-text-color:hover {
							color: <?php echo get_option('fpd_frontend_primary'); ?>;
						}

						.fancy-product .fpd-secondary-bg-color {
							background-color: <?php echo get_option('fpd_frontend_secondary'); ?>;
						}

						.fancy-product .fpd-secondary-text-color,
						.fancy-product .fpd-secondary-text-color:hover {
							color: <?php echo get_option('fpd_frontend_secondary'); ?>;
						}

						.fancy-product .fpd-border-color {
							border-color: <?php echo get_option('fpd_frontend_border'); ?>;
						}

						.fancy-product .fpd-button {
							background-color: <?php echo get_option('fpd_frontend_primary_elements'); ?>;
							color: <?php echo get_option('fpd_frontend_secondary'); ?>;
						}

						.fancy-product .fpd-button svg * {
							fill: <?php echo get_option('fpd_frontend_secondary'); ?>;
						}

						.fancy-product .fpd-button-danger {
							background-color: <?php echo get_option('fpd_frontend_danger_button'); ?>;
						}

						.fancy-product .fpd-button-submit {
							background-color: <?php echo get_option('fpd_frontend_submit_button'); ?>;
						}

						.fancy-product .fpd-tooltip-theme {
							background: <?php echo get_option('fpd_frontend_secondary'); ?>;
							color: <?php echo get_option('fpd_frontend_primary'); ?>;
							border-color: <?php echo get_option('fpd_frontend_border'); ?>;
						}

					</style>
					<?php
				}

				?>
				<style type="text/css">

					<?php if( $fancy_product->get_option('background_type') ): ?>
					.fpd-product-stage {
						background: <?php echo $fancy_product->get_option('background_type') == 'color' ? $fancy_product->get_option('background_color') : 'url('.$fancy_product->get_option('background_image').')'; ?> !important;
					}
					<?php endif; ?>


					<?php echo get_option( 'fpd_custom_css' ); ?>

				</style>
				<?php

				FPD_Fonts::output_webfont_links();

			}

		}

		public function footer_handler() {

			if( self::$add_script ) {

				wp_enqueue_script( 'fpd-jspdf' );
				wp_enqueue_script( 'fpd-jquery-form' );
				wp_enqueue_script( 'jquery-fpd' );

			}

		}

	}

}

new FPD_Scripts_Styles();

?>