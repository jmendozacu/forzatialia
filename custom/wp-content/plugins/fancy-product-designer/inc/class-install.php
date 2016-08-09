<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if(!class_exists('FPD_Install')) {

	class FPD_Install {

		const VERSION_NAME = 'fancyproductdesigner_version';

		public function __construct() {

			register_activation_hook( FPD_PLUGIN_ROOT_PHP, array( &$this, 'activate_plugin' ) );
            //Uncomment this line to delete all database tables when deactivating the plugin
            //register_deactivation_hook( FPD_PLUGIN_ROOT_PHP, array( &$this,'deactive_plugin' ) );
            add_action( 'plugins_loaded', array( &$this,'check_version' ) );
            add_action( 'wpmu_new_blog', array( &$this, 'new_blog'), 10, 6);

		}

		public function check_version() {

			if( get_option(self::VERSION_NAME) != Fancy_Product_Designer::VERSION) {

				$this->upgrade();

			}
		}

		//install when a new network site is added
		public function new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

			if ( ! function_exists( 'is_plugin_active_for_network' ) )
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

		    global $wpdb;

		    if ( is_plugin_active_for_network('fancy-product-designer/fancy-product-designer.php') ) {
		        $old_blog = $wpdb->blogid;
		        switch_to_blog($blog_id);
		        $this->activate_plugin();
		        switch_to_blog($old_blog);
		    }

		}

		public function activate_plugin( $networkwide ) {

		   if(version_compare(PHP_VERSION, '5.2.0', '<')) {
			  deactivate_plugins(FPD_PLUGIN_ROOT_PHP); // Deactivate plugin
			  wp_die("Sorry, but you can't run this plugin, it requires PHP 5.2 or higher.");
			  return;
			}

			global $wpdb;

			if ( is_multisite() ) {
	    		if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
	                $current_blog = $wpdb->blogid;
	    			// Get all blog ids
	    			$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
	    			foreach ($blogids as $blog_id) {
	    				switch_to_blog($blog_id);
	    				$this->install();
	    			}
	    			switch_to_blog($current_blog);
	    			return;
	    		}
	    	}

			$this->install();

		}

		public function deactive_plugin($networkwide) {

			global $wpdb;

		    if (is_multisite()) {
		        if ($networkwide) {
		            $old_blog = $wpdb->blogid;
		            // Get all blog ids
		            $blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
		            foreach ($blogids as $blog_id) {
		                switch_to_blog($blog_id);
		                $this->deinstall();
		            }
		            switch_to_blog($old_blog);
		            return;
		        }
		    }

		    $this->deinstall();

		}

		//all things that need to be installed on activation
		private function install() {

			//install options
			foreach(FPD_Admin_Settings::get_settings() as $section_settings) {
				foreach($section_settings as $setting) {
					if ( isset( $setting['default'] ) && isset( $setting['id'] ) ) {
			        	add_option( $setting['id'], $setting['default'] );
			        }
				}
			}

	        foreach( FPD_Admin_Settings::$styling_colors as $key => $value ) {
		        add_option( $key, $value );
	        }

		}

		private function deinstall() {

			global $wpdb;

			foreach(FPD_Admin_Settings::get_settings() as $section_settings) {
				foreach($section_settings as $setting) {
					if ( isset( $setting['default'] ) && isset( $setting['id'] ) ) {
			        	delete_option( $setting['id'] );
			        }
				}
			}

	        foreach( FPD_Admin_Settings::$styling_colors as $key => $value ) {
		        delete_option( $key );
	        }

			if( fpd_views_table_exist() ) {
				$wpdb->query("SET FOREIGN_KEY_CHECKS=0;");
				$wpdb->query("DROP TABLE ".FPD_VIEWS_TABLE."");
				$wpdb->query("SET FOREIGN_KEY_CHECKS=1;");
			}

		}

		private function upgrade() {

			global $wpdb;

			//upgrade to V1.0.1
			if( get_option(self::VERSION_NAME) == '1.0.0' || !get_option(self::VERSION_NAME) ) {
				add_option( 'fpd_stage_menu_bar_reset', 'Reset Product');
		   		update_option(self::VERSION_NAME, '1.0.1');
			}

			//upgrade to V1.0.11
			if( get_option(self::VERSION_NAME) == '1.0.1' ) {
		   		update_option(self::VERSION_NAME, '1.0.11');
			}

			//upgrade to V1.0.2
			if( get_option(self::VERSION_NAME) == '1.0.11' ) {
				update_option( 'fpd_fb_headline', 'Add Facebook Photos' );
				update_option( 'fpd_select_album', 'Select an album' );
				update_option( 'fpd_navigation_tab_facebook', 'Add Photos From Facebook' );
				update_option( 'fpd_layout', 'fpd-vertical' );
				update_option( 'fpd_sidebar_height', 600 );
				update_option( 'fpd_stage_height', 600 );
				update_option( 'fpd_designs_parameter_z', -1 );
				update_option( 'fpd_custom_texts_parameter_patternable', 'no' );
		   		update_option( self::VERSION_NAME, '1.0.2' );
			}

			//upgrade to V1.0.21
			if( get_option(self::VERSION_NAME) == '1.0.2' ) {
				update_option( 'fpd_custom_texts_parameter_z', -1 );
		   		update_option(self::VERSION_NAME, '1.0.21');
			}

			//upgrade to V1.0.22
			if( get_option(self::VERSION_NAME) == '1.0.21' ) {
		   		update_option(self::VERSION_NAME, '1.0.22');
			}

			//upgrade to V1.0.23
			if( get_option(self::VERSION_NAME) == '1.0.22' ) {
		   		update_option(self::VERSION_NAME, '1.0.23');
			}

			//upgrade to V1.0.24
			if( get_option(self::VERSION_NAME) == '1.0.23' ) {
		   		update_option(self::VERSION_NAME, '1.0.24');
			}

			//upgrade to V1.0.25
			if( get_option(self::VERSION_NAME) == '1.0.24' ) {
		   		update_option(self::VERSION_NAME, '1.0.25');
			}

			//upgrade to V1.0.26
			if( get_option(self::VERSION_NAME) == '1.0.25' ) {
				if( get_option('fpd_layout') == 'fpd-vertical' ) {
					update_option( 'fpd_layout', 'icon-sb-left' );
				}
				else {
					update_option( 'fpd_layout', 'icon-sb-top' );
				}

				update_option( 'fpd_placement', 'fpd-replace-image' );
		   		update_option(self::VERSION_NAME, '1.0.26');
			}

			//upgrade to V1.1
			if( get_option(self::VERSION_NAME) == '1.0.26' ) {

				update_option( 'fpd_sidebar_nav_size', get_option('fpd_sidebar_nav_width') );
				update_option( 'fpd_sidebar_size', get_option('fpd_sidebar_height') );
				update_option( 'fpd_custom_texts_parameter_text_size', get_option('fpd_default_text_size') );
				update_option( 'fpd_view_selection_position', 'tr' );
				update_option( 'fpd_menu_bar_position', 'outside' );
				update_option( 'fpd_zoom_factor', 1.2 );
				update_option( 'fpd_min_zoom_range', 0.2 );
				update_option( 'fpd_max_zoom_range', 2 );
				update_option( 'fpd_default_text', 'Double-click to change text' );
				update_option( 'fpd_tooltips', 'yes' );
				update_option( 'fpd_out_of_containment_alert', 'Move it in his containment!' );
				update_option( 'fpd_selected_color', '#d5d5d5' );
				update_option( 'fpd_bounding_box_color', '#005ede' );
				update_option( 'fpd_out_of_boundary_color', '#990000' );
				update_option( 'fpd_use_label_settings', 'yes' );
				update_option( 'fpd_init_text', 'Initializing product designer' );
				update_option( 'fpd_uploaded_images_category_name', 'Your uploaded images' );
				update_option( 'fpd_navigation_tab_instagram', 'Add Photos From Instagram' );
				update_option( 'fpd_customize_section_filling', 'Filling' );
				update_option( 'fpd_customize_section_font_styles', 'Font & Styles' );
				update_option( 'fpd_customize_section_curved_text', 'Curved Text' );
				update_option( 'fpd_customize_section_helpers', 'Helpers' );
				update_option( 'fpd_curved_text_info', 'You can only change the text when you switch to normal text.' );
				update_option( 'fpd_curved_text_switcher', 'Switch between curved and normal Text' );
				update_option( 'fpd_curved_text_reverse', 'Reverse' );
				update_option( 'fpd_curved_text_spacing', 'Spacing' );
				update_option( 'fpd_curved_text_radius', 'Radius' );
				update_option( 'fpd_insta_headline', 'Instagram Photos' );
				update_option( 'fpd_insta_my_feed', 'My Feed' );
				update_option( 'fpd_insta_recent_images', 'My Recent Images' );
				update_option( 'fpd_insta_load_next', 'Load next stack' );
				update_option( 'fpd_stage_menu_bar_zoom_in', 'Zoom In' );
				update_option( 'fpd_stage_menu_bar_zoom_out', 'Zoom Out' );
				update_option( 'fpd_stage_menu_bar_zoom_reset', 'Zoom Reset' );
				update_option( 'fpd_stage_menu_bar_your_saved_product', 'Your saved products' );
				update_option( 'fpd_common_parameter_originx', 'center' );
				update_option( 'fpd_common_parameter_originy', 'center' );
				update_option( 'fpd_custom_css', FPD_Admin_Settings::$custom_css );
				update_option( 'fpd_custom_texts_parameter_max_length', 0 );
				update_option( 'fpd_custom_texts_parameter_curvable', 'yes' );
				update_option( 'fpd_start_customizing_button', '' );
				update_option( 'fpd_stage_menu_bar_add_image', 'Add your own image' );
				update_option( 'fpd_stage_menu_bar_add_text', 'Add your own text' );
				update_option( 'fpd_sidebar_size', 600 );

				delete_option( 'fpd_frontend_border_highlight' );
				delete_option( 'fpd_frontend_text_button' );

				delete_transient('fpd_google_webfonts');

		   		update_option(self::VERSION_NAME, '1.1');
			}

			//upgrade to V1.1.1
			if( get_option(self::VERSION_NAME) == '1.1' ) {

				update_option( 'fpd_customize_tooltip_trash', 'Trash' );
				update_option( 'fpd_start_customizing_css_class', '' );
				update_option( 'fpd_designs_parameter_replace', '' );
				update_option( 'fpd_custom_texts_parameter_replace', '' );

		   		update_option(self::VERSION_NAME, '1.1.1');
			}

			//upgrade to V1.1.2
			if( get_option(self::VERSION_NAME) == '1.1.1' ) {
		   		update_option(self::VERSION_NAME, '1.1.2');
			}

			//upgrade to V1.1.3
			if( get_option(self::VERSION_NAME) == '1.1.2' ) {

				$wpdb->query("ALTER TABLE ".FPD_VIEWS_TABLE." ADD view_order INT COLLATE utf8_general_ci NULL DEFAULT 0;");
		   		update_option(self::VERSION_NAME, '1.1.3');
			}

			//upgrade to V1.1.31
			if( get_option(self::VERSION_NAME) == '1.1.3' ) {
		   		update_option(self::VERSION_NAME, '1.1.31');
			}

			//upgrade to V1.1.4
			if( get_option(self::VERSION_NAME) == '1.1.31' ) {
		   		update_option(self::VERSION_NAME, '1.1.4');
			}

			//upgrade to V1.2.2
			if( get_option(self::VERSION_NAME) == '1.2.1' ) {
		   		update_option(self::VERSION_NAME, '1.2.2');
			}

		}
	}
}

new FPD_Install();

?>