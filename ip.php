<?php

class IP{
	//Cunstructor is called everytime a new instance of the class Ip is created.
	public function __construct(){
		//Determine clients IP with get method
		$this->ip = $this->get();
	}

	//Get users IP
	function get(){
		if(isset($_SERVER['HTTP_CLIENT_IP']))
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_X_FORWARDED']))
			$ip = $_SERVER['HTTP_X_FORWARDED'];
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ip = $_SERVER['HTTP_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_FORWARDED']))
			$ip = $_SERVER['HTTP_FORWARDED'];
		else if(isset($_SERVER['REMOTE_ADDR']))
			$ip = $_SERVER['REMOTE_ADDR'];
		else
			die("Unknown IP");
		return $ip;
	}

	//Check if the IP is flagged as bad ip on Proxycheck
	function proxycheck($api = NULL){
		if(strlen($api) > 25){
			$url = "https://proxycheck.io/v2/".$this->ip."?api={$api}&VPN=1&asn=1&node=1&time=1&port=1&seen=1&days=7&tag=msg";
		}
		else{
			$url = "https://proxycheck.io/v2/".$this->ip."?VPN=1&asn=1&node=1&time=1&port=1&seen=1&days=7&tag=msg";
		}
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch,CURLOPT_FAILONERROR, TRUE); //trigger an exception if HTTP status >= 400 (error)
		$proxycheck = json_decode(curl_exec($ch), TRUE);
		curl_close($ch);

		//check result
		if(!is_array($proxycheck)){
			$this->error = array("code" => 1, "message" => "Failed to reach our third party Ip checking service proxcheck.io to validate your IP. {$proxycheck}");
			return FALSE;
		}

		//Proxycheck api request limit has been hit
		if($proxycheck["status"] === "denied"){
			$this->error = array("code" => 2, "message" => $proxycheck["message"]);
			return FALSE;
		}

		//Proxycheck found the IP in their proxy/VPN/tor exit node db
		if($proxycheck[$this->ip]["proxy"] === "yes"){
			$this->error = array("code" => 3, "message" => "Sorry, form here it seems like you are using a Proxy/VPN or Tor! For security reasons we can't allow these kind of annonymous connections. (discovered by: proxycheck.io)");
			return FALSE;
		}

		//Proxycheck returned an error
		if($proxycheck["status"] === "error"){
			$this->error = array("code" => 0, "message" => "Our third party Ip checking service proxcheck.io retunred an unknown error: ".$proxycheck["message"]);
			return FALSE;
		}

		//everything went fine, allow IP
		return TRUE;
	}

	//Check if the IP is flagged as bad ip on Iphub
	function iphub($api){
		$ch = curl_init("http://v2.api.iphub.info/ip/{$this->ip}");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Key: ".$api));
		curl_setopt($ch,CURLOPT_FAILONERROR, TRUE); //trigger an exception if HTTP status >= 400 (error)
		$iphub = @json_decode(curl_exec($ch), TRUE);
		curl_close($ch);

		//check result
		if(!is_array($iphub)){
			$this->error = array("code" => 1, "message" => "Failed to reach our third party Ip checking service iphub.info to validate your IP.");
			return FALSE;
		}
		
		//Iphub returned an error, most likely because api request limit was exceeded
		if(isset($iphub["error"])){
			//sadly iphub doesn't return an error message
			$this->error = array("code" => 0, "message" => "Our third party Ip checking service proxcheck.io retunred an unknown error.");
			return FALSE;
		}

		//Iphub found the IP in their proxy/VPN/tor exit node db
		if($iphub["block"] != 0){
			$this->error = array("code" => 3, "message" => "Sorry, form here it seems like you are using a Proxy/VPN or Tor! For security reasons we can't allow these kind of annonymous connections. (discovered by: iphub.info)");
			return FALSE;
		}

		//everything went fine, allow IP
		return TRUE;
	}

	//Check geo Location using Ipstack api
	function ipstack($api){
		$ipstack = @json_decode(file_get_contents("http://api.ipstack.com/".$this->ip."?access_key=".$api), TRUE);
		if(isset($ipstack["error"])){
			$this->error = array("code" => 2, "message" => "Our third party Ip checking service proxcheck.io returned the following error: {$ipstack["error"]}");
			return FALSE;
		}
		return $ipstack;
	}
	
	function ipapi(){
		$fget = file_get_contents("http://ip-api.com/json/".$this->ip."?fields=175647");
		$ipapi = json_decode($fget ,true);
		if(isset($ipapi["message"])){
			return array(FALSE);
		}
		return $ipapi;
	}
	
	//Only get the 2-letter country code (e.g. US, RU) using ipapi
	function getCountryCode(){
		if(strlen($res = @json_decode(file_get_contents("http://ip-api.com/json/".$this->ip."?fields=175647"), TRUE)["countryCode"]) == 2){
			return $res;
		}

		$this->error = array("code" => 2, "message" => "Our third party Ip checking service ipapi.com returned anb unknown error.");
		return FALSE;
	}
}
