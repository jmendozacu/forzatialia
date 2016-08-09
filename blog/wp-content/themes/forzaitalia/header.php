<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package forzaitalia
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>
<?php wp_title( '|', true, 'right' ); ?>
</title>
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon.ico" />

<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
<link href='//fonts.googleapis.com/css?family=Open+Sans:200,300,400,500,600,700,800' rel='stylesheet' type='text/css'>
<style>
/********** update google Font */
.std h2, .std h4,
.page-title h1, .page-title h2, .page-head-alt h3,
.block .block-title strong,
.slideshow ul li a, .slideshow ul li strong, .slider-container h2,
.cart .crosssell h2, .opc .step-title,
.banners a span, .banner a span,
.products-list .product-name,
#shopping-cart-totals-table strong,
.cart .cart-collaterals .col2-set h2,
button.button span,
footer .footer-subscribe .title,
.product-view h1, .product-view h2,
.product-tabs a, .product-tabs-content h3, .product-tabs-content h4,
#product-customer-reviews .review-title, .add-review h3.title, 
#customer-reviews .form-add h2, #customer-reviews .form-add h3, #customer-reviews .form-add h4, #customer-reviews dt a,
.product-view .box-tags .form-add label,
#nav>li>a, #nav li.menu-category-description strong, #nav li.menu-category-description a { font-family: 'Open Sans', sans-serif; font-weight: 400; }

/********** update theme color */
#slide-timeline,
#prev,
.slider-container .jcarousel-list .btn-cart:hover,
.products-grid .btn-cart:hover,
.products-list .btn-cart:hover,
.slideshow ul li a:hover,
.banners a:hover span,
.banner a:hover span,
#addTagForm button.button span,
.add-review  button.button span,
.jcarousel-next-horizontal:hover, .jcarousel-prev-horizontal:hover,
button.btn-checkout span,
button.button:hover span,
button.btn-proceed-checkout span span,
.product-view button.btn-cart span,
.product-view button.btn-cart span span,
.cart .cart-collaterals .col2-set button.button span,
.block .block-content button.button span,
.opc .active .step-title:hover,
.product-image em,
#zoom-prev:hover,
#zoom-next:hover,
header .cart-top,
header .cart-top .summary,
footer .footer-subscribe button.button span,
.search-autocomplete ul li:hover,
.light .search-autocomplete ul li:hover,
#search_mini_form .form-search button:hover,
.light #search_mini_form .form-search button:hover,
.pager .pages li a.next:hover, .pager .pages li a.previous:hover,
#nav>li>a:hover,
#nav>li.over>a,
#nav>li.active>a,
#nav li.menu-category-description a,
#nav li.menu-category-description button.button span,
#nav li.menu-category-description a:hover { background-color:#095aa5; }

.top-border,
#nav ul,
#nav div.sub-wrapper { border-top-color: #095aa5; }
#nav ul div, #nav ul ul,
.light #nav ul div, .light #nav ul ul, #nav div.sub-wrapper ul div.sub-wrapper{ border-left-color: #095aa5;}
#nav ul ul:before { border-right-color: #095aa5; }
.opc .active .step-title:hover { border-color: #095aa5; }
.products-list .price,
#shopping-cart-table a{ color: #095aa5; }

.header-container, .light .header-container {background-color:#043057}
.footer-container, .light .footer-container {background-color:#043057}
.main-container, .fixed {background-color:#e9e9e9}

</style>
</head>

<body <?php body_class(); ?>>
<div class="top-border"></div>
<div id="page" class="page">
<?php do_action( 'before' ); ?>
<header class="header-container" style="background-color: #043057; width:100%;" >
  <header>
    <div class="clearfix">
      <div id="gift_box">
        <div class="gift"><a href="http://www.forzaitalia.com.au/gift-voucher-1/"><img src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/gift-img.png"></a></div>
        
        <!-- LANGUAGES BOF --> 
        <!-- LANGUAGES EOF --> 
        
        <!-- cart BOF -->
        <div class="cart-top"> <a href="http://www.forzaitalia.com.au/checkout/cart/" class="summary">
          <div class="text"> Shopping cart - <span class="price"><span class="price">$0.00</span></span> </div>
          </a>
          <div class="details">
            <p class="a-center">You have no items in your shopping cart.</p>
          </div>
        </div>
        <!-- cart EOF --> 
        <!-- Currency BOF --> 
        <!-- Currency EOF --> 
      </div>
    </div>
    <div class="clearfix logo-container" style="background-color: #043057; width:100%;" > <a class="logo" title="Forza Italia" href="http://www.forzaitalia.com.au/"><strong>Forza Italia</strong><img alt="Celebrity Store" src="http://www.forzaitalia.com.au/media/olegnax/celebrity/logo.png"></a>
      <div class="money"><a href="http://www.forzaitalia.com.au/terms/"><img src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/money.png"></a></div>
      <ul class="links">
        <li><a title="My Account" href="https://www.forzaitalia.com.au/customer/account/">My Account</a></li>
        <li class="separator">|</li>
        <li><a title="My Wishlist" href="https://www.forzaitalia.com.au/wishlist/">My Wishlist</a></li>
        <li class="separator">|</li>
        <li><a class="top-link-checkout" title="Checkout" href="http://www.forzaitalia.com.au/checkout/">Checkout</a></li>
        <li class="separator">|</li>
        <li><a title="Log In" href="https://www.forzaitalia.com.au/customer/account/login/">Log In</a></li>
        <li class="separator">|</li>
        <li><a href="https://www.forzaitalia.com.au/customer/account/create/">Sign Up</a></li>
      </ul>
    </div>
  </header>
  
  <!-- <div class="site-branding">
			<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
			<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
		</div>-->
  <div class="menu-container clearfix">
    <?php
            // Custom Nav Call
            function custom_nav() {
                if ( function_exists( 'wp_nav_menu' ) ) :
                    wp_nav_menu( array(
                        'theme_location'  => 'primary',
                        'depth'         => 3,
						'menu'            => '',
                        'container'       => 'nav',
                        'container_class' => 'olegnax',
                        'container_id'    => '',
                        'menu_class'      => '',
                        'menu_id'         => 'nav',
                        'echo'            => true,
                        'fallback_cb'     => 'wp_nav_fallback',
                        'before'          => '',
                        'after'           => '',
                        'link_before'     => '',
                        'link_after'      => '',
                        'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                        'depth'           => 0,
                        'walker'          =>  new ik_walker()
                    ));
                else :
                    nav_fallback();
                endif;
            }

            // Navigation Fallback Call
            function wp_nav_fallback() {
                //wp_list_pages arguments as an array
                $nav_wpflex = array(
                    'depth'         => 2,
                    'show_date'     => '',
                    'date_format'   => get_option( 'date_format' ),
                    'child_of'      => 0,
                    'exclude'       => '',
                    'include'       => '',
                    'title_li'      => '',
                    'echo'          => 1,
                    'authors'       => '',
                    'sort_column'   => 'menu_order',
                    'link_before'   => '',
                    'link_after'    => '',
                    'walker'        => ''
                );?>
    <nav class="olegnax">
      <ul id="nav">
        <?php
                        //begin wp_list_pages loop
                        if( wp_list_pages( $nav_wpflex ) ) : while ( wp_list_pages( $nav_wpflex ) ) :
                            //list items from the array above
                            wp_list_pages( $nav_wpflex );
                        endwhile;
                        endif;
                    ?>
      </ul>
    </nav>
    <?php } // end wp_nav_fallback

            // custom_nav call
            custom_nav();
		
        ?>
    </nav>
    <div class="free-order-1">
      <div class="free-order"><img border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/order-over.png"></div>
      <div class="facebooklikebutton">
        <div data-show-faces="false" data-width="450" data-layout="button_count" data-send="false" data-href="http://www.facebook.com/forzaitalia.com.au" class="fb-like fb_edge_widget_with_comment fb_iframe_widget" fb-xfbml-state="rendered"><span style="height: 20px; width: 82px;">
          <iframe scrolling="no" id="f3c2fc9255058" name="f5c7e3d7c04e28" style="border: medium none; overflow: hidden; height: 20px; width: 82px;" title="Like this content on Facebook." class="fb_ltr" src="http://www.facebook.com/plugins/like.php?api_key=&amp;channel_url=http%3A%2F%2Fstatic.ak.facebook.com%2Fconnect%2Fxd_arbiter.php%3Fversion%3D29%23cb%3Df21039a74ea1814%26domain%3Dwww.forzaitalia.com.au%26origin%3Dhttp%253A%252F%252Fwww.forzaitalia.com.au%252Ff1dd5f1e338a3b6%26relation%3Dparent.parent&amp;colorscheme=light&amp;extended_social_context=false&amp;href=http%3A%2F%2Fwww.facebook.com%2Fforzaitalia.com.au&amp;layout=button_count&amp;locale=en_US&amp;node_type=link&amp;sdk=joey&amp;send=false&amp;show_faces=false&amp;width=450"></iframe>
          </span></div>
      </div>
    </div>
    <form method="get" action="http://www.forzaitalia.com.au/catalogsearch/result/" id="search_mini_form">
    <div class="form-search">
        <input type="text" class="input-text" value="Search over 5000+ Italian products" name="q" id="search" autocomplete="on" onClick="Clear();">
        <button title="Search" type="submit"></button>
    </div>
	<div class="search-autocomplete" id="search_autocomplete" style="display: none;"></div>
    <script type="text/javascript">
	//&lt;![CDATA[
	   function Clear()
       {    
       document.getElementById("search").value= "";
        }
	    var searchForm = new Varien.searchForm('search_mini_form', 'search', 'Search over 5000+ Italian products');
	    searchForm.initAutocomplete('http://www.forzaitalia.com.au/catalogsearch/ajax/suggest/', 'search_autocomplete');
	//]]&gt;
	</script>
</form>
    <?php 	
	
			?>
  </div>
  <!-- #site-navigation --> 
</header>
<!-- #masthead -->
<?php 
if ( is_home() ) {
  $class='main-container';
}else{
  $postid = get_the_ID();
  if($postid==377){$class='main-container_post';}else{$class='main-container';}
}
?> 
<div id="content" class="<?php echo $class; ?> col2-left-layout">
<div style="height: 93px;width: 1010px;margin: 0 auto;padding: 0 auto;">
<div class="logos_bg" >
        <ul>
          <li class="official"><a href="http://www.forzaitalia.com.au/sport/motorsport/ferrari/"><img height="70" width="106" border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/spacer-img.png"></a></li>
          <li class="multi1">
            <ul>
              <li class="ducati"><a href="http://www.forzaitalia.com.au/sport/motorsport/ducati/"><img height="43" width="58" border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/spacer-img.png"></a></li>
              <li class="puma"><a href="http://www.forzaitalia.com.au/sport/by-manufacturer/puma/"><img height="29" width="58" border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/spacer-img.png"></a></li>
            </ul>
          </li>
          <li class="multi2">
            <ul>
              <li class="vr"><a href="http://www.forzaitalia.com.au/sport/motorsport/valentino-rossi-vr-46/"><img height="23" width="70" border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/spacer-img.png"></a></li>
              <li class="moto"><a href="http://www.forzaitalia.com.au/sport/motorsport/moto-guzzi/"><img height="33" width="70" border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/spacer-img.png"></a></li>
            </ul>
          </li>
          <li class="multi3">
            <ul>
              <li class="sepox"><a href="http://www.forzaitalia.com.au/sport/motorsport/vespa/"><img height="28" width="78" border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/spacer-img.png"></a></li>
              <li class="flat"><a href="http://www.forzaitalia.com.au/sport/motorsport/fiat/"><img height="35" width="58" border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/spacer-img.png"></a></li>
            </ul>
          </li>
          <li class="alogo"><a href="http://www.forzaitalia.com.au/sport/football/adp-del-piero/"><img height="80" width="58" border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/spacer-img.png"></a></li>
          <li class="italia"><a href="http://www.forzaitalia.com.au/sport/football/italia/"><img height="76" width="52" border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/spacer-img.png"></a></li>
          <!--multi-6-->
          <li class="multi6">
            <ul>
              <li class="multi-top">
                <ul>
                  <li class="logo1"><a href="http://www.forzaitalia.com.au/sport/football/ac-milan/"><img height="35" width="20" border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/spacer-img.png"></a></li>
                  <li class="logo2"><a href="http://www.forzaitalia.com.au/sport/football/other-italian-clubs/"><img height="21" width="40" border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/spacer-img.png"></a></li>
                  <li class="logo3"><a href="http://www.forzaitalia.com.au/sport/football/juventus/"><img height="35" width="21" border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/spacer-img.png"></a></li>
                </ul>
              </li>
              <li class="multi-bottom">
                <ul>
                  <li class="logo4"><a href="http://www.forzaitalia.com.au/sport/football/napoli/"><img height="32" width="32" border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/spacer-img.png"></a></li>
                  <li class="logo5"><a href="http://www.forzaitalia.com.au/sport/football/as-roma/"><img height="32" width="24" border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/spacer-img.png"></a></li>
                  <li class="logo6"><a href="http://www.forzaitalia.com.au/sport/football/inter/"><img height="32" width="32" border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/spacer-img.png"></a></li>
                </ul>
              </li>
            </ul>
          </li>
          <!--multi-6--> <!--multi-5-->
          <li class="multi5">
            <ul>
              <li class="multi_top">
                <ul>
                  <li class="logo7"><a href="http://www.forzaitalia.com.au/accessories/beauty-fragrance/marvis/"><img height="17" width="86" border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/spacer-img.png"></a></li>
                </ul>
              </li>
              <li class="multi-middle">
                <ul>
                  <li class="logo8"><a href="http://www.forzaitalia.com.au/accessories/beauty-fragrance/felce-azzurra/"><img height="25" width="38" border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/spacer-img.png"></a></li>
                  <li class="logo9"><a href="http://www.forzaitalia.com.au/accessories/beauty-fragrance/proraso/"><img height="22" width="41" border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/spacer-img.png"></a></li>
                </ul>
              </li>
              <li class="multi_bottom">
                <ul>
                  <li class="logo10"><a href="http://www.forzaitalia.com.au/accessories/beauty-fragrance/intesa/"><img height="21" width="28" border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/spacer-img.png"></a></li>
                  <li class="logo11"><a href="http://www.forzaitalia.com.au/accessories/beauty-fragrance/malizia/"><img height="21" width="35" border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/spacer-img.png"></a></li>
                </ul>
              </li>
            </ul>
          </li>
          <!--multi-5-->
          <li class="itly">
            <ul>
              <li class="logo1"><a href="http://www.forzaitalia.com.au/gifts/murano-glass/"><img height="32" width="66" border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/spacer-img.png"></a></li>
              <li class="logo2"><a href="http://www.forzaitalia.com.au/gifts/venetian-masks/"><img height="14" width="66" border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/spacer-img.png"></a></li>
            </ul>
          </li>
          <li class="red"><a href="http://www.forzaitalia.com.au/accessories/pinocchio/"><img height="66" width="66" border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/spacer-img.png"></a></li>
          <li class="last">
            <ul>
              <li class="thatitaly"><a href="http://www.forzaitalia.com.au/accessories/that-s-italia/"><img height="44" width="78" border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/spacer-img.png"></a></li>
              <li class="kappa"><a href="http://www.forzaitalia.com.au/kappa"><img height="19" width="80" border="0" alt="" src="http://www.forzaitalia.com.au/skin/frontend/default/celebrity/images/spacer-img.png"></a></li>
            </ul>
          </li>
        </ul>
      </div></div>
<!---For additional link --Begin-->
<!--
<div class="additional_links"><p><a href="http://www.forzaitalia.com.au/index.php/contacts/">Email Us</a>&nbsp; &nbsp; &nbsp;&nbsp;<a href="http://www.forzaitalia.com.au/index.php/faq">Frequently Asked Questions</a><span style="white-space: pre;">&nbsp; </span>Tel +61 3 9654 6660 &nbsp; &nbsp; &nbsp;<a href="http://www.forzaitalia.com.au/index.php/italian-online-games">Italian Online Games</a>&nbsp; &nbsp; &nbsp; <a target="_self" href="http://www.forzaitalia.com.au/schools-libraries">Schools</a> &nbsp; &nbsp; &nbsp;<a target="_self" href="http://www.forzaitalia.com.au/vip">VIP Specials</a></p></div>-->
<!---For additional link --End-->
<div class="main-shadow">
<div class="main">
