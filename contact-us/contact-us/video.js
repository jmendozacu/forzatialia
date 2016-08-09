var $pvideo = jQuery.noConflict();
$pvideo(document).ready(function() {
	$pvideo(".pvideo").click(function() {
		$pvideo.fancybox({
			'padding'		: 0,
			'autoScale'		: false,
			'transitionIn'	: 'none',
			'transitionOut'	: 'none',
			'title'			: this.title,
			'width'			: videoProductWidth,
			'height'		: videoProductHeight,
			'href'			: this.href.replace(new RegExp("watch\\?v=", "i"), 'v/'),
			'type'			: 'swf',
			'swf'			: {
			'wmode'				: 'transparent',
			'allowfullscreen'	: 'true'
			}
		});
		return false;
	});
});
