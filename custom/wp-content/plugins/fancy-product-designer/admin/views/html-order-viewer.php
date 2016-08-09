<div id="fpd-order-panel">
	<div id="fpd-order-designer-wrapper">

		<p class="description"><strong><?php _e( 'To load an ordered product, you need to click the "Open" button next to the ordered item in the "Order Items" panel!', 'radykal' ); ?></strong></p>
		<!-- Product Designer Container -->
		<div id="fpd-order-designer"></div>
		<!-- Tools -->
		<div id="fpd-export-tools" class="ui form green segment">

			<div class="two fields">

				<div class="field">
					<h2><?php _e( 'Export', 'radykal' ); ?></h2>
					<div class="inline fields">
						<div class="field three wide">
							<?php _e('Output File', 'radykal' ); ?>
						</div>
						<div class="field nine wide">
							<label><input type="radio" name="fpd_output_file" value="pdf" checked="checked" /> <?php _e('PDF', 'radykal' ); ?></label>
							<label><input type="radio" name="fpd_output_file" value="image" /> <?php _e('IMAGE', 'radykal' ); ?></label>
						</div>
					</div>
					<div class="inline fields">
						<div class="field three wide">
							<?php _e('Image Format', 'radykal' ); ?>
						</div>
						<div class="field nine wide">
							<label><input type="radio" name="fpd_image_format" value="png" checked="checked" /> PNG</label>
							<label><input type="radio" name="fpd_image_format" value="jpeg" /> JPEG</label>
							<label class="help_tip" data-tip="<?php _e( 'Exporting as SVG format allows to create a multi-layer PDF. Bounding box clippings are ignored!', 'radykal' ); ?>"><input type="radio" name="fpd_image_format" value="svg" /> SVG</label>
						</div>
					</div>
					<div class="inline fields" id="fpd-size-fields">
						<div class="field three wide">
							<p><?php _e('Size:', 'radykal' ); ?></p>
							<div><a href="http://www.hdri.at/dpirechner/dpirechner_en.htm" target="_blank" style="font-size: 11px;"><?php _e('DPI - Pixel Converter', 'radykal' ); ?></a></div>
						</div>
						<div class="field three wide">
							<label><input type="number" value="210" id="fpd-pdf-width" /> <?php _e('PDF width in mm', 'radykal' ); ?></label>
						</div>
						<div class="field three wide">
							<label><input type="number" value="297" id="fpd-pdf-height" /> <?php _e('PDF height in mm', 'radykal' ); ?></label>
						</div>
						<div class="field three wide">
							<label><input type="text" value="" name="fpd_scale" placeholder="1" /><?php _e('Scale Factor', 'radykal' ); ?></label>
						</div>
					</div>
					<div class="inline fields">
						<div class="field three wide">
							<?php _e('View(s)', 'radykal' ); ?>
						</div>
						<div class="field nine wide">
							<label><input type="radio" name="fpd_export_views" value="all" checked="checked" /> <?php _e('ALL', 'radykal' ); ?></label>
							<label><input type="radio" name="fpd_export_views" value="current" /> <?php _e('CURRENT SHOWING', 'radykal' ); ?></label>
						</div>
					</div>
					<button id="fpd-generate-file" class="button button-primary"><?php _e( 'Create', 'radykal' ); ?></button>
					<img class="help_tip" data-tip="<?php _e( 'The created pdfs will be stored in: ', 'radykal' ); echo content_url('/fancy_products_orders/pdfs'); ?>" src="<?php echo $woocommerce->plugin_url() . '/assets/images/help.png'; ?>" height="16" width="16" />
				</div>

				<div class="field">
					<h2><?php _e( 'Single element images', 'radykal' ); ?><img class="help_tip" data-tip="<?php _e( 'You can save each element in the designed product as image on your web server. They will be stored in: ', 'radykal' ); echo content_url('/fancy_products_orders/images'); ?>" src="<?php echo $woocommerce->plugin_url() . '/assets/images/help.png'; ?>" height="16" width="16" /></h2>
					<div class="inline fields">
						<div class="field three wide">
							<?php _e('Image Format', 'radykal' ); ?>
						</div>
						<div class="field nine wide">
							<label><input type="radio" name="fpd_single_image_format" value="png" checked="checked" /> PNG</label>
							<label><input type="radio" name="fpd_single_image_format" value="jpeg" /> JPEG</label>
							<label class="help_tip" data-tip="<?php _e( 'When creating a SVG image from a text element, be sure that the font is installed on your computer, otherwise you will not see the correct font.', 'radykal' ); ?>" src="<?php echo $woocommerce->plugin_url() . '/assets/images/help.png'; ?>"><input type="radio" name="fpd_single_image_format" value="svg" /> SVG</label>
						</div>
					</div>
					<div class="inline fields">
						<div class="field">
							<label>
								<input type="checkbox" id="fpd-restore-oring-size" />
								<?php _e( 'Use origin size, that will set the scaling to 1, when exporting the image.', 'radykal' ); ?>
							</label>
						</div>
					</div>
					<div class="inline fields">
						<div class="field">
							<label>
								<input type="checkbox" id="fpd-save-on-server" />
								<?php _e( 'Save exported image on server.', 'radykal' ); ?>
							</label>
						</div>
					</div>
					<div class="inline fields">
						<div class="field">
							<label>
								<input type="checkbox" id="fpd-without-bounding-box" />
								<?php _e( 'Export without bounding box clipping if element has one.', 'radykal' ); ?>
							</label>
						</div>
					</div>
					<div class="inline fields">
						<div class="field">
							<label>
								<input type="number" min="0" value="" name="fpd_single_element_padding" placeholder="0" />
								<?php _e('Padding around exported element.', 'radykal' ); ?>
							</label>
						</div>
					</div>
					<button id="fpd-save-element-as-image" class="button button-primary"><?php _e( 'Create', 'radykal' ); ?></button>
					<ul id="fpd-order-image-list"></ul>
				</div>

			</div>
		</div><!-- Tools -->

		<div id="fpd-export-tools" class="ui segment">
				<p>
					<button type="button" class="button button-primary" id="fpd-save-as-template"><?php _e( 'Save as Template', 'radykal' ); ?></button>
					<img class="help_tip" data-tip="<?php _e( 'Templates can be retrieved in the Data panel when editing a product.', 'radykal' ); ?>" src="<?php echo $woocommerce->plugin_url() . '/assets/images/help.png'; ?>" height="16" width="16" />
				</p>
			</div>

	</div>
</div>
<script type="text/javascript">

	jQuery(document).ready(function($) {

		var $fancyProductDesigner = $('#fpd-order-designer'),
			$orderImageList = $('#fpd-order-image-list'),
			currentItemId = null,
			isReady = false,
			stageWidth = <?php echo FPD_Admin_Settings::get_option('fpd_stage_width'); ?>,
			stageHeight = <?php echo FPD_Admin_Settings::get_option('fpd_stage_height'); ?>;

		$(document).ajaxError( function(e, xhr, settings, exception) {
		 	//console.log(e, xhr, settings, exception);
		});

		var fancyProductDesigner = $fancyProductDesigner.fancyProductDesigner({
			editorMode: true,
			fonts: ["<?php echo implode('","', FPD_Fonts::get_enabled_fonts()); ?>"],
			templatesDirectory: "<?php echo plugins_url('/templates/', dirname(dirname(__FILE__))); ?>",
			layout: 'semantic',
			tooltips: false,
			dimensions: {
				productStageWidth: stageWidth,
				productStageHeight: stageHeight
			}
		}).data('fancy-product-designer');

		//api buttons first available when
		$fancyProductDesigner.on('ready', function() {
			isReady = true;
		});

		$('.fancy-product').on('click', '.fpd-show-order-item', function(evt) {

			evt.preventDefault();

			if(	isReady ) {

				var $this = $(this),
					order = $this.data('order');

				//stringify it to unescape it to replace %27 with ', last make it to an object again
				order = JSON.parse(unescape(JSON.stringify(order)));

				$orderImageList.empty();

				currentItemId = $this.attr('id');

				if(typeof order == 'object') {

					stageWidth = $this.data('stagewidth');
					stageHeight = $this.data('stageheight');

					fancyProductDesigner.setStageDimensions(stageWidth, stageHeight);
					fancyProductDesigner.loadProduct(order);

					$.ajax({
						url: "<?php echo admin_url('admin-ajax.php'); ?>",
						data: {
							action: 'fpd_loadorderitemimages',
							_ajax_nonce: "<?php echo FPD_Admin::$ajax_nonce; ?>",
							order_id: <?php echo $thepostid; ?>,
							item_id: currentItemId
						},
						type: 'post',
						dataType: 'json',
						success: function(data) {
							if(data == undefined || data.code == 500) {
								fpdMessage("<?php _e( 'Could not load order item image. Please try again!', 'radykal' ); ?>", 'info');
							}
							//append order item images to list
							else if( data.code == 200 ) {
								for (var i=0; i < data.images.length; ++i) {
									var title = data.images[i].substr(data.images[i].lastIndexOf('/')+1);
									$orderImageList.append('<li><a href="'+data.images[i]+'" title="'+data.images[i]+'" target="_blank" >'+title+'</a></li>');
								}
							}
						}
					});
				}
			}

		});

		$('#fpd-save-as-template').click(function(evt) {

			if(	_checkAPI() ) {

				var title = prompt("<?php _e( 'Please enter a title for the template', 'radykal' ); ?>:", "");

				if(title == null) {
					return false;
				}
				else if(title.length == 0) {
					fpdMessage("<?php _e( 'Please enter a title for the template', 'radykal' ); ?>!", 'error');
					return false;
				}

				$.ajax({
					url: "<?php echo admin_url('admin-ajax.php'); ?>",
					data: {
						action: 'fpd_saveastemplate',
						_ajax_nonce: "<?php echo FPD_Admin::$ajax_nonce; ?>",
						title: title,
						views: JSON.stringify(fancyProductDesigner.getProduct())
					},
					type: 'post',
					dataType: 'json',
					success: function(data) {
						if(data == undefined || data.error) {
							fpdMessage(data.message, 'error');
						}
						else {
							fpdMessage(data.message, 'success');
						}
					}
				});

			}

			evt.preventDefault();

		});


		$('[name="fpd_output_file"]').change(function() {

			if($('[name="fpd_output_file"]:checked').val() == 'pdf') {
				$('#fpd-pdf-width').parents('.field:first').show();
				$('#fpd-pdf-height').parents('.field:first').show();
			}
			else {
				$('#fpd-pdf-width').parents('.field:first').hide();
				$('#fpd-pdf-height').parents('.field:first').hide();
			}

		}).change();

		$('[name="fpd_image_format"]').change(function() {

			if($('[name="fpd_image_format"]:checked').val() == 'svg') {
				$('#fpd-size-fields').hide();
			}
			else {
				$('#fpd-size-fields').show();
			}

		}).change();

		$('#fpd-generate-file').click(function(evt) {

			evt.preventDefault();

			if(_checkAPI()) {

				if($('[name="fpd_output_file"]:checked').val() == 'image') {
					createImage();
				}
				else {
					createPdf();
				}

			}

		});

		$('#fpd-save-element-as-image').click(function(evt) {

			evt.preventDefault();

			if(_checkAPI()) {

				var stage = fancyProductDesigner.getStage(),
					format = $('input[name="fpd_single_image_format"]:checked').val(),
					backgroundColor = format == 'jpeg' ? '#ffffff' : 'transparent',
					currentViewIndex = fancyProductDesigner.getViewIndex(),
					objects = stage.getObjects();

				if(stage.getActiveObject()) {

					var $this = $(this),
						element = stage.getActiveObject(),
						dataObj;

					if(format == 'svg') {

						if(element.toSVG().search('<image') != -1) {
							fpdMessage("<?php _e( 'You are trying to create a SVG from a bitmap. You can only create SVG files from text elements or from image elements with SVG as source.', 'radykal' ); ?>", 'info');
							return false;
						}

					}

					fancyProductDesigner.deselectElement();

					//check if origin size should be rendered
					if($('#fpd-restore-oring-size').is(':checked')) {
						element.setScaleX(1);
						element.setScaleY(1);
					}

					stage.setBackgroundColor(backgroundColor, function() {

						var paddingTemp = element.padding;
						element.padding = $('input[name="fpd_single_element_padding"]').val().length == 0 ? 0 : Number($('input[name="fpd_single_element_padding"]').val());

						var clipToTemp = element.getClipTo();
						if(clipToTemp != null) {

							if($('#fpd-without-bounding-box').is(':checked')) {
								element.setClipTo(null);
								stage.renderAll();
							}
							else {
								for(var i=0; i < objects.length; ++i) {

									var object = objects[i];
									if(object.viewIndex == currentViewIndex) {
										object.visible = false;
									}

								}

								element.visible = true;
							}


							/*stage.setDimensions({width: clippingArea.width + clippingArea.width - element.getBoundingRect().left, height: clippingArea.top + clippingArea.height - element.getBoundingRect().top}).renderAll();
							element.setLeft(element.left - clippingArea.left).setTop(element.top - clippingArea.top)
							.center()
							.setCoords();*/

						}

						element.setCoords();

						var source;

						if(format == 'svg') {
							source = element.toSVG();
						}
						else {
							source = clipToTemp != null && !$('#fpd-without-bounding-box').is(':checked') ? stage.toDataURL({format: format}) : element.toDataURL({format: format});
						}

						if($('#fpd-save-on-server').is(':checked')) {

							$('#fpd-export-tools').addClass('loading');

							if(format == 'svg') {

								dataObj = {
									action: 'fpd_imagefromsvg',
									_ajax_nonce: "<?php echo FPD_Admin::$ajax_nonce; ?>",
									order_id: <?php echo $thepostid; ?>,
									item_id: currentItemId,
									svg: source,
									width: stage.getWidth(),
									height: stage.getHeight(),
									title: element.title
								};

							}
							else {

								dataObj = {
									action: 'fpd_imagefromdataurl',
										_ajax_nonce: "<?php echo FPD_Admin::$ajax_nonce; ?>",
										order_id: <?php echo $thepostid; ?>,
										item_id: currentItemId,
										data_url: source,
										title: element.title,
										format: format
								};
							}

							$.ajax({
								url: "<?php echo admin_url('admin-ajax.php'); ?>",
								data: dataObj,
								type: 'post',
								dataType: 'json',
								complete: function(data) {

									var json = data.responseJSON;
									if(data.status != 200 || json.code == 500) {
										fpdMessage("<?php _e( 'Could not create image. Please try again!', 'radykal' ); ?>", 'error');
									}
									else if( json.code == 201 ) {
										$orderImageList.append('<li><a href="'+json.url+'" title="'+json.url+'" target="_blank">'+json.title+'.'+format+'</a></li>');
									}
									else {
										//prevent caching
										$orderImageList.find('a[title="'+json.url+'"]').attr('href', json.url+'?t='+new Date().getTime());
									}

									$('#fpd-export-tools').removeClass('loading');

								}
							});

						}
						else { //dont save it on server

							var popup = window.open('','_blank');
							if(!_popupBlockerEnabled(popup)) {

								popup.document.title = element.title;


								if(format == 'svg') {
									source = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="'+stage.getWidth()+'" height="'+stage.getHeight()+'" xml:space="preserve">'+element.toSVG()+'</svg>';
									$(popup.document.body).append(source);
								}
								else {
									$(popup.document.body).append('<img src="'+source+'" title="Product" />');

								}

							}

						}

						for(var i=0; i < objects.length; ++i) {

							var object = objects[i];
							if(object.viewIndex == currentViewIndex) {
								object.visible = true;
							}

						}

						element.set({scaleX: element.params.scale, scaleY: element.params.scale, padding: paddingTemp})
						.setClipTo(clipToTemp)
						.setCoords();

						stage.setBackgroundColor('transparent')
						.setDimensions({width: stageWidth, height: stageHeight})
						.renderAll();

					});

				}
				else {
					fpdMessage("<?php _e('No element selected!', 'radykal'); ?>", 'info');
				}
			}

		});

		$('input[name="fpd_scale"]').keyup(function() {

			var scale = !isNaN(this.value) && this.value.length > 0 ? this.value : 1,
				mmInPx = 3.779528;

			$('#fpd-pdf-width').val(Math.round((stageWidth * scale) / mmInPx));
			$('#fpd-pdf-height').val(Math.round((stageHeight * scale) / mmInPx));

		}).keyup();

		function createImage() {

			var format = $('input[name="fpd_image_format"]:checked').val(),
				data;

			if(format == 'svg') {
				data = fancyProductDesigner.getViewsSVG();
			}
			else {
				var backgroundColor = format == 'jpeg' ? '#ffffff' : 'transparent',
					multiplier = $('input[name="fpd_scale"]').val().length == 0 ? 1 : Number($('input[name="fpd_scale"]').val());

				data = fancyProductDesigner.getViewsDataURL(format, backgroundColor, multiplier);
			}

			if($('[name="fpd_export_views"]:checked').val() == 'current') {
				var requestedIndex = data[fancyProductDesigner.getViewIndex()];
				data = [];
				data.push(requestedIndex);
			}

			var popup = window.open('','_blank');
			if(!_popupBlockerEnabled(popup)) {
				popup.document.title = "<?php echo $thepostid; ?>";
				for(var i=0; i < data.length; ++i) {
					if(format == 'svg') {
						$(popup.document.body).append(data[i]);
					}
					else {
						$(popup.document.body).append('<img src="'+data[i]+'" title="View'+i+'" />');
					}

				}

			}

		};

		function createPdf() {

			if($('#fpd-pdf-width').val() == '') {
				fpdMessage("<?php _e( 'No width is set. Please set one!', 'radykal' ); ?>", 'error');
				return false;
			}
			else if($('#fpd-pdf-height').val() == '') {
				fpdMessage("<?php _e( 'No width is set. Please set one!', 'radykal' ); ?>", 'error');
				return false;
			}

			$('#fpd-export-tools').addClass('loading');

			var format = $('input[name="fpd_image_format"]:checked').val(),
				backgroundColor = format == 'jpeg' ? '#ffffff' : 'transparent',
				data;

			if(format == 'svg') {
				data = fancyProductDesigner.getViewsSVG();
			}
			else {
				var multiplier = $('input[name="fpd_scale"]').val().length == 0 ? 1 : Number($('input[name="fpd_scale"]').val());
				data = fancyProductDesigner.getViewsDataURL(format, backgroundColor, multiplier);
			}

			if($('[name="fpd_export_views"]:checked').val() == 'current') {
				var requestedIndex = data[fancyProductDesigner.getViewIndex()];
				data = [];
				data.push(requestedIndex);
			}

			var data_str = JSON.stringify(data);

			$.ajax({
				url: "<?php echo admin_url('admin-ajax.php'); ?>",
				data: {
					action: 'fpd_pdffromdataurl',
					_ajax_nonce: "<?php echo FPD_Admin::$ajax_nonce; ?>",
					order_id: <?php echo $thepostid; ?>,
					data_strings: data_str,
					width: $('#fpd-pdf-width').val(),
					height: $('#fpd-pdf-height').val(),
					image_format: $('input[name="fpd_image_format"]:checked').val(),
					orientation: stageWidth > stageHeight ? 'L' : 'P'
				},
				type: 'post',
				dataType: 'json',
				complete: function(data) {
					if(data == undefined || data.status != 200) {

						var message = '';
						if(data.responseJSON && data.responseJSON.message) {
							message += data.responseJSON.message;
						}
						message += '.\n';
						message += '<?php _e( 'PDF could not be created. The transmitted data is too great, you need to increase the memory limit in your php.ini, export only a single view or use jpeg as image format!', 'radykal' ); ?>';
						fpdMessage(message, 'error');

					}
					else {
						var json = data.responseJSON;
						if(json !== undefined) {
							window.open(json.url, '_blank');
						}
						else {
							fpdMessage("<?php _e('JSON could not be parsed. Go to wp-content/fancy_products_orders/pdfs and check if a PDF has been generated.'); ?>", 'error');
						}
					}

					$('#fpd-export-tools').removeClass('loading');

				}
			});

		};

		function _checkAPI() {

			if(fancyProductDesigner.getStage().getObjects().length > 0 && isReady) {
				return true;
			}
			else {
				fpdMessage("<?php _e( 'No Fancy Product is selected. Please open one from the Order Items!', 'radykal' ); ?>", 'error');
				return false;
			}

		};


		// Convert dataURL to Blob object
		function _dataURLtoBlob(dataURL, imageFormat) {
		  var binary = atob(dataURL.split(',')[1]);
		  var array = [];
		  for(var i = 0; i < binary.length; i++) {
		      array.push(binary.charCodeAt(i));
		  }
		  // Return Blob object
		  return new Blob([new Uint8Array(array)], {type: 'image/'+imageFormat+''});
		}

		function _popupBlockerEnabled(popup) {

			if (popup == null || typeof(popup)=='undefined') {
				fpdMessage("<?php _e( 'Your Pop-Up Blocker is enabled. The image will be opened in a new window. Please allow this website for your Pop-Up Blocker!', 'radykal' ); ?>", 'info');
				return true;
			}
			else {
				return false;
			}

		}

	});

</script>