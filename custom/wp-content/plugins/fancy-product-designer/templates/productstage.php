<?php
//labels
$add_image_tooltip = isset($_POST['addImageTooltip']) ? $_POST['addImageTooltip'] : 'Add your own Image';
$add_text_tooltip = isset($_POST['addTextTooltip']) ? $_POST['addTextTooltip'] : 'Add your own Text';
$zoom_in_tooltip = isset($_POST['zoomInTooltip']) ? $_POST['zoomInTooltip'] : 'Zoom In';
$zoom_out_tooltip = isset($_POST['zoomOutTooltip']) ? $_POST['zoomOutTooltip'] : 'Zoom Out';
$zoom_reset_tooltip = isset($_POST['zoomResetTooltip']) ? $_POST['zoomResetTooltip'] : 'Zoom Reset';
$download_image_tooltip = isset($_POST['downloadImageTooltip']) ? $_POST['downloadImageTooltip'] : 'Download Product Image';
$print_tooltip = isset($_POST['printTooltip']) ? $_POST['printTooltip'] : 'Print';
$pdf_tooltip = isset($_POST['pdfTooltip']) ? $_POST['pdfTooltip'] : 'Save As PDF';
$save_product_tooltip = isset($_POST['saveProductTooltip']) ? $_POST['saveProductTooltip'] : 'Save product';
$saved_products_tooltip = isset($_POST['savedProductsTooltip']) ? $_POST['savedProductsTooltip'] : 'My saved products';
$reset_tooltip = isset($_POST['resetTooltip']) ? $_POST['resetTooltip'] : 'Reset Product';
?>

<section class="fpd-product-container fpd-border-color">
	<div class="fpd-menu-bar fpd-clearfix fpd-primary-bg-color fpd-secondary-text-color">
		<!-- Menu -->
		<div class="fpd-clearfix">
			<span class="fpd-add-image fpd-tooltip" title="<?php echo $add_image_tooltip; ?>"><i class="fa fa-image"></i></span>
			<span class="fpd-add-text fpd-tooltip" title="<?php echo $add_text_tooltip; ?>"><i class="fa fa-font"></i></span>
			<form class="fpd-upload-form" style="display: none;">
				<input type="file" class="fpd-input-image" name="uploaded_file"  />
			</form>
			</div>
		<div class="fpd-clearfix">
			<span class="fpd-zoom-in fpd-tooltip" title="<?php echo $zoom_in_tooltip; ?>"><i class="fa fa-search-plus"></i></span>
			<span class="fpd-zoom-out fpd-tooltip" title="<?php echo $zoom_out_tooltip; ?>"><i class="fa fa-search-minus"></i></span>
			<span class="fpd-zoom-reset fpd-tooltip" title="<?php echo $zoom_reset_tooltip; ?>"><i class="fa fa-search"></i></span>
			<span class="fpd-download-image fpd-tooltip" title="<?php echo $download_image_tooltip; ?>"><i class="fa fa-cloud-download"></i></span>
			<span class="fpd-save-pdf fpd-tooltip" title="<?php echo $pdf_tooltip; ?>"><i class="fa fa-file-o"></i></span>
			<span class="fpd-print fpd-tooltip" title="<?php echo $print_tooltip; ?>"><i class="fa fa-print"></i></span>
			<span class="fpd-save-product fpd-tooltip" title="<?php echo $save_product_tooltip; ?>"><i class="fa fa-floppy-o"></i></span>
			<div class="fpd-saved-products fpd-border-color fpd-tooltip" title="<?php echo $saved_products_tooltip; ?>">
				<i class="fa fa-th-list"></i>
				<div class="menu"></div>
			</div>
			<span class="fpd-reset-product fpd-tooltip" title="<?php echo $reset_tooltip; ?>"><i class="fa fa-refresh"></i></span>
			<a href="" download="" target="_blank" class="fpd-download-anchor" style="display: none;"></a>
		</div>
	</div>
	<!-- Kinetic Stage -->
	<div class="fpd-product-stage fpd-border-color">
		<canvas></canvas>
		<div class="fpd-element-tooltip fpd-border-color fpd-secondary-bg-color fpd-primary-text-color"></div>
	</div>
</section>