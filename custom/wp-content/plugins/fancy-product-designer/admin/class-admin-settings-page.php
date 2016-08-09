<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( !class_exists('FPD_Admin_Settings_Page') ) {

	class FPD_Admin_Settings_Page extends WC_Settings_Page {

		public function __construct() {

			$this->id    = 'fancy_product_designer';
			$this->label = __( 'Fancy Product Designer', 'radykal' );

			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
			add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
			add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
			add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
			add_action( 'woocommerce_admin_field_fpd_styling', array( $this, 'styling_color_picker' ) );

			if( isset($_GET['section']) && $_GET['section'] == 'fonts' && FPD_Fonts::get_google_webfonts() === false ) {

				WC_Admin_Settings::add_error( __( 'Google Webfonts could not be loaded with the PHP functions cURL and file_get_contents(). Please contact your provider and ask him what you need to do to load content with one of these functions from external URLs!', 'radykal') );

			}

		}

		public function get_sections() {

			$sections = array(
				''          => __( 'General', 'woocommerce' ),
				'default_parameters' => __( 'Element Parameters', 'radykal' ),
				'labels'          => __( 'Labels', 'radykal' ),
				'fonts'          => __( 'Fonts', 'radykal' )
			);

			return $sections;

		}

		public function output() {

			global $current_section;

			$settings = $this->get_settings( $current_section );

	 		WC_Admin_Settings::output_fields( $settings );

		}

		public function save() {

			global $current_section;

			$settings = $this->get_settings( $current_section );
			WC_Admin_Settings::save_fields( $settings );

			if( isset( $_POST['fpd_frontend_primary'] ) ) {

				foreach ( FPD_Admin_Settings::$styling_colors as $key => $value ) {
			        if( isset($_POST[$key]) ) {
						update_option( $key, woocommerce_format_hex($_POST[$key]) );
					}
		        }

		        foreach ( FPD_Admin_Settings::$boundary_colors as $key => $value ) {
			        if( isset($_POST[$key]) ) {
						update_option( $key, woocommerce_format_hex($_POST[$key]) );
					}
		        }

			}
			else if($current_section == 'fonts') {

				FPD_Fonts::save_woff_fonts_css();

			}

		}

		//add colorpicker to settings for styling
		public function styling_color_picker() {

			?><tr valign="top" class="fpd_frontend_css_colors">
				<th scope="row" class="titledesc">
					<label><?php _e( 'Flat Scheme', 'radykal' ); ?></label>
				</th>
			    <td class="forminp fpd-clearfix">
			    <?php
					$this->color_picker( __( 'Primary', 'radykal' ), 'fpd_frontend_primary', get_option('fpd_frontend_primary'), __( 'Sidebar Navigation, Product Stage Header', 'radykal' ) );
		            $this->color_picker( __( 'Secondary', 'radykal' ), 'fpd_frontend_secondary', get_option('fpd_frontend_secondary'), __( 'Sidebar Content Background, Tooltip Background', 'radykal' ) );
					$this->color_picker( __( 'Border', 'radykal' ), 'fpd_frontend_border', get_option('fpd_frontend_border'), __( 'Border Color', 'radykal' ) );
					$this->color_picker( __( 'Button', 'radykal' ), 'fpd_frontend_primary_elements', get_option('fpd_frontend_primary_elements'), __( 'Button Background', 'radykal' ) );
					$this->color_picker( __( 'Submit', 'radykal' ), 'fpd_frontend_submit_button', get_option('fpd_frontend_submit_button'), __( 'Submit Button Background', 'radykal' ) );
					$this->color_picker( __( 'Danger', 'radykal' ), 'fpd_frontend_danger_button', get_option('fpd_frontend_danger_button'), __( 'Danger Button Background.', 'radykal' ) );
				?>
				</td>
			</tr>
			<tr valign="top" class="fpd_boundary_colors">
				<th scope="row" class="titledesc">
					<label><?php _e( 'Boundaries', 'radykal' ); ?></label>
				</th>
			    <td class="forminp fpd-clearfix">
			    <?php
		            $this->color_picker( __( 'Selected', 'radykal' ), 'fpd_selected_color', get_option('fpd_selected_color'), __( 'The border color of the selected element.', 'radykal' ) );
		            $this->color_picker( __( 'Bounding Box', 'radykal' ), 'fpd_bounding_box_color', get_option('fpd_bounding_box_color'), __( 'The border color of the bounding box.', 'radykal' ) );
					$this->color_picker( __( 'Out Of Boundary', 'radykal' ), 'fpd_out_of_boundary_color', get_option('fpd_out_of_boundary_color'), __( 'The border color of the bounding box if an element his out of his boundary.', 'radykal' ) );
				?>
				</td>
			</tr>
			<?php

		}

		public function get_settings( $current_section = '' ) {

			$settings = FPD_Admin_Settings::get_settings();
			$current_section = $current_section == '' ? 'general' : $current_section;

			return $settings[$current_section];

		}

		private function color_picker( $name, $id, $value, $desc = '' ) {

			echo '<div class="fpd-color-box"><span>' . esc_html( $name ) . '<img class="help_tip" data-tip="' . esc_attr( $desc ) . '" src="' . WC()->plugin_url() . '/assets/images/help.png" height="16" width="16" /></span><input name="' . esc_attr( $id ). '" id="' . esc_attr( $id ) . '" type="text" value="' . esc_attr( $value ) . '" class="fpd-color-picker" /></div>';

		}

	}
}

new FPD_Admin_Settings_Page();

?>