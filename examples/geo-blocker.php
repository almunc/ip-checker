<?php
require("../ip.php");

//array with disallowed countries
$DISALLOWED_COUNTRIES = array("DE", "FR", "GB", "IT", "US", "RU");

//message to show if a country has been blocked
$blockmsg = "Sadly we needed to block your country from our site ):";

$IP = new IP();

//blocked
if(in_array($IP->getCountryCode(), $DISALLOWED_COUNTRIES, TRUE)){
    die($blockmsg);
}

//not blocked
echo "Your country currently isn't blacklisted, come in!";
