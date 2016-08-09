<!DOCTYPE html>
<html>
<head>

	<title>Products - Thomastown Produce & Pet Supplies P/L</title>
	
	<link rel="stylesheet" type="text/css" href="style.css">

	<link href='http://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
	<script src="jquery.colorbox.js"></script>

	<link rel="stylesheet" type="text/css" href="colorbox.css">

</head>

<body>

<center>
 	<!-- SITE -->
	<div id='site'>

		<!-- HEADER -->
		<div id="header">
			<div id="h-phone">(03) 9464 2439</div>
			<div id='menu'>
				<a href='index.html'><img src='images/i-home.png'/></a>
				<a href="about.html" style="margin-left: 38px;">ABOUT US</a>
				<a href="products.php" style="margin-left: 43px;">PRODUCTS</a>
				<a href="contact.html" style="margin-left: 37px;">CONTACT</a>
			</div>
		</div>

		<!-- CONTENT -->
		<div id="content" style='background:url("images/pages-bg.png") no-repeat scroll 95px 50px transparent;'>
			<br/>

			<div id='cspacer' style='background:url("images/products-bg.png") no-repeat scroll right 20px transparent;'>
				<strong style='font-size:23px;'>OUR PRODUCTS</strong>
				<br/><br/>
				- We specialize in a wide range of seed, seed mixes, feed, health supplements and bedding material
	<br/><br/>- All of our a products are Australian grown and owned
	<br/><br/>- We only select the highest quality produce when making our products, all of which are made on site
	<br/><br/>- Packaging sizes available in 1kg, 2kg, 5kg, 10kg and 20kg with the exception of a few products which come in 25kg
	<br/><br/>- Customer orders which consist of 1kg - 10kg bags have the option of labeled stickers with their own business logo and details, all <br/> they need to do is supply us with a jpeg image
	<br/><br/>- Barcodes are available upon request
	<br/><br/>- Our price list contains all the information such as product description, available packaging sizes and prices which do not include GST
	<br/><br/>- Feel free to contact us should you have any questions regarding our products and services
				<br/>
				<br/>
			</div>
			
			<div class="sep_b"></div>
	
			<div id='cspacer' style='background:url("images/products-newsletter.png") no-repeat scroll 60px 20px transparent;padding-left:470px;padding-top:50px;width:430px;'>
			
						<?php
						if(isset($_POST['email'])) {
						
							// email to ADMIN 
							$to = "ttps@producepetsupplies.com.au";
							//$to = "binaryangel@abv.bg";
							$from = $_POST['email'];
							$subject = "WHOLESALE PRODUCT LIST SUBSCRIPTION ";

							//begin of HTML message
							$message = '<html>
							  <body>
									<b>WHOLESALE PRODUCT LIST SUBSCRIPTION </b>
								  <br><br>
								  business name: '.$_POST['bname'].'<br/>
								  abn: '.$_POST['abn'].'<br/>
								  contact: '.$_POST['contact'].'<br/>
								  email: '.$_POST['email'].'<br/>
							  </body>
							</html>';
							//end of message
						   
							$headers  = "From: $from\r\n";
							$headers .= "Content-type: text/html\r\n";
							
							// now lets send the email.
							//mail($to, $subject, $message, $headers);
							
							/*
							// email to USER
							$to = $_POST['email'];
							$from = "ttps@producepetsupplies.com.au";
							$subject = "WHOLESALE PRODUCT LIST SUBSCRIPTION ";

							//begin of HTML message
							$message = '<html>
							  <body>
								Thank you for your interest in our products.
<br/><br/>
Please view the below link to download our interactive wholesale pricelist where you can fill out your order and it will automatically calculate the total. You then have the option to print, save or email.
<br/><br/>
link: <br/>
<a href="www.producepetsupplies.com.au/ttps-wholesale-pricelist.pdf">www.producepetsupplies.com.au/ttps-wholesale-pricelist.pdf</a>
<br/>
<br/>
If you have any further questions please do not hesitate to call us on 03 9464 2439 or email us at ttps@producepetsupplies.com.au
<br/>
<br/>
Kind regards,
<br/>
<br/>
<strong>Thomastown Produce & Pet Supplies</strong>
<br/>
<br/>
21 Apex Court, Thomastown, Vic Australia 3074<br/>
Tel (03) 9464 2439 Fax (03) 9402 5620<br/>
<a href="www.producepetsupplies.com.au">www.producepetsupplies.com.au</a>
							  </body>
							</html>';
							//end of message
						   
							$headers  = "From: $from\r\n";
							$headers .= "Content-type: text/html\r\n";
							*/
							
							
							// now lets send the email.
							if(mail($to, $subject, $message, $headers)){
							?>
							
							<script>
								alert('Thank you for registering! We will be on contact shortly.');
							</script>
							
							<?php }

							
							
						} ?>			
						<form id='forma' method='post' action='products.php'>
							<span style=''>
							<br/>
							<span class='heading' style='text-align:right;'>WHOLESALE PRODUCT LIST</span><br/>
								<label id='lbname'>business name</label><input name='bname' id='bname' type="text"/><br/>
								<label id='labn'>abn</label><input name='abn' id='abn' type="text" onkeypress='validate(event)'/><br/>
								<label id='lcontact'>contact</label><input name='contact' id='contact' type="text"/><br/>
								<label id='lemail'>email</label><input name='email' id='email' type="text"/>
							</span>
							<span>
							<br/>
								<label>&nbsp;</label><img  onClick='submitForm();' style='cursor:hand;cursor:pointer;'  src="images/sub_button2.png">
							</span>
						</form>
						<br/>
						<br/>	
						
			</div>
			
				
				<script>
				
				$(document).ready(function() {
					$("#abn").keydown(function(event) {
						// Allow: backspace, delete, tab, escape, and enter
						if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || 
							 // Allow: Ctrl+A
							(event.keyCode == 65 && event.ctrlKey === true) || 
							 // Allow: home, end, left, right
							(event.keyCode >= 35 && event.keyCode <= 39)) {
								 // let it happen, don't do anything
								 return;
						}
						else {
							// Ensure that it is a number and stop the keypress
							if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
								event.preventDefault(); 
							}   
						}
					});
				});

				function submitForm(){
					var errors = false;
					if($('#bname').val()=='' || $('#bname').val()==' '){
						$('#bname').focus();
						$('#lbname').css('color','red');
						errors = true;
					}else{
						$('#lbname').css('color','#929292');
					}
					if($('#abn').val()=='' || $('#abn').val()==' '){
						$('#abn').focus();
						$('#labn').css('color','red');
						errors = true;
					}else{
						$('#labn').css('color','#929292');
					}
					if($('#contact').val()=='' || $('#contact').val()==' '){
						$('#contact').focus();
						$('#lcontact').css('color','red');
						errors = true;
					}else{
						$('#lcontact').css('color','#929292');
					}
					if($('#email').val()=='' || $('#email').val()==' '){
						$('#email').focus();
						$('#lemail').css('color','red');
						errors = true;
					}else{
						$('#lemail').css('color','#929292');
					}
					
					if(errors==false){
						$('#forma').submit();
					}
				}
				</script>
			
		</div>

		<!-- FOOTER -->
		<div id="footer">
			<div id="f-phone">(03) 9464 2439</div>
			<div id="f-fb"><a target='_blank' href='https://www.facebook.com/pages/Thomastown-Produce-Pet-Supplies-PL/385284658258755?ref=br_tf'>&nbsp;</a></div>
			<br/>
			<br/>
			
			<table style='padding:20px;text-align:left;'>
				<tr>
					<td style='width:800px;'>&copy; 2013 All rights reserved | Thomastown Produce and Pet Supplies Pty ltd</td>
					<td style='text-align:right;'><a href='www.marketingessentials.com.au'>Website by M.E.W</a></td>
				</tr>
			</table>
		</div>
	</div>
</center>



</body>

</html> 