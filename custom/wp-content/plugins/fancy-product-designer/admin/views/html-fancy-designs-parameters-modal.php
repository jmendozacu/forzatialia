<div class="fpd-modal-wrapper" id="fpd-modal-parameters">
	<div class="modal-dialog">

		<!-- Only for single designs to set a custom thumbnail -->
		<div class="fpd-set-design-thumbnail-wrapper">
			<h3><?php _e('Design Thumbnail', 'radykal'); ?></h3>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><button class="button fpd-set-design-thumbnail"><?php _e('Set Design Thumbnail', 'radykal'); ?></button></th>
						<td>
							<img src="" class="fpd-design-thumbnail" />
							<button class="button fpd-remove-design-thumbnail" style="margin-left: 10px;"><?php _e('Remove', 'radykal'); ?></button>
						</td>
					</tr>
				</tbody>
			</table>
			<br />
		</div>

		<!-- Parameters Form -->
		<h3><?php _e('Edit Parameters', 'radykal'); ?></h3>
		<form>
			<label><input type="checkbox" value="1" name="enabled" /> <strong><?php _e('Enable parameters', 'radykal'); ?></strong></label>
			<br />
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><label><?php _e('X-Position', 'radykal'); ?></label></th>
						<td><input type="number" step="1" min="0" value="0" style="width:50px;" name="x" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Y-Position', 'radykal'); ?></label></th>
						<td><input type="number" step="1" min="0" value="0" style="width:50px;" name="y" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Z-Position', 'radykal'); ?></label></th>
						<td><input type="number" step="1" min="-1" value="-1" style="width:50px;" name="z" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Scale', 'radykal'); ?></label></th>
						<td><input type="number" style="width:50px;" name="scale" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Color(s)', 'radykal'); ?></label></th>
						<td><input type="text" value="#000000" style="width:300px;" name="colors" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Price', 'radykal'); ?></label></th>
						<td><input type="number" step="1" min="0" value="0" style="width:50px;" name="price" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Auto-Center', 'radykal'); ?></label></th>
						<td>
							<input type="hidden" value="0" name="autoCenter" />
							<input type="checkbox" value="1" name="autoCenter" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Draggable', 'radykal'); ?></label></th>
						<td>
							<input type="hidden" value="0" name="draggable" />
							<input type="checkbox" value="1" name="draggable" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Rotatable', 'radykal'); ?></label></th>
						<td>
							<input type="hidden" value="0" name="rotatable" />
							<input type="checkbox" value="1" name="rotatable" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Resizable', 'radykal'); ?></label></th>
						<td>
							<input type="hidden" value="0" name="resizable" />
							<input type="checkbox" value="1" name="resizable" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Replace', 'radykal'); ?></label></th>
						<td>
							<input type="text" value="" name="replace" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Auto-Select', 'radykal'); ?></label></th>
						<td>
							<input type="hidden" value="0" name="autoSelect" />
							<input type="checkbox" value="1" name="autoSelect" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Stay On Top', 'radykal'); ?></label></th>
						<td>
							<input type="hidden" value="0" name="topped" />
							<input type="checkbox" value="1" name="topped" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Bounding Box', 'radykal'); ?></label></th>
						<td>
							<select name="bounding_box_control">
								<option value="0" data-class="custom-bb"><?php _e( 'Custom Bounding Box', 'radykal' ); ?></option>
								<option value="1" data-class="target-bb"><?php _e( 'Use another element as bounding box', 'radykal' ); ?></option>
							</select>
						</td>
					</tr>
					<tr valign="top" class="custom-bb">
						<th scope="row"><label><?php _e('Bounding Box X-Position', 'radykal'); ?></label></th>
						<td><input type="number" name="bounding_box_x" min="0" step="1" value=""></td>
					</tr>
					<tr valign="top" class="custom-bb">
						<th scope="row"><label><?php _e('Bounding Box Y-Position', 'radykal'); ?></label></th>
						<td><input type="number" name="bounding_box_y" min="0" step="1" value=""></td>
					</tr>
					<tr valign="top" class="custom-bb">
						<th scope="row"><label><?php _e('Bounding Box Width', 'radykal'); ?></label></th>
						<td><input type="number" name="bounding_box_width" min="0" step="1" value=""></td>
					</tr>
					<tr valign="top" class="custom-bb">
						<th scope="row"><label><?php _e('Bounding Box Height', 'radykal'); ?></label></th>
						<td><input type="number" name="bounding_box_height" min="0" step="1" value=""></td>
					</tr>
					<tr valign="top" class="target-bb">
						<th scope="row"><label><?php _e('Bounding Box Target', 'radykal'); ?></label></th>
						<td><input type="text" name="bounding_box_by_other" value=""></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Bounding Box Clipping', 'radykal'); ?></label></th>
						<td>
							<input type="hidden" value="0" name="boundingBoxClipping" />
							<input type="checkbox" value="1" name="boundingBoxClipping" />
						</td>
					</tr>
				</tbody>
			</table>
		</form>
		<br />
		<div>
			<button class="button button-primary fpd-save-modal"><?php _e('Set', 'radykal'); ?></button>
			<button class="button button-secondary fpd-close-modal"><?php _e('Cancel', 'radykal'); ?></button>
		</div>
	</div><!-- modal dialog -->
</div><!-- modal wrapper -->