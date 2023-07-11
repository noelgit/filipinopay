<?php 
	//use Queries\{Insert, Select, Update, Delete};

	class db{
		 
		function dbDetails($dbuser, $dbpass, $dbname, $dbhost) {
			try {
			    $this->conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
			    // set the PDO error mode to exception
			    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			catch(PDOException $e){
			    echo "Connection failed: " . $e->getMessage();
			    die();
			}
		}
 
	    function getRow(?string $table = null, $values=array()) {
	   		
	   		$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	   		$this->conn->query('SET NAMES utf8');

	   		$keyValue = ""; 
			$ctr  = 0;  
			foreach($values as $key => $value) {
				if( $ctr == 0 ) { 
					$keyValue .= $key.' = "'.$value.'"';
				}
				else {  
					$keyValue .= ' AND '.$key.' = "'.$value.'"'; 
				}
				$ctr++;
			}   
	   		$stmt = $this->conn->prepare('SELECT * FROM '.$table.' WHERE '.$keyValue.' LIMIT 1');
			$stmt->execute(); 
			$row  = $stmt->fetch(PDO::FETCH_OBJ);
 
			return $row; 
	    	
	    }
 		
 		function getResults(?string $table = null, $values=array()) {

 			$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	   		$this->conn->query('SET NAMES utf8');
	   		
	   		$keyValue = ""; 
			$ctr  = 0;  
			foreach($values as $key => $value) {
				if( $ctr == 0 ) { 
					$keyValue .= $key.' = "'.$value.'"';
				}
				else {  
					$keyValue .= ' AND '.$key.' = "'.$value.'"'; 
				}
				$ctr++;
			}  
	   		$stmt = $this->conn->prepare('SELECT * FROM '.$table.' WHERE '.$keyValue);
			$stmt->execute(); 
			$result = $stmt->fetchAll(PDO::FETCH_OBJ); 

			return $result;
		}
		
		function query(?string $query){
			$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	   		$this->conn->query('SET NAMES utf8');

	   		$stmt = $this->conn->prepare($query);
			$stmt->execute(); 
			$result = $stmt->fetchAll(PDO::FETCH_OBJ); 

			return $result;
		}

		function rawQuery(?string $query){
			$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	   		$this->conn->query('SET NAMES utf8');

	   		$stmt = $this->conn->prepare($query);
			$stmt->execute();  
		}

		function queryRow(?string $query){
			$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	   		$this->conn->query('SET NAMES utf8');

	   		$stmt = $this->conn->prepare($query);
			$stmt->execute(); 
			//$result = $stmt->fetchAll(PDO::FETCH_OBJ); 
			$result  = $stmt->fetch(PDO::FETCH_OBJ);
			return $result;
		}

		function insert_all($tablename="",$values=array())
		{
			$timeStamp 	  = date('Y-m-d G:i:s');
			$fields			= "";
			$data_values	= "";
			$data_array 	= array();
			$data_column	= "";
			$ctr			= 0;
			foreach($values as $key=> $value)
			{
				if( $ctr == 0 )
				{ 				
					if($value == "now")
					{
						$fields				.= "`$key`";
						$data_values		.= "NOW()";
						//$data_array[':'.$key.''] = "NOW()";
						$data_column 	    .= "'$timeStamp'";

					}else{
						$fields				.= "`$key`";
						$data_values		.= "'$value'"; 
						$data_array[':'.$key.''] = "$value";
						$data_column 	    .= ":$key";
					}
				}
				else
				{ 
					if($value == "now")
					{
						$fields				.= ",`$key`";
						$data_values		.= ", NOW()"; 
						//$data_array[':'.$key.''] = "NOW()";
						$data_column 	    .= ", '$timeStamp'";
					}else{
						$fields				.= ",`$key`";
						$data_values		.= ",'$value'";	
						$data_array[':'.$key.''] = "$value";
						$data_column 	    .= ", :$key";
					}
				}
				$ctr++;
			}    
 
	   		$sql = $this->conn->prepare("INSERT INTO `$tablename` ( $fields ) VALUES ($data_column)");
	   		$sql->execute($data_array);
			//$sql->execute(); 
		}

	    function getCount(?string $table = null, $values=array()) {
	   		
	   		$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	   		$this->conn->query('SET NAMES utf8');

	   		$keyValue = ""; 
			$ctr  = 0;  
			foreach($values as $key => $value) {
				if( $ctr == 0 ) { 
					$keyValue .= $key.' = "'.$value.'"';
				}
				else {  
					$keyValue .= ' AND '.$key.' = "'.$value.'"'; 
				}
				$ctr++;
			}  
	   		$stmt = $this->conn->prepare('SELECT COUNT(*) FROM '.$table.' WHERE '.$keyValue.' LIMIT 1');
			$stmt->execute(); 
			$row  = $stmt->fetchColumn();
 
			return $row; 
	    	
	    }

		//Check errors 
		function checkMerchant($merchantCode){ 
			$search = $this->conn->prepare('SELECT * FROM vw_merchant_entity_rates WHERE MERCHANT_CODE = :MERCHANT_CODE LIMIT 1');
	   		
	   		$search->execute(array('MERCHANT_CODE' => $merchantCode));	   		
			//$search->execute();
			$searchResult = $search->fetchAll(PDO::FETCH_OBJ);  
			
			if($searchResult){
				return 1;
			}else{
				$jsonObj = array();
				$jsonObj['error_code'] = "-1002";
				$jsonObj['error'] = "invalid merchant code";
				$jsonObj['error_description'] = "Merchant code does not exist";
				return json_encode($jsonObj);
			}
		}		

		function checkSubmerchant($merchantCode, $subMerchantCode = array()){ 
			$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	   		$this->conn->query('SET NAMES utf8');
	   		
	   	  	$subMerchant = "";
	   	  	$ctr = 0;
	   	  	foreach($subMerchantCode AS $data){
	   	  		if( $ctr == 0 ){
					$subMerchant .= '"'.$data["'sub_merchant_code'"].'"';
				}else{
					$subMerchant .= ', "'.$data["'sub_merchant_code'"].'"';
				}  
 
	   	  		$ctr++;
	   	  	}  
 
	   	  	$countUniqueCode = count(array_unique(explode(", ",$subMerchant)));

	   		$stmt = $this->conn->prepare('SELECT COUNT(*) FROM vw_merchant_and_submerchant WHERE MERCHANT_CODE = :MERCHANT_CODE AND SUB_MERCHANT_CODE IN ('.$subMerchant.')');
			
			$stmt->execute(array('MERCHANT_CODE' => $merchantCode));	
			$stmt->execute();  

			//$result = $stmt->fetchAll(PDO::FETCH_OBJ); 
			$countResult = $stmt->fetchColumn(); 

			if($countUniqueCode == $countResult){
				return 1;
			}else{
				$jsonObj = array();
				$jsonObj['error_code'] = "-1003";
				$jsonObj['error'] = "invalid sub merchant code";
				$jsonObj['error_description'] = "Sub Merchant code is not exist";
				return json_encode($jsonObj);
			}
		}

		function checkTRN($TRN){ 	 
			$search = $this->conn->prepare('SELECT * FROM tbl_transactions_hdr WHERE TRN = :TRN LIMIT 1');
			
			$search->execute(array('TRN' => $TRN));
			$searchResult = $search->fetchAll(PDO::FETCH_OBJ);  
			
			if($searchResult){  
				return 1;
			}else{
				$jsonObj = array();
				$jsonObj['error_code'] = "-1004";
				$jsonObj['error'] = "invalid TRN";
				$jsonObj['error_description'] = "Transaction Reference number is not exist.";
				return json_encode($jsonObj);
			}

		}

		function checkEmailAddress($emailAddress){
			$emailAddress = filter_var($emailAddress, FILTER_SANITIZE_EMAIL);
			if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
			  return 1;
			} else {
				$jsonObj = array();
				$jsonObj['error_code'] = "-1005";
				$jsonObj['error'] = "invalid email address";
				$jsonObj['error_description'] = "Email address format is invalid";
				return json_encode($jsonObj);
			}
		}

		function checkMobileNumber($mobileNumber){
			if(is_numeric($mobileNumber)){
				return 1;
			}else{ 
				$jsonObj = array();
				$jsonObj['error_code'] = "-1006";
				$jsonObj['error'] = "invalid mobile number";
				$jsonObj['error_description'] = "Mobile number is not valid format";
				return json_encode($jsonObj);
			}
		}
 

		function checkAmount($amount = array()){
	   	  	$ctr = 0;
	   	  	$errorValue = 0;
			foreach($amount AS $data){
			   if(preg_match('/^[0-9]+\.[0-9]{2}$/', $data["'transaction_amount'"])){
				
				}else{
	   	  			$errorValue++; 
				} 
	   	  		$ctr++;  
			}
			if($errorValue >= 1){
				$jsonObj = array();
				$jsonObj['error_code'] = "-1007";
				$jsonObj['error'] = "invalid amount";
				$jsonObj['error_description'] = "The amount is invalid format use decimal values with two decimal places";
				return json_encode($jsonObj);
			}else{
				return 1;
			}

		}
		function checkMerchantRefNo($merchantRefNo, $merchantCode){
			$search = $this->conn->prepare('SELECT * FROM tbl_transactions_hdr WHERE MERCHANT_REF_NUM = :MERCHANT_REF_NUM AND MERCHANT_CODE = :MERCHANT_CODE LIMIT 1');

			$search->execute(array('MERCHANT_REF_NUM' => $merchantRefNo, 'MERCHANT_CODE' => $merchantCode));
			//$search->execute();
			$searchResult = $search->fetchAll(PDO::FETCH_OBJ);  
			
			if($searchResult){ 
				$jsonObj = array();
				$jsonObj['error_code'] = "-1008";
				$jsonObj['error'] = "invalid merchant reference number";
				$jsonObj['error_description'] = "The merchant reference number already exist.";
				return json_encode($jsonObj);
			}else{
				return 1;
			}
		}		

		function encrypt($key, $string){
			$iv = mcrypt_create_iv(
			    mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC),
			    MCRYPT_DEV_URANDOM
			);

			$encrypted = base64_encode(
			    $iv .
			    mcrypt_encrypt(
			        MCRYPT_RIJNDAEL_128,
			        hash('sha256', $key, true),
			        $string,
			        MCRYPT_MODE_CBC,
			        $iv
			    )
			);

			return $encrypted;
		}

		function getUserEmail($TRN){
			$search = $this->conn->prepare('SELECT * FROM tbl_transactions_hdr WHERE TRN = "'.$TRN.'" LIMIT 1');
			$search->execute();
			$search  = $search->fetch(PDO::FETCH_OBJ); 
			return $search->REQUESTOR_EMAIL_ADDRESS;
		}

		function getUserIP() {
		    // Get real visitor IP behind CloudFlare network
		    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
		              $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
		              $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
		    }
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


	}
?>