if (!("ontouchstart" in document.documentElement)) {
	document.documentElement.className += " no-touch";
} else {
	//enable :active class for links
	document.addEventListener("touchstart", function(){}, true);
	//hide cloudzoom
	jQuery('.mousetrap, #cloud-big-zoom').css('display', 'none');
}

// Set pixelRatio to 1 if the browser doesn't offer it up.
var pixelRatio = !!window.devicePixelRatio ? window.devicePixelRatio : 1;
jQuery(window).on("load", function() {
	if (pixelRatio == 1) return;

	//product images
	jQuery('img[data-srcX2]').each(function(){
		jQuery(this).attr('src',jQuery(this).attr('data-srcX2'));
	});
	//custom block images.
	jQuery('img.retina').each(function(){
		var file_ext = jQuery(this).attr('src').split('.').pop();
		var pattern = new RegExp("^(.*)\."+file_ext+"+$");
		jQuery(this).attr('src',jQuery(this).attr('src').replace(pattern,"$1_2x."+file_ext));
	});

});

jQuery.fn.extend({
	scrollToMe: function () {
		jQuery('html,body').animate({scrollTop: (jQuery(this).offset().top - 100)}, 500);
	}
});

jQuery(function($){
	//cart dropdown
	var config = {
	     over: function(){
	     			$('.cart-top .details').animate({opacity:1, height:'toggle'}, 400);
	     		},
	     timeout: 500, // number = milliseconds delay before onMouseOut
	     out: function(){
	     			$('.cart-top .details').animate({opacity:0, height:'toggle'}, 400);
	     		}
	};
	$("div.cart-top").hoverIntent( config );

    var config_menu = {
        over: function(){
            $(this).children('div').addClass('shown-sub');
        },
        timeout: 400, // number = milliseconds delay before onMouseOut
        out: function(){
            $(this).children('div').removeClass('shown-sub');
        }
    };
    $('#nav li').hoverIntent( config_menu );

	//fix description height
	$('#nav li.menu-category-description').each(function(){
		var height = 0;
		$(this).parent().children('li').each(function(){
			if ( $(this).height() > height )
				height = $(this).height();
		});
		$(this).height( height );
	});

	//mobile navigation
	$('.menu-container li.parent > a').prepend('<em>+</em>');
	$('.menu-container li.parent > a em').click(function(){
		if ( $(this).text() == '+') {
			$(this).parent().parent().addClass('over');
			$(this).parent().next().show();
			$(this).text('-');
		} else {
			$(this).parent().parent().removeClass('over');
			$(this).parent().next().hide();
			$(this).text('+');
		}
		return false;
	});
	$('.menu-container .nav-top-title').click(function(){
		$(this).toggleClass('over').next().toggle();
		return false;
	});
	$(window).resize(function(){
		sw = $(window).width();
		if ( sw > 1049 ) {
			$('#nav, #nav li.parent').removeAttr('style');
			$('#nav li.parent').removeClass('over');
			$('.menu-container li.parent > a em').text('+');
		}
	});

	//mobile search
	var form_search_over = false;
	$('.header-container .form-search').on({
		click: function(event){
			if ( $(window).width() > 767 ) return;

			event.stopPropagation();
			if ( form_search_over ) {
				return true;
			}
			form_search_over = true;
			$(this).addClass('hover');
			$('#search').stop( true, true).css('opacity', 0).animate({ opacity:1}, 200, 'easeOutExpo');
			return false;
		}
	});
	//Hide search if visible
	$('html').click(function() {
		if ( $(window).width() > 767 ) return;
		if ( form_search_over ) {
			form_search_over = false;
			$('#search').stop( true, true ).animate({ opacity:0}, 300, 'easeInExpo', function(){
				$(this).parent().removeClass('hover');
			});
		}
	});
	$(window).resize(function(){
		sw = $(window).width();
		if ( sw > 767 ) {
			form_search_over = false;
			$('#search').parent().removeClass('hover');
			$('#search').css('opacity', 1);
		}
	});

	//qty
	$('.qty-container .qty-inc').click(function(){
		var $qty = $(this).parent().next(), $qtyVal;
		$qtyVal = parseInt($qty.val(), 10);
		if ( $qtyVal < 0 || !$.isNumeric($qtyVal) ) $qtyVal = 0;
		$qty.val(++$qtyVal);
		return false;
	});
	$('.qty-container .qty-dec').click(function(){
		var $qty = $(this).parent().next(), $qtyVal;
		$qtyVal = parseInt($qty.val(), 10);
		if ( $qtyVal < 2 || !$.isNumeric($qtyVal) ) $qtyVal = 2;
		$qty.val(--$qtyVal);
		return false;
	});

	//newsletter default val
	$('#newsletter').focus( function() {
	    if( $(this).val() == $(this).attr('title') ) { $(this).val(''); }
	  })
	  .blur( function() {
		if( $(this).val() == '' ) { $(this).val( $(this).attr('title') ); }
	});

	//banner hover
	$('.banners a, .banner a').each(function(i, obj){
		$(obj).append('<em/>');
	});

    //site-blocks
    $(".site-block-title").hover(
        function(e){
            if ( $(this).parent().hasClass('left-side') ) {
                $(this).parent().animate({ left: "0"} , 500);
            } else {
                $(this).parent().animate({ right: "0"} , 500);
            }
        },
        function(e){
            if ( $(this).parent().hasClass('left-side') ) {
                $(this).parent().animate({ left: -$(this).parent().width()} , 500);
            } else {
                $(this).parent().animate({ right: -$(this).parent().width()} , 500);
            }
        }
    );

	//product accordion
	$('.product-tabs-container h2.tab-heading a').click(function () {
		$('.product-tabs li.active').toggleClass('active');
		$('#'+$(this).parent().attr('id').replace("product_acc_", "product_tabs_")).toggleClass('active');
		that = $(this).parent();
		if($(that).is('.active')) {
			$(that).toggleClass('active');
			$(that).next().slideToggle(function(){ $(that).scrollToMe(); });
		} else {
			$('.product-tabs-container h2.tab-heading.active').toggleClass('active').next().slideToggle();
			$(that).toggleClass('active');
			$(that).next().slideToggle(function(){ $(that).scrollToMe(); });
		}
		return false;
	});
	$('.product-tabs-container h2:first').toggleClass('active');
	$('.product-tabs a').click(function(){
		$('.product-tabs-container h2.tab-heading.active').toggleClass('active');
		$('#'+$(this).parent().attr('id').replace("product_tabs_", "product_acc_")).toggleClass('active');
	});

});