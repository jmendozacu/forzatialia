<?php 
	$name=$_REQUEST['name']; 		// name 
	$email=$_REQUEST['email'];		// Email address
	$cname=$_REQUEST['cname'];  	// Club Name
	$conname=$_REQUEST['conname'];  // Contact Name
	$mname=$_REQUEST['mnname'];     // membership number 
	$attachment = chunk_split(base64_encode(file_get_contents($_FILES['attachment']['tmp_name'])));
    $filename = $_FILES['attachment']['name'];

// $htmlbody = " Your Mail Contant Here.... You can use html tags here...";
$htmlbody = "\n\nName :$name \n\nEmail :$email \n\nClub Name :$cname \n\nContact Number :$conname \n\nMembership Number :$mname";

$to = "Clubs@forzaitalia.com.au, support@forzaitalia.com.au"; //Recipient Email Address

$subject = "Forzaitalia Club Form"; // Email Subject

$headers = "From: $email \r\nReply-To: $to";

$random_hash = md5(date('r', time()));

$headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\"";

//$attachment = chunk_split(base64_encode(file_get_contents('logo.png'))); // Set your file path here

//define the body of the message.

$message = "--PHP-mixed-$random_hash\r\n"."Content-Type: multipart/alternative; boundary=\"PHP-alt-$random_hash\"\r\n\r\n";
$message .= "--PHP-alt-$random_hash\r\n"."Content-Type: text/plain; charset=\"iso-8859-1\"\r\n"."Content-Transfer-Encoding: 7bit\r\n\r\n";

//Insert the html message.
$message .= $htmlbody;
$message .="\r\n\r\n--PHP-alt-$random_hash--\r\n\r\n";

//include attachment
$message .= "--PHP-mixed-$random_hash\r\n"."Content-Type: application/zip; name=\"$filename\"\r\n"."Content-Transfer-Encoding: base64\r\n"."Content-Disposition: attachment\r\n\r\n";
$message .= $attachment;
$message .= "/r/n--PHP-mixed-$random_hash--";

//send the email
$mail = mail( $to, $subject , $message, $headers );

$return_msg = $mail ? "Your details submitted Successfully" : "Mail failed";

Header("Location: index.php?success=$return_msg#mail_msg");	

?>