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

		function getSecureParam($link, $method){
	 	    $entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
   			$replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]");

   			if($method == "GET"){
    			return str_replace($entities, $replacements, urlencode($link));
   			}else{
    			return str_replace($entities, $replacements, $link); 
   			}		
   		}

   		function generateRandomString($length) {
		    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		    $charactersLength = strlen($characters);
		    $randomString = '';
		    for ($i = 0; $i < $length; $i++) {
		        $randomString .= $characters[rand(0, $charactersLength - 1)];
		    }
		    return $randomString;
		}

	}
?>