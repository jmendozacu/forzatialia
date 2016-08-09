<div id="fancy_product_data" class="panel woocommerce_options_panel wc-metaboxes-wrapper">
	<div class="options_group">
		<input type="file" value="Upload" class="fpd-hidden" id="fpd-file-import" />
		<p class="toolbar">
			<input type="hidden" name="fpd_product_settings" value="<?php echo $custom_fields["fpd_product_settings"][0]; ?>" />
			<button type="button" class="button button-secondary" id="fpd-change-settings"><?php _e( 'Change Settings', 'radykal' ); ?></button>
			<button type="button" class="button button-secondary" id="fpd-open-templates-modal"><?php _e( 'Add Template', 'radykal' ); ?></button>
			<button type="button" class="button button-secondary fpd-right" id="fpd-import"><?php _e( 'Import', 'radykal' ); ?></button>
			<button type="button" class="button button-secondary fpd-right" id="fpd-export"><?php _e( 'Export', 'radykal' ); ?></button>
		</p>
		<!-- Settings Modal -->
		<div class="fpd-modal-wrapper" id="fpd-modal-settings">
			<div class="modal-dialog">
				<h3><?php _e('Individual Settings', 'radykal'); ?></h3>
				<p class="description"><?php _e('Here you can set some individual product designer settings for this product.', 'radykal'); ?></p>
				<br />
				<ul class="fpd-tabs">
					<li><a href="#" name="tab1"><?php _e('General', 'radykal'); ?></a></li>
					<li><a href="#" name="tab2"><?php _e('Image Parameters', 'radykal'); ?></a></li>
					<li><a href="#" name="tab3"><?php _e('Custom Text Parameters', 'radykal'); ?></a></li>
				</ul>
				<div class="fpd-tabs-content">
					<div id="tab1">
						<table class="form-table">
							<tbody>
								<tr valign="top">
									<th scope="row"><label><?php _e('Theme', 'radykal'); ?></label></th>
									<td>
										<select name="layout">
											<option value=""><?php _e( 'Use Option From Main Settings', 'radykal' ); ?></option>
											<?php
												//get all created categories
												foreach(FPD_Admin_Settings::get_layouts_options() as $key => $value) {
													echo '<option value="'.$key.'">'.$value.'</option>';
												}
											?>
										</select>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('View Selection Position', 'radykal'); ?></label></th>
									<td>
										<select name="view_selection_position">
											<option value=""><?php _e( 'Use Option From Main Settings', 'radykal' ); ?></option>
											<?php
												//get all created categories
												foreach(FPD_Admin_Settings::get_view_selection_posititions_options() as $key => $value) {
													echo '<option value="'.$key.'">'.$value.'</option>';
												}
											?>
										</select>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('View Selection Items Floating', 'radykal'); ?></label></th>
									<td><input type="checkbox" name="view_selection_floated" value="1"></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Product Stage Width', 'radykal'); ?></label></th>
									<td><input type="number" min="10" style="width:70px;" name="stage_width" placeholder="<?php echo FPD_Admin_Settings::get_option('fpd_stage_width'); ?>"></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Product Stage Height', 'radykal'); ?></label></th>
									<td><input type="number" min="10" style="width:70px;" name="stage_height" placeholder="<?php echo FPD_Admin_Settings::get_option('fpd_stage_height'); ?>"></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Fancy Design Categories', 'radykal'); ?></label></th>
									<td>
										<select class="fpd-select2" name="design_categories[]" multiple data-placeholder="<?php _e('All categories', 'radykal'); ?>">
											<?php
												//get all created categories
												$categories = get_terms( 'fpd_design_category', array(
												 	'hide_empty' => false
												));
												foreach($categories as $category) {
													echo '<option value="'.$category->term_id.'">'.$category->name.'</option>';
												}
											?>
										</select>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('"Start Customizing" Button', 'radykal'); ?></label></th>
									<td><input type="text" name="start_customizing_button" value="<?php echo FPD_Admin_Settings::get_option('fpd_start_customizing_button'); ?>"></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Open in lightbox', 'radykal'); ?></label></th>
									<td>
										<select name="open_in_lightbox">
											<option value="<?php echo FPD_Admin_Settings::get_option('fpd_open_in_lightbox'); ?>"><?php _e( 'Use Option From Main Settings', 'radykal' ); ?></option>
											<option value="0"><?php _e( 'No', 'radykal' ); ?></option>
											<option value="1"><?php _e( 'Yes', 'radykal' ); ?></option>
										</select>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Background Type', 'radykal'); ?></label></th>
									<td>
										<label><input type="radio" name="background_type" value="image" checked="checked" /> <?php _e('Image', 'radykal'); ?></label>
										<label><input type="radio" name="background_type" value="color" /> <?php _e('Color', 'radykal'); ?></label>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Background Image', 'radykal'); ?></label></th>
									<td>
										<button class="button button-secondary" id="fpd-set-background-image"><?php _e('Set Image', 'radykal'); ?></button>
										<input type="hidden" value="<?php echo plugins_url('/images/fpd/grid.png', dirname(dirname(__FILE__))); ?>" name="background_image">
										<img src="" alt="Background Image" id="fpd-background-image-preview" />
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Background Color', 'radykal'); ?></label></th>
									<td><input type="text" name="background_color" value="#ffffff" class="fpd-color-picker" /></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Hide Designs Tab', 'radykal'); ?></label></th>
									<td><input type="checkbox" name="hide_designs_tab" value="1"></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Hide Facebook Tab', 'radykal'); ?></label></th>
									<td><input type="checkbox" name="hide_facebook_tab" value="1"></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Hide Instagram Tab', 'radykal'); ?></label></th>
									<td><input type="checkbox" name="hide_instagram_tab" value="1"></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Hide Custom Image Upload', 'radykal'); ?></label></th>
									<td><input type="checkbox" name="hide_custom_image_upload" value="1"></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Hide Custom Text', 'radykal'); ?></label></th>
									<td><input type="checkbox" name="hide_custom_text" value="1"></td>
								</tr>
							</tbody>
						</table>
					</div>
					<div id="tab2">
						<h4><?php _e('Common Image', 'radykal'); ?></h4>
						<table class="form-table">
							<tbody>
								<tr valign="top">
									<th scope="row"><label><?php _e('Price', 'radykal'); ?></label></th>
									<td><input type="number" min="0" step="0.01" name="designs_parameter_price" placeholder="<?php echo FPD_Admin_Settings::get_option('fpd_designs_parameter_price'); ?>" value=""></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Replace', 'radykal'); ?></label></th>
									<td><input type="text" name="designs_parameter_replace" placeholder="<?php echo get_option('fpd_designs_parameter_replace'); ?>" value=""></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Bounding Box', 'radykal'); ?></label></th>
									<td>
										<select name="designs_parameter_bounding_box_control">
											<option value=""><?php _e( 'Use Option From Main Settings', 'radykal' ); ?></option>
											<option value="0" data-class="custom-bb"><?php _e( 'Custom Bounding Box', 'radykal' ); ?></option>
											<option value="1" data-class="target-bb"><?php _e( 'Use another element as bounding box', 'radykal' ); ?></option>
										</select>
									</td>
								</tr>
								<tr valign="top" class="custom-bb">
									<th scope="row"><label><?php _e('Bounding Box X-Position', 'radykal'); ?></label></th>
									<td><input type="number" name="designs_parameter_bounding_box_x" min="0" step="1" placeholder="<?php echo get_option('fpd_designs_parameter_bounding_box_x'); ?>" value=""></td>
								</tr>
								<tr valign="top" class="custom-bb">
									<th scope="row"><label><?php _e('Bounding Box Y-Position', 'radykal'); ?></label></th>
									<td><input type="number" name="designs_parameter_bounding_box_y" min="0" step="1" placeholder="<?php echo get_option('fpd_designs_parameter_bounding_box_y'); ?>" value=""></td>
								</tr>
								<tr valign="top" class="custom-bb">
									<th scope="row"><label><?php _e('Bounding Box Width', 'radykal'); ?></label></th>
									<td><input type="number" name="designs_parameter_bounding_box_width" min="0" step="1" placeholder="<?php echo get_option('fpd_designs_parameter_bounding_box_width'); ?>" value=""></td>
								</tr>
								<tr valign="top" class="custom-bb">
									<th scope="row"><label><?php _e('Bounding Box Height', 'radykal'); ?></label></th>
									<td><input type="number" name="designs_parameter_bounding_box_height" min="0" step="1" placeholder="<?php echo get_option('fpd_designs_parameter_bounding_box_height'); ?>" value=""></td>
								</tr>
								<tr valign="top" class="target-bb">
									<th scope="row"><label><?php _e('Bounding Box Target', 'radykal'); ?></label></th>
									<td><input type="text" name="designs_parameter_bounding_box_by_other" placeholder="<?php echo get_option('fpd_designs_parameter_bounding_box_by_other'); ?>" value=""></td>
								</tr>
								</tbody>
						</table>
						<h4><?php _e('Custom Image', 'radykal'); ?></h4>
						<table class="form-table">
							<tbody>
								<tr valign="top">
									<th scope="row"><label><?php _e('Minimum Width', 'radykal'); ?></label></th>
									<td><input type="number" min="1" step="1" name="uploaded_designs_parameter_minW" placeholder="<?php echo FPD_Admin_Settings::get_option('fpd_uploaded_designs_parameter_minW'); ?>" value=""></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Minimum Height', 'radykal'); ?></label></th>
									<td><input type="number" min="1" step="1" name="uploaded_designs_parameter_minH" placeholder="<?php echo FPD_Admin_Settings::get_option('fpd_uploaded_designs_parameter_minH'); ?>" value=""></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Maximum Width', 'radykal'); ?></label></th>
									<td><input type="number" min="1" step="1" name="uploaded_designs_parameter_maxW" placeholder="<?php echo FPD_Admin_Settings::get_option('fpd_uploaded_designs_parameter_maxW'); ?>" value=""></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Maximum Height', 'radykal'); ?></label></th>
									<td><input type="number" min="1" step="1" name="uploaded_designs_parameter_maxH" placeholder="<?php echo FPD_Admin_Settings::get_option('fpd_uploaded_designs_parameter_maxH'); ?>" value=""></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Resize To Width', 'radykal'); ?></label></th>
									<td><input type="number" min="1" step="1" name="uploaded_designs_parameter_resizeToW" placeholder="<?php echo FPD_Admin_Settings::get_option('fpd_uploaded_designs_parameter_resizeToW'); ?>" value=""></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Resize To Height', 'radykal'); ?></label></th>
									<td><input type="number" min="1" step="1" name="uploaded_designs_parameter_resizeToH" placeholder="<?php echo FPD_Admin_Settings::get_option('fpd_uploaded_designs_parameter_resizeToH'); ?>" value=""></td>
								</tr>
							</tbody>
						</table>
					</div>
					<div id="tab3">
						<table class="form-table">
							<tbody>
								<tr valign="top">
									<th scope="row"><label><?php _e('Bounding Box', 'radykal'); ?></label></th>
									<td>
										<select name="custom_texts_parameter_bounding_box_control">
											<option value=""><?php _e( 'Use Option From Main Settings', 'radykal' ); ?></option>
											<option value="0" data-class="custom-bb"><?php _e( 'Custom Bounding Box', 'radykal' ); ?></option>
											<option value="1" data-class="target-bb"><?php _e( 'Use another element as bounding box', 'radykal' ); ?></option>
										</select>
									</td>
								</tr>
								<tr valign="top" class="custom-bb">
									<th scope="row"><label><?php _e('Bounding Box X-Position', 'radykal'); ?></label></th>
									<td><input type="number" name="custom_texts_parameter_bounding_box_x" min="0" step="1" placeholder="<?php echo get_option('fpd_custom_texts_parameter_bounding_box_x'); ?>" value=""></td>
								</tr>
								<tr valign="top" class="custom-bb">
									<th scope="row"><label><?php _e('Bounding Box Y-Position', 'radykal'); ?></label></th>
									<td><input type="number" name="custom_texts_parameter_bounding_box_y" min="0" step="1" placeholder="<?php echo get_option('fpd_custom_texts_parameter_bounding_box_y'); ?>" value=""></td>
								</tr>
								<tr valign="top" class="custom-bb">
									<th scope="row"><label><?php _e('Bounding Box Width', 'radykal'); ?></label></th>
									<td><input type="number" name="custom_texts_parameter_bounding_box_width" min="0" step="1" placeholder="<?php echo get_option('fpd_custom_texts_parameter_bounding_box_width'); ?>" value=""></td>
								</tr>
								<tr valign="top" class="custom-bb">
									<th scope="row"><label><?php _e('Bounding Box Height', 'radykal'); ?></label></th>
									<td><input type="number" name="custom_texts_parameter_bounding_box_height" min="0" step="1" placeholder="<?php echo get_option('fpd_custom_texts_parameter_bounding_box_height'); ?>" value=""></td>
								</tr>
								<tr valign="top" class="target-bb">
									<th scope="row"><label><?php _e('Bounding Box Target', 'radykal'); ?></label></th>
									<td><input type="text" name="custom_texts_parameter_bounding_box_by_other" placeholder="<?php echo get_option('fpd_custom_texts_parameter_bounding_box_by_other'); ?>" value=""></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Default Text', 'radykal'); ?></label></th>
									<td><input type="text" name="default_text" placeholder="<?php echo FPD_Admin_Settings::get_option('fpd_default_text'); ?>" value=""></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Price', 'radykal'); ?></label></th>
									<td><input type="number" min="0" step="0.01" name="custom_texts_parameter_price" placeholder="<?php echo FPD_Admin_Settings::get_option('fpd_custom_texts_parameter_price'); ?>" value=""></td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Colors', 'radykal'); ?></label></th>
									<td><input type="text" name="custom_texts_parameter_colors" value="" placeholder="<?php echo FPD_Admin_Settings::get_option('fpd_custom_texts_parameter_colors'); ?>"></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<br />
				<div>
					<button class="button button-primary fpd-save-modal"><?php _e('Set', 'radykal'); ?></button>
					<button class="button button-secondary fpd-close-modal"><?php _e('Cancel', 'radykal'); ?></button>
				</div>
			</div>
		</div>
		<!-- Templates Modal -->
		<div class="fpd-modal-wrapper" id="fpd-modal-templates">
			<div class="modal-dialog">
				<h4><?php _e( 'Select a template:', 'radykal' ); ?></h4>
				<ul>
					<?php

						$no_templates_info = __('No templates created. Templates can be created in the Order Viewer.', 'radykal');

						if(FPD_Admin_Template::table_exists()) {
							$templates = $wpdb->get_results("SELECT * FROM ".FPD_TEMPLATES_TABLE." ORDER BY title ASC");

							if(sizeof($templates) == 0)
								echo $no_templates_info;

							foreach($templates as $template) {
								echo "<li><a href='#' id='".$template->ID."' data-views='".str_replace("'", "%27", stripslashes($template->views) )."'>".$template->title."</a><span class='fpd-remove-template fpd-right'>&times;</span></li>";
							}

						}
						else {
							echo $no_templates_info;
						}

					?>
				</ul>
				<button class="button button-secondary fpd-close-modal"><?php _e('Cancel', 'radykal'); ?></button>
			</div>
		</div>
	</div>
	<div class="options_group wc-metaboxes">

		<ul id="fpd-view-list">
			<?php

			if( fpd_views_table_exist() ) {

				$fancy_product = new Fancy_Product($post->ID);
				$views = $fancy_product->get_views();

				if(is_array($views)) {
					foreach($views as $view) {
						echo fpd_admin_get_view_list_item($view->ID, $view->title, $view->thumbnail);
					}
				}
			}

			?>
		</ul>
	</div>
	<p class="toolbar">
		<button type="button" class="button button-secondary" id="fpd-save-views"><?php _e( 'Save Views', 'radykal' ); ?></button>
		<button type="button" class="button button-primary fpd-right" id="fpd-add-view"><?php _e( 'Add View', 'radykal' ); ?></button>

	</p>
</div>

<script type="text/javascript">

	jQuery(document).ready(function($) {

		var mediaUploader = $currentMediaInput = null,
			$viewsList = $('#fpd-view-list'),
			$fileImport = $('#fpd-file-import'),
			ajaxUrl = "<?php echo admin_url('admin-ajax.php'); ?>",
			ajaxErrorFunction = function(data) {

				fpdMessage("<?php _e( 'Something went wrong. Please try again!', 'radykal' ); ?>", 'error');
				_unblock();

			};

		//sortable list
		$viewsList.sortable({
			cursor: 'move',
			axis: 'y',
			handle: 'h3',
			scrollSensitivity: 40,
			forcePlaceholderSize: true,
			helper: 'clone',
			opacity: 0.65,
			placeholder: 'fpd-sortable-placeholder',
			start: function( event, ui ) {
				ui.item.css( 'background-color', '#f6f6f6' );
			},
			stop: function ( event, ui ) {
				ui.item.removeAttr( 'style' );
			}
		});


		//FANCY PRODUCT CHECKBOX

		$('#_fancy_product').change(function() {
			if($(this).is(':checked')) {
				$('.hide_if_fancy_product').show();
			}
			else {
				$('.hide_if_fancy_product').hide();
			}
		}).change();


		//TEMPLATES
		var $modalTemplates = $('#fpd-modal-templates').remove();
		$modalTemplates = $('body').append($modalTemplates).children('#fpd-modal-templates');
		$('#fpd-open-templates-modal').click(function() {

			openModal($modalTemplates);

		});

		$modalTemplates.find('li a').click(function(evt) {

			var views = JSON.parse(unescape(JSON.stringify($(this).data('views'))));
			_importViews(views, false);
			closeModal($modalTemplates);

			evt.preventDefault();

		});

		$modalTemplates.find('.fpd-remove-template').click(function(evt) {

			var c = confirm("<?php _e( 'Remove this template?', 'radykal' ); ?>"),
				$this = $(this),
				templateID = $this.prev('a').attr('id');

			if(c) {
				$.ajax({
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					data: { action: 'fpd_removetemplate', _ajax_nonce: "<?php echo FPD_Admin::$ajax_nonce; ?>", id: templateID },
					type: 'post',
					dataType: 'json',
					success: function(data) {

						if(data == 0) {
							fpdMessage("<?php _e( 'Could not delete template. Please try again!', 'radykal' ); ?>", 'error');
						}
						else {
							$this.parents('li').remove();
							closeModal($modalTemplates);
						}

						_unblock();

					}
				});
			}

			evt.preventDefault();

		});

		//SETTINGS

		var $modalWrapper = $('#fpd-modal-settings').remove();
		$modalWrapper = $('body').append($modalWrapper).children('#fpd-modal-settings');
		$modalWrapper.find('.fpd-select2').select2({width: '100%'});

		$(".fpd-tabs-content").find("[id^='tab']").hide(); // Hide all content
	    $(".fpd-tabs li:first").attr("id","current"); // Activate the first tab
	    $(".fpd-tabs-content #tab1").fadeIn(); // Show first tab's content

	    $('.fpd-tabs a').click(function(e) {
	        e.preventDefault();

	        $modalWrapper.find('.fpd-select2').select2("close");

	        if ($(this).closest("li").attr("id") == "current"){ //detection for current tab
	         return;
	        }
	        else{
	          $(".fpd-tabs-content").find("[id^='tab']").hide(); // Hide all content
	          $(".fpd-tabs li").attr("id",""); //Reset id's
	          $(this).parent().attr("id","current"); // Activate this
	          $('#' + $(this).attr('name')).fadeIn(); // Show content for the current tab
	        }
	    });


		$('.fpd-color-picker').wpColorPicker();

		$('#fpd-change-settings').click(function() {

			openModal($modalWrapper);

			try {
				var settingsObject = JSON.parse($('[name="fpd_product_settings"]').val());

				for(var prop in settingsObject) {
					if(settingsObject.hasOwnProperty(prop)) {

						var value = settingsObject[prop],
							$formElement = $modalWrapper.find('[name="'+prop+'"]');

						if($formElement.is('input[type="radio"]') || $formElement.is('input[type="checkbox"]')) {
							$formElement.filter('[value="'+value+'"]').prop('checked', true);
						}
						else {
							$formElement.val(value);
						}

					}
				}
			}
			catch(e) {
			  // nothing
			}

			$modalWrapper.find('.fpd-select2').change();
			$modalWrapper.find('[name="background_type"]:checked').change();
			$modalWrapper.find('#fpd-background-image-preview').attr('src', $modalWrapper.find('[name="background_image"]').val());

		});

		//export product
		$('#fpd-export').click(function(evt) {

			if($viewsList.children('li').length == 0) {

				fpdMessage("<?php echo __('Nothing to export. You have not created views for this product. Please create one or more views!', 'radykal'); ?>", 'info');
				return;
			}

			var urlAjaxExport = ajaxUrl+'?action=fpd_export&_ajax_nonce=<?php echo FPD_Admin::$ajax_nonce; ?>&id=<?php echo $post->ID; ?>';
			location.href = urlAjaxExport;

			evt.preventDefault();

		});

		//export product
		$('#fpd-import').click(function(evt) {

			$fileImport.click();
			evt.preventDefault();

		});

		$fileImport.change(function(evt) {

			if(window.FileReader) {

				var addToLibrary = confirm("<?php _e('Add imported image source to media library?', 'radykal'); ?>");

				var reader = new FileReader();
				reader.readAsText(evt.target.files[0]);
				reader.onload = function (evt) {

					var file = evt.target.result,
						json;

					try {
					  json = JSON.parse(file);
					} catch (exception) {
					  json = null;
					}

					if(json == null) {
						fpdMessage("<?php _e('Sorry, but the selected file is not a valid JSON object. Are you sure you have selected the correct file to import?', 'radykal'); ?>", 'error');
					}
					else {
						_importViews(json, addToLibrary);
					}

				};
			}

			$fileImport.val('');
		});

		//background type switcher
		$modalWrapper.find('[name="background_type"]').change(function() {

			if(this.value == 'image') {
				$modalWrapper.find('[name="background_image"]').parents('tr:first').show();
				$modalWrapper.find('[name="background_color"]').parents('tr:first').hide();
			}
			else {
				$modalWrapper.find('[name="background_image"]').parents('tr:first').hide();
				$modalWrapper.find('[name="background_color"]').parents('tr:first').show();
			}

		});

		//bounding box switcher
		$('[name="designs_parameter_bounding_box_control"], [name="custom_texts_parameter_bounding_box_control"]').change(function() {

			var $this = $(this),
				$tbody = $this.parents('tbody');

			$tbody.find('.custom-bb, .target-bb').hide().addClass('no-serialization');
			if(this.value != '') {
				$tbody.find('.'+$this.find(":selected").data('class')).show().removeClass('no-serialization');
			}


		});

		$('#fpd-set-background-image').click(function(evt) {

			evt.preventDefault();

			if (mediaUploader) {
	            mediaUploader.open();
	            return;
	        }

	        mediaUploader = wp.media({
	            title: '<?php _e( 'Choose a background image', 'radykal' ); ?>',
	            button: {
	                text: '<?php _e( 'Set background image', 'radykal' ); ?>'
	            },
	            multiple: false
	        });

	        mediaUploader.on('select', function() {

				backgroundImage = mediaUploader.state().get('selection').toJSON()[0].url;
				$modalWrapper.find('[name="background_image"]').val(backgroundImage);
				$modalWrapper.find('#fpd-background-image-preview').attr('src', backgroundImage);

			});

			mediaUploader.open();

		});

		$('body').on('click', '.fpd-save-modal', function() {

			var $formFields = $modalWrapper.find('input[type="number"],input[type="text"],input[type="hidden"],input[type="radio"]:checked,select,input[type="checkbox"]:checked').filter(':not(.no-serialization)'),
				serializedStr = JSON.stringify(_serializeObject($formFields));

			$('[name="fpd_product_settings"]').val(serializedStr);

			closeModal($modalWrapper);

		});



		//VIEWS MANAGEMENT

		//add or edit view
		$('#fpd-add-view').click(_addOrEditView);
		$('a.fpd-edit-view').on('click', _addOrEditView);
		$viewsList.on('click', 'a.fpd-edit-view', _addOrEditView);

		//duplicate view
		$('a.fpd-duplicate-view').on('click', _duplicateView);
		$viewsList.on('click', 'a.fpd-duplicate-view', _duplicateView);

		//remove view
		$('a.fpd-remove-view').on('click', _removeView);
		$viewsList.on('click', 'a.fpd-remove-view', _removeView);

		//save views
		$('#fpd-save-views').on('click', function() {

			var ids = $viewsList.children('li').map(function(){
			  return $(this).data('id');
			}).toArray();

			_block();

			$.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				data: { action: 'fpd_saveviews', _ajax_nonce: "<?php echo FPD_Admin::$ajax_nonce; ?>", ids: ids},
				type: 'post',
				dataType: 'json',
				success: function(data) {

					if(data == 0) {
						fpdMessage("<?php _e( 'Could not save views. Please try again!', 'radykal' ); ?>", 'error');
					}

					_unblock();

				}
			});

		});

		function _addOrEditView() {

			var $this = $(this),
				$listItem = $this.parent().parent(),
				editView = $this.hasClass('fpd-edit-view');

			var viewTitle = prompt("<?php _e( 'Enter a title for the view:', 'radykal' ); ?>", editView ? $this.prevAll('span:first').text() : "");
			if(viewTitle == null) {
				return false;
			}
			else if(viewTitle.length == 0) {
				fpdMessage("<?php _e( 'Please enter a title!', 'radykal' ); ?>", 'error');
				return false;
			}

			var viewThumbnail = editView ? $this.prevAll('img:first').attr('src') : "";

			if (mediaUploader) {
				mediaUploader.viewTitle = viewTitle;
				mediaUploader.editView = editView;
	            mediaUploader.open();
	            return;
	        }

	        mediaUploader = wp.media({
	            title: '<?php _e( 'Choose a view thumbnail', 'radykal' ); ?>',
	            button: {
	                text: '<?php _e( 'Choose a view thumbnail', 'radykal' ); ?>'
	            },
	            multiple: false
	        });

	        mediaUploader.viewTitle = viewTitle;
	        mediaUploader.editView = editView;


	        mediaUploader.on('select', function() {

	        	_block();

				viewThumbnail = mediaUploader.state().get('selection').toJSON()[0].url;

	        	if(viewThumbnail.length > 4) {
		        	if(mediaUploader.editView) {

	        			var viewId = $listItem.data('id');
						$.ajax({
							url: "<?php echo admin_url('admin-ajax.php'); ?>",
							data: { action: 'fpd_editview', _ajax_nonce: "<?php echo FPD_Admin::$ajax_nonce; ?>", title: mediaUploader.viewTitle, thumbnail: viewThumbnail, id: viewId },
							type: 'post',
							dataType: 'json',
							success: function(data) {
								if(data == 0) {
									fpdMessage("<?php _e( 'Could not change view. Please try again', 'radykal' ); ?>", 'error');
								}
								else {
									$listItem.children('h3').children('img').attr('src', data.thumbnail).next('span').text(data.title);
								}

								_unblock();

							}
						});
	        		}
	        		else {
		        		$.ajax({
							url: "<?php echo admin_url('admin-ajax.php'); ?>",
							data: { action: 'fpd_newview', _ajax_nonce: "<?php echo FPD_Admin::$ajax_nonce; ?>", title: mediaUploader.viewTitle, thumbnail: viewThumbnail, product_id: <?php echo $post->ID; ?> },
							type: 'post',
							dataType: 'json',
							success: function(data) {
								if(data == 0) {
									fpdMessage("<?php _e( 'Could not create view. Please try again', 'radykal' ); ?>", 'error');
								}
								else {
									$viewsList.append(data.html);
								}

								_unblock();

							}
						});
	        		}

	        	}
	        });

			mediaUploader.open();

			return false;
		};

		function _duplicateView(evt) {

			evt.preventDefault();

			var $listItem = $(this).parent().parent(),
				viewId = $listItem.data('id');

			var viewTitle = prompt("<?php _e( 'Enter a title for the view:', 'radykal' ); ?>", "");
			if(viewTitle == null) {
				return false;
			}
			else if(viewTitle.length == 0) {
				fpdMessage("<?php _e( 'Please enter a title!', 'radykal' ); ?>", 'error');
				return false;
			}

			_block();

			$.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				data: { action: 'fpd_duplicateview', _ajax_nonce: "<?php echo FPD_Admin::$ajax_nonce; ?>", id: viewId, title:viewTitle},
				type: 'post',
				dataType: 'json',
				success: function(data) {

					if(data == 0) {
						fpdMessage("<?php _e( 'Could not duplicate view. Please try again', 'radykal' ); ?>", 'error');
					}
					else {
						$viewsList.append(data.html);
					}

					_unblock();

				},
				error: ajaxErrorFunction
			});

		}

		function _removeView() {

			var $listItem = $(this).parents('li:first'),
				viewId = $listItem.data('id');

			var c = confirm("<?php _e( 'Remove this view?', 'radykal' ); ?>");

			_block();

			if(c) {
				$.ajax({
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					data: { action: 'fpd_removeview', _ajax_nonce: "<?php echo FPD_Admin::$ajax_nonce; ?>", id: viewId },
					type: 'post',
					dataType: 'json',
					success: function(data) {

						if(data == 0) {
							fpdMessage("<?php _e( 'Could not delete view. Please try again', 'radykal' ); ?>", 'error');
						}
						else {
							$listItem.remove();
						}

						_unblock();

					}
				});
			}

			return false;
		};

		function _importViews(views, addToLibrary) {

			var keys = Object.keys(views),
				viewCount = 0;

			function _importView(view) {

				$.ajax({
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					data: {
						action: 'fpd_newview',
						_ajax_nonce: "<?php echo FPD_Admin::$ajax_nonce; ?>",
						title: view.title,
						elements: JSON.stringify(view.elements),
						thumbnail: view.thumbnail,
						thumbnail_name: view.thumbnail_name ? view.thumbnail_name : view.title,
						add_images_to_library: addToLibrary ? 1 : 0,
						product_id: <?php echo $post->ID; ?>
					},
					type: 'post',
					dataType: 'json',
					success: function(data) {

						viewCount++;

						if(data == 0) {
							fpdMessage("<?php _e( 'Could not create view. Please try again!', 'radykal' ); ?>", 'error');
							_unblock();
						}
						else {
							$viewsList.append(data.html);
						}

						if(viewCount < keys.length) {
							_importView(views[keys[viewCount]]);
						}
						else {
							_unblock();
						}

					},
					error: ajaxErrorFunction
				});

			}

			if(!keys.length == 0) {
				_block();
				_importView(views[keys[0]]);
			}

		};

		function _serializeObject(fields) {
		    var o = {};
		    var a = fields.serializeArray();
		    $.each(a, function() {
		        if (o[this.name] !== undefined) {
		            if (!o[this.name].push) {
		                o[this.name] = [o[this.name]];
		            }
					if(this.value) {
						o[this.name].push(this.value || '');
					}

		        } else {
		        	if(this.value) {
			        	o[this.name] = this.value || '';
		        	}
		        }
		    });
		    return o;
		};

		function _serializedStringToObject(string) {

			var splitParams = string.split("&");

			//convert parameter string into object
			var object = {};
			for(var i=0; i < splitParams.length; ++i) {
				var splitIndex = splitParams[i].indexOf("=");
				object[splitParams[i].substr(0, splitIndex)] = splitParams[i].substr(splitIndex+1).replace(/\_/g, ' ');
			}
			return object;

		};

		function _block() {

			$viewsList.block({
				message: null,
				overlayCSS: { background: '#fff url(' + woocommerce_admin_meta_boxes.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 }
			});

		}

		function _unblock() {

			$viewsList.unblock();

		}

	});

</script>