<!DOCTYPE html>
<html class="no-js" lang="en"><!--<![endif]--><head>

	<meta http-equiv="X-UA-Compatible" content="IE=8">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Ask us</title>

<link rel="stylesheet" href="http://forzaitalia.businessgrid.com.au/skin/frontend/default/celebrity-new/css/local2.css" type="text/css" />
<link rel="stylesheet" href="http://forzaitalia.businessgrid.com.au/skin/frontend/default/celebrity-new/css/styles2.css" type="text/css" />
</head><body class="flexible dark  contacts-index-index">
<!--[if lt IE 7]>
<script type="text/javascript">
//<![CDATA[
    var BLANK_URL = 'http://forzaitalia.businessgrid.com.au/js/blank.html';
    var BLANK_IMG = 'http://forzaitalia.businessgrid.com.au/js/spacer.gif';
//]]>
</script>
<![endif]-->

<script type="text/javascript" src="http://forzaitalia.businessgrid.com.au/js/prototype/prototype.js"></script>
<script type="text/javascript" src="http://forzaitalia.businessgrid.com.au/js/lib/ccard.js"></script>
<script type="text/javascript" src="http://forzaitalia.businessgrid.com.au/js/prototype/validation.js"></script>
<script type="text/javascript" src="http://forzaitalia.businessgrid.com.au/js/scriptaculous/builder.js"></script>
<script type="text/javascript" src="http://forzaitalia.businessgrid.com.au/js/scriptaculous/effects.js"></script>
<script type="text/javascript" src="http://forzaitalia.businessgrid.com.au/js/scriptaculous/dragdrop.js"></script>
<script type="text/javascript" src="http://forzaitalia.businessgrid.com.au/js/scriptaculous/controls.js"></script>
<script type="text/javascript" src="http://forzaitalia.businessgrid.com.au/js/scriptaculous/slider.js"></script>
<script type="text/javascript" src="http://forzaitalia.businessgrid.com.au/js/varien/js.js"></script>
<script type="text/javascript" src="http://forzaitalia.businessgrid.com.au/js/varien/form.js"></script>
<script type="text/javascript" src="http://forzaitalia.businessgrid.com.au/js/mage/translate.js"></script>
<script type="text/javascript" src="http://forzaitalia.businessgrid.com.au/js/mage/cookies.js"></script>
<script type="text/javascript" src="http://forzaitalia.businessgrid.com.au/js/olegnax/jquery-1.7.1.min.js"></script>

<script type="text/javascript" src="http://forzaitalia.businessgrid.com.au/js/olegnax/jquery.cycle.js"></script>
<script type="text/javascript" src="http://forzaitalia.businessgrid.com.au/js/olegnax/jquery.hoverIntent.min.js"></script>
<script type="text/javascript" src="http://forzaitalia.businessgrid.com.au/js/varien/configurable.js"></script>
<script type="text/javascript" src="http://forzaitalia.businessgrid.com.au/js/calendar/calendar.js"></script>
<script type="text/javascript" src="http://forzaitalia.businessgrid.com.au/js/calendar/calendar-setup.js"></script>
<script type="text/javascript" src="http://forzaitalia.businessgrid.com.au/js/varien/productsimple.js"></script>

<script type="text/javascript" src="http://forzaitalia.businessgrid.com.au/skin/frontend/default/celebrity/js/script.js"></script>
<!--[if lt IE 8]>
<link rel="stylesheet" type="text/css" href="http://forzaitalia.businessgrid.com.au/skin/frontend/default/celebrity/css/styles-ie.css" media="all" />
<![endif]-->
<!--[if lt IE 7]>
<script type="text/javascript" src="http://forzaitalia.businessgrid.com.au/js/lib/ds-sleight.js"></script>
<script type="text/javascript" src="http://forzaitalia.businessgrid.com.au/skin/frontend/base/default/js/ie6.js"></script>
<![endif]-->
<!--[if lt IE 9]>
<script type="text/javascript" src="http://forzaitalia.businessgrid.com.au/js/olegnax/html5shiv.js"></script>
<![endif]-->

<script type="text/javascript">
//<![CDATA[
Mage.Cookies.path     = '/';
Mage.Cookies.domain   = '.forzaitalia.businessgrid.com.au';
//]]>
</script>

<script type="text/javascript">
//<![CDATA[
optionalZipCountries = [];
//]]>
</script>
<script type="text/javascript">var Translator = new Translate({"Please use only letters (a-z or A-Z), numbers (0-9) or underscore(_) in this field, first character should be a letter.":"Please use only letters (a-z or A-Z), numbers (0-9) or underscores (_) in this field, first character must be a letter."});</script>



<!-- Prompt IE 6 users to install Chrome Frame. Remove this if you support IE 6.
     chromium.org/developers/how-tos/chrome-frame-getting-started -->
<!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->


	<div class="top-border"></div>
        <noscript>
        <div class="noscript">
            <div class="noscript-inner">
                <p><strong>JavaScript seem to be disabled in your browser.</strong></p>
                <p>You must have JavaScript enabled in your browser to utilize the functionality of this website.</p>
            </div>
        </div>
    </noscript>

        
<!-- HEADER BOF -->

<!-- HEADER EOF -->
	  
		  
          
                <!-- breadcrumbs BOF -->
<!-- breadcrumbs EOF -->
                <div class="col-main">
                                        <div id="messages_product_view"></div>
<?php if(@$_POST['contact-us']){
$name = @$_POST['name'];
$email = @$_POST['email'];
$product = @$_POST['product-url'];
$comments = @$_POST['comment'];
 //$to="sales@bellaterracs.com";
$to="info@forzaitalia.com.au";
$contactsubject = "Availability - ".$product;
            $headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
			$headers .= "From: $email\r\n"; 
$message = "
<html>
  <body bgcolor=\"#DCEEFC\">
       	   <h5><span>Name :  </span>".$name." </h5>
	   	   <h5><span>E-Mail :    </span>".$email." </h5>
	   <h5><span>Product :   </span>".$product." </h5>
	   <h5><span>Question :    </span>".$comments." </h5>
	   </body>
</html>
";
 mail($to, $contactsubject, $message, $headers); ?>
<div id="messages_product_view">
<ul class="messages">
<li class="success-msg">
<ul>
<li>
<span>Email sent.</span>
</li>
</ul>
</li>
</ul>
</div>

 <?php } 
?> 
										
<div class="page-title">
    <h1>Use the below form to contact a sales representative regarding this product</h1>
</div>
<form action="" id="contactForm" method="post">
<input name="contact-us" type="hidden" value="1">
    <div class="fieldset">
      
        <ul class="form-list">
		  <li>
                <div class="field">
                    <label for="product-url" class="required url-pro"><em>*</em>Product</label>
                    <div class="input-box">
                        <input name="product-url" id="product-url" class="required-entry input-text" title="Product Url" value="<?php echo @$_GET['producturl']; ?>" type="text" />
                    </div>
                </div>
            </li>
            <li class="fields">
                <div class="field">
                    <label for="name" class="required"><em>*</em>Your Name</label>
                    <div class="input-box">
                        <input name="name" id="name" title="Name" class="input-text required-entry" type="text">
                    </div>
                </div>
			</li>
			<li>
                <div class="field">
                    <label for="email" class="required"><em>*</em>Your Email Address</label>
                    <div class="input-box">
                        <input name="email" id="email" title="Email" class="input-text required-entry validate-email" type="text">
                    </div>
                </div>
            </li>
        
            <li class="wide">
                <label for="comment" class="required"><em>*</em>Your Question</label>
                <div class="input-box">
                    <textarea name="comment" id="comment" title="Comment" class="required-entry input-text" cols="5" rows="3"></textarea>
                </div>
            </li>
        </ul>
    </div>
    <div class="buttons-set">
      <button id="button-subs" class="button" title="Submit" type="submit">
<span>
<span>Submit</span>
</span>
</button>
        
    </div>
</form>
<script type="text/javascript">
//<![CDATA[
    var contactForm = new VarienForm('contactForm', true);
//]]>
</script>
                </div>
	            <div class="col-right sidebar"></div>
       
        
		    <!-- footer BOF -->

<!-- footer EOF -->
     
</div>

<div class="site-block bottom"><p style="display:none">bb</p></div>


<div id="fancybox-tmp"></div><div id="fancybox-loading"><div></div></div><div id="fancybox-overlay"></div><div id="fancybox-wrap"><div id="fancybox-outer"><div class="fancybox-bg" id="fancybox-bg-n"></div><div class="fancybox-bg" id="fancybox-bg-ne"></div><div class="fancybox-bg" id="fancybox-bg-e"></div><div class="fancybox-bg" id="fancybox-bg-se"></div><div class="fancybox-bg" id="fancybox-bg-s"></div><div class="fancybox-bg" id="fancybox-bg-sw"></div><div class="fancybox-bg" id="fancybox-bg-w"></div><div class="fancybox-bg" id="fancybox-bg-nw"></div><div id="fancybox-content"></div><a id="fancybox-close"></a><div id="fancybox-title"></div><a href="javascript:;" id="fancybox-left"><span class="fancy-ico" id="fancybox-left-ico"></span></a><a href="javascript:;" id="fancybox-right"><span class="fancy-ico" id="fancybox-right-ico"></span></a></div></div></body></html>