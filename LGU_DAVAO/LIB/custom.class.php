<?php 
	class custom{
		
		function readableDate($date){ 
			return date("F d, Y",strtotime($date)); 
		}
		
		function readableDateTime($date){ 
			return date("F d, Y h:i:sa",strtotime($date)); 
		}	

		function upperCaseWords($string){ 
			return ucwords(strtolower($string));
		}

		function upperCaseString($string){ 
			return strtoupper(strtolower($string));
		}
		
		function getUserIP(){
			$client  = @$_SERVER['HTTP_CLIENT_IP'];
			$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
			$remote  = $_SERVER['REMOTE_ADDR'];

			if(filter_var($client, FILTER_VALIDATE_IP))
			{
				$ip = $client;
			}
			elseif(filter_var($forward, FILTER_VALIDATE_IP))
			{
				$ip = $forward;
			}
			else
			{
				$ip = $remote;
			}

			return $ip;
		}
		
		function showDescription($description, $length){
			if(strlen($description) >= $length){
				return substr($description, 0, $length)."...";
			}
			else{
				return $description;
			} 
		}
	}
?>