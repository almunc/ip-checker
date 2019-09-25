<?php
require("../ip.php");

$iphubApi = "IPHUB API KEY GOES HERE";
$proxycheckApi = "PROXYCHECK API KEY";


$IP = new IP();
if(!$IP->proxycheck($proxycheckApi)){
	die($IP->error["message"]);
}
echo "you are not using a VPN! allowed!";

