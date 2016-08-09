<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( !class_exists('FPD_Admin_Fancy_Designs') ) {

	class FPD_Admin_Fancy_Designs {

		public function __construct() {

			add_action( 'delete_term',  array( &$this, 'term_delete' ), 10, 4 );

		}

		//delete category parameters if fancy design category is deleted
		public function term_delete( $term_id, $tax_id, $tax_slug, $term ) {

			delete_option( 'fpd_category_parameters_'.$term->slug );

		}

		public function output() {

			?>
			<div class="wrap" id="manage-designs-page">
				<h2>
					<?php _e('Manage Fancy Designs', 'radykal'); ?>
						<a class="add-new-h2" href="<?php echo admin_url('edit-tags.php?taxonomy=fpd_design_category&post_type=attachment'); ?>"><?php _e('Create Category', 'radykal'); ?></a>
						<?php fpd_admin_display_version_info(); ?>
				</h2>
				<?php

					//get all created categories
					$categories = get_terms( 'fpd_design_category', array(
					 	'hide_empty' => false
					));

					//check that categories are not empty
					if( empty($categories) ) {
						echo '<div class="error"><p><strong>'.__('No categories found. You need to create a category first!', 'radykal').'</strong></p></div></div>';
						return false;
					}

					//select first category id
					$selected_category = $categories[0];
					$selected_category_slug = $selected_category->slug;

					//loop through all categories
					foreach($categories as $category) {

						//check if a category is selected
						if( isset($_POST['design_category']) && $_POST['design_category'] == $category->slug) {
							$selected_category = $category;
							$selected_category_slug = $selected_category->slug;
						}

					}

					if( isset($_POST['save_designs']) ) {

						check_admin_referer( 'fpd_save_designs' );

						//remove all designs from design category
						$args = array(
							'posts_per_page' => -1,
							'post_type' => 'attachment',
							'fpd_design_category' => $selected_category_slug
						);

						//get all attachments and remove the from category
						$designs = get_posts( $args );
						foreach( $designs as $design ) {
							wp_delete_object_term_relationships($design->ID, 'fpd_design_category');
						}

					 	$order = 0;
					 	//loop through all submitted images
					 	foreach( $_POST['image_ids'] as $image_id ) {

						 	//update menu order
					 		$attachment = array(
								'ID'           => $image_id,
								'menu_order' => $order
							);
							wp_update_post( $attachment );

							//set relation between image and design category
					 		wp_set_object_terms( $image_id, $selected_category_slug, 'fpd_design_category', true );

					 		//set parameters for design
					 		update_post_meta( $image_id, 'fpd_parameters', $_POST['parameters'][$order]);
					 		update_post_meta( $image_id, 'fpd_thumbnail', $_POST['thumbnail'][$order]);

					 		$order++;

					 	}

					 	update_option( 'fpd_category_parameters_'.$selected_category_slug, $_POST['fpd_category_parameters'] );

						echo '<div class="updated"><p><strong>'.__('Designs saved.', 'radykal').'</strong></p></div>';
					}

					//get category parametes
					$category_parameters = get_option( 'fpd_category_parameters_'.$selected_category_slug );

				?>

				<br class="clear" />

				<form method="post" id="fpd-designs-form">
					<div>
						<p class="description"><?php _e('Categories', 'radykal'); ?></p>
						<select name="design_category" style="min-width: 300px;">
							<?php
								foreach($categories as $category) {

									$selected = '';
									//check if a category is selected
									if( isset($_POST['design_category']) && $_POST['design_category'] == $category->slug) {
										$selected = 'selected="selected"';
									}

									//output category option
									echo '<option value="'.$category->slug.'" '.$selected.'>'.$category->name.'</option>';

								}
							?>
						</select>
					</div>
					<br /><br />
					<p class="description"><?php _e('Designs in "', 'radykal'); echo $selected_category->name.'"'; ?></p>
					<div class="postbox fpd-design-category">
						<?php

						?>
						<input type="hidden" value="<?php if( $category_parameters ) echo $category_parameters; ?>" name="fpd_category_parameters" />
					 	<h3>
					 		<button class="button button-secondary fpd-add-designs"><?php _e('Add Designs', 'radykal'); ?></button>
					 		<button class="button button-secondary fpd-change-category-parameters"><?php _e('Change Parameters', 'radykal'); ?></button>
					 	</h3>
					 	<div class="inside">
						 	<ul class="fpd-clearfix">
						 	<?php

							 //get designs by category id
							$args = array(
								'posts_per_page' => -1,
								'post_type' => 'attachment',
								'orderby' => 'menu_order',
								'order' => 'ASC',
								'fpd_design_category' => $selected_category_slug
							);

							$designs = get_posts( $args );

							//loop through all designs
							foreach( $designs as $design ) {

								$parameters = get_post_meta($design->ID, 'fpd_parameters', true);
								$thumbnail = get_post_meta($design->ID, 'fpd_thumbnail', true);
								echo '<li><img src="'.$design->guid.'" /><a href="#" class="fa fa-gear fpd-edit-parameters"></a><a href="#" class="fa fa-times fpd-remove-design"></a><input type="hidden" value="'.$design->ID.'" name="image_ids[]" /><input type="hidden" value="'.$parameters.'" name="parameters[]" /><input type="hidden" value="'.$thumbnail.'" name="thumbnail[]" /></li>';

							}
						 	?>
						 	</ul>
					 	</div>

					 </div>
					<?php wp_nonce_field( 'fpd_save_designs'); ?>
					<input type="submit" name="save_designs"  value="<?php _e('Save Changes', 'radykal'); ?>" class="button button-primary" />
				</form>

				<!-- Parameters Modal -->
				<?php require_once( FPD_PLUGIN_ADMIN_DIR.'/views/html-fancy-designs-parameters-modal.php' ); ?>

			</div>

			<script type="text/javascript">

				jQuery(document).ready(function($) {

					var mediaUploader = null,
						$currentParametersInput = null;


					//select2 box
					$('[name="design_category"]').select2({width: 400}).change(function() {

						$('#fpd-designs-form').submit();

					});


					//make image list draggable
					$( ".fpd-design-category ul" ).sortable({
						placeholder: 'ui-state-highlight',
						connectWith: ".fpd-design-category ul",
						receive: function(evt, ui) {
							ui.item.children('input[name="category_slugs[]"]').val(ui.item.parents('.fpd-design-category:first').attr('id'));
						}
					}).disableSelection();


					//set images from media library
					$('.fpd-design-category').on('click', '.fpd-add-designs', function(evt) {
						evt.preventDefault();

						if (mediaUploader) {
				            mediaUploader.open();
				            return;
				        }

				        mediaUploader = wp.media({
				            title: '<?php _e( 'Choose Designs', 'radykal' ); ?>',
				            button: {
				                text: '<?php _e( 'Set Designs', 'radykal' ); ?>'
				            },
				            multiple: true
				        });

						mediaUploader.on('select', function() {

							mediaUploader.state().get('selection').each(function(item) {

								var attachment = item.toJSON();
								$('.fpd-design-category ul').append('<li><img src="'+attachment.url+'" /><a href="#" class="fa fa-gear fpd-edit-parameters"></a><a href="#" class="fa fa-times fpd-remove-design"></a><input type="hidden" value="'+attachment.id+'" name="image_ids[]" /><input type="hidden" value="" name="parameters[]" /><input type="hidden" value="" name="thumbnail[]" /></li>');

							});

				        });

				        mediaUploader.open();
					});


					//change parameters of design
					$('#fpd-designs-form').on('click', '.fpd-change-category-parameters, .fpd-edit-parameters', function(evt) {

						evt.preventDefault();

						var $this = $(this),
							$thumbnailInput = false;

						if($this.hasClass('fpd-change-category-parameters')) {
							$currentParametersInput = $('[name="fpd_category_parameters"]');
						}
						else {
							$currentParametersInput = $this.parent().children('input[name="parameters[]"]');
							$thumbnailInput = $this.parent().children('input[name="thumbnail[]"]');
						}

						fpdSetFormParams($currentParametersInput, $thumbnailInput);

					});

					//save and close modal
					$('#fpd-modal-parameters').on('click', '.fpd-save-modal', function(evt) {

						evt.preventDefault();

						var $modalWrapper = $(this).parents('.fpd-modal-wrapper:first');
						$currentParametersInput.parent().children('[name="thumbnail[]"]').val($('.fpd-design-thumbnail').attr('src'));

						closeModal($modalWrapper);

						$currentParametersInput.val($modalWrapper.find('form').serialize());

						$currentParametersInput = null;

					})
					.on('click', '.fpd-close-modal', function(evt) {

						evt.preventDefault();
						closeModal($(this).parents('.fpd-modal-wrapper:first'));
						$currentParametersInput = null;

					});

					//remove design
					$('.fpd-design-category').on('click', '.fpd-remove-design', function(evt) {

						evt.preventDefault();
						$(this).parent('li:first').remove();

					});

				});
			</script>
			<?php

		}
	}
}

new FPD_Admin_Fancy_Designs();

?>