<?php


class Helper{

	function get_length_of_stay($arrival_date,$departure_date){						 
		$datediff = strtotime($departure_date) - strtotime($arrival_date);		 
		return floor($datediff/(60*60*24));			
	}
	
	function get_date_diff($arrival_date,$departure_date){						 
		$datediff = strtotime($departure_date) - strtotime($arrival_date);		 
		return ($datediff/(60*60*24));			
	}
	
	
	
	
	
	function searchForId($id, $array, $field) {
	   foreach ($array as $key => $val) {
		   if ($val[$field] === $id) {	
			   return $key;
		   }
	   }
	   return null;
	}
	
	
	
	function readable_date($date){ 
		return date("F d, Y",strtotime($date)); 
	}
	
	function readable_datetime($date){ 
		return date("F d, Y h:i:sa",strtotime($date)); 
	}	
	

	
	function reservation_number($property_id){
		$length = 6;
		$characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		
		switch($property_id){
			case 1:
				$prefix = '';
				$suffix = 'BOR';    
			break;
			case 2:
				$prefix = '';
				$suffix = 'COR';    
			break;
			case 3:
				$prefix = '';
				$suffix = 'CCT';    
			break;
		}
		
		for ($p = 0; $p < $length; $p++) {	$prefix .= $characters[mt_rand(0, strlen($characters))];	}
		//for ($p = 0; $p < $length; $p++) {	$suffix .= $characters[mt_rand(0, strlen($characters))];	}
		return $prefix.date("mdYHi", time())."-".$suffix;
	}
	
	function sessionID(){
		$length = 2;
		$characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$prefix = '';
		$suffix = ''; 
		for ($p = 0; $p < $length; $p++) {	$prefix .= $characters[mt_rand(0, strlen($characters))];	}
		return $prefix.date("mdYHi", time());
		
	}
	
	function varkeydump(){
		$length = 12;
		$characters = date("mdYgisu", time())."ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$characters .= strtolower("ABCDEFGHIJKLMNOPQRSTUVWXYZ");
		$key = '';
		    
		for ($p = 0; $p < $length; $p++) {
			$key .= $characters[mt_rand(0, strlen($characters))];	
		}
		return $key;
	}
	
	
	
	
	function curl_request($data,$url){
	
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
		curl_setopt( $ch, CURLOPT_ENCODING, 'gzip,deflate');
		curl_setopt( $ch, CURLOPT_POST, true );
		//curl_setopt( $ch, CURLOPT_HEADER,true);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		$result = curl_exec($ch);

		return $result;
		curl_close($ch);
	}
	
	
	function convert($amount,$from_Currency,$to_Currency){
		$amount = urlencode($amount);
		$from_Currency = urlencode($from_Currency);
		$to_Currency = urlencode($to_Currency);
		$get = file_get_contents("http://www.google.com/finance/converter?a=$amount&from=$from_Currency&to=$to_Currency");
		$get = explode("<span class=bld>",$get);
		$get = explode("</span>",$get[1]);
		return preg_replace("/[^0-9\.]/", null, $get[0]);
		/* return $from_Currency;*/
	}
	
	
	
	
	function my_number_encrypt($data, $key, $base64_safe=true, $shrink=true) {
        if ($shrink) $data = base_convert($data, 10, 36);
        $data = @mcrypt_encrypt(MCRYPT_ARCFOUR, $key, $data, MCRYPT_MODE_STREAM);
        if ($base64_safe) $data = str_replace('=', '', base64_encode($data));
        return $data;
	}

	function my_number_decrypt($data, $key, $base64_safe=true, $expand=true) {
			if ($base64_safe) $data = base64_decode($data.'==');
			$data = @mcrypt_encrypt(MCRYPT_ARCFOUR, $key, $data, MCRYPT_MODE_STREAM);
			if ($expand) $data = base_convert($data, 36, 10);
			return $data;
	}
	
	function check_data(){
		
		if(!$_POST){
		
			header('Location: '.WEBSITE_URL);
			
		}
	
	}
	
	
	function php_mailer_authenticate($sender_email = NULL,$sender_name = NULL,$receiver_email = NULL,$cc_emails = array(),$bcc_emails = array(),$subject = NULL,$email_body = NULL){
	
		global $mail;
		
	
		try {
		
			$mail->isMail();
			//$mail->isHTML(true);
			$mail->Host = 'smtp.gmail.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->SMTPSecure = 'tls';
			$mail->AuthType = 'CRAM-MD5';
			$mail->Username = '';
			$mail->Password = '';
			//$mail->Body = $email_body;
			
			$mail->Subject = $subject;
			$mail->clearAllRecipients();
			
			$mail->addReplyTo = $sender_email;
			$mail->setFrom($sender_email, $sender_name);
			$mail->addAddress($receiver_email,'');
			
			foreach($cc_emails as $cc_email){
				$mail->addCC($cc_email, "");
			}
			
			foreach($bcc_emails as $bcc_email){
				$mail->addBCC($bcc_email, "");
			}
			$mail->msgHTML($email_body);
			
			return $mail->send();
			//return 1;
		} catch (phpmailerException $e) {
			return $e->errorMessage(); //Pretty error messages from PHPMailer
		} catch (Exception $e) {
			return $e->getMessage(); //Boring error messages from anything else!
		}
		
	}
	
	
	
	
	
	
	function unique_id()
	{
		list($usec, $sec) = explode(" ", microtime());
		list($int, $dec) = explode(".", $usec);
		return $sec.$dec;   
	}


	function get_photo_size($file, $postfix="")
	{
		$dot = strrpos($file, '.');
		$ext = substr($file, $dot);		
		$basename = preg_replace('#\.[^.]*$#', '', $file);
		$filename = $basename.$postfix.$ext;

		return $filename;	
	}
	
	
	function currency_convert($from,$to,$amount,$withcurrency){
		$url = "http://www.google.com/finance/converter?a=$amount&from=$from&to=$to"; 
		$request = curl_init(); 
		$timeOut = 0; 
		curl_setopt ($request, CURLOPT_URL, $url); 
		curl_setopt ($request, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt ($request, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)"); 
		curl_setopt ($request, CURLOPT_CONNECTTIMEOUT, $timeOut); 
		$response = curl_exec($request); 
		curl_close($request); 
		$regularExpression	= '#\<span class=bld\>(.+?)\<\/span\>#s';
		preg_match($regularExpression, $response, $finalData);
		$data = strip_tags($finalData[0]);
		$nocurrency = explode(' ',$data);
		$response = $withcurrency?$data:$nocurrency[0];
		return $response;
	}
	
	
	
	function getUserIP()
	{
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
	
	
	

	function time_elapsed_string($datetime, $full = false) {
	
		$now = new DateTime;
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);

		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;

		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}

		if (!$full) $string = array_slice($string, 0, 1);
		return $string ? implode(', ', $string) . ' ago' : 'just now';
		
	}


	function countryCityFromIP($ipAddr)
	{
	   //function to find country and city name from IP address
	   //Developed by Roshan Bhattarai 
	   //Visit http://roshanbh.com.np for this script and more.
	  
	  //verify the IP address for the  
	  ip2long($ipAddr)== -1 || ip2long($ipAddr) === false ? trigger_error("Invalid IP", E_USER_ERROR) : "";
	  // This notice MUST stay intact for legal use
	  $ipDetail=array(); //initialize a blank array
	  //get the XML result from hostip.info
	  $xml = file_get_contents("http://api.hostip.info/?ip=".$ipAddr);
	  //get the city name inside the node <gml:name> and </gml:name>
	  preg_match("@<Hostip>(\s)*<gml:name>(.*?)</gml:name>@si",$xml,$match);
	  //assing the city name to the array
	  $ipDetail['city']=$match[2]; 
	  //get the country name inside the node <countryName> and </countryName>
	  preg_match("@<countryName>(.*?)</countryName>@si",$xml,$matches);
	  //assign the country name to the $ipDetail array 
	  $ipDetail['country']=$matches[1];
	  //get the country name inside the node <countryName> and </countryName>
	  preg_match("@<countryAbbrev>(.*?)</countryAbbrev>@si",$xml,$cc_match);
	  $ipDetail['country_code']=$cc_match[1]; //assing the country code to array
	  //return the array containing city, country and country code
	  return $ipDetail;
	} 
	function geoip($ip = "127.0.0.1"){

		if($ip == "127.0.0.1"){$ip = $_SERVER["REMOTE_ADDR"];}//if no IP specified use your own

		$ch = curl_init();//faster than file_get_contents()
		curl_setopt($ch, CURLOPT_URL,'http://www.geoplugin.net/php.gp?ip='.$ip);//fetch data from geoplugin.net
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		$curl = curl_exec($ch);
		curl_close($ch);

		$geoip = unserialize($curl);
		return $geoip["geoplugin_countryName"].":@:".$geoip["geoplugin_city"];//return country and city
	}

}


?>