jQuery(document).ready(function($) {

	var mediaUploader = null;
	/*----- SETTINGS ----------*/

	if($('[name="fpd_frontend_primary"]').size() > 0) {
		$('.fpd-color-picker').wpColorPicker();
	}

	var $designsParamsBBControl = $('#fpd_designs_parameter_bounding_box_control');
	toggleBetweenElements(
		$designsParamsBBControl,
		$designsParamsBBControl.parents('tbody').find('.fpd-bounding-box-target-input').parents('tr'),
		$designsParamsBBControl.parents('tbody').find('.fpd-bounding-box-custom-input').parents('tr')
	);

	var $textParamsBBControl = $('#fpd_custom_texts_parameter_bounding_box_control');
	toggleBetweenElements(
		$textParamsBBControl,
		$textParamsBBControl.parents('tbody').find('.fpd-bounding-box-target-input').parents('tr'),
		$textParamsBBControl.parents('tbody').find('.fpd-bounding-box-custom-input').parents('tr')
	);

	function toggleBetweenElements($switcher, $groupOne, $groupTwo) {
		$switcher.change(function() {
			if($switcher.is(':checked')) {
				$groupOne.show();
				if($groupTwo) {
					$groupTwo.hide();
				}
			}
			else {
				$groupOne.hide();
				if($groupTwo) {
					$groupTwo.show();
				}
			}
		}).change();

	}

	//general settings: layout changed
	$('[name="fpd_layout"]').change(function() {

		$('[name="fpd_sidebar_nav_size"], [name="fpd_sidebar_content_width"]').parents('tr').show();
		$('.fpd_frontend_css_colors').show();

		if(this.value == 'semantic') {
			$('[name="fpd_sidebar_nav_size"], [name="fpd_sidebar_content_width"]').parents('tr').hide();
			$('.fpd_frontend_css_colors').hide();
		}
		else if(this.value == 'icon-sb-top' || this.value == 'icon-sb-bottom') {
			$('[name="fpd_sidebar_content_width"]').parents('tr').hide();
		}

	}).change();

	//general settings: Position
	$('[name="fpd_placement"]').change(function() {

		if(this.value == 'fpd-custom-hook') {
			$('[name="fpd_template_product_image"]').parents('tr').hide();
		}
		else {
			$('[name="fpd_template_product_image"]').parents('tr').show();
		}

	}).change();

	//general settings: image uploader
	$('input[name="fpd_type_of_uploader"]').change(function() {

		var $radios = $('input[name="fpd_type_of_uploader"]:checked'),
			$phpUploaderElements = $('[name="fpd_max_image_size"],[name="fpd_upload_designs_php_logged_in"]').parents('tr');

		if($radios.val() == 'filereader') {
			$phpUploaderElements.hide();
		}
		else {
			$phpUploaderElements.show();
		}

	}).change();


	//Modal Wrapper
	$('body').on('click', '.fpd-close-modal', function(evt) {
		closeModal($(this).parents('.fpd-modal-wrapper'));

		evt.preventDefault();
	});

	//enable/disable form
	$('.fpd-modal-wrapper').on('change', 'input[name="enabled"]', function() {

		var $this = $(this),
			$allInputs = $this.parent().parent().children('table').find('input,select');

		if($this.is(':checked')) {
			$this.val(1);
			$allInputs.attr('disabled', false);
		}
		else {
			$this.val(0);
			$allInputs.attr('disabled', true);
		}

	});

	//bounding box control
	$('[name="bounding_box_control"]').change(function() {

		var $this = $(this),
			$tbody = $this.parents('tbody');

		$tbody.find('.custom-bb, .target-bb').hide().addClass('no-serialization');
		if(this.value != '') {
			$tbody.find('.'+$this.find(":selected").data('class')).show().removeClass('no-serialization');
		}

	});

	//set the thumbnail for a design in the modal parameters form
	$('#fpd-modal-parameters').on('click', '.fpd-set-design-thumbnail', function(evt) {

		if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        mediaUploader = wp.media({
            title: 'Choose Thumbnail',
            button: {
                text: 'Set Thumbnail'
            },
            multiple: true
        });

		mediaUploader.on('select', function() {

			var imgUrl = mediaUploader.state().get('selection').toJSON()[0].url
			$('.fpd-design-thumbnail').attr('src', imgUrl).show();

        });

        mediaUploader.open();

        evt.preventDefault();

	});

	$('#fpd-modal-parameters').on('click', '.fpd-remove-design-thumbnail', function(evt) {

		evt.preventDefault();
		$('.fpd-design-thumbnail').attr('src', '').hide();

	});

});

//update the form fields
var fpdSetFormParams = function(paramsInput, thumbnailInput) {

	var $modalWrapper = jQuery('#fpd-modal-parameters'),
		$designThumbnail = jQuery('.fpd-design-thumbnail'); //thumbnail img-element

	if(thumbnailInput) {
		jQuery('.fpd-set-design-thumbnail-wrapper').show();
		$designThumbnail.attr('src', thumbnailInput.val());
		thumbnailInput.val().length > 0 ? $designThumbnail.show() : $designThumbnail.hide();

	}
	else {
		jQuery('.fpd-set-design-thumbnail-wrapper').hide();
	}

	var parameter_str = paramsInput.val().length > 0 ? paramsInput.val() : 'enabled=0&x=0&y=0&z=-1&scale=1&colors=%23000000&price=0&replace=&bounding_box_control=0&boundingBoxClipping=0';

	jQuery.each(parameter_str.split('&'), function (index, elem) {
		var vals = elem.split('='),
			$targetElement = $modalWrapper.find("form [name='" + vals[0] + "']");

		if($targetElement.is(':checkbox')) {
			$targetElement.prop('checked', vals[1] == 1);
		}
		else {
			$targetElement.val(unescape(vals[1]));
		}

	});

	$modalWrapper.find('input[name="enabled"],[name="bounding_box_control"]').change();

	openModal($modalWrapper);

};

var openModal = function( $modalWrapper ) {

	jQuery('body').addClass('modal-open');
	$modalWrapper.stop().fadeIn(300);

};

var closeModal = function( $modalWrapper ) {

	$modalWrapper.stop().fadeOut(200);
	jQuery('body').removeClass('modal-open');
	$modalWrapper.find('.fpd-select2').select2("close");

};

var fpdMessage = function(text, type) {

	jQuery('.fpd-message-box').remove();

	var $messageBox = jQuery('body').append('<div class="fpd-message-box fpd-'+type+'"><p>'+text+'</p></div>').children('.fpd-message-box').hide();
	$messageBox.css('margin-left', -$messageBox.width() * 0.5).fadeIn(300);

	$messageBox.delay(6000).fadeOut(200, function() {
		jQuery(this).remove();
	});

};