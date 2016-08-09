<form role="form" id="fpd-elements-form" class="">

	<!-- Hidden inputs for parameters set are set to true by default -->
	<input type="hidden" name="editable" value="0" />
	<input type="checkbox" name="locked" value="1" class="fpd-hidden" />
	<input type="checkbox" name="uploadZone" value="1" class="fpd-hidden" />

	<!-- Several inputs -->
	<div class="fpd-children-floated fpd-clearfix">
		<table class="form-table">
			<tbody>
				<tr>
					<th><?php _e('Position', 'radykal'); ?>:</th>
					<td>
						<label><?php _e('x', 'radykal'); ?>=<input type="text" name="x" size="3" placeholder="0" style="margin-right: 15px;" class="fpd-only-numbers"></label>
						<label><?php _e('y', 'radykal'); ?>=<input type="text" name="y" size="3" placeholder="0" class="fpd-only-numbers"></label>
					</td>
				</tr>
				<tr>
					<th><?php _e('Scale', 'radykal'); ?>:</th>
					<td>
						<input type="text" name="scale" size="3" placeholder="1" class="fpd-only-numbers fpd-allow-dots">
					</td>
				</tr>
				<tr>
					<th><?php _e('Angle', 'radykal'); ?>:</th>
					<td>
						<input type="text" name="angle" size="3" placeholder="0" class="fpd-only-numbers">
					</td>
				</tr>
				<tr>
					<th><?php _e('Price', 'radykal'); ?>: <img class="help_tip" data-tip="<?php _e('Use always a dot as decimal separator!', 'radykal'); ?>" src="<?php echo $woocommerce->plugin_url() . '/assets/images/help.png'; ?>" height="16" width="16" /></th>
					<td>
						<input type="text" name="price" size="3" placeholder="0" class="fpd-prevent-whitespace fpd-only-numbers fpd-allow-dots">
					</td>
				</tr>
				<tr>
					<th><?php _e('Replace', 'radykal'); ?>: <img class="help_tip" data-tip="<?php _e('Elements with the same replace name will replace each other.', 'radykal'); ?>" src="<?php echo $woocommerce->plugin_url() . '/assets/images/help.png'; ?>" height="16" width="16" /></th>
					<td>
						<input type="text" name="replace" value="" class="widefat input-sm">
					</td>
				</tr>
				<tr class="fpd-color-options">
					<th><?php _e('Colors', 'radykal'); ?>:</th>
					<td>
						<?php if($fancy_view->get_data()->view_order && $fancy_view->get_data()->view_order > 0) : ?>
						<label class="checkbox-inline" style="margin-bottom: 15px;"><input type="checkbox" name="color_control" value="1"> <?php _e('Enable Color Control', 'radykal'); ?> <img class="help_tip" data-tip="<?php _e('To change the color of this element as soon as the color of an element in first view changes, you can enter a title of an element in the first view. This operation works only with PNG images that are NOT in the first view.', 'radykal'); ?>" src="<?php echo $woocommerce->plugin_url() . '/assets/images/help.png'; ?>" height="16" width="16" /></label>
						<?php endif; ?>
						<input type="text" name="colors" class="tm-input" placeholder="<?php _e('e.g. #000000', 'radykal') ; ?>" size="9" />
						<img id="fpd-color-tags-desc" class="help_tip" data-tip="<?php _e('One color value: Colorpicker, Multiple color values: Fixed color palette', 'radykal'); ?>" src="<?php echo $woocommerce->plugin_url() . '/assets/images/help.png'; ?>" height="16" width="16" />
					</td>
				</tr>
			</tbody>
		</table>
		<table class="form-table">
			<tbody>
				<tr>
					<th><?php _e('Opacity', 'radykal'); ?>: <img class="help_tip" data-tip="<?php _e('A value between 0-1', 'radykal'); ?>" src="<?php echo $woocommerce->plugin_url() . '/assets/images/help.png'; ?>" height="16" width="16" /></th>
					<td>
						<input type="text" name="opacity" size="3" placeholder="1" class="fpd-prevent-whitespace fpd-only-numbers fpd-allow-dots">
					</td>
				</tr>
				<tr class="fpd-color-options">
					<th><?php _e('Current Color', 'radykal'); ?>: <img class="help_tip" data-tip="<?php _e('Enter one hexadecimal color to change the initial color of this element.', 'radykal'); ?>" src="<?php echo $woocommerce->plugin_url() . '/assets/images/help.png'; ?>" height="16" width="16" /></th>
					<td>
						<input type="text" name="currentColor" placeholder="<?php _e('e.g. #000000', 'radykal') ; ?>" size="9" />
					</td>
				</tr>
				<tr>
					<th><?php _e('Modifications', 'radykal'); ?></th>
					<td>
						<label class="checkbox-inline"><input type="checkbox" name="removable" value="1"> <?php _e('Removable', 'radykal'); ?></label>
						<label class="checkbox-inline"><input type="checkbox" name="draggable" value="1"> <?php _e('Draggable', 'radykal'); ?></label>
						<label class="checkbox-inline"><input type="checkbox" name="rotatable" value="1"> <?php _e('Rotatable', 'radykal'); ?></label>
						<label class="checkbox-inline"><input type="checkbox" name="resizable" value="1"> <?php _e('Resizable', 'radykal'); ?></label>
						<label class="checkbox-inline"><input type="checkbox" name="zChangeable"value="1"> <?php _e('Z-Position Changeable', 'radykal'); ?></label>
						<label class="checkbox-inline"><input type="checkbox" name="topped"value="1"> <?php _e('Stay On Top', 'radykal'); ?></label>
						<label class="checkbox-inline"><input type="checkbox" name="autoSelect"value="1"> <?php _e('Auto-Select', 'radykal'); ?></label>
					</td>
				</tr>
			</tbody>
		</table>
		<table class="form-table">
			<thead>
				<tr>
					<th colspan="2"><?php _e('Bounding Box', 'radykal'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="2"><label><input type="checkbox" name="bounding_box_control" value="1"> <?php _e('Use another element as bounding box', 'radykal'); ?></label></td>
				</tr>
				<tr>
					<td colspan="2"><label><input type="checkbox" name="boundingBoxClipping" value="1"> <?php _e('Clip element into bounding box', 'radykal'); ?></label></td>
				</tr>
				<tr>
					<td colspan="2">
						<div id="boundig-box-params">
							<label><?php _e('x', 'radykal'); ?>:</label><input type="text" name="bounding_box_x" size="3" placeholder="0" style="margin-right: 15px;">
							<label><?php _e('y', 'radykal'); ?>:</label><input type="text" name="bounding_box_y" size="3" placeholder="0">
							<br />
							<label><?php _e('width', 'radykal'); ?>:</label><input type="text" name="bounding_box_width" size="3" placeholder="0" style="margin-right: 15px;">
							<label><?php _e('height', 'radykal'); ?>:</label><input type="text" name="bounding_box_height" size="3" placeholder="0">
						</div>
						<input type="text" name="bounding_box_by_other" class="widefat input-sm" placeholder="<?php _e('Title of an image element in the same view.', 'radykal'); ?>" style="display: none;" />
					</td>
				</tr>
			</tbody>
		</table>
		<table class="form-table only-for-text-elements">
			<thead>
				<tr>
					<th><?php _e('Text', 'radykal'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th><?php _e('Font', 'radykal'); ?></th>
					<td>
						<select name="font" data-placeholder="<?php _e('Select a font', 'radykal'); ?>" class="fpd-font-changer">
							<?php
							foreach(FPD_Fonts::get_enabled_fonts() as $font) {
								echo "<option value='$font' style='font-family: $font;'>$font</option>";
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<th><?php _e('Styling & Alignment', 'radykal'); ?></th>
					<td>
						<span class="fpd-text-styling" style="margin-right: 20px;">
							<button class="fpd-bold button"><i class="fa fa-bold"></i></button>
							<button class="fpd-italic button"><i class="fa fa-italic"></i></button>
							<input type="checkbox" name="fontWeight" value="bold" class="fpd-hidden" />
							<input type="checkbox" name="fontStyle" value="italic" class="fpd-hidden" />
						</span>
						<span class="fpd-text-align">
							<button class="fpd-align-left button" data-value="left"><i class="fa fa-align-left"></i></button>
							<button class="fpd-align-center button" data-value="center"><i class="fa fa-align-center"></i></button>
							<button class="fpd-align-right button" data-value="right"><i class="fa fa-align-right"></i></button>
							<input type="hidden" name="textAlign" value="left" />
						</span>
					</td>
				</tr>
				<tr>
					<th><?php _e('Maximum Characters', 'radykal'); ?></th>
					<td><input type="text" name="maxLength" size="3" placeholder="0" class="fpd-only-numbers"></td>
				</tr>
				<tr>
					<th><?php _e('Modifications', 'radykal'); ?></th>
					<td>
						<label class="checkbox-inline"><input type="checkbox" name="editable" value="1"> <?php _e('Editable', 'radykal'); ?></label>
						<label class="checkbox-inline"><input type="checkbox" name="patternable" value="1"> <?php _e('Patternable', 'radykal'); ?></label>
						<label class="checkbox-inline"><input type="checkbox" name="curvable" value="1"> <?php _e('Curvable', 'radykal'); ?></label>
					</td>
				</tr>
				<tr id="fpd-curved-text-opts">
					<th><?php _e('Curved Text', 'radykal'); ?></th>
					<td>
						<input type="checkbox" name="curved" value="1" class="fpd-hidden">
						<label style="width: 60px;"><?php _e('Spacing', 'radykal'); ?>:</label><input type="text" name="curveSpacing" size="3" placeholder="10" class="fpd-only-numbers">
						<br />
						<label style="width: 60px;"><?php _e('Radius', 'radykal'); ?>:</label><input type="text" name="curveRadius" size="3" placeholder="80" class="fpd-only-numbers">
						<br />
						<label class="checkbox-inline"><input type="checkbox" name="curveReverse" value="1"> <?php _e('Reverse', 'radykal'); ?></label>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

</form>