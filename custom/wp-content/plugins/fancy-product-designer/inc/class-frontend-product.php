<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if(!class_exists('FPD_Frontend_Product')) {

	class FPD_Frontend_Product {

		public function __construct() {

			require_once(FPD_PLUGIN_DIR.'/inc/class-parameters.php');

			//CATEGORY
			add_filter( 'woocommerce_loop_add_to_cart_link', array(&$this, 'add_to_cart_cat_text'), 10, 2 );


			//SINGLE FANCY PRODUCT
			add_filter( 'template_include', array( &$this, 'use_custom_template'), 99 );
			add_filter( 'body_class', array( &$this, 'add_fancy_product_class') );
			//add product designer
			if( get_option('fpd_placement')  == 'fpd-replace-image') {
				add_action( 'woocommerce_before_single_product_summary', array( &$this, 'add_product_designer'), 15 );
			}
			else {
				add_action( 'fpd_product_designer', array( &$this, 'add_product_designer') );
			}

			//add customize button
			add_action( 'woocommerce_single_product_summary', array( &$this, 'add_customize_button'), 25 );
			//add additional form fields to cart form
			add_action( 'woocommerce_before_add_to_cart_button', array( &$this, 'add_product_designer_form') );
			//php uploader - image upload
			add_action( 'wp_ajax_fpduploadimage', array( &$this, 'upload_image' ) );
			if( get_option('fpd_upload_designs_php_logged_in') == 'no' ) {
				add_action( 'wp_ajax_nopriv_fpduploadimage', array( &$this, 'upload_image' ) );
			}


		}

		//custom text for the add-to-cart button in catalog
		public function add_to_cart_cat_text( $handler, $product ) {

			if( is_fancy_product( $product->id ) ) {
				return sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="button product_type_%s">%s</a>',
					esc_url( get_permalink($product->ID) ),
					esc_attr( $product->id ),
					esc_attr( $product->get_sku() ),
					esc_attr( $product->product_type ),
					esc_html( get_option( 'fpd_add_to_cart_text' ) )
				);
			}

			return $handler;

		}

		//loads a custom template for single fancy product pages
		public function use_custom_template( $template ) {

			global $post;

			$template_slug = basename(rtrim( $template, '.php' ));
			if($template_slug == 'single-product' && is_fancy_product($post->ID) && get_option('fpd_template_full') == 'yes') {
				//set ptoduct title above product designer
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
				add_action( 'woocommerce_before_single_product_summary', 'woocommerce_template_single_title', 10 );

				$template = FPD_PLUGIN_DIR . '/single-fancy-product.php';
			}

			return $template;

		}

		//add fancy-product class in body
		public function add_fancy_product_class( $classes ) {

			global $post;
			if( is_fancy_product( $post->ID ) ) {

				$fancy_product = new Fancy_Product( $post->ID );

				$classes[] = 'fancy-product';

				if( fpd_start_customizing_button_used($post->ID) || (isset($_GET['cart_item_key']) && $fancy_product->get_option('open_in_lightbox')) ) {
					$classes[] = 'fpd-customize-button-visible';
				}
				else {
					$classes[] = 'fpd-customize-button-hidden';
				}

				//check if tablets are supported
				if( FPD_Admin_Settings::get_option( 'fpd_disable_on_tablets' ) )
					$classes[] = 'fpd-hidden-tablets';


				//check if smartphones are supported
				if( FPD_Admin_Settings::get_option( 'fpd_disable_on_smartphones' ) )
					$classes[] = 'fpd-hidden-mobile';

			}

			return $classes;

		}

		//the actual product designer will be added
		public function add_product_designer() {

			global $post, $wpdb, $product, $woocommerce;

			$master_id = fpd_get_master_id( $post->ID );
			$fancy_product = new Fancy_Product( $master_id );
			$open_in_lightbox = $fancy_product->get_option('open_in_lightbox') && trim($fancy_product->get_option('start_customizing_button')) != '';

			if( is_fancy_product( $master_id  ) && (!fpd_start_customizing_button_used($post->ID) || $open_in_lightbox) ) {

				$fpd_parameters = new FPD_Parameters( $master_id );

				//remove product image, there you gonna see the product designer
				if( !FPD_Admin_Settings::get_option('fpd_template_product_image') && !$open_in_lightbox ) {
					remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
				}

				FPD_Scripts_Styles::$add_script = true;
				$selector = 'fancy-product-designer-'.$master_id.'';

				$load_from_form = 0;
				//added to cart, recall added product
				if( isset($_POST['fpd_product']) ) {

					$load_from_form = 1;
					$views = $_POST['fpd_product'];
					$views = stripslashes($views);

				}
				else if( isset($_GET['cart_item_key']) ) {

					//load from cart item
					$load_from_form = 1;
					$cart = $woocommerce->cart->get_cart();
					$cart_item = $cart[$_GET['cart_item_key']];
					if($cart_item) {
						if( isset($cart_item['fpd_data']) ) {
							$views = $cart_item['fpd_data']['fpd_product'];
							$views = stripslashes($views);
						}
					}
					else {
						//cart item could not be found
						echo '<p><strong>';
						_e('Sorry, but the cart item could not be found!', 'radykal');
						echo '</strong></p>';
						return;
					}

				}
				else if( isset($_GET['order']) && isset($_GET['item_id']) ) {

					//load ordered product in designer
					$load_from_form = 1;
					$order = new WC_Order( $_GET['order'] );
					$item_meta = $order->get_item_meta( $_GET['item_id'], 'fpd_data' );
					$views = $item_meta[0]["fpd_product"];

				}
				else {

					//load product view(s) from database
					if( !fpd_views_table_exist() ) { return; }
					$views = $wpdb->get_results("SELECT * FROM ".FPD_VIEWS_TABLE." WHERE product_id=$master_id ORDER BY view_order ASC");

				}

				//check if views are not empty
				if( empty($views) ) { return; }

				$modal_box_css = $open_in_lightbox ? 'fpd-hide-container' : '';
				?>
				<div class="<?php echo $modal_box_css; ?>">
					<div id="<?php echo $selector; ?>" class="fpd-container">

						<?php if(!$load_from_form) {
							$first_view = $views[0];
						?>
						<div class="fpd-product" title="<?php echo $first_view->title; ?>" data-thumbnail="<?php echo $first_view->thumbnail; ?>">
							<?php
								echo $this->get_element_anchors_from_view($first_view->elements);
							?>
							<?php
							if(sizeof($views) > 1) {
								for($i = 1; $i <  sizeof($views); $i++) {
									$sub_view = $views[$i];
									?>
									<div class="fpd-product" title="<?php echo $sub_view->title; ?>" data-thumbnail="<?php echo $sub_view->thumbnail; ?>"><?php echo $sub_view->title; ?>
										<?php
										echo $this->get_element_anchors_from_view($sub_view->elements);
										?>
									</div>
									<?php
								}
							}
							?>
						</div>
						<?php } ?>

						<div class="fpd-design">
							<?php

							//get all category terms
							$category_terms = get_terms( 'fpd_design_category', array(
								'hide_empty' => false,
								'include'	=> $fancy_product->get_option('design_categories[]') ? $fancy_product->get_option('design_categories[]') : array()
							));

							//general parameters
							$general_parameters_array = $fpd_parameters->get_images_parameters();
							$final_parameters ;

							//loop through all categories
							if(is_array($category_terms) && !intval($fancy_product->get_option('hide_designs_tab')) ) {
								foreach($category_terms as $category_term) {

									//get attachments from fancy design category
									$args = array(
										 'posts_per_page' => -1,
										 'post_type' => 'attachment',
										 'orderby' => 'menu_order',
										 'order' => 'ASC',
										 'fpd_design_category' => $category_term->slug
									);
									$designs = get_posts( $args );

									//category parameters
									$category_parameters_array = array();
									$category_parameters = get_option( 'fpd_category_parameters_'.$category_term->slug );
									if(strpos($category_parameters,'enabled') !== false) {
										//convert string to array
										parse_str($category_parameters, $category_parameters_array);
									}

									if( !empty($designs) ) :
									?>
									<div class="fpd-category" title="<?php echo $category_term->name; ?>">
										<?php

										if(is_array($designs)) {
											foreach( $designs as $design ) {

												//merge general parameters with category parameters
												$final_parameters = array_merge($general_parameters_array, $category_parameters_array);

												//single element parameters
												$single_design_parameters = get_post_meta($design->ID, 'fpd_parameters', true);
												if (strpos($single_design_parameters,'enabled') !== false) {
													$single_design_parameters_array = array();
													parse_str($single_design_parameters, $single_design_parameters_array);
													$final_parameters = array_merge($final_parameters, $single_design_parameters_array);
												}

												//convert array to string
												$design_parameters_str = FPD_Parameters::convert_parameters_to_string($final_parameters);

												//get design thumbnail
												$design_thumbnail = get_post_meta($design->ID, 'fpd_thumbnail', true); //custom thumbnail
												if( empty($design_thumbnail) ) {
													$design_thumbnail = wp_get_attachment_image_src( $design->ID, 'medium' );
													$design_thumbnail = $design_thumbnail[0];
												}

												echo "<img data-src='{$design->guid}' title='{$design->post_title}' data-parameters='$design_parameters_str' data-thumbnail='$design_thumbnail' />";
											}
										}

										?>
									</div>
									<?php
									endif;
								}
							}
							?>
						</div>

					</div>
					<p class="fpd-not-supported-device-info">
						<strong><?php echo FPD_Admin_Settings::get_option('fpd_not_supported_device_info'); ?></strong>
					</p>
				</div>

				<script type="text/javascript">

					var fancyProductDesigner;

					jQuery(document).ready(function() {

						//return;

						var $selector = jQuery('#<?php echo $selector; ?>'),
							buttonClass = "<?php echo trim(FPD_Admin_Settings::get_option('fpd_start_customizing_css_class')) == '' ? 'fpd-modal-button' : trim(FPD_Admin_Settings::get_option('fpd_start_customizing_css_class')); ?>";

						if(jQuery('.fpd-hide-container').size() > 0) {

							var $modalWrapper = jQuery('body').append('<div class="fpd-modal-overlay"><div class="fpd-modal-wrapper"><div class="fpd-modal-buttons"><a href="#" id="fpd-modal-done" class="'+buttonClass+'"><?php echo FPD_Admin_Settings::get_option('fpd_lightbox_submit_button') ? get_option('fpd_lightbox_submit_button') :  __('Done', 'fpd_label'); ?></a></div></div></div>').find('.fpd-modal-wrapper');

							$selector.clone().prependTo($modalWrapper);
							$selector.remove();
							$selector = jQuery('#<?php echo $selector; ?>').css('display', 'inline-block');

							jQuery('#fpd-start-customizing-button').click(function(evt) {

								if(!isReady) { return false;}

								jQuery('html,body').addClass('fpd-modal-open');
							    $modalWrapper.parent('.fpd-modal-overlay').show();

								$selector.find('.fpd-nav-item:visible').first().click();

								containerWidth = $selector.innerWidth();
								$modalWrapper.parent('.fpd-modal-overlay').hide().fadeIn(400);

								$modalWrapper.width(containerWidth).css('margin-left', -(containerWidth/2)+'px');
								jQuery(window).resize();

								evt.preventDefault();

							});

							$modalWrapper.find('#fpd-modal-done').click(function(evt) {

								jQuery('html,body').removeClass('fpd-modal-open');
								$modalWrapper.parent('.fpd-modal-overlay').fadeOut(300);

								if(<?php echo intval(FPD_Admin_Settings::get_option('fpd_lightbox_add_to_cart')); ?>) {
									$cartForm.find(':submit').click();
								}
								else {
									_setProductImage();
								}

								evt.preventDefault();

							});

						}


						<?php

						//create javascript object
						$hex_names = '{}';
						if( fpd_not_empty(trim(FPD_Admin_Settings::get_option( 'fpd_hex_names' ))) ) {
							$hex_names = '{"'.str_replace('#', '', FPD_Admin_Settings::get_option( 'fpd_hex_names' ) ) ;
							$hex_names = str_replace(':', '":"', $hex_names);
							$hex_names = str_replace(',', '","', $hex_names);
							$hex_names .= '"}';
						}

						?>

						var customImagesParams = jQuery.extend(<?php echo $fpd_parameters->get_images_parameters_string(); ?>, <?php echo $fpd_parameters->get_custom_images_parameters_string(); ?> );

						//call fancy product designer plugin
						fancyProductDesigner = $selector.fancyProductDesigner({
							uploadDesigns: <?php echo $fancy_product->get_option('hide_custom_image_upload') ? 0 : intval(FPD_Admin_Settings::get_option('fpd_upload_designs')) ?>,
							customTexts: <?php echo $fancy_product->get_option('hide_custom_text') ? 0 : intval(FPD_Admin_Settings::get_option('fpd_custom_texts')) ?>,
							imageDownloadable: <?php echo FPD_Admin_Settings::get_option('fpd_download_product_image'); ?>,
							saveAsPdf: <?php echo FPD_Admin_Settings::get_option('fpd_pdf_button'); ?>,
							printable: <?php echo FPD_Admin_Settings::get_option('fpd_print'); ?>,
							allowProductSaving: <?php echo FPD_Admin_Settings::get_option('fpd_allow_product_saving'); ?>,
							resettable: <?php echo FPD_Admin_Settings::get_option('fpd_reset'); ?>,
							fonts: [<?php echo FPD_Admin_Settings::get_option('fpd_fonts_dropdown') ? '"'.implode('", "', FPD_Fonts::get_enabled_fonts()).'"' : ""; ?>],
							templatesDirectory: "<?php echo plugins_url('/templates/', FPD_PLUGIN_ROOT_PHP ); ?>",
							phpDirectory: "<?php echo plugins_url('/inc/', FPD_PLUGIN_ROOT_PHP); ?>",
							facebookAppId: "<?php echo $fancy_product->get_option('hide_facebook_tab') ? '' : FPD_Admin_Settings::get_option('fpd_facebook_app_id'); ?>",
							instagramClientId: "<?php echo $fancy_product->get_option('hide_instagram_tab') ? '' : FPD_Admin_Settings::get_option('fpd_instagram_client_id'); ?>",
							instagramRedirectUri: "<?php echo FPD_Admin_Settings::get_option('fpd_instagram_redirect_uri'); ?>",
							patterns: ["<?php echo implode('", "', $this->get_pattern_urls()); ?>"],
							layout: "<?php echo $fancy_product->get_option('layout'); ?>",
							menubarPosition: "<?php echo FPD_Admin_Settings::get_option('fpd_menu_bar_position'); ?>",
							viewSelectionPosition: "<?php echo $fancy_product->get_option('view_selection_position'); ?>",
							viewSelectionFloated: <?php echo $fancy_product->get_option('view_selection_floated'); ?>,
							zoomFactor: <?php echo FPD_Admin_Settings::get_option('fpd_zoom_factor'); ?>,
							zoomRange: [<?php echo FPD_Admin_Settings::get_option('fpd_min_zoom_range'); ?>, <?php echo FPD_Admin_Settings::get_option('fpd_max_zoom_range'); ?>],
							defaultText: "<?php echo $fancy_product->get_option('default_text'); ?>",
							tooltips: <?php echo intval(FPD_Admin_Settings::get_option('fpd_tooltips')); ?>,
							hexNames: <?php echo $hex_names; ?>,
							selectedColor:  "<?php echo FPD_Admin_Settings::get_option('fpd_selected_color'); ?>",
							boundingBoxColor:  "<?php echo FPD_Admin_Settings::get_option('fpd_bounding_box_color'); ?>",
							outOfBoundaryColor:  "<?php echo FPD_Admin_Settings::get_option('fpd_out_of_boundary_color'); ?>",
							paddingControl:  <?php echo FPD_Admin_Settings::get_option('fpd_padding_controls'); ?>,
							elementParameters: {
								originX: "<?php echo FPD_Admin_Settings::get_option('fpd_common_parameter_originX'); ?>",
								originY: "<?php echo FPD_Admin_Settings::get_option('fpd_common_parameter_originY'); ?>"
							},
							textParameters: {
								font: "<?php echo FPD_Admin_Settings::get_option('fpd_font'); ?>"
							},
							customImagesParameters: customImagesParams,
							customTextParameters: <?php echo $fpd_parameters->get_custom_texts_parameters_string(); ?>,
							dimensions: {
								sidebarNavSize: <?php echo FPD_Admin_Settings::get_option('fpd_sidebar_nav_size'); ?>,
								sidebarContentWidth: <?php echo FPD_Admin_Settings::get_option('fpd_sidebar_content_width'); ?>,
								sidebarSize: <?php echo FPD_Admin_Settings::get_option('fpd_sidebar_size'); ?>,
								productStageWidth: <?php echo $fancy_product->get_option('stage_width'); ?>,
								productStageHeight: <?php echo $fancy_product->get_option('stage_height'); ?>
							},
							labels: { //different labels used for the UI
								outOfContainmentAlert: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_out_of_containment_alert') : __('Move it in his containment!', 'fpd_label'); ?>",
								confirmProductDelete: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_confirm_product_delete') :  __('Delete saved product?', 'fpd_label'); ?>",
								colorpicker : {
									cancel: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_colorpicker_cancel') :  __('Cancel', 'fpd_label') ?>",
									change: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_colorpicker_choose') :  __('Change Color', 'fpd_label') ?>"
								},
								uploadedDesignSizeAlert: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_uploaded_design_size_alert') :  __('Sorry! But the uploaded image size does not conform our indication of size.', 'fpd_label'); ?>",
								initText: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_init_text') :  __('Initializing product designer', 'fpd_label'); ?>",
								myUploadedImgCat: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_uploaded_images_category_name') :  __('Your uploaded images', 'fpd_label'); ?>"
							},
							sidebarLabels: { //all labels in the sidebar
								designsMenu: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_navigation_tab_designs') :  __('Designs', 'fpd_label'); ?>",
								editElementsMenu: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_navigation_tab_edit_elements') :  __('Edit Elements', 'fpd_label'); ?>",
								fbPhotosMenu: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_navigation_tab_facebook') :  __('Add Photos From Facebook', 'fpd_label'); ?>",
								instaPhotosMenu: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_navigation_tab_instagram') :  __('Add Photos From Instagram', 'fpd_label'); ?>",
								editElementsHeadline: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_customize_headline') :  __('Edit Elements', 'fpd_label'); ?>",
								editElementsDropdownNone: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_customize_dropdown_none') :  __('None', 'fpd_label'); ?>",
								sectionFilling: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_customize_section_filling') :  __('Filling', 'fpd_label'); ?>",
								sectionFontsStyles: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_customize_section_font_styles') :  __('Font & Style', 'fpd_label'); ?>",
								sectionCurvedText: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_customize_section_curved_text') :  __('Curved Text', 'fpd_label'); ?>",
								sectionHelpers: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_customize_section_helpers') :  __('Helpers', 'fpd_label'); ?>",
								textAlignLeft: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_customize_text_tooltip_align_left') :  __('Align Left', 'fpd_label'); ?>",
								textAlignCenter: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_customize_text_tooltip_align_center') :  __('Align Center', 'fpd_label'); ?>",
								textAlignRight: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_customize_text_tooltip_align_right') :  __('Align Right', 'fpd_label'); ?>",
								textBold: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_customize_text_tooltip_bold') :  __('Bold', 'fpd_label'); ?>",
								textItalic: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_customize_text_tooltip_italic') :  __('Italic', 'fpd_label'); ?>",
								curvedTextInfo: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_curved_text_info') :  __('You can only change the text when you switch to normal text.', 'fpd_label'); ?>",
								curvedTextSpacing: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_curved_text_spacing') :  __('Spacing', 'fpd_label'); ?>",
								curvedTextRadius: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_curved_text_radius') :  __('Radius', 'fpd_label'); ?>",
								curvedTextReverse: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_curved_text_reverse') :  __('Reverse', 'fpd_label'); ?>",
								curvedTextToggle: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_curved_text_switcher') :  __('Switch between curved and normal Text', 'fpd_label'); ?>",
								centerH: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_customize_tooltip_center_horizontal') :  __('Center Horizontal', 'fpd_label'); ?>",
								centerV: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_customize_tooltip_center_vertical') :  __('Center Vertical', 'fpd_label'); ?>",
								moveDown: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_customize_tooltip_move_it_down') :  __('Bring It Down', 'fpd_label'); ?>",
								moveUp: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_customize_tooltip_move_it_up') :  __('Bring It Up', 'fpd_label'); ?>",
								reset: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_customize_tooltip_reset') :  __('Reset', 'fpd_label'); ?>",
								trash: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_customize_tooltip_trash') :  __('Trash', 'fpd_label'); ?>",
								fbPhotosHeadline: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_fb_headline') :  __('Facebook Photos', 'fpd_label'); ?>",
								fbSelectAlbum: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_select_album') :  __('Select an album', 'fpd_label'); ?>",
								instaPhotosHeadline: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_insta_headline') :  __('Instagram Photos', 'fpd_label'); ?>",
								instaFeedButton: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_insta_my_feed') :  __('My Feed', 'fpd_label'); ?>",
								instaRecentImagesButton: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_insta_recent_images') :  __('My Recent Images', 'fpd_label'); ?>",
								instaLoadNext: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_insta_load_next') :  __('Load next Stack', 'fpd_label'); ?>"
							},
							productStageLabels: { //all labels in the product stage
								addImageTooltip: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_stage_menu_bar_add_image') :  __('Add your own Image', 'fpd_label'); ?>",
								addTextTooltip: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_stage_menu_bar_add_text') :  __('Add your own Text', 'fpd_label'); ?>",
								zoomInTooltip: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_stage_menu_bar_zoom_in') :  __('Zoom In', 'fpd_label'); ?>",
								zoomOutTooltip: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_stage_menu_bar_zoom_out') :  __('Zoom Out', 'fpd_label'); ?>",
								zoomResetTooltip: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_stage_menu_bar_zoom_reset') :  __('Zoom Reset', 'fpd_label'); ?>",
								downloadImageTooltip: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_stage_menu_bar_download_product_image') :  __('Download Product Image', 'fpd_label'); ?>",
								printTooltip: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_stage_menu_bar_print') :  __('Print', 'fpd_label'); ?>",
								pdfTooltip: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_stage_menu_bar_pdf') :  __('Save As PDF', 'fpd_label'); ?>",
								saveProductTooltip: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_stage_menu_bar_save_product') :  __('Save product', 'fpd_label'); ?>",
								savedProductsTooltip: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_stage_menu_bar_your_saved_product') :  __('Your saved products', 'fpd_label'); ?>",
								resetTooltip: "<?php echo FPD_Admin_Settings::get_option('fpd_use_label_settings') ? get_option('fpd_stage_menu_bar_reset') :  __('Reset Product', 'fpd_label'); ?>"
							}
						}).data('fancy-product-designer');

						var $productWrapper = jQuery('#product-<?php echo $post->ID; ?>'),
							$cartForm = jQuery('[name="fpd_product"]:first').parents('form:first'),
							productCreated = false,
							wcPrice = <?php echo $product->get_price() ? $product->get_price() : 0; ?>,
							fpdPrice = 0,
							currencySymbol = '<?php echo get_woocommerce_currency_symbol(); ?>',
							decimalSeparator = "<?php echo get_option('woocommerce_price_decimal_sep'); ?>",
							thousandSeparator = "<?php echo get_option('woocommerce_price_thousand_sep'); ?>",
							numberOfDecimals = <?php echo get_option('woocommerce_price_num_decimals'); ?>,
							currencyPos = "<?php echo get_option('woocommerce_currency_pos'); ?>",
							isReady = false;

						//when load from cart or order, use loadProduct
						$selector.on('ready', function() {

							if(<?php echo $load_from_form; ?>) {
								var views = <?php echo $load_from_form ? $views : 0; ?>;
								fancyProductDesigner.clear();
								fancyProductDesigner.loadProduct(views);
							}

							//replace filereader uploader with php uploader
							if("<?php echo FPD_Admin_Settings::get_option('fpd_type_of_uploader'); ?>" == 'php') {

								var $imageInput = $selector.find('.fpd-input-image');

								$selector.find('.fpd-upload-form').off('change').change(function() {

									$selector.find('.fpd-upload-form').ajaxSubmit({
										url: "<?php echo admin_url('admin-ajax.php'); ?>",
										dataType: 'json',
										data: {action: 'fpduploadimage', product_id: <?php echo $post->ID; ?>},
										type: 'post',
										beforeSubmit: function(arr, $form, options) {
											$phpUploaderInfo.animate({opacity: 1, bottom: 20}, 200)
											.children('p:first').text('<?php echo _e('Uploading', 'radykal'); ?>'+': '+arr[0].value.name);

											$progressBar.children('.fpd-progress-bar-move').css('width', 0);
										},
										success: function(responseText, statusText) {

											if(responseText.code == 200) {

												//successfully uploaded

												fancyProductDesigner.addCustomImage(responseText.url, responseText.filename);

											}
											else if(responseText.code == 500) {
												//failed
												$imageInput.data('fpd-placeholder', '');
												alert(responseText.message);
											}
											else {
												//failed
												$imageInput.data('fpd-placeholder', '');
												alert("<?php echo _e('You need to be logged in to upload images!', 'radykal'); ?>");
											}

											$imageInput.val('');
											$phpUploaderInfo.animate({opacity: 0, bottom: -100}, 300);

										},
										error: function() {
											$imageInput.val('').data('fpd-placeholder', '');
											alert("<?php echo _e('Server error: Image could not be uploaded, please try again!', 'radykal'); ?>");
										},
										uploadProgress: function(evt, pos, total, percentComplete) {
											$progressBar.children('.fpd-progress-bar-move').css('width', percentComplete+'%');
										}
									});

								})

								jQuery('body').append('<div class="fpd-php-uploader-info"><p></p><div class="fpd-upload-progess-bar"><div class="fpd-progress-bar-bg"></div><div class="fpd-progress-bar-move"></div></div></div>');

								$phpUploaderInfo = jQuery('body').children('.fpd-php-uploader-info');
								$progressBar = $phpUploaderInfo.children('.fpd-upload-progess-bar');

							}

							isReady = true;

						});

						//calculate initial price
						$selector.on('productCreate', function() {

							productCreated = true;
							fpdPrice = fancyProductDesigner.getPrice();
							_setTotalPrice();
							if(<?php echo $load_from_form; ?>) {
								_setProductImage();
							}

						});

						//check when variation has been selected
						jQuery(document).on('found_variation', '.variations_form', function(evt, variation) {

							if(variation.price_html) {

								//- get last price, if a sale price is found, use it
								//- set thousand and decimal separator
								//- parse it as number
								wcPrice = jQuery(variation.price_html).find('span:last').text().replace(currencySymbol,'').replace(thousandSeparator, '').replace(decimalSeparator, '.').replace(/[^\d.]/g,'');
								_setTotalPrice();
							}

						});

						//listen when price changes
						$selector.on('priceChange', function(evt, sp, tp) {

							fpdPrice = tp;
							_setTotalPrice();

						});

						//fill custom form with values and then submit
						$cartForm.on('click', ':submit', function(evt) {

							evt.preventDefault();

							if(!productCreated) { return false; }

							var product = fancyProductDesigner.getProduct();
							if(product != false) {

								$cartForm.find('input[name="fpd_product"]').val(JSON.stringify(product));
								$cartForm.find('input[name="fpd_product_thumbnail"]').val(fancyProductDesigner.getViewsDataURL('png', 'transparent', 0.3)[0]);
								_setTotalPrice();
								$cartForm.submit();
							}

						});

						//set total price depending from wc and fpd price
						function _setTotalPrice() {

							var totalPrice = parseFloat(wcPrice) + parseFloat(fpdPrice),
								htmlPrice;

							totalPrice = totalPrice.toFixed(numberOfDecimals);
							htmlPrice = totalPrice.toString().replace('.', decimalSeparator);
							if(thousandSeparator.length > 0) {
								htmlPrice = _addThousandSep(htmlPrice);
							}

							if(currencyPos == 'right') {
								htmlPrice = htmlPrice + currencySymbol;
							}
							else if(currencyPos == 'right_space') {
								htmlPrice = htmlPrice + ' ' + currencySymbol;
							}
							else if(currencyPos == 'left_space') {
								htmlPrice = currencySymbol + ' ' + htmlPrice;
							}
							else {
								htmlPrice = currencySymbol + htmlPrice;
							}

							//check if variations are used
							if($productWrapper.find('.variations_form').size() > 0) {
								//check if amount contains 2 prices or sale prices. If yes different prices are used
								if($productWrapper.find('.price:first > .amount').size() == 2 || $productWrapper.find('.price:first ins > .amount').size() == 2) {
									//different prices
									$productWrapper.find('.single_variation .price .amount:last').html(htmlPrice);
								}
								else {
									//same price
									$productWrapper.find('.price:first .amount:last').html(htmlPrice);
								}

							}
							//no variations are used
							else {
								$productWrapper.find('.price:first .amount:last').html(htmlPrice);
							}

							$cartForm.find('input[name="fpd_product_price"]').val(totalPrice);

						};

						function _addThousandSep(n){
						    var rx=  /(\d+)(\d{3})/;
						    return String(n).replace(/^\d+/, function(w){
						        while(rx.test(w)){
						            w= w.replace(rx, '$1'+thousandSeparator+'$2');
						        }
						        return w;
						    });
						};

						function _setProductImage() {
							if(jQuery('.fpd-hide-container').size() > 0) {
								var firstViewImg = fancyProductDesigner.getViewsDataURL('png', 'transparent')[0];
								jQuery('.woocommerce-main-image:first').attr('href', firstViewImg).children('img').attr('src', firstViewImg);
							}
						};
					});

				</script>

				<?php
			}

		}

		//adds a customize button to the summary
		public function add_customize_button() {

			global $post;
			$fancy_product = new Fancy_Product($post->ID);
			$open_in_lightbox = $fancy_product->get_option('open_in_lightbox') && trim($fancy_product->get_option('start_customizing_button')) != '';

			if( is_fancy_product($post->ID) && (fpd_start_customizing_button_used($post->ID) || $open_in_lightbox ) ) {

				$button_class = trim(FPD_Admin_Settings::get_option('fpd_start_customizing_css_class')) == '' ? 'fpd-start-customizing-button' : FPD_Admin_Settings::get_option('fpd_start_customizing_css_class');

				?>
				<p><a href="<?php echo add_query_arg( 'start_customizing', 'yes' ); ?>" id="fpd-start-customizing-button" class="<?php echo $button_class; ?>"><?php echo $fancy_product->get_option('start_customizing_button'); ?></a></p>
				<?php

			}

		}

		//the additional form fields
		public function add_product_designer_form() {

			global $post;
			$fancy_product = new Fancy_Product($post->ID);
			$open_in_lightbox = $fancy_product->get_option('open_in_lightbox') && trim($fancy_product->get_option('start_customizing_button')) != '';

			if( is_fancy_product($post->ID) && (!fpd_start_customizing_button_used($post->ID) || $open_in_lightbox) ) {
				?>
				<input type="hidden" value="" name="fpd_product" />
				<input type="hidden" value="" name="fpd_product_price" />
				<input type="hidden" value="" name="fpd_product_thumbnail" />
				<input type="hidden" value="<?php echo isset($_GET['cart_item_key']) ? $_GET['cart_item_key'] : ''; ?>" name="fpd_remove_cart_item" />
				<?php
			}

		}

		private function get_pattern_urls() {

			$urls = array();

			$path = FPD_PLUGIN_DIR . '/patterns/';
		  	$folder = opendir($path);

			$pic_types = array("jpg", "jpeg", "png");

			while ($file = readdir ($folder)) {

			  if(in_array(substr(strtolower($file), strrpos($file,".") + 1),$pic_types)) {
				  $urls[] = plugins_url('/patterns/'.$file, FPD_PLUGIN_ROOT_PHP );
			  }
			}

			closedir($folder);

			return $urls;

		}

		private function get_element_anchors_from_view($elements) {

			//unserialize when necessary
			if( @unserialize($elements) !== false ) {
				$elements = unserialize($elements);
			}

			$view_html = '';
			if(is_array($elements)) {
				foreach($elements as $element) {
					$element = (array) $element;
					$view_html .= $this->get_element_anchor($element['type'], $element['title'], $element['source'], (array) $element['parameters']);
				}
			}

			return $view_html;

		}

		//return a single element markup
		private function get_element_anchor($type, $title, $source, $parameters) {

			$parameters_string = FPD_Parameters::convert_parameters_to_string($parameters);

			if($type == 'image') {

				$protocol = is_ssl() ? 'https://' : 'http://';
				$domain = parse_url(get_site_url());
				$domain = $domain['host'];
				$path = parse_url($source);
				$path = $path['path'];
				$source = $protocol . $domain . $path;

				return "<img data-src='$source' title='$title' data-parameters='$parameters_string' />";
			}
			else {
				$source = stripslashes($source);
				return "<span data-parameters='$parameters_string'>$source</span>";
			}

		}

		//ajax image upload handler
		public function upload_image() {

			if( !class_exists('Fancy_Product') ) {
				require_once(FPD_PLUGIN_DIR.'/inc/class-fancy-product.php');
			}

			$fancy_product = new Fancy_Product(intval($_POST['product_id']));

			$mb_size =  intval(FPD_Admin_Settings::get_option('fpd_max_image_size'));
			$maximum_filesize = $mb_size * 1024 * 1000;

			foreach($_FILES as $fieldName => $file) {

				$filename = $file['name'];

				//check if its an image
				if(!getimagesize($file['tmp_name'])) {
					echo json_encode(array('code' => 500, 'message' => __('File is not an image!', 'radykal'), 'filename' => $file['name']));
					die;
				}

				//check for php errors
				if($file['error'] !== UPLOAD_ERR_OK) {
					echo json_encode(array('code' => 500, 'message' => file_upload_error_message($file['error']), 'filename' => $filename));
					die;
				}

				//check for maximum upload size
				if($file['size'] > $maximum_filesize) {
					echo json_encode(array('code' => 500, 'message' => sprintf(__('Uploaded image is too big! Maximum image size is %d MB!', 'radykal'), $mb_size), 'filename' => $filename));
					die;
				}

				//check dimensions
				$image_dimensions = getimagesize($file['tmp_name']);
				$image_w = $image_dimensions[0];
				$image_h = $image_dimensions[1];

				if( $image_w < floatval($fancy_product->get_option('uploaded_design_parameters_minW')) ||  $image_w > floatval($fancy_product->get_option('uploaded_designs_parameter_maxW')) ||
				 	$image_h < floatval($fancy_product->get_option('uploaded_design_parameters_minH')) ||  $image_h > floatval($fancy_product->get_option('uploaded_designs_parameter_maxH'))
				 ){
					echo json_encode( array(
						'code' => 500,
						'message' => FPD_Admin_Settings::get_option('fpd_use_label_settings') ? FPD_Admin_Settings::get_option('fpd_uploaded_design_size_alert') :  __('Sorry! But the uploaded image size does not conform our indication of size.', 'fpd_label'),
						'filename' => $filename
					));
					die;
				}



				$upload_path = WP_CONTENT_DIR . '/uploads/fancy_products_uploads/';

				if(!file_exists($upload_path))
					wp_mkdir_p($upload_path);

				$upload_path = $upload_path . '/'. date('Y') . '/';
				if(!file_exists($upload_path))
					wp_mkdir_p($upload_path);

				$upload_path = $upload_path . '/'. date('m') . '/';
				if(!file_exists($upload_path))
					wp_mkdir_p($upload_path);

				$upload_path = $upload_path . '/'. date('d') . '/';
				if(!file_exists($upload_path))
					wp_mkdir_p($upload_path);

				$filename = sanitize_file_name($filename);
				$file_url = $upload_path.$filename;

				$file_counter = 1;
				$real_filename = $filename;
				while(file_exists($file_url)) {
					$real_filename = $file_counter.'-'.$filename;
					$file_url = $upload_path.$real_filename;
					$file_counter++;
				}

				if( @move_uploaded_file($file['tmp_name'], $file_url) ) {
					$img_url = WP_CONTENT_URL . '/uploads/fancy_products_uploads/' . date('Y') . '/' . date('m') . '/' . date('d') . '/' . $real_filename;
					echo json_encode(array('code' => 200, 'url' => $img_url, 'filename' => preg_replace("/\\.[^.\\s]{3,4}$/", "", $real_filename), 'dim' => $image_dimensions));
				}
				else {
					echo json_encode(array('error' => 2, 'message' => 'PHP Issue - move_uploaed_file failed', 'filename' => $real_filename));
				}

			}

			die;

		}

		private function file_upload_error_message($error_code) {

		    switch ($error_code) {
		        case UPLOAD_ERR_INI_SIZE:
		            return __('The uploaded file exceeds the upload_max_filesize directive in php.ini', 'radykal');
		        case UPLOAD_ERR_FORM_SIZE:
		            return __('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form', 'radykal');
		        case UPLOAD_ERR_PARTIAL:
		            return __('The uploaded file was only partially uploaded', 'radykal');
		        case UPLOAD_ERR_NO_FILE:
		            return __('No file was uploaded', 'radykal');
		        case UPLOAD_ERR_NO_TMP_DIR:
		            return __('Missing a temporary folder', 'radykal');
		        case UPLOAD_ERR_CANT_WRITE:
		            return __('Failed to write file to disk', 'radykal');
		        case UPLOAD_ERR_EXTENSION:
		            return __('File upload stopped by extension', 'radykal');
		        default:
		            return __('Unknown upload error', 'radykal');
		    }

		}
	}
}

new FPD_Frontend_Product();

?>