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

});