<?php
$deny = array("4.225.110.63", "93.182.132.103", "31.172.30.2", "70.167.202.103", "67.78.239.100", "74.15.112.131", "50.97.98.131");

if (in_array ($_SERVER['REMOTE_ADDR'], $deny))

{

header("location: http://www.google.com/");

exit();
}

?>
<?php
$ip = $_SERVER['REMOTE_ADDR'];
$pagina = $_SERVER['REQUEST_URI'];
$datum = date("d-m-y / H:i:s");
$invoegen = $datum . " - " . $ip . " - " . $pagina . "<br />";
$fopen = fopen("ips.html", "a");
fwrite($fopen, $invoegen);
fclose($fopen);
?>