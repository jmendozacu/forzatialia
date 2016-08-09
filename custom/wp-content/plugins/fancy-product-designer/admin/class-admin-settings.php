<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( !class_exists('FPD_Admin_Settings') ) {

	class FPD_Admin_Settings {

		public static $styling_colors = array(
			'fpd_frontend_primary' => '#2C3E50',
			'fpd_frontend_secondary' => '#F6F6F6',
			'fpd_frontend_border' => '#DAE4EB',
			'fpd_frontend_primary_elements' => '#c1cfd9',
			'fpd_frontend_submit_button' => '#a8bd44',
			'fpd_frontend_danger_button' => '#f97e76'
		);
		public static $boundary_colors = array(
			'fpd_selected_color' => '#d5d5d5',
			'fpd_bounding_box_color' => '#005ede',
			'fpd_out_of_boundary_color' => '#990000'
		);
		public static $custom_css = '/* Fancy Product Designer Container */
			.fancy-product.fpd-customize-button-hidden .fpd-container  {
				float: left;
				margin-right: 40px;
			}

			/* Woocommerce summary, includes i.a. title, description, cart form */
			.fancy-product.fpd-customize-button-hidden .summary {
				float: left !important;
				width: 200px !important;
			}

			/* the on-sale element */
			.woocommerce .fancy-product span.onsale {
				top: 17px;
				left: -17px;
		}';

		public static function get_general_settings() {

			return apply_filters('fancy_product_designer_general_settings', array(

				array( 'title' => __( 'Layout & Skin', 'radykal' ), 'type' => 'title'),

				array(
					'title' 	=> __( 'Theme', 'radykal' ),
					'id' 		=> 'fpd_layout',
					'css' 		=> 'min-width:350px;',
					'default'	=> 'icon-sb-left',
					'type' 		=> 'select',
					'class'		=> 'chosen_select',
					'options'   => self::get_layouts_options()
				),

				array(
					'title' => __( 'Sidebar Navigation Width/Height', 'radykal' ),
					'desc' 		=> __( 'The size for the navigation in the sidebar for the flat layout. Vertical layout = Width, Horizontal Layout = Height', 'radykal' ),
					'id' 		=> 'fpd_sidebar_nav_size',
					'css' 		=> 'width:70px;',
					'default'	=> '50',
					'desc_tip'	=>  true,
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' => __( 'Sidebar Content Width', 'radykal' ),
					'desc' 		=> __( 'The width for the content box in the sidebar. Only necessary for the flat-vertical layout.', 'radykal' ),
					'id' 		=> 'fpd_sidebar_content_width',
					'css' 		=> 'width:70px;',
					'default'	=> '200',
					'desc_tip'	=>  true,
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' => __( 'Sidebar Height/Width', 'radykal' ),
					'desc' 		=> __( 'The size for the sidebar. Vertical layout = Height, Horizontal Layout = Width', 'radykal' ),
					'id' 		=> 'fpd_sidebar_size',
					'css' 		=> 'width:70px;',
					'default'	=> '600',
					'desc_tip'	=>  true,
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' => __( 'Product Stage Width', 'radykal' ),
					'desc' 		=> __( 'The width for the product stage.', 'radykal' ),
					'id' 		=> 'fpd_stage_width',
					'css' 		=> 'width:70px;',
					'default'	=> '550',
					'desc_tip'	=>  true,
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' => __( 'Product Stage Height', 'radykal' ),
					'desc' 		=> __( 'The height for the product stage', 'radykal' ),
					'id' 		=> 'fpd_stage_height',
					'css' 		=> 'width:70px;',
					'default'	=> '600',
					'desc_tip'	=>  true,
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' 	=> __( 'View Selection Position', 'radykal' ),
					'desc' 		=> __( 'The position of the view selection.', 'radykal' ),
					'id' 		=> 'fpd_view_selection_position',
					'css' 		=> 'min-width:350px;',
					'default'	=> 'tr',
					'desc_tip'	=>  true,
					'type' 		=> 'select',
					'class'		=> 'chosen_select',
					'options'   => self::get_view_selection_posititions_options()
				),

				array(
					'title' 	=> __( 'View Selection Items Floating', 'radykal' ),
					'desc'	 	=> __( 'Enable floating for the items in the view selection, so these are aligned in one line.', 'radykal' ),
					'id' 		=> 'fpd_view_selection_floated',
					'default'	=> 'no',
					'type' 		=> 'checkbox'
				),

				array(
					'title' 	=> __( 'Hide On Smartphones', 'radykal' ),
					'desc'	 	=> sprintf(__( 'Hide product designer on smartphones and display an <a href="%s">information</a> instead.', 'radykal'), esc_url(admin_url('admin.php?page=wc-settings&tab=fancy_product_designer&section=labels#fpd_not_supported_device_info')) ),
					'id' 		=> 'fpd_disable_on_smartphones',
					'default'	=> 'no',
					'type' 		=> 'checkbox'
				),

				array(
					'title' 	=> __( 'Hide On Tablets', 'radykal' ),
					'desc'	 	=> sprintf(__( 'Hide product designer on tablets and display an <a href="%s">information</a> instead.', 'radykal' ), esc_url(admin_url('admin.php?page=wc-settings&tab=fancy_product_designer&section=labels#fpd_not_supported_device_info')) ),
					'id' 		=> 'fpd_disable_on_tablets',
					'default'	=> 'no',
					'type' 		=> 'checkbox'
				),

				array( 'type' => 'sectionend'),

				array(	'title' => __( 'Colors', 'radykal' ), 'type' => 'title' ),

				array( 'type' => 'fpd_styling'),

				array( 'type' => 'sectionend'),

				array(	'title' => __( 'Menu Bar', 'radykal' ), 'type' => 'title','desc' => '' ),

				array(
					'title' 	=> __( 'Position', 'radykal' ),
					'desc' 		=> __( 'The position of the menu bar.', 'radykal' ),
					'id' 		=> 'fpd_menu_bar_position',
					'css' 		=> 'min-width:350px;',
					'default'	=> 'outside',
					'desc_tip'	=>  true,
					'type' 		=> 'select',
					'class'		=> 'chosen_select',
					'options'   => array(
						'inside' => __( 'Inside the Product Stage', 'radykal' ),
						'outside' => __( 'Above the Product Stage', 'radykal' ),
					)
				),

				array(
					'title' 	=> __( 'Custom Image Upload', 'radykal' ),
					'desc'	 	=> __( 'Can customers upload own images to the product?', 'radykal' ),
					'id' 		=> 'fpd_upload_designs',
					'default'	=> 'yes',
					'type' 		=> 'checkbox'
				),

				array(
					'title' 	=> __( 'Custom Text', 'radykal' ),
					'desc'	 	=> __( 'Can customers add own text elements to the product?', 'radykal' ),
					'id' 		=> 'fpd_custom_texts',
					'default'	=> 'yes',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Zoom Factor', 'radykal' ),
					'desc' 		=> __( 'The factor for zooming in and out. A value lower or equal 1, will disable the zoom buttons.', 'radykal' ),
					'id' 		=> 'fpd_zoom_factor',
					'default'	=> '1.2',
					'type' 		=> 'number',
					'desc_tip'	=>  true,
					'custom_attributes' => array(
						'min' 	=> 1,
						'step' 	=> 0.01
					)
				),

				array(
					'title' => __( 'Download Product Image', 'radykal' ),
					'desc' 		=> __( 'Can customers download a product image?', 'radykal' ),
					'id' 		=> 'fpd_download_product_image',
					'default'	=> 'yes',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Save as PDF', 'radykal' ),
					'desc' 		=> __( 'Can customers save the product as PDF?', 'radykal' ),
					'id' 		=> 'fpd_pdf_button',
					'default'	=> 'yes',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Print', 'radykal' ),
					'desc' 		=> __( 'Can customers print the product?', 'radykal' ),
					'id' 		=> 'fpd_print',
					'default'	=> 'yes',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Allow Product Saving', 'radykal' ),
					'desc' 		=> __( 'Can customers save their customized products?', 'radykal' ),
					'id' 		=> 'fpd_allow_product_saving',
					'default'	=> 'yes',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Resettable', 'radykal' ),
					'desc' 		=> __( 'Can customers reset the product?', 'radykal' ),
					'id' 		=> 'fpd_reset',
					'default'	=> 'yes',
					'type' 		=> 'checkbox'
				),

				array( 'type' => 'sectionend'),

				array(	'title' => __( 'Product Page', 'radykal' ), 'type' => 'title','desc' => '' ),

				array(
					'title' 	=> __( 'Product Designer Positioning', 'radykal' ),
					'desc' 		=> __( 'By default the product designer will replace the product image. You can only use a custom hook to place it anywhere in the product page. Check out the documentation how to use the custom hook!', 'radykal' ),
					'desc_tip'	=>  true,
					'id' 		=> 'fpd_placement',
					'css' 		=> 'min-width:350px;',
					'default'	=> 'fpd-replace-image',
					'type' 		=> 'select',
					'class'		=> 'chosen_select',
					'options'   => array(
						'fpd-replace-image'	 => __( 'Replace Product Image', 'radykal' ),
						'fpd-custom-hook' => __( 'Use Custom Hook', 'radykal' ),
					)
				),

				array(
					'title' => __( 'Show Product Image', 'radykal' ),
					'desc' 		=> __( 'Show the product image as well, this could cause that the product designer is not aligned correctly.', 'radykal' ),
					'id' 		=> 'fpd_template_product_image',
					'default'	=> 'no',
					'type' 		=> 'checkbox'
				),

				array(
					'title' 	=> __( 'No Sidebar', 'radykal' ),
					'desc'	 	=> __( 'A template without sidebar and the product title will be placed over the product designer.', 'radykal' ),
					'id' 		=> 'fpd_template_full',
					'default'	=> 'no',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Custom CSS', 'radykal' ),
					'desc' 		=> __( 'CSS styles for fancy product pages.', 'radykal' ),
					'id' 		=> 'fpd_custom_css',
					'type' 		=> 'textarea',
					'css' 		=> 'width:500px;height:150px;font-size:12px;',
					'default'	=> self::$custom_css
				),

				array(
					'title' => __( 'Button CSS class', 'radykal' ),
					'desc' 		=> __( 'The CSS class that will be added to the "Start Customizing" and lightbox buttons.', 'radykal' ),
					'id' 		=> 'fpd_start_customizing_css_class',
					'css' 		=> 'width:500px;',
					'default'	=> '',
					'desc_tip'	=>  true,
					'type' 		=> 'text'
				),

				array( 'type' => 'sectionend'),

				array(	'title' => __( 'Miscellaneous', 'radykal' ), 'type' => 'title' ),

				array(
					'title' => __( 'Show Fonts Dropdown', 'radykal' ),
					'desc' 		=> __( 'Let the customers select a font for text elements', 'radykal' ),
					'id' 		=> 'fpd_fonts_dropdown',
					'default'	=> 'yes',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Tooltips', 'radykal' ),
					'desc' 		=> __( 'Use tooltips in the product designer.', 'radykal' ),
					'id' 		=> 'fpd_tooltips',
					'default'	=> 'yes',
					'type' 		=> 'checkbox'
				),

				array(
					'title' 	=> __( 'Use Label Settings', 'radykal' ),
					'desc'	 	=> __( 'Use the labels from the "Labels" settings page. If you want to translate the labels with a multilingual plugin like WPML, deactivate this option.', 'radykal' ),
					'id' 		=> 'fpd_use_label_settings',
					'default'	=> 'yes',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Image Uploader', 'radykal' ),
					'id' 		=> 'fpd_type_of_uploader',
					'default'	=> 'filereader',
					'type' 		=> 'radio',
					'desc_tip'	=>  __( 'When customers can upload own images, you can choose between 2 different uploaders.', 'radykal' ),
					'options'	=> array(
						'filereader' => __( 'Filereader Uploader', 'radykal' ),
						'php' => __( 'PHP Uploader', 'radykal' )
					),
				),

				array(
					'title' 	=> __( 'Maximum Image Size (MB)', 'radykal' ),
					'desc' 		=> __( 'The maximum image size in Megabytes, when using the PHP uploader.', 'radykal' ),
					'id' 		=> 'fpd_max_image_size',
					'css' 		=> 'width:70px;',
					'default'	=> '1',
					'desc_tip'	=>  true,
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' 	=> __( 'Only logged-in users can upload images?', 'radykal' ),
					'desc'	 	=> __( 'Because the PHP uploader uploads the image to your web server, you can allow the image upload for logged-in users only.', 'radykal' ),
					'id' 		=> 'fpd_upload_designs_php_logged_in',
					'default'	=> 'no',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Facebook App-ID', 'radykal' ),
					'desc' 		=> __( 'To allow users to add photos from facebook, you have to enter a Facebook App-Id.', 'radykal' ),
					'id' 		=> 'fpd_facebook_app_id',
					'css' 		=> 'width:500px;',
					'default'	=> '',
					'desc_tip'	=>  true,
					'type' 		=> 'text'
				),

				array(
					'title' => __( 'Instagram Client ID', 'radykal' ),
					'desc' 		=> __( 'To allow users to add photos from instagram, you have to enter a Instagram Client ID.', 'radykal' ),
					'id' 		=> 'fpd_instagram_client_id',
					'css' 		=> 'width:500px;',
					'default'	=> '',
					'desc_tip'	=>  true,
					'type' 		=> 'text'
				),

				array(
					'title' => __( 'Instagram Redirect URI', 'radykal' ),
					'desc' 		=> __( 'This is the URI you need to paste in as OAuth Redirect URI when creating a Instagram Client ID. Do not change it!', 'radykal' ),
					'id' 		=> 'fpd_instagram_redirect_uri',
					'css' 		=> 'width:500px;',
					'default'	=> plugins_url( '/inc/instagram_auth.php', dirname(__FILE__) ),
					'desc_tip'	=>  true,
					'type' 		=> 'text'
				),

				array(
					'title' => __( 'Minimum Zoom', 'radykal' ),
					'desc' 		=> __( 'The minimum zoom factor for zooming out.', 'radykal' ),
					'id' 		=> 'fpd_min_zoom_range',
					'css' 		=> 'width:60px;',
					'default'	=> '0.2',
					'type' 		=> 'number',
					'desc_tip'	=>  true,
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 0.1
					)
				),

				array(
					'title' => __( 'Maximum Zoom', 'radykal' ),
					'desc' 		=> __( 'The maximum zoom factor for zooming in.', 'radykal' ),
					'id' 		=> 'fpd_max_zoom_range',
					'css' 		=> 'width:60px;',
					'default'	=> '2',
					'type' 		=> 'number',
					'desc_tip'	=>  true,
					'custom_attributes' => array(
						'min' 	=> 1,
						'step' 	=> 0.1
					)
				),

				array(
					'title' => __( 'Padding Controls', 'radykal' ),
					'desc' 		=> __( 'The padding of the controls when an element is selected in the product stage.', 'radykal' ),
					'id' 		=> 'fpd_padding_controls',
					'css' 		=> 'width:60px;',
					'default'	=> '7',
					'type' 		=> 'number',
					'desc_tip'	=>  true,
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' => __( 'Hexadecimal Color Names', 'radykal' ),
					'desc' 		=> __( 'You can set custom names for your hexadecimal colors, that will be used in the tooltips, when using a predefined color list. Example: 000000:black,ffffff:white', 'radykal' ),
					'id' 		=> 'fpd_hex_names',
					'css' 		=> 'width:500px;',
					'default'	=> '',
					'desc_tip'	=>  true,
					'type' 		=> 'text'
				),

				array(
					'title' => __( '"Start Customizing" Button', 'radykal' ),
					'desc' 		=> __( 'Enable a "Start Customizing" Button, that will show the standard product first. The product designer will come up first, if the customer clicks on this button. Just enter a text for the button to enable this feature.', 'radykal' ),
					'id' 		=> 'fpd_start_customizing_button',
					'css' 		=> 'width:500px;',
					'default'	=> '',
					'desc_tip'	=>  true,
					'type' 		=> 'text'
				),

				array(
					'title' 	=> __( 'Open in lightbox', 'radykal' ),
					'desc'	 	=> __( 'When using the "Start Customizing" button, open product designer in a lightbox.', 'radykal' ),
					'id' 		=> 'fpd_open_in_lightbox',
					'default'	=> 'no',
					'type' 		=> 'checkbox'
				),

				array(
					'title' 	=> __( 'Lightbox: Add to cart', 'radykal' ),
					'desc'	 	=> __( 'When clicking the submit button in the lightbox, add designed product directly into cart.', 'radykal' ),
					'id' 		=> 'fpd_lightbox_add_to_cart',
					'default'	=> 'no',
					'type' 		=> 'checkbox'
				),

				array(
					'title' 	=> __( 'Debug Mode', 'radykal' ),
					'desc' 		=> __( 'Theme Check and loading of unminified Javascript files.', 'radykal' ),
					'desc_tip'	=> __( 'Enabling this option will help you to find any issue in the product page. A modal window will come up and gives you information about missing hooks in your theme.', 'radykal' ),
					'id' 		=> 'fpd_debug_mode',
					'default'	=> 'no',
					'type' 		=> 'checkbox'
				),

				array( 'type' => 'sectionend'),
			));
		}

		public static function get_default_parameters_settings() {

			return apply_filters('fancy_product_designer_default_parameters_settings', array(

				array(
					'title' => __( 'Image Parameters', 'radykal' ),
					'type' => 'title',
					'desc' => __('The parameters for all image elements in the Fancy Design Categories and uploaded by the customer.', 'radykal')
				),

				array(
					'title' => __( 'X-Position', 'radykal' ),
					'id' 		=> 'fpd_designs_parameter_x',
					'css' 		=> 'width:70px;',
					'default'	=> '0',
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' => __( 'Y-Position', 'radykal' ),
					'id' 		=> 'fpd_designs_parameter_y',
					'css' 		=> 'width:70px;',
					'default'	=> '0',
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' => __( 'Z-Position', 'radykal' ),
					'desc' 		=> __( '-1 means that the element will be added at the top. An value higher than that, will add the element to that z-position.', 'radykal' ),
					'id' 		=> 'fpd_designs_parameter_z',
					'css' 		=> 'width:70px;',
					'default'	=> '-1',
					'desc_tip'	=>  true,
					'type' 		=> 'text',
					'custom_attributes' => array(
						'min' 	=> -1,
						'step' 	=> 1
					)
				),

				array(
					'title' => __( 'Colors', 'radykal' ),
					'desc' 		=> __( 'Enter hex color(s) separated by comma.', 'radykal' ),
					'id' 		=> 'fpd_designs_parameter_colors',
					'css' 		=> 'width:300px;',
					'default'	=> '',
					'desc_tip'	=>  true,
					'type' 		=> 'text'
				),

				array(
					'title' => __( 'Price', 'radykal' ),
					'desc' 		=> __( 'Enter the additional price for a design element. Use always a dot as decimal separator!', 'radykal' ),
					'id' 		=> 'fpd_designs_parameter_price',
					'css' 		=> 'width:70px;',
					'default'	=> '0',
					'desc_tip'	=>  true,
					'type' 		=> 'text',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' => __( 'Auto-Center', 'radykal' ),
					'id' 		=> 'fpd_designs_parameter_autoCenter',
					'default'	=> 'yes',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Draggable', 'radykal' ),
					'id' 		=> 'fpd_designs_parameter_draggable',
					'default'	=> 'yes',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Rotatable', 'radykal' ),
					'id' 		=> 'fpd_designs_parameter_rotatable',
					'default'	=> 'yes',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Resizable', 'radykal' ),
					'id' 		=> 'fpd_designs_parameter_resizable',
					'default'	=> 'yes',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Z-Changeable', 'radykal' ),
					'id' 		=> 'fpd_designs_parameter_zChangeable',
					'default'	=> 'yes',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Replace', 'radykal' ),
					'desc' 		=> __( 'Elements with the same replace name will replace each other.', 'radykal' ),
					'id' 		=> 'fpd_designs_parameter_replace',
					'css' 		=> 'width:150px;',
					'default'	=> '',
					'desc_tip'	=>  true,
					'type' 		=> 'text'
				),

				array(
					'title' => __( 'Auto-Select', 'radykal' ),
					'id' 		=> 'fpd_designs_parameter_autoSelect',
					'default'	=> 'no',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Stay On Top', 'radykal' ),
					'id' 		=> 'fpd_designs_parameter_topped',
					'default'	=> 'no',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Use another element as bounding box?', 'radykal' ),
					'id' 		=> 'fpd_designs_parameter_bounding_box_control',
					'class'		=> 'fpd-bounding-box-control',
					'default'	=> 'no',
					'type' 		=> 'checkbox'
				),

				array(
					'title' 	=> __( 'Bounding Box Target', 'radykal' ),
					'desc' 		=> __( 'Enter the title of another element that should be used as bounding box for design elements.', 'radykal' ),
					'id' 		=> 'fpd_designs_parameter_bounding_box_by_other',
					'css' 		=> 'width:150px;',
					'class'		=> 'fpd-bounding-box-target-input',
					'default'	=> '',
					'desc_tip'	=>  true,
					'type' 		=> 'text'
				),

				array(
					'title'		=> __( 'Bounding Box X-Position', 'radykal' ),
					'id' 		=> 'fpd_designs_parameter_bounding_box_x',
					'css' 		=> 'width:70px;',
					'class'		=> 'fpd-bounding-box-custom-input',
					'default'	=> '',
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' 	=> __( 'Bounding Box Y-Position', 'radykal' ),
					'id' 		=> 'fpd_designs_parameter_bounding_box_y',
					'css' 		=> 'width:70px;',
					'class'		=> 'fpd-bounding-box-custom-input',
					'default'	=> '',
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' 	=> __( 'Bounding Box Width', 'radykal' ),
					'id' 		=> 'fpd_designs_parameter_bounding_box_width',
					'css' 		=> 'width:70px;',
					'class'		=> 'fpd-bounding-box-custom-input',
					'default'	=> '',
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' 	=> __( 'Bounding Box Height', 'radykal' ),
					'id' 		=> 'fpd_designs_parameter_bounding_box_height',
					'css' 		=> 'width:70px;',
					'class'		=> 'fpd-bounding-box-custom-input',
					'default'	=> '',
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' => __( 'Bounding Box Clipping', 'radykal' ),
					'id' 		=> 'fpd_designs_parameter_boundingBoxClipping',
					'default'	=> 'no',
					'type' 		=> 'checkbox'
				),

				array( 'type' => 'sectionend'),

				array(
					'title' => __( 'Uploaded Image Parameters', 'radykal' ),
					'type' => 'title',
					'desc' => __('Additional parameters for uploaded images by the customer.', 'radykal')
				),

				array(
					'title' 	=> __( 'Minimum Width', 'radykal' ),
					'desc' 		=> __( 'The minimum image width for uploaded designs from the customers.', 'radykal' ),
					'id' 		=> 'fpd_uploaded_designs_parameter_minW',
					'css' 		=> 'width:70px;',
					'default'	=> '100',
					'desc_tip'	=>  true,
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' 	=> __( 'Minimum Height', 'radykal' ),
					'desc' 		=> __( 'The minimum image height for uploaded designs from the customers.', 'radykal' ),
					'id' 		=> 'fpd_uploaded_designs_parameter_minH',
					'css' 		=> 'width:70px;',
					'default'	=> '100',
					'desc_tip'	=>  true,
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' 	=> __( 'Maximum Width', 'radykal' ),
					'desc' 		=> __( 'The maximum image width for uploaded designs from the customers.', 'radykal' ),
					'id' 		=> 'fpd_uploaded_designs_parameter_maxW',
					'css' 		=> 'width:70px;',
					'default'	=> '1000',
					'desc_tip'	=>  true,
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' 	=> __( 'Maximum Height', 'radykal' ),
					'desc' 		=> __( 'The maximum image height for uploaded designs from the customers.', 'radykal' ),
					'id' 		=> 'fpd_uploaded_designs_parameter_maxH',
					'css' 		=> 'width:70px;',
					'default'	=> '1000',
					'desc_tip'	=>  true,
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' 	=> __( 'Resize To Width', 'radykal' ),
					'desc' 		=> __( 'Resize the uploaded image to this width, when width is larger than height.', 'radykal' ),
					'id' 		=> 'fpd_uploaded_designs_parameter_resizeToW',
					'css' 		=> 'width:70px;',
					'default'	=> '300',
					'desc_tip'	=>  true,
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' 	=> __( 'Resize To Height', 'radykal' ),
					'desc' 		=> __( 'Resize the uploaded image to this height, when height is larger than width.', 'radykal' ),
					'id' 		=> 'fpd_uploaded_designs_parameter_resizeToH',
					'css' 		=> 'width:70px;',
					'default'	=> '300',
					'desc_tip'	=>  true,
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array( 'type' => 'sectionend'),

				array(	'title' => __( 'Custom Text Parameters', 'radykal' ), 'type' => 'title','desc' => __('Set here the default parameters, that will be used when a customer adds a custom text.', 'radykal') ),

				array(
					'title' => __( 'Default Text', 'radykal' ),
					'desc' 		=> __( 'The default text when the customers add an own text element.', 'radykal' ),
					'id' 		=> 'fpd_default_text',
					'css' 		=> 'width:500px;',
					'default'	=> 'Double-click to change text',
					'desc_tip'	=>  true,
					'type' 		=> 'text'
				),

				array(
					'title' => __( 'X-Position', 'radykal' ),
					'desc' 		=> __( 'The x-position of the custom text element.', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_x',
					'css' 		=> 'width:70px;',
					'default'	=> '0',
					'desc_tip'	=>  true,
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' => __( 'Y-Position', 'radykal' ),
					'desc' 		=> __( 'The y-position of the custom text element.', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_y',
					'css' 		=> 'width:70px;',
					'default'	=> '0',
					'desc_tip'	=>  true,
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' => __( 'Z-Position', 'radykal' ),
					'desc' 		=> __( '-1 means that the element will be added at the top. An value higher than that, will add the element to that z-position.', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_z',
					'css' 		=> 'width:70px;',
					'default'	=> '-1',
					'desc_tip'	=>  true,
					'type' 		=> 'text',
					'custom_attributes' => array(
						'min' 	=> -1,
						'step' 	=> 1
					)
				),

				array(
					'title' => __( 'Colors', 'radykal' ),
					'desc' 		=> __( 'Enter hex color(s) separated by comma.', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_colors',
					'css' 		=> 'width:300px;',
					'default'	=> '#000000',
					'desc_tip'	=>  true,
					'type' 		=> 'text'
				),

				array(
					'title' => __( 'Price', 'radykal' ),
					'desc' 		=> __( 'Enter the additional price for a text element. Use always a dot as decimal separator!', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_price',
					'css' 		=> 'width:70px;',
					'default'	=> '0',
					'desc_tip'	=>  true,
					'type' 		=> 'text',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' => __( 'Auto-Center', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_autoCenter',
					'default'	=> 'yes',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Draggable', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_draggable',
					'default'	=> 'yes',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Rotatable', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_rotatable',
					'default'	=> 'yes',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Resizable', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_resizable',
					'default'	=> 'yes',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Z-Changeable', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_zChangeable',
					'default'	=> 'yes',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Replace', 'radykal' ),
					'desc' 		=> __( 'Elements with the same replace name will replace each other.', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_replace',
					'css' 		=> 'width:150px;',
					'default'	=> '',
					'desc_tip'	=>  true,
					'type' 		=> 'text'
				),

				array(
					'title' => __( 'Auto-Select', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_autoSelect',
					'default'	=> 'no',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Stay On Top', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_topped',
					'default'	=> 'no',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Patternable', 'radykal' ),
					'desc' 		=> __( 'Can the customer choose a pattern?', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_patternable',
					'default'	=> 'no',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Curvable', 'radykal' ),
					'desc' 		=> __( 'Can the customer make the text curved?', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_curvable',
					'default'	=> 'yes',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Curve Spacing', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_curveSpacing',
					'default'	=> 10,
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 1,
						'step' 	=> 1
					)
				),

				array(
					'title' => __( 'Curve Radius', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_curveRadius',
					'default'	=> 80,
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 1,
						'step' 	=> 1
					)
				),

				array(
					'title' => __( 'Curve Reverse', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_curveReverse',
					'default'	=> 'no',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Use another element as bounding box?', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_bounding_box_control',
					'class'		=> 'fpd-bounding-box-control',
					'default'	=> 'no',
					'type' 		=> 'checkbox'
				),

				array(
					'title' 	=> __( 'Bounding Box Target', 'radykal' ),
					'desc' 		=> __( 'Enter the title of another element that should be used as bounding box for design elements.', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_bounding_box_by_other',
					'css' 		=> 'width:150px;',
					'class'		=> 'fpd-bounding-box-target-input',
					'default'	=> '',
					'desc_tip'	=>  true,
					'type' 		=> 'text'
				),

				array(
					'title'		=> __( 'Bounding Box X-Position', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_bounding_box_x',
					'css' 		=> 'width:70px;',
					'class'		=> 'fpd-bounding-box-custom-input',
					'default'	=> '',
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' 	=> __( 'Bounding Box Y-Position', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_bounding_box_y',
					'css' 		=> 'width:70px;',
					'class'		=> 'fpd-bounding-box-custom-input',
					'default'	=> '',
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' 	=> __( 'Bounding Box Width', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_bounding_box_width',
					'css' 		=> 'width:70px;',
					'class'		=> 'fpd-bounding-box-custom-input',
					'default'	=> '',
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' 	=> __( 'Bounding Box Height', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_bounding_box_height',
					'css' 		=> 'width:70px;',
					'class'		=> 'fpd-bounding-box-custom-input',
					'default'	=> '',
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' => __( 'Bounding Box Clipping', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_boundingBoxClipping',
					'default'	=> 'no',
					'type' 		=> 'checkbox'
				),

				array(
					'title' => __( 'Default Text Size', 'radykal' ),
					'desc' 		=> __( 'The default text size for all text elements.', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_textSize',
					'css' 		=> 'width:70px;',
					'default'	=> '18',
					'desc_tip'	=>  true,
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				),

				array(
					'title' => __( 'Default Font', 'radykal' ),
					'desc' 		=> __( 'Enter the default font. If you leave it empty, the first font from the fonts dropdown will be used.', 'radykal' ),
					'id' 		=> 'fpd_font',
					'css' 		=> 'width:300px;',
					'default'	=> '',
					'desc_tip'	=>  true,
					'type' 		=> 'text'
				),

				array(
					'title' 	=> __( 'Maximum Characters', 'radykal' ),
					'desc' 		=> __( 'You can limit the number of characters. 0 means unlimited characters.', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_maxLength',
					'css' 		=> 'width:70px;',
					'default'	=> 0,
					'desc_tip'	=>  true,
					'type' 		=> 'number'
				),

				array(
					'title' 	=> __( 'Alignment', 'radykal' ),
					'id' 		=> 'fpd_custom_texts_parameter_textAlign',
					'css' 		=> 'min-width:350px;',
					'default'	=> 'left',
					'type' 		=> 'select',
					'class'		=> 'chosen_select',
					'options'   => array(
						'left' => __( 'Left', 'radykal' ),
						'center' => __( 'Center', 'radykal' ),
						'right' => __( 'Right', 'radykal' )
					)
				),

				array( 'type' => 'sectionend'),

				array(	'title' => __( 'Common Parameters', 'radykal' ),
						'type' => 'title',
						'desc' => __( 'Common parameters for image and text elements.', 'radykal' )
				),

				array(
					'title' => __( 'Origin-X Point', 'radykal' ),
					'id' 		=> 'fpd_common_parameter_originX',
					'css' 		=> 'min-width:350px;',
					'default'	=> 'center',
					'type' 		=> 'select',
					'class'		=> 'chosen_select',
					'options'   => array(
						'center'	 => __( 'Center', 'radykal' ),
						'left' => __( 'Left', 'radykal' ),
					)
				),

				array(
					'title' => __( 'Origin-Y Point', 'radykal' ),
					'id' 		=> 'fpd_common_parameter_originY',
					'css' 		=> 'min-width:350px;',
					'default'	=> 'center',
					'type' 		=> 'select',
					'class'		=> 'chosen_select',
					'options'   => array(
						'center'	 => __( 'Center', 'radykal' ),
						'top' => __( 'Top', 'radykal' ),
					)
				),

				array( 'type' => 'sectionend'),

			));

		}

		public static function get_labels_settings() {

			return apply_filters('fancy_product_designer_labels_settings', array(

				array(
					'title' => __( 'Sidebar Labels', 'radykal' ),
					'type' => 'title',
					'desc' => __( 'Edit the text and tooltips for the elements in the sidebar.', 'radykal' )
				),

				/*array(
					'title' => __( 'Navigation Products Tooltip', 'radykal' ),
					'desc' 		=> __( 'Tooltip for the "Products" navigation tab.', 'radykal' ),
					'id' 		=> 'fpd_navigation_tab_products',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Products'
				),*/

				array(
					'title' => __( 'Designs Menu', 'radykal' ),
					'id' 		=> 'fpd_navigation_tab_designs',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Designs'
				),

				array(
					'title' => __( 'Edit-Elements Menu', 'radykal' ),
					'id' 		=> 'fpd_navigation_tab_edit_elements',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Edit Elements'
				),

				array(
					'title' => __( 'Facebook Menu', 'radykal' ),
					'id' 		=> 'fpd_navigation_tab_facebook',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Facebook Photos'
				),

				array(
					'title' => __( 'Instagram Menu', 'radykal' ),
					'id' 		=> 'fpd_navigation_tab_instagram',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Instagram Photos'
				),

				array(
					'title' => __( 'Edit Elements: Headline', 'radykal' ),
					'id' 		=> 'fpd_customize_headline',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Edit Elements'
				),

				array(
					'title' => __( 'Edit Elements: None in Elements Dropdown', 'radykal' ),
					'id' 		=> 'fpd_customize_dropdown_none',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'None'
				),

				array(
					'title' => __( 'Edit Elements: Filling', 'radykal' ),
					'id' 		=> 'fpd_customize_section_filling',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Filling'
				),

				array(
					'title' => __( 'Edit Elements: Font & Styles', 'radykal' ),
					'id' 		=> 'fpd_customize_section_font_styles',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Font & Styles'
				),

				array(
					'title' => __( 'Edit Elements: Curved Text', 'radykal' ),
					'id' 		=> 'fpd_customize_section_curved_text',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Curved Text'
				),

				array(
					'title' => __( 'Edit Elements: Helpers', 'radykal' ),
					'id' 		=> 'fpd_customize_section_helpers',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Helpers'
				),

				array(
					'title' => __( 'Edit Elements: Align Left', 'radykal' ),
					'id' 		=> 'fpd_customize_text_tooltip_align_left',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Align Left'
				),

				array(
					'title' => __( 'Edit Elements: Align Center', 'radykal' ),
					'id' 		=> 'fpd_customize_text_tooltip_align_center',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Align Center'
				),

				array(
					'title' => __( 'Edit Elements: Align Right', 'radykal' ),
					'id' 		=> 'fpd_customize_text_tooltip_align_right',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Align Right'
				),

				array(
					'title' => __( 'Edit Elements: Bold', 'radykal' ),
					'id' 		=> 'fpd_customize_text_tooltip_bold',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Bold'
				),

				array(
					'title' => __( 'Edit Elements: Italic', 'radykal' ),
					'id' 		=> 'fpd_customize_text_tooltip_italic',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Italic'
				),

				array(
					'title' => __( 'Edit Elements: Center Horizontal', 'radykal' ),
					'id' 		=> 'fpd_customize_tooltip_center_horizontal',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Center Horizontal'
				),

				array(
					'title' => __( 'Edit Elements: Center Vertical', 'radykal' ),
					'id' 		=> 'fpd_customize_tooltip_center_vertical',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Center Vertical'
				),

				array(
					'title' => __( 'Edit Elements: Bring-It-Down', 'radykal' ),
					'id' 		=> 'fpd_customize_tooltip_move_it_down',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Bring it down'
				),

				array(
					'title' => __( 'Edit Elements: Bring-It-Up', 'radykal' ),
					'id' 		=> 'fpd_customize_tooltip_move_it_up',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Bring it up'
				),

				array(
					'title' => __( 'Edit Elements: Reset', 'radykal' ),
					'id' 		=> 'fpd_customize_tooltip_reset',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Reset to his origin'
				),

				array(
					'title' => __( 'Edit Elements: Trash', 'radykal' ),
					'id' 		=> 'fpd_customize_tooltip_trash',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Trash'
				),

				array(
					'title' => __( 'Curved Text: Info', 'radykal' ),
					'id' 		=> 'fpd_curved_text_info',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'You can only change the text when you switch to normal text.'
				),

				array(
					'title' => __( 'Curved Text: Switcher', 'radykal' ),
					'id' 		=> 'fpd_curved_text_switcher',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Switch between curved and normal Text'
				),

				array(
					'title' => __( 'Curved Text: Reverse', 'radykal' ),
					'id' 		=> 'fpd_curved_text_reverse',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Reverse'
				),

				array(
					'title' => __( 'Curved Text: Spacing', 'radykal' ),
					'id' 		=> 'fpd_curved_text_spacing',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Spacing'
				),

				array(
					'title' => __( 'Curved Text: Radius', 'radykal' ),
					'id' 		=> 'fpd_curved_text_radius',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Radius'
				),

				array(
					'title' => __( 'Facebook: Headline', 'radykal' ),
					'id' 		=> 'fpd_fb_headline',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Add Facebook Photos'
				),

				array(
					'title' => __( 'Facebook: Select album', 'radykal' ),
					'id' 		=> 'fpd_select_album',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Select an album'
				),

				array(
					'title' => __( 'Instagram: Headline', 'radykal' ),
					'id' 		=> 'fpd_insta_headline',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Add Instagram Photos'
				),

				array(
					'title' => __( 'Instagram: My Feed', 'radykal' ),
					'id' 		=> 'fpd_insta_my_feed',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'My Feed'
				),

				array(
					'title' => __( 'Instagram: My Recent Images', 'radykal' ),
					'id' 		=> 'fpd_insta_recent_images',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'My Recent Images'
				),

				array(
					'title' => __( 'Instagram: Load next stack', 'radykal' ),
					'id' 		=> 'fpd_insta_load_next',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Load next stack'
				),

				array( 'type' => 'sectionend'),

				array(	'title' => __( 'Product Stage Labels', 'radykal' ), 'type' => 'title','desc' => __( 'Edit the texts and tooltips for the elements in the product stage.', 'radykal' ) ),

				array(
					'title' => __( 'Add Image', 'radykal' ),
					'id' 		=> 'fpd_stage_menu_bar_add_image',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Add your own Image'
				),

				array(
					'title' => __( 'Add Text', 'radykal' ),
					'id' 		=> 'fpd_stage_menu_bar_add_text',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Add your own text'
				),

				array(
					'title' => __( 'Zoom In', 'radykal' ),
					'id' 		=> 'fpd_stage_menu_bar_zoom_in',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Zoom In'
				),

				array(
					'title' => __( 'Zoom Out', 'radykal' ),
					'id' 		=> 'fpd_stage_menu_bar_zoom_out',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Zoom Out'
				),

				array(
					'title' => __( 'Zoom Reset', 'radykal' ),
					'id' 		=> 'fpd_stage_menu_bar_zoom_reset',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Zoom Reset'
				),


				array(
					'title' => __( 'Download Product Image', 'radykal' ),
					'id' 		=> 'fpd_stage_menu_bar_download_product_image',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Download Product Image'
				),

				array(
					'title' => __( 'Print', 'radykal' ),
					'id' 		=> 'fpd_stage_menu_bar_print',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Print'
				),

				array(
					'title' => __( 'Save As PDF', 'radykal' ),
					'id' 		=> 'fpd_stage_menu_bar_pdf',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Save As PDF'
				),

				array(
					'title' => __( 'Save Product', 'radykal' ),
					'id' 		=> 'fpd_stage_menu_bar_save_product',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Save Product'
				),

				array(
					'title' => __( 'Your saved products', 'radykal' ),
					'id' 		=> 'fpd_stage_menu_bar_your_saved_product',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Your saved products'
				),

				array(
					'title' => __( 'Reset Product', 'radykal' ),
					'id' 		=> 'fpd_stage_menu_bar_reset',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Reset Product'
				),

				array( 'type' => 'sectionend'),

				array(	'title' => __( 'Miscellaneous', 'radykal' ), 'type' => 'title'),

				array(
					'title' => __( 'Out Of Containment Alert', 'radykal' ),
					'id' 		=> 'fpd_out_of_containment_alert',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'is out of his containment!'
				),

				array(
					'title' => __( 'Uploaded Design Size Alert', 'radykal' ),
					'desc_tip'	=>  true,
					'id' 		=> 'fpd_uploaded_design_size_alert',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Sorry! But the uploaded image size does not conform our indication of size.'
				),

				array(
					'title' => __( 'Confirm Product Deletion', 'radykal' ),
					'id' 		=> 'fpd_confirm_product_delete',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Delete saved product?'
				),

				array(
					'title' => __( 'Colorpicker Cancel', 'radykal' ),
					'id' 		=> 'fpd_colorpicker_cancel',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Cancel'
				),

				array(
					'title' => __( 'Colorpicker Choose', 'radykal' ),
					'id' 		=> 'fpd_colorpicker_choose',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Choose'
				),

				array(
					'title' => __( 'Catalog: Add To Cart Button', 'radykal' ),
					'id' 		=> 'fpd_add_to_cart_text',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Customize'
				),

				array(
					'title' => __( 'Initializing Text', 'radykal' ),
					'desc_tip'	=>  true,
					'id' 		=> 'fpd_init_text',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Initializing product designer'
				),

				array(
					'title' => __( 'Uploaded Images Category Name', 'radykal' ),
					'desc_tip'	=>  true,
					'id' 		=> 'fpd_uploaded_images_category_name',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Your uploaded images'
				),

				array(
					'title' => __( 'Not Supported Device Information', 'radykal' ),
					'desc' 		=> __( 'The text that will be displayed instead the product designer, if you disable the product designer for smartphones or tablets.', 'radykal' ),
					'desc_tip'	=>  true,
					'id' 		=> 'fpd_not_supported_device_info',
					'type' 		=> 'textarea',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Sorry! But the product designer is not adapted for your device. Please use a device with a larger screen!'
				),

				array(
					'title' => __( 'Lightbox Submit Button', 'radykal' ),
					'desc_tip'	=>  true,
					'id' 		=> 'fpd_lightbox_submit_button',
					'type' 		=> 'text',
					'css' 		=> 'min-width:300px;',
					'default'	=> 'Done'
				),

				array( 'type' => 'sectionend'),

			));

		}

		public static function get_fonts_settings() {

			return apply_filters('fancy_product_designer_fonts_settings', array(

				array(
					'title' => __( 'Set the fonts for the fonts dropdown', 'radykal' ),
					'type' => 'title'
				),

				array(
					'title' => __( 'Common Fonts', 'radykal' ),
					'desc' 		=> 'Enter here common fonts separated by comma, which are installed on all system by default, e.g. Arial.',
					'id' 		=> 'fpd_common_fonts',
					'css' 		=> 'width:500px;',
					'type' 		=> 'text',
					'desc_tip'	=>  true,
					'default'	=> 'Arial,Helvetica,Times New Roman,Verdana,Geneva'
				),

				array(
					'title' 	=> __( 'Google Webfonts', 'radykal' ),
					'desc' 		=> __( "Choose fonts from Google Webfonts. Note that too many fonts will slow down the loading of your website.", 'radykal' ),
					'id' 		=> 'fpd_google_webfonts',
					'css' 		=> 'min-width:500px;',
					'default'	=> '',
					'type' 		=> 'multiselect',
					'class'		=> 'chosen_select',
					'desc_tip'	=>  true,
					'value'		=> '',
					'options' 	=> FPD_Fonts::get_google_webfonts()
				),

				array(
					'title' 	=> __( 'Fonts Directory', 'radykal' ),
					'desc' 		=> __( "You can add your own fonts to the fonts directory of the plugin, these font files need to be .woff files.", 'radykal' ),
					'id' 		=> 'fpd_fonts_directory',
					'css' 		=> 'min-width:500px;',
					'default'	=> '',
					'type' 		=> 'multiselect',
					'class'		=> 'chosen_select',
					'desc_tip'	=>  true,
					'options' 	=> FPD_Fonts::get_woff_fonts()
				),

				array( 'type' => 'sectionend'),

			));

		}

		public static function get_settings() {

			return array(
				'general' => self::get_general_settings(),
				'default_parameters' => self::get_default_parameters_settings(),
				'labels'=> self::get_labels_settings(),
				'fonts' => self::get_fonts_settings()
			);

		}

		/**
		 * Get the layout options
		 *
		 */
		public static function get_layouts_options() {

			return array(
				'icon-sb-left'	 => __( 'Flat - Left Sidebar', 'radykal' ),
				'icon-sb-top' => __( 'Flat - Top Sidebar', 'radykal' ),
				'icon-sb-right'	 => __( 'Flat - Right Sidebar', 'radykal' ),
				'icon-sb-bottom' => __( 'Flat - Bottom Sidebar', 'radykal' ),
				'semantic' => __( 'Semantic', 'radykal' )
			);

		}

		/**
		 * Get the view selection positions options
		 *
		 */
		public static function get_view_selection_posititions_options() {

			return array(
				'tr'	 => __( 'Top-Right in Product Stage', 'radykal' ),
				'tl' => __( 'Top-Left in Product Stage', 'radykal' ),
				'br'	 => __( 'Bottom-Right in Product Stage', 'radykal' ),
				'bl' => __( 'Bottom-Left in Product Stage', 'radykal' ),
				'outside' => __( 'Under the Product Stage', 'radykal' )
			);

		}

		/**
		 * Get an option value. If no value is found in database, return default value
		 *
		 */
		public static function get_option( $key ) {

			if( get_option($key) === false ) {

				return self::get_default_option($key);

			}
			else {

				//check if option is type of number and has an empty string as value
				if( self::get_option_type($key) == 'number' && trim(get_option($key)) == '') {
					return self::get_default_option($key);
				}
				else {
					return fpd_convert_string_value_to_int(get_option($key));
				}

			}

		}

		/**
		 * Get the default value of an option
		 *
		 */
		public static function get_default_option( $key ) {

			foreach(self::get_settings() as $section_settings ) {

				foreach( $section_settings as $section_option ) {
					if(isset($section_option['id']) && $section_option['id'] == $key) {
						return fpd_convert_string_value_to_int($section_option['default']);
						break;
					}
				}

			}

			return false;

		}

		/**
		 * Get the type of an option
		 *
		 */
		public static function get_option_type( $key ) {

			foreach(self::get_settings() as $section_settings ) {

				foreach( $section_settings as $section_option ) {
					if(isset($section_option['id']) && $section_option['id'] == $key) {
						return $section_option['type'];
						break;
					}
				}

			}

			return false;

		}

	}

}

?>