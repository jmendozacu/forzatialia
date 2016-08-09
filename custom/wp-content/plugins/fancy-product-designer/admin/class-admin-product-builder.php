<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( !class_exists('FPD_Admin_Product_Builder') ) {

	class FPD_Admin_Product_Builder {

		public function output() {

			?>
			<div class="wrap" id="fpd-product-builder">

				<h2>
					<?php _e('Fancy Product Builder', 'radykal'); ?>
					<?php fpd_admin_display_version_info(); ?>
				</h2>
				<?php

				global $wpdb, $woocommerce;

				$request_view_id = isset($_GET['view_id']) ? $_GET['view_id'] : NULL;

				//get all fancy products
				$fancy_products = $wpdb->get_results("SELECT * FROM ".FPD_VIEWS_TABLE." GROUP BY product_id");

				if(sizeof($fancy_products) == 0) {
					echo '<div class="updated"><p><strong>'.__('There are no fancy products!', 'radykal').'</strong></p></div></div>';
					exit;
				}

				//save elements of view
				if(isset($_POST['save_elements'])) {

					check_admin_referer( 'fpd_save_elements' );

					$request_view_id = $_POST['view_id'];

					$elements = array();
					for($i=0; $i < sizeof($_POST['element_types']); $i++) {

						$element = array();

						$element['type'] = $_POST['element_types'][$i];
						$element['title'] = $_POST['element_titles'][$i];
						$element['source'] = $_POST['element_sources'][$i];

						$parameters = array();
						parse_str($_POST['element_parameters'][$i], $parameters);

						if(is_array($parameters)) {
							foreach($parameters as $key => $value) {
								if($value == '') {
									$parameters[$key] = NULL;
								}
								else {
									$parameters[$key] = preg_replace('/\s+/', '', $value);
								}
							}
						}

						$element['parameters'] = $parameters;

						array_push($elements, $element);

					}

					$fancy_view = new Fancy_View($request_view_id);
					$fancy_view->update( array('elements' => serialize($elements)) );

					$requested_view_elements = $elements;

					echo '<div class="updated"><p><strong>'.__('Elements saved.', 'radykal').'</strong></p></div>';

				}

				?>
				<br class="clear" />
				<p class="description"><?php _e( 'Select a view of a fancy product:', 'radykal' ); ?></p>
				<select id="fpd-view-switcher">
					<?php

					if(is_array($fancy_products)) {
						foreach($fancy_products as $fancy_product) {

							$fancy_product_id = $fancy_product->product_id;
							echo '<optgroup label="'.get_the_title($fancy_product_id).'" id="'.$fancy_product_id.'">';
							$fancy_product = new Fancy_Product($fancy_product_id);
							$views = $fancy_product->get_views();

							if(is_array($views)) {

								for($i=0; $i < sizeof($views); ++$i) {

									$view = $views[$i];

									//get first view
									if($request_view_id == NULL) {
										$request_view_id = $view->ID;
									}
									//get requested view
									if($request_view_id == $view->ID && !isset($requested_view_elements) ) {
										$requested_view_elements = unserialize($view->elements);
									}
									echo '<option value="'.$view->ID.'" '.selected( $request_view_id ,  $view->ID, false).'>'.$view->title.'</option>';
								}

							}
							echo '</optgroup>';

						}
					}

					?>
				</select>
				<?php

				//create instance of selected fancy view
				$fancy_view = new Fancy_View( $request_view_id );
				$product_id = $fancy_view->get_product_id();

				//get stage dimensions
				$fancy_product = new Fancy_Product($product_id);
				$stage_width = $fancy_product->get_option('stage_width');
				$stage_height = $fancy_product->get_option('stage_height');
				?>
				<p>
					<a href="<?php echo get_edit_post_link($product_id); ?>" class="button"><?php _e( 'Edit Product', 'radykal' ); ?></a>
					<a href="<?php echo get_permalink($product_id); ?>" class="button" target="_blank"><?php _e( 'View Product', 'radykal' ); ?></a>
				</p>
				<br />
				<!-- Manage elements -->
				<div id="fpd-manage-elements">

					<h3><?php _e( 'Manage elements', 'radykal' ); ?></h3>
					<div id="fpd-add-element">
						<button class="button button-secondary" id="fpd-add-image-element"><?php _e( 'Add Image', 'radykal' ); ?></button>
						<button class="button button-secondary" id="fpd-add-text-element"><?php _e( 'Add Text', 'radykal' ); ?></button>
						<button class="button button-secondary" id="fpd-add-curved-text-element"><?php _e( 'Add Curved Text', 'radykal' ); ?></button>
						<button class="button button-secondary" id="fpd-add-upload-zone"><?php _e( 'Add Upload Zone', 'radykal' ); ?></button>
					</div>
					<form method="post" id="fpd-submit">

						<input type="hidden" value="<?php echo $request_view_id; ?>" name="view_id" />
						<ul class="fpd-clearfix" id="fpd-elements-list">
						<?php

						$index = 0;
						if(is_array($requested_view_elements)) {

							foreach($requested_view_elements as $view_element) {

								echo $this->get_element_list_item(
									$index,
									$view_element['title'],
									$view_element['type'],
									stripslashes($view_element['source']),
									http_build_query($view_element['parameters'])
								);
								$index++;

							}

						}

						?>
						</ul>
						<p class="description"><?php _e( 'You can drag the list items to change the z-position of the associated element.', 'radykal' ); ?></p>
						<?php wp_nonce_field( 'fpd_save_elements' ); ?>
						<input type="submit" class="button button-primary" name="save_elements" value="<?php _e( 'Save Elements', 'radykal' ); ?>" />

					</form>

				</div>

				<!-- Edit Parameters -->
				<div id="fpd-edit-parameters">
					<h3><?php _e( 'Edit parameters for ', 'radykal' ); ?>"<span id="fpd-edit-parameters-for"></span>"</h3>
					<?php require_once(FPD_PLUGIN_ADMIN_DIR.'/views/html-product-builder-parameters-form.php'); ?>
				</div>

				<!-- Product Stage -->
				<div id="fpd-product-stage">
					<h3 class="fpd-clearfix"><?php _e('Product Stage', 'radykal'); ?>
						<span class="description"><?php echo $stage_width; ?>px * <?php echo $stage_height; ?>px</span>
						<a class="fpd-help" data-tip="<?php _e('If you can not touch a new added element, save the elements and try again!', 'radykal'); ?>" style="float: right;"><?php _e( 'Problems?', 'radykal' ); ?></a>
					</h3>
					<div id="fpd-element-toolbar">
						<button class="button button-secondary fpd-center-horizontal" title="<?php _e( 'Center Element Horizontal', 'radykal' ); ?>"><i class="fa fa-arrows-h"></i></button>
						<button class="button button-secondary fpd-center-vertical" title="<?php _e( 'Center Element Vertical', 'radykal' ); ?>"><i class="fa fa-arrows-v"></i></button>
					</div>
					<div id="fpd-fabric-stage-wrapper">
						<canvas id="fpd-fabric-stage" width="<?php echo $stage_width; ?>" height="<?php echo $stage_height; ?>"></canvas>
					</div>
				</div>
			</div>
			<?php

		}

		private function get_element_list_item( $index, $title, $type, $source, $parameters ) {

			$change_image_icon = $type == 'image' ? '<span class="fa fa-refresh fpd-change-image" title="'.__( 'Change Image Source', 'radykal' ).'"></span>' : '';
			$element_identifier = $type == 'image' ? '<img src="'.$source.'" />' : '<i class="fa fa-font"></i>';
			$lock_icon = 'fa-unlock';
			if(strpos($parameters,'locked=1') !== false) {
				$lock_icon = 'fa-lock';
			}

			return '<li id="'.$index.'"><input type="text" name="element_titles[]" value="'.($type == 'image' ? $title : $source).'" />'. $change_image_icon.'<div class="fpd-element-identifier">'.$element_identifier.'</div><div class="fpd-clearfix"><span class="fa '.$lock_icon.' fpd-lock-element"></span><span class="fa fa-times fpd-trash-element"></span></div><textarea name="element_sources[]">'.$source.'</textarea><input type="hidden" name="element_types[]" value="'.$type.'"/><input type="hidden" name="element_parameters[]" value="'.$parameters.'"/></li>';

		}
	}
}

return new FPD_Admin_Product_Builder();

?>