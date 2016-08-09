<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Forza Italia Partner Club Member</title>
        <link rel="icon" href="http://www.forzaitalia.com.au/media/favicon/default/forzafavicon.png" type="image/x-icon" />
        <link rel="shortcut icon" href="http://www.forzaitalia.com.au/blog/wp-content/themes/forzaitalia/favicon.ico" />

        <link rel="stylesheet" href="css/foundation.css" />
        <link rel="stylesheet" href="css/app.css" />
        <script src="js/vendor/modernizr.js"></script>
    </head>
    <body>
        <div class="header-new">
            <div class="new-top">
                <div class="row">
                    <div class="large-3 columns">
                        <span class="logo-light"></span>
                        <div class="logo" class="three columns">
                            <a href="index.php"><img src="img/forza-italia-official-partner.png" alt=""/></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="main-title">
                <div class="row text-center">
                    <h1 class="pagetile">SIGN UP FORM</h1>
                </div>
            </div>
        </div>
        <div class="clearfix">
            <div class="outermain">
                <div class="row">
                    <div class="large-8 columns">
                        <h6>PARTNER CLUB MEMBER</h6>
                        <hr/>
						<div id="mail_msg" style="color:#458B00; padding-bottom:15px;">
							<?php
							if (isset($_GET['success']))
								{
								// treat the succes case ex:
								$message = $_GET['success'];
								echo "$message";
								}
							?>
     									
						</div>

                        <form action="process.php" method="post" enctype="multipart/form-data" data-abide>
                            <div class="row">
                                <div class="large-6 medium-6 columns">
                                    <div class="name-field"> 
                                        <label>Name <small>required</small> 
                                        <input type="text" name="name" id="name" required pattern="[a-zA-Z]+" placeholder="Your Name"> </label> 
                                        <small class="error">Name is required.</small> 
                                    </div>
                                </div>
                                <div class="large-6 medium-6 columns">
                                    <div class="email-field"> 
                                        <label>Email <small>required</small> <input type="email" name="email" id="email" placeholder="Email" required> </label> 
                                        <small class="error">An email address is required.</small> 
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="large-6 medium-6 columns">
                                    <div class="name-field"> 
                                        <label>Club Name <small>required</small> 
                                        <input type="text" required pattern="[a-zA-Z]+" name="cname" id="cname" placeholder="Club Number"> </label> 
                                        <small class="error">Club Name is required.</small> 
                                    </div>
                                </div>
                                <div class="large-6 medium-6 columns">
                                    <label>Contact Number </label>
                                    <input type="text" placeholder="Contact Number" name="conname" id="conname" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="large-12 columns">
                                    <div class="name-field">
                                        <label>Membership Number <small>required</small> 
                                        <input type="text" required pattern="integer" name="mnname" id="mnname" placeholder="Membership Number"> </label> 
                                        <small class="error">Membership Number is required.</small> 
                                        <p style="font-size:12px;">Please Note : Your details will be forwarded to your club for verification.
                                            Alternatively, you can attach a photo of your membership card below
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="large-12 columns">
                                    <label>Attachment</label>
                                    <input type="file" name='attachment' id='attachment' /><br/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="large-12 columns text-left">
                                    <input type='submit' name='Submit' value='Submit' class="button small" />
                                </div>
                            </div>
                        </form>
                        <br />
                    </div>
                    <div class="large-4 columns">
				<div class="large-12 columns">
					<h5 class="widget-title">Contact Us</h5>
					
					<p style="font-size:13px;">
					<strong>Forza Italia</strong><br/>
                    204 Lygon st, Carlton<br/>
                    VIC 3053 Australia</p>
                                    
<p style="font-size:13px;">Tel: 03 9654 6660 <br/>
Email: <a href="mailto:clubs@forzaitalia.com.au">clubs@forzaitalia.com.au</a><br/>
Shop Online: <a href="www.forzaitalia.com.au" >www.forzaitalia.com.au</a> </p>
					
				</div>
				
				<div class="large-12 columns">
					<h2 class="widget-title"></h2>
					
					<p><iframe width="auto" height="auto" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?q=FORZA%20ITALIA%2C%20204%20Lygon%20St%2C%20Carlton%2C%20Victoria%2C%20Australia&key=AIzaSyAKEgy-xoZo-OWq-QqrFqDqlZYsZjBoNiA"></iframe></p>
					
				</div>
				
			</div>
                </div>
            </div>
        </div>
        <!-- Footer -->
        <footer class="footer">
            <div class="row">
                <div class="large-12 columns text-left">
                    <i class="fi-laptop"></i>
			  <p> © Forza Italia™ 2015 | All rights reserved. </p>
                </div>
            </div>
        </footer>
        <script src="js/vendor/jquery.js"></script>
        <script src="js/foundation.min.js"></script>
        <script>
            $(document).foundation();
        </script>
    </body>
</html>

