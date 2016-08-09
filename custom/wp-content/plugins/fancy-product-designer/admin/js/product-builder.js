jQuery(document).ready(function($) {

	var mediaUploader = null,
		$currentListItem = null,
		changesAreSaved = true,
		boundingBoxRect = null,
		$onlyForTextElements = $('.only-for-text-elements').hide(),
		$elementLists = $('#fpd-elements-list'),
		$parametersForm =  $('form#fpd-elements-form'),
		updatingFormFields = false;

	var defaultObjectParams = {
		originX: fpd_product_builder_opts.originX,
		originY: fpd_product_builder_opts.originY,
		padding: parseInt(fpd_product_builder_opts.paddingControl),
		lockUniScaling: true,
		fontFamily: fpd_product_builder_opts.defaultFont,
		fontSize: 18,
		lineHeight: 1
	};

	$(".help_tip, .fpd-help").tipTip({attribute:"data-tip",fadeIn:50,fadeOut:50,delay:200});

	//make elements list sortable
	$elementLists.sortable({
		placeholder: 'ui-state-highlight',
		helper : 'clone',
		update: function(evt, ui) {

			//when item index changes, change also the z-index for the element in stage
			var newIndex = $elementLists.children('li').index(ui.item),
				element = _getElementById(ui.item.attr('id'));

			element.moveTo(newIndex);
			stage.renderAll();

			changesAreSaved = false;

		}
	});



	//enable spinner for text inputs
	var spinnerOpts = {min: 0, spin: _triggerChangeForm};
	$parametersForm.find('input[name="x"], input[name="y"], input[name="angle"], input[name="maxLength"]').spinner(spinnerOpts);
	$parametersForm.find('input[name="scale"],input[name="price"]').spinner($.extend({step: 0.01}, spinnerOpts));
	$parametersForm.find('input[name="opacity"]').spinner($.extend({max: 1, step: 0.01}, spinnerOpts));
	$('#boundig-box-params input').spinner(spinnerOpts);

	$(".tm-input").tagsManager({
		validator: function(str) {
			return /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(str);
		}
	});

	function _triggerChangeForm() {
		$(this).change();
	}

	//dropdown handler for choicing a view
	$('#fpd-view-switcher').change(function() {
		var $this = $(this);

		$('#fpd-submit').attr('action', fpd_product_builder_opts.adminUrl+"edit.php?post_type=product&page=fancy_products&view_id="+$this.val()+"").submit();

	}).chosen({width: '400px'});


	//add new element buttons handler
	$('#fpd-add-image-element, #fpd-add-text-element, #fpd-add-curved-text-element, #fpd-add-upload-zone').click(function(evt) {
		evt.preventDefault();

		var $this = $(this);

		//add image or upload zone
		if(this.id == 'fpd-add-image-element' || this.id == 'fpd-add-upload-zone') {

			//enter title
			var elementTitle = prompt(fpd_product_builder_opts.enterTitlePrompt+':', ""),
				addUploadZone = this.id == 'fpd-add-upload-zone';

			if(elementTitle == null) {
				return false;
			}
			else if(elementTitle.length == 0) {
				fpdMessage(fpd_product_builder_opts.enterTitlePrompt+'!', 'error');
				return false;
			}

	        mediaUploader = wp.media({
	            title: fpd_product_builder_opts.chooseElementImageTitle,
	            button: {
	                text: fpd_product_builder_opts.set
	            },
	            multiple: false
	        });

			mediaUploader.elementTitle = elementTitle;
			mediaUploader.on('select', function() {

				var imageParams = {index: stage.getObjects().length-1};

				if(addUploadZone) {

					imageParams.uploadZone = 1;

				}
				else {

				}

				_addElement(
					mediaUploader.elementTitle,
					mediaUploader.state().get('selection').toJSON()[0].url,
					'image',
					imageParams
				);

				mediaUploader = null;
	        });

	        mediaUploader.open();

		}
		//add text
		else {

			var params = {index: stage.getObjects().length-1};
			if(this.id == 'fpd-add-curved-text-element') {
				params.curved = 1;
				params.curveSpacing = 10;
				params.curveRadius = 80;
				params.textAlign = 'center';
			}

			_addElement(
				fpd_product_builder_opts.enterYourText,
				fpd_product_builder_opts.enterYourText,
				'text',
				params
			);
		}

    });

    //when select a list item, select the corresponding element in stage
	$elementLists.on('click', '.fpd-element-identifier', function(evt) {
		stage.setActiveObject(_getElementById($(this).parents('li:first').attr('id')));
	});

	//change element text when related input text field is changed
	$elementLists.on('keyup', '[name="element_titles[]"]', function(evt) {

		var $this = $(this),
			type = $this.parent().children('[name="element_types[]"]').val(),
			element = _getElementById($this.parents('li:first').attr('id'));

		if(type == 'text') {
			element.setText($this.val());
			element.setCoords();
			stage.renderAll();
			$this.parent().children('[name="element_sources[]"]:first').val($this.val());
		}

	});

	//change image source handler
	$elementLists.on('click', '.fpd-change-image', function(evt) {
		var $this = $(this),
			$listItem = $this.parents('li:first'),
			element = _getElementById($listItem.attr('id'));

        mediaUploader = wp.media({
            title: fpd_product_builder_opts.chooseElementImageTitle,
            button: {
                text: fpd_product_builder_opts.set
            },
            multiple: false
        });

		mediaUploader.on('select', function() {

			fabric.util.loadImage(mediaUploader.state().get('selection').toJSON()[0].url, function(img) {

				$listItem.find('.fpd-element-identifier img').attr('src', img.src);
				$listItem.find('[name="element_sources[]"]').val(img.src);
				element.setElement(img);
				element.setCoords();
				stage.renderAll();

			});

			mediaUploader = null;

        });

        mediaUploader.open();

	});

	//element lock handler
	$elementLists.on('click', '.fpd-lock-element', function(evt) {

		var $this = $(this),
			$lockInput = $('[name="locked"]'),
			element = _getElementById($this.parents('li:first').attr('id'));

		$this.parent().prevAll('.fpd-element-identifier:first').click();

		//lock
		if($this.hasClass('fa-unlock')) {
			$this.removeClass('fa-unlock').addClass('fa-lock');
			$lockInput.prop('checked', true).change();
			element.set('evented', false);
			stage.discardActiveObject();
		}
		//unlock
		else {
			$this.removeClass('fa-lock').addClass('fa-unlock');
			$lockInput.prop('checked', false).change();
			element.set('evented', true);
		}

		_updateFormState();
	});

	//remove element
	$elementLists.on('click', '.fpd-trash-element', function() {

		var c = confirm(fpd_product_builder_opts.removeElement);
		if(!c) {
			return false;
		}

		_removeElement($(this).parents('li').attr('id'));

	});

	//let the page know that elements are now saved
	$('input[name="save_elements"]').click(function() {
		stage.discardActiveObject();
		changesAreSaved = true;
	});


	//dropdown handler for choicing a font
	$parametersForm.find('select').chosen().change(function() {

		var activeObj = stage.getActiveObject();
		if(activeObj && (activeObj.type == 'i-text' || activeObj.type == 'curvedText') ) {
			activeObj.setFontFamily(this.value);
			activeObj.setCoords();
			stage.renderAll().calcOffset();
		}

	});

    //only allow numeric values for text inputs with .fpd-only-numbers
    $parametersForm.on('keypress', 'input.fpd-only-numbers', function(evt) {

		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if($(this).hasClass('fpd-allow-dots')) {

			if (charCode > 31 && (charCode < 48 || charCode > 57) && (charCode != 46)) {
			    return false;
		    }
		    else {
			    return true;
		    }
		}
		else {
			if (charCode > 31 && (charCode < 48 || charCode > 57)) {
			    return false;
		    }
		    else {
			    return true;
			}
		}

    });

	$('.fpd-allow-dots').keyup(function(){

        if($(this).val().indexOf('.')!=-1){
            if($(this).val().split(".")[1].length > 2){
                if( isNaN( parseFloat( this.value ) ) ) return;
                this.value = parseFloat(this.value).toFixed(2);
            }
         }
         return this;

    });

	//check that number inputs has a leading 0 if dots are allowed and first char is a dot
    $('.fpd-allow-dots').change(function(){

        if(this.value.charAt(0) == '.') {
	        this.value = '0'+this.value;
        }

    });


	//form change handler
	$parametersForm.on('change', function(evt) {

		if(updatingFormFields === false) {

			if($('input[name="bounding_box_control"]').is(':checked')) {
				//get bounding box from other element
				_updateBoundingBox($('input[name="bounding_box_by_other"]').val());
			}
			else {
				_updateBoundingBox({
					x: $('input[name="bounding_box_x"]').val(),
					y: $('input[name="bounding_box_y"]').val(),
					width: $('input[name="bounding_box_width"]').val(),
					height: $('input[name="bounding_box_height"]').val()
				});
			}

			_setParameters();

		}

	})
	.on('keypress', function(evt) {
		if (evt.keyCode == 13) {
			$(evt.target).change();
			return false;
		}
	})
	.on('change', '.fpd-only-numbers', function() {

		if($('input[name="opacity"]').val() > 1 || $('input[name="opacity"]').val() == '') {
			$('input[name="opacity"]').val(1);
		}

		_updateFabricElement(this.name, this.value);

	});

	$('input[name="bounding_box_control"]').change(function() {

		boundingBoxRect.visible = false;
		stage.renderAll();
		if($(this).is(':checked')) {
			$('#boundig-box-params').hide();
			$('input[name="bounding_box_by_other"]').show().val('');
		}
		else {
			$('#boundig-box-params').show().children('input').val('');
			$('input[name="bounding_box_by_other"]').hide();
		}

	});

	//text styling
	$('.fpd-text-styling').find('button').click(function(evt) {

		evt.preventDefault();

		var $this = $(this),
			currentElement = stage.getActiveObject();

		if(!currentElement) { return false; }

		$this.hasClass('active') ? $this.removeClass('active') : $this.addClass('active');

		var styleType = $this.hasClass('fpd-bold') ? 'fontWeight' : 'fontStyle';
			styleValue = styleType == 'fontWeight' ? 'bold' : 'italic';

		$('[name="'+styleType+'"]').prop('checked', $this.hasClass('active')).change();
		currentElement.set(styleType, $this.hasClass('active') ? styleValue : 'normal').setCoords();
		stage.renderAll();

	});

	//text alignment
	$('.fpd-text-align').find('button').click(function(evt) {

		evt.preventDefault();

		var $this = $(this),
			currentElement = stage.getActiveObject();

		if(!currentElement) { return false; }

		$this.siblings('.button').removeClass('active');
		$this.addClass('active');

		$('[name="textAlign"]').val($this.data('value')).change();
		currentElement.set('textAlign', $this.data('value')).setCoords();
		stage.renderAll();

	});

	//center fabric element
	$('.fpd-center-horizontal, .fpd-center-vertical').click(function() {
		var currentElement = stage.getActiveObject();
		if(currentElement) {
			if($(this).hasClass('fpd-center-horizontal')) {
				currentElement.centerH();
			}
			else {
				currentElement.centerV();
			}
			_setFormFields(currentElement);
		}
	});


	$('.tm-input').on('tm:pushed', function(evt, tag, tagId) {

		$('.tm-tag:last').css('background-color', tag);

	});

	$('[name="currentColor"]').change(function(evt) {

		if(!updatingFormFields)  {

			var hex = this.value;

			if(!/(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(hex) && hex.length > 0) {
				fpdMessage('Not a valid hexadecimal color!', 'error');
				return false;
			}

			if(hex.length > 0) {
				_changeColor(stage.getActiveObject(), hex);
			}
			else {
				_changeColor(stage.getActiveObject(), false);
			}


		}

	});

	var $colorTags = $('[name="colors"]'),
		$colorControl = $colorTags.after('<input type="text" name="color_control_title" class="widefat" />').next('input:first').hide();
	$('[name="color_control"]').change(function() {

		if($(this).is(':checked')) { //color control enabled
			$('#fpd-color-tags-desc').hide();
			$colorTags.hide();
			$colorControl.show();
			$(".tm-input").tagsManager('empty');
		}
		else {
			$('#fpd-color-tags-desc').show();
			$colorControl.hide();
			$colorTags.show();
			$colorControl.val('');
		}

	});


	//curved text options
	$('[name="curveSpacing"],[name="curveRadius"],[name="curveReverse"]').change(function() {

		var currentElement = stage.getActiveObject();

		if(this.name == 'curveSpacing') {
			var value = this.value.length == 0 ? 10 : this.value;
			currentElement.set('spacing', value);
		}
		else if(this.name == 'curveRadius') {
			var value = this.value.length == 0 ? 80 : this.value;
			currentElement.set('radius', value);
		}
		else {
			currentElement.set('reverse', $(this).is(':checked'));
		}

	});


	//create fabricjs stage
	var stage = new fabric.Canvas('fpd-fabric-stage', {
		selection: false,
		hoverCursor: 'pointer'
	});

	//create a bounding box rectangle
	boundingBoxRect = new fabric.Rect({
		stroke: 'blue',
		strokeWidth: 1,
		fill: false,
		selectable: false,
		originX: fpd_product_builder_opts.originX,
		originY: fpd_product_builder_opts.originX,
		visible: false,
		evented: false,
		selectable: false,
		transparentCorners: false,
		cornerSize: 20,
		originX: 'left',
		originY: 'top'
	});
	stage.add(boundingBoxRect);

	//fabricjs stage handlers
	stage.on({
		'mouse:down': function(opts) {
			if(opts.target == undefined) {
				_updateFormState();
			}
		},
		'object:moving': function(opts) {
			_setFormFields(opts.target);
		},
		'object:scaling': function(opts) {
			_setFormFields(opts.target);
		},
		'object:rotating': function(opts) {
			_setFormFields(opts.target);
		},
		'object:selected': function(opts) {
			_updateFormState();
			_setFormFields(opts.target.setCoords());
		},
		'text:changed': function(opts) {
			$elementLists.children('li#'+opts.target.id+'').children('[name="element_titles[]"]').val(opts.target.text);
			$elementLists.children('li#'+opts.target.id+'').children('[name="element_sources[]"]').val(opts.target.text);
		}
	});

	//add a new element to stage
	function _addElement(title, source, type, params) {

		$.extend(params, defaultObjectParams);

		//new element
		if(params.left == undefined) {
			changesAreSaved = false;
			params.id = String(new Date().getTime());

			var elementIdentifier,
				paramsStr;
			if(type == 'image') {
				elementIdentifier = '<img src="'+source+'" />';
			}
			else {
				elementIdentifier = '<i class="fa fa-font"></i>';
			}

			var changeImageIcon = type == 'image' ? '<span class="fa fa-refresh fpd-change-image" title=""></span>' : '';
			$elementLists.append('<li id="'+params.id+'"><input type="text" name="element_titles[]" value="'+title+'" />'+changeImageIcon+'<div class="fpd-element-identifier">'+elementIdentifier+'</div><div class="fpd-clearfix"><span class="fa fa-unlock fpd-lock-element"></span><span class="fa fa-times fpd-trash-element"></span></div><textarea name="element_sources[]">'+source+'</textarea><input type="hidden" name="element_types[]" value="'+type+'"/><input type="hidden" name="element_parameters[]" value="'+$.param(params)+'"/></li>');
		}

		if(type == 'image') {
			var imageParts = source.split('.');
			if($.inArray('svg', imageParts) != -1) {
				fabric.loadSVGFromURL(source, function(objects, options) {
					var svgGroup = fabric.util.groupSVGElements(objects, options);
					svgGroup.set(params);
					_addElementToStage(svgGroup, params);
				});
			}
			else {
				new fabric.Image.fromURL(source, function(fabricImg) {
					_addElementToStage(fabricImg, params);
				}, params);
			}

		}
		else {
			//replace underscore with space again
			if(params.font) params.fontFamily = params.font;

			var fabricText,
				text = source.replace(/\\n/g, '\n');

			if(params.curved == 1) {
				params.spacing = params.curveSpacing ? parseInt(params.curveSpacing) : 10;
				params.radius = params.curveRadius ? parseInt(params.curveRadius) : 80;
				params.reverse = params.curveReverse ? Boolean(params.curveReverse) : false;
				fabricText = new fabric.CurvedText(text, params);
			}
			else {
				fabricText = new fabric.IText(text, params);
			}

			_addElementToStage(fabricText, params);
		}

	}

	//set element params and create list item for it
	function _addElementToStage(element, params) {

		stage.add(element);

		if(params.left == undefined) {
			//new element is added
			element.center();
			stage.setActiveObject(element);
		}

		if(params.currentColor) {
			_changeColor(element, unescape(params.currentColor));
		}

		element.moveTo(params.index).setCoords();
		stage.renderAll().calcOffset();

	}

	//enable editing of the form when an element is selected in stage
	function _updateFormState() {

		updatingFormFields = true;

		$('.tm-input').tagsManager('empty');

		$parametersForm.find('button').removeClass('active').addClass('disabled');

		if(stage.getActiveObject() && stage.getActiveObject().selectable) {
			$parametersForm.find('input').attr("disabled", false);
			$elementLists.children('li').removeClass('fpd-active-item');
			$currentListItem = $elementLists.children('#'+stage.getActiveObject().id).addClass('fpd-active-item');
			$('#fpd-edit-parameters-for').text($currentListItem.children('input[name="element_titles[]"]').val());
			if(stage.getActiveObject().type == 'i-text' || stage.getActiveObject().type == 'curvedText') {
				$parametersForm.find('.fpd-font-changer').attr("disabled", false);
				$parametersForm.find('input[name="patternable"]').attr("disabled", false);
				$parametersForm.find('input[name="editable"]').attr("disabled", false);
				$parametersForm.find('input[name="curvable"]').attr("disabled", false);
				$parametersForm.find('button').removeClass('disabled');
				$onlyForTextElements.show();
			}
			else {
				$parametersForm.find('.fpd-font-changer').attr("disabled", true);
				$parametersForm.find('input[name="patternable"]').attr("disabled", true);
				$parametersForm.find('input[name="editable"]').attr("disabled", true);
				$parametersForm.find('input[name="curvable"]').attr("disabled", true);
				$onlyForTextElements.hide();
			}

			$parametersForm.find('.fpd-font-changer').trigger('chosen:updated');
		}
		else {
			$parametersForm.find('input, select').attr("disabled", true).trigger('chosen:updated');
			$elementLists.children('li').removeClass('fpd-active-item');
			boundingBoxRect.visible = false;
			$currentListItem = null;
		}

	}

	//update form fields when element is changed via product stage
	function _setFormFields(element) {

		$('input[name="x"]').val(Math.round(element.left) || 0);
		$('input[name="y"]').val(Math.round(element.top) || 0);
		$('input[name="scale"]').val(Number(element.scaleX).toFixed(2));
		$('input[name="angle"]').val(Math.round(element.angle) % 360);

		var paramsFromInput = $currentListItem.children('input[name="element_parameters[]"]').val(),
			splitParams = paramsFromInput.split("&");

		//convert parameter string into object
		var paramsObject = {};
		for(var i=0; i < splitParams.length; ++i) {
			var splitIndex = splitParams[i].indexOf("=");
			paramsObject[splitParams[i].substr(0, splitIndex)] = splitParams[i].substr(splitIndex+1)  ;
		}

		$('input[name="locked"]').prop('checked', paramsObject.locked == '1');
		$('input[name="uploadZone"]').prop('checked', paramsObject.uploadZone == '1');
		$('input[name="price"]').val(paramsObject.price);
		$('input[name="opacity"]').val(paramsObject.opacity);
		$('input[name="removable"]').prop('checked', paramsObject.removable == '1');
		$('input[name="draggable"]').prop('checked', paramsObject.draggable == '1');
		$('input[name="rotatable"]').prop('checked', paramsObject.rotatable == '1');
		$('input[name="resizable"]').prop('checked', paramsObject.resizable == '1');
		$('input[name="zChangeable"]').prop('checked', paramsObject.zChangeable == '1');
		$('input[name="topped"]').prop('checked', paramsObject.topped == '1');
		$('input[name="autoSelect"]').prop('checked', paramsObject.autoSelect == '1');
		$('input[name="patternable"]').prop('checked', paramsObject.patternable == '1');
		$('input[name="editable"]').prop('checked', paramsObject.editable == '1');
		$('input[name="curvable"]').prop('checked', paramsObject.curvable == '1');
		$('input[name="curved"]').prop('checked', paramsObject.curved == '1');


		if(element.type == 'image') {
			$('[name="color_control"]').parent('label').show();
		}
		else {
			$('[name="color_control"]').parent('label').hide();
		}

		paramsObject.currentColor ? $('[name="currentColor"]').val(unescape(paramsObject.currentColor)) : $('[name="currentColor"]').val('');

		if(paramsObject.colors && paramsObject.colors.length > 0) {

			if(unescape(paramsObject.colors).charAt(0) == '#') {

				$('[name="color_control"]').prop('checked', false).change();

				var colorArray = unescape(paramsObject.colors).split(',');
				for(var i=0; i < colorArray.length; ++i) {
					$('.tm-input').tagsManager('pushTag', colorArray[i]);
				}
			}
			else {
				$('[name="color_control"]').prop('checked', true).change();
				$colorControl.val(paramsObject.colors.replace('_', ' '));
			}

		}
		else {
			$('[name="color_control"]').prop('checked', false).change();
		}

		boundingBoxRect.visible = false;
		stage.renderAll();

		$('input[name="bounding_box_control"]').prop('checked', paramsObject.bounding_box_control == '1');
		$('input[name="boundingBoxClipping"]').prop('checked', paramsObject.boundingBoxClipping == '1');
		if(paramsObject.bounding_box_control == '1') {
			$('#boundig-box-params').hide();
			paramsObject.bounding_box_by_other = paramsObject.bounding_box_by_other.replace(/\_/g, ' ');
			$('input[name="bounding_box_by_other"]').show().val(paramsObject.bounding_box_by_other);
			if(paramsObject.bounding_box_by_other) {
				_updateBoundingBox(paramsObject.bounding_box_by_other);
			}
		}
		else {
			$('#boundig-box-params').show();
			$('input[name="bounding_box_by_other"]').hide();
			$('input[name="bounding_box_x"]').val(paramsObject.bounding_box_x);
			$('input[name="bounding_box_y"]').val(paramsObject.bounding_box_y);
			$('input[name="bounding_box_width"]').val(paramsObject.bounding_box_width);
			$('input[name="bounding_box_height"]').val(paramsObject.bounding_box_height);
			_updateBoundingBox({x: paramsObject.bounding_box_x, y: paramsObject.bounding_box_y, width: paramsObject.bounding_box_width, height: paramsObject.bounding_box_height});
		}

		$('input[name="replace"]').val(paramsObject.replace ? paramsObject.replace.replace(/\_/g, ' ') : '');

		if(element.type == 'i-text' || element.type == 'curvedText') {
			$('select[name="font"]').val(element.fontFamily).trigger('chosen:updated');
			$('input[name="fontWeight"]').prop('checked', paramsObject.fontWeight == 'bold');
			$('.fpd-bold').addClass(paramsObject.fontWeight == 'bold' ? 'active' : '');
			$('input[name="fontStyle"]').prop('checked', paramsObject.fontStyle == 'italic');
			$('.fpd-italic').addClass(paramsObject.fontStyle == 'italic' ? 'active' : '');
			if(paramsObject.textAlign == undefined) { paramsObject.textAlign = 'left'; }
			$('input[name="textAlign"]').val(paramsObject.textAlign);
			$('.fpd-align-'+paramsObject.textAlign+'').addClass('active');
			$('input[name="maxLength"]').val(paramsObject.maxLength);
		}

		//show only allowed parameters for upload zones
		if(paramsObject.uploadZone == '1') {
			$parametersForm.find('input').parent('label').hide();
			$parametersForm.find('input').parents('tr').hide();
			var $uploadZoneParams = $parametersForm.find('[name="x"],[name="y"],[name="scale"],[name="price"],[name="draggable"],[name="resizable"],[name="rotatable"],[name="boundingBoxClipping"]');
			$uploadZoneParams.parent('label').show();
			$uploadZoneParams.parents('tr').show();
		}
		else {
			$parametersForm.find('input').parent('label').show();
			$parametersForm.find('input').parents('tr').show();
		}

		//hide color options if element is a svg or jpeg
		if(element.type == 'path-group' || (element.getSrc != undefined && $.inArray('jpg', element.getSrc().split('.')) != -1)) {
			//svg or jpeg
			$('.fpd-color-options').hide();
		}
		else {
			$('.fpd-color-options').show();
		}

		if(element.type == 'curvedText') {

			$('[name="curveSpacing"]').val(paramsObject.curveSpacing);
			$('[name="curveRadius"]').val(paramsObject.curveRadius);
			$('[name="curveReverse"]').prop('checked', paramsObject.curveReverse == '1');

			$('#fpd-curved-text-opts').show()
			.children('[name="curved"]').prop('checked', true);
		}
		else {
			$('#fpd-curved-text-opts').hide()
			.children('[name="curved"]').prop('checked', false);
		}


		element.setCoords();

		updatingFormFields = false;

		_setParameters();
	}

	//update element in product stage when form fields are changed
	function _updateFabricElement(name, value) {

		if(stage.getActiveObject()) {

			var currentElement = stage.getActiveObject();

			if(name == 'x' || name == 'y' || name == 'angle' || name == 'opacity') {

				if(name == 'x') {
					name = 'left'
				}
				else if(name == 'y') {
					name = 'top';
				}

				currentElement.set(name, value);

			}
			else if(name == 'scale') {
				currentElement.set({scaleX: value, scaleY: value});
			}

			currentElement.setCoords();
			stage.renderAll();
		}

	}

	function _updateBoundingBox(target) {
		//set by another element
		if(typeof target == 'string') {
			var targetElement = _getElementByTitle(target);

			if(targetElement) {
				var boundingRect = targetElement.getBoundingRect();
				boundingBoxRect.left = boundingRect.left;
				boundingBoxRect.top = boundingRect.top;
				boundingBoxRect.width = boundingRect.width;
				boundingBoxRect.height = boundingRect.height;
				boundingBoxRect.visible = true;
			}
			else {
				boundingBoxRect.visible = false;
			}
		}
		//set by custom parameters
		else {
			boundingBoxRect.left = parseInt(target.x);
			boundingBoxRect.top = parseInt(target.y);
			boundingBoxRect.width = parseInt(target.width);
			boundingBoxRect.height = parseInt(target.height);
			boundingBoxRect.visible = true;
		}
		boundingBoxRect.setCoords();
		stage.renderAll();
	}

	//set parameters from form to the current list item
	function _setParameters() {

		var serializedForm = $parametersForm.serialize().replace(/\+/g, '_'); //replace whitespace with underscore

		if($('.tm-input').is(':visible')) {
			serializedForm = serializedForm.replace('hidden-colors', 'colors'); //replace hidden-colors with colors - when color tags are visible
		}
		else {
			serializedForm = serializedForm.replace('color_control_title', 'colors'); //color control is visible
		}

		serializedForm = serializedForm.replace(/[^&]+=&/g, '').replace(/&[^&]+=$/g, '');//remove all empty parameters

		$currentListItem.children('input[name="element_parameters[]"]').val(serializedForm);

		changesAreSaved = false;
	}

	function _getElementById(id) {
		var objects = stage.getObjects();
		for(var i=0; i < objects.length; ++i) {
			if(objects[i].id == id) {
				return objects[i];
				break;
			}
		}
	}

	function _getElementByTitle(title) {
		var objects = stage.getObjects();
		for(var i=0; i < objects.length; ++i) {
			if(objects[i].title == title) {
				return objects[i];
				break;
			}
		}
		return false;
	}

	function _removeElement(id) {

		stage.discardActiveObject();
		boundingBoxRect.visible = false;

		$elementLists.children('#'+id).remove();
		var element = _getElementById(id);
		stage.remove(element).renderAll();

		_updateFormState();

		changesAreSaved = false;

	}

	function _parameterStringToObject(paramStr) {

		var splitParams = paramStr.split("&");

		//convert parameter string into object
		var paramsObject = {};
		for(var i=0; i < splitParams.length; ++i) {
			var splitIndex = splitParams[i].indexOf("=");
			paramsObject[splitParams[i].substr(0, splitIndex)] = splitParams[i].substr(splitIndex+1).replace(/\_/g, ' ');
		}
		return paramsObject;

	};

	function _changeColor(element, hex) {

		if(element.type == 'i-text' || element.type == 'curvedText') {

			if(hex) {
				element.setFill(hex);
			}
			else {
				element.setFill('#000');
			}

		}
		else {
			if(hex) {
				element.filters.push(new fabric.Image.filters.Tint({color: hex}));

			}
			else {
				for(var i=0; i < element.filters.length; ++i) {
					if(element.filters[i].type == 'Tint') {
						element.filters.splice(i, 1);
					}
				}
			}

			element.applyFilters(stage.renderAll.bind(stage));

		}

		stage.renderAll();

	};


	$elementLists.children('li').each(function(index, item) {
		var $item = $(item),
			title = $item.find('input[name="element_titles[]"]').val(),
			source = $item.find('textarea[name="element_sources[]"]').val(),
			type = $item.find('input[name="element_types[]"]').val(),
			parameters = $item.find('input[name="element_parameters[]"]').val();

		var params = _parameterStringToObject(parameters);
		params.left = parseInt(params.x);
		params.top = parseInt(params.y);
		params.angle = params.angle ? parseInt(params.angle) : 0;
		params.scaleX = params.scaleY = params.scale ? Number(params.scale) : 1;
		params.title = title;
		params.index = index;
		params.id = $item.attr('id');
		params.evented = params.locked ? false : true;
		if(params.text != undefined) {
			params.text = unescape(params.text).replace(/\+/g, ' ');
			source = params.text;
		}

		_addElement(type == 'image' ? title : source, source, type, params);
	});

	_updateFormState();
	stage.renderAll();

	//check if changes are saved before page unload
	/*$(window).on('beforeunload', function () {
		if(!changesAreSaved) {
			return fpd_product_builder_opts.notChanged;
		}
	});*/

});


fabric.IText.prototype.initHiddenTextarea = function() {

	this.hiddenTextarea = fabric.document.createElement('textarea');

    this.hiddenTextarea.setAttribute('autocapitalize', 'off');
    this.hiddenTextarea.style.cssText = 'position: absolute; top: 0; left: -9999px';

    if (this.canvas && this.canvas.upperCanvasEl) {
		this.canvas.upperCanvasEl.appendChild(this.hiddenTextarea);
	} else {
		fabric.document.body.appendChild(this.hiddenTextarea);
	}

    fabric.util.addListener(this.hiddenTextarea, 'keydown', this.onKeyDown.bind(this));
    fabric.util.addListener(this.hiddenTextarea, 'keypress', this.onKeyPress.bind(this));

    if (!this._clickHandlerInitialized && this.canvas) {
      fabric.util.addListener(this.canvas.upperCanvasEl, 'click', this.onClick.bind(this));
      this._clickHandlerInitialized = true;
    }

};