<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( !class_exists('FPD_Admin') ) {

	class FPD_Admin {

		public static $ajax_nonce;

		public function __construct() {

			require_once(FPD_PLUGIN_ADMIN_DIR.'/fpd-admin-functions.php');
			require_once(FPD_PLUGIN_ADMIN_DIR . '/class-admin-designs.php' );
			require_once(FPD_PLUGIN_ADMIN_DIR . '/class-admin-template.php' );
			require_once(FPD_PLUGIN_ADMIN_DIR.'/class-admin-ajax.php');
			require_once(FPD_PLUGIN_ADMIN_DIR . '/class-admin-scripts-styles.php' );
			require_once(FPD_PLUGIN_ADMIN_DIR . '/class-admin-menus.php' );
			require_once(FPD_PLUGIN_ADMIN_DIR.'/class-admin-settings.php');

			add_action( 'admin_init', array( &$this, 'init_admin' ) );
			add_action( 'admin_notices',  array( &$this, 'display_admin_notices' ) );
			add_filter( 'upload_mimes', array( &$this, 'allow_svg_upload') );
			//add fancy product designer settings section page to woocommerce settings
			add_filter( 'woocommerce_get_settings_pages', array( &$this, 'add_settings_page' ), 20 );

		}

		public function init_admin() {

			self::$ajax_nonce = wp_create_nonce( 'fpd_ajax_nonce' );

			//add capability to administrator
			$role = get_role( 'administrator' );
			$role->add_cap( Fancy_Product_designer::CAPABILITY );

			require_once(FPD_PLUGIN_ADMIN_DIR . '/class-admin-product.php' );
			require_once(FPD_PLUGIN_ADMIN_DIR . '/class-admin-order.php' );

		}

		public function display_admin_notices() {

			global $woocommerce;

			if( !function_exists('get_woocommerce_currency') ): ?>
		    <div class="error">
		        <p><?php _e( 'Please install the <a href="http://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce plugin</a>, otherwise you can not use Fancy Product Designer for woocommerce!', 'radykal' ); ?></p>
		    </div>
		    <?php endif;

			if( version_compare($woocommerce->version, '2.1', '<') ): ?>
			<div class="error">
		        <p><?php _e( 'Please update woocommerce to the latest version! Fancy Product Designer is only working with V2.1 or newer.', 'radykal' ); ?></p>
		    </div>
			<?php endif;

		}

		public function allow_svg_upload( $svg_mime ) {

			$svg_mime['svg'] = 'image/svg+xml';
			return $svg_mime;

		}

		public function add_settings_page( $settings ) {

			$settings[] = include( FPD_PLUGIN_ADMIN_DIR.'/class-admin-settings-page.php' );

			return $settings;

		}
	}
}

new FPD_Admin();

?>