<?php
require("../ip.php");

$iphubApi = "IPHUB API KEY GOES HERE";
$proxycheckApi = "PROXYCHECK API KEY";

$blockmessage = "Sorry, form here it seems like you are using a Proxy/VPN or Tor! For security reasons we can't allow these kind of annonymous connections.";


$IP = new IP();
if(!$IP->proxycheck($proxycheckApi)){
	die($IP->error["message"]);
}
echo "you are not using a VPN! allowed!";

