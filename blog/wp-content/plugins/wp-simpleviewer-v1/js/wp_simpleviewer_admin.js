jQuery(document).ready(function() {
	//Initialize sortable table to edit galleries
	jQuery("#sortable_table").tableDnD({
		onDrop: function(table, row) {
			jQuery("input.wp_simpleviewer_order").each(function(i){
				this.value = i+1;
			});
			jQuery("#sortable_table tr.sortable_row:odd").addClass("alternate");
			jQuery("#sortable_table tr.sortable_row:even").removeClass("alternate");
		}
	});
	
	//tTips have been removed with WP 2.7, this is disabled right now. 
	// tooltipize elements with classname "wp_simpleviewer_tips"
	//jQuery('.wp_simpleviewer_tips').tTips();
	
	// Use filenames as captions checkbox actions
	jQuery('#wp_simpleviewer_download_link_use_filename').click(function(){
		if ( this.checked )
			jQuery('#wp_simpleviewer_default_download_link_text').attr("value", wp_simpleviewer_download_link_use_filename).hide();
		else if ( !this.checked )
			jQuery('#wp_simpleviewer_default_download_link_text').attr("value", wp_simpleviewer_default_download_link_text).show(); 
	});
	
	//Initialize colorpickers and hide them
	jQuery('#wp_simpleviewer_textcolor_picker').farbtastic('#wp_simpleviewer_textcolor').hide();
	jQuery('#wp_simpleviewer_framecolor_picker').farbtastic('#wp_simpleviewer_framecolor').hide();
	jQuery('#wp_simpleviewer_bgcolor_picker').farbtastic('#wp_simpleviewer_bgcolor').hide();
	//Apply slidefunctions to colorpickers
	jQuery('#wp_simpleviewer_textcolor').focus(function() {
		jQuery('#wp_simpleviewer_textcolor_picker').slideDown('normal');
	});
	jQuery('#wp_simpleviewer_textcolor').blur(function() {
		jQuery('#wp_simpleviewer_textcolor_picker').slideUp('normal');
	});
	jQuery('#wp_simpleviewer_framecolor').focus(function() {
		jQuery('#wp_simpleviewer_framecolor_picker').slideDown('normal');
	});
	jQuery('#wp_simpleviewer_framecolor').blur(function() {
		jQuery('#wp_simpleviewer_framecolor_picker').slideUp('normal');
	});
	jQuery('#wp_simpleviewer_bgcolor').focus(function() {
		jQuery('#wp_simpleviewer_bgcolor_picker').slideDown('normal');
	});
	jQuery('#wp_simpleviewer_bgcolor').blur(function() {
		jQuery('#wp_simpleviewer_bgcolor_picker').slideUp('normal');
	});
	
});
