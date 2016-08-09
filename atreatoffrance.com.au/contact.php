<?php

if(isset($_POST['submit'])) {

	//Check to make sure that the name field is not empty
	if(trim($_POST['name']) == '') {
		$hasError = true;
	} else {
		$name = trim($_POST['name']);
	}

	//Check to make sure sure that a valid email address is submitted
	if(trim($_POST['email']) == '')  {
		$hasError = true;
	} else if (!eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", trim($_POST['email']))) {
		$hasError = true;
	} else {
		$email = trim($_POST['email']);
	}
}


if(isset($hasError)) { ?>

           
		   <script language="javascript" type="text/javascript">
	    alert('You have missed something');
		
		window.location = 'http://atreatoffrance.com.au/index.html';
	</script>
<?php }


if(!isset($hasError)){


$field_name = $_POST['name'];
$field_email = $_POST['email'];

$mail_to = 'info@atreatoffrance.com.au';
$subject = 'VIP Request from Client';

$body_message = 'Name: '.$field_name."\n";
$body_message .= 'Email: '.$field_email;



$headers = 'From: '.$field_email."\r\n";
$headers .= 'Reply-To: '.$field_email."\r\n";

$mail_status = mail($mail_to, $subject, $body_message, $headers);

if ($mail_status) { ?>
	<script language="javascript" type="text/javascript">
	    alert('Thank you for registering!')
		window.location = 'http://atreatoffrance.com.au/index.html';
	</script>
<?php
}
}
?>

