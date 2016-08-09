<!DOCTYPE html>
<html>
<head>

	<title>Thomastown Produce & Pet Supplies P/L</title>
	
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
		<div id="content">
			<br/>

			<div id='cspacer'>
				<div id='cont1'>
					<span class='heading'>WELCOME TO Thomastown Produce & Pet Supplies</span>
					Thomastown Produce & Pet Supplies, with over 30 years experience in animal products prides itself in the highest quality animal grains and supplements. Owner Sam Cavalieri formulates the mixes himself and ensures only the best for his clients. Sam's knowledge and experience in the industry is second to none and he can help you choose the right products for the best results. Simply contact him on (03) 9464 2439.
					
					<br/><br/>
					<div class="sep_s"></div><br/>
					<span style='font-size:14px;'>
We manufacturer our mixes for many retailers around Australia and are seen as the market leader in the industry supplying the highest quality animal grains and supplements plus much, much more along with a level of service deemed unmatchable!<span>




					<br/>
				</div>
			</div>

					<br/>
					<br/>

					<div id="boxes">
						
							<span id='b1'>We specialise in a wide range of seed, seed mixes, feed, health supplements and bedding material</span>
							<span id='b2'>100% Australian owned & Australian grown products</span>
							<span id='b3'>We only select the highest quality produce when making our  products, all of which are made on site</span>
							<span id='b4'>Packaging sizes available in 1kg, 2kg, 5kg, 10kg and 20kg with the exception of a few products which come in 25kg</span>
						
					</div>
			
			<div class="sep_b"></div>

			<div id='cspacer'>
				

				<div id='s21'>
						<span class='heading'>HOW TO FIND US</span>
						<br/>
						<span><iframe width="300" height="280" style='margin-right:10px;' frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?f=q&amp;source=s_q&amp;geocode=&amp;q=Thomastown+Produce+%26+Pet+Supplies,+21+Apex+Court,+Thomastown,+Victoria,+Australia&amp;aq=1&amp;oq=21+Apex+Court,+Thomastown,&amp;sll=-37.695568,145.034402&amp;sspn=0.008642,0.021136&amp;t=m&amp;g=21+Apex+Court,+Thomastown,+Victoria,+Australia&amp;ie=UTF8&amp;hq=Thomastown+Produce+%26+Pet+Supplies,&amp;hnear=21+Apex+Ct,+Thomastown+Victoria+3074,+%D0%90%D0%B2%D1%81%D1%82%D1%80%D0%B0%D0%BB%D0%B8%D1%8F&amp;ll=-37.695571,145.034401&amp;spn=0.006295,0.006295&amp;output=embed"></iframe></span>
						<span>
							<img src="images/findus.jpg"><br/>
							<strong>OPEN</strong><br/>
							Tue - Fri: 9:00 am - 5:00 pm<br/>
							Sat: 9:00 am - 3:00 pm<br/>
							Sun: 9:00 am - 2:00 pm<br/>
								<span style='margin-top:10px;margin-right:5px;vertical-align:'><img style='vertical-align:middle;' src="images/i-pin.png"></span>
								<span style='margin-top:5px;'>21 Apex Court, Thomastown,<br/>
										Melbourne, Victoria, Australia 3074
								</span><br/>
								<span style='margin-top:5px;'><img src="images/i-phone.png"></span>
								<span style='margin-top:5px;margin-right:15px;'>(03) 9464 2439</span>
								<span style='margin-top:3px;'><img src="images/e-mail.png"></span>
								<span style='margin-top:5px;'>ttps@optusnet.com.au</span>
						</span>

						<div class="sep_s"></div>

						<div  id='theForm'>
						<?php
						if(isset($_POST['email'])) {
						
							// email to ADMIN 
							$to = "ttps@producepetsupplies.com.au";
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
							
							
							/*
							// now lets send the email.
							mail($to, $subject, $message, $headers);
							
							
							// email to ADMIN 
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
							$headers .= "Content-type: text/html\r\n";*/
							
							// now lets send the email.
							if(mail($to, $subject, $message, $headers)){
							?>
							
							<script>
								alert('Thank you for registering! We will be on contact shortly.');
							</script>
							
							<?php }

							
							
						}
						?>
						<form id='forma' method='post' action='index.php'>
							<span style='width:450px;'>
							<br/>
							<span class='heading'>WHOLESALE PRODUCT LIST</span><br/>
								<label id='lbname'>business name</label><input name='bname' id='bname' type="text"/><br/>
								<label id='labn'>abn</label><input name='abn' id='abn' type="text" onkeypress='validate(event)'/><br/>
								<label id='lcontact'>contact</label><input name='contact' id='contact' type="text"/><br/>
								<label id='lemail'>email</label><input name='email' id='email' type="text"/>
							</span>
							<span>
							<br/>	
								<img src="images/subscribe.png"><br/>
								<img onClick='submitForm();' style='cursor:hand;cursor:pointer;' src="images/sub_button.png">
							</span>
						</form>
					</div>
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

				<div id='s22'>
					<iframe src="//www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2Fpages%2FThomastown-Produce-Pet-Supplies-PL%2F385284658258755&amp;width=280&amp;height=570&amp;show_faces=true&amp;colorscheme=light&amp;stream=true&amp;show_border=true&amp;header=true" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:280px; height:570px;" allowTransparency="true"></iframe>

				</div>

			</div>

			<div class="sep_b"></div>

			<div id='cspacer'>
				<span class='heading'>GALLERY</span>
				<div id="gallery">

					<span>
					<img src="gallery/1.jpg">
					</span>
					<span>
					<img src="gallery/2.jpg">
					</span>
					<span>
						<img src="gallery/3.jpg">
						<br/>
						<img src="gallery/4.jpg">
					</span>
					<span>
						<img src="gallery/5.jpg">
						<br/>
						<img src="gallery/6.jpg">
					</span>
					<span>
					<img src="gallery/7.jpg">
					</span>
				</div>

			</div>
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


<script type="text/javascript">
	//$('#gallery a').colorbox({rel:'gal'});
</script>


</body>

</html> 