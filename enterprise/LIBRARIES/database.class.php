<?php  
	class db{
		 
		function db($dbuser, $dbpass, $dbname, $dbhost) {
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

		function getRow($query, $values = array()){				   		
	   		$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	   		$this->conn->query('SET NAMES utf8'); 
	   		$stmt = $this->conn->prepare($query);
	   		$stmt->execute($values); 
			$result  = $stmt->fetch(PDO::FETCH_OBJ);
			return $result; 
		}

		function getResults($query, $values = array()){				   		
	   		$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	   		$this->conn->query('SET NAMES utf8');
	   		$stmt = $this->conn->prepare($query);
	   		$stmt->execute($values); 
			$result  = $stmt->fetchAll(PDO::FETCH_OBJ);
			return $result; 
		}

		function insert($tableName="",$values=array()){
			$fields			= "";
			$data_values	= "";
			$ctr			= 0;
			foreach($values as $key => $value)
			{
				if( $ctr == 0 )
				{ 
					$fields			.= "`$key`";
					$data_values	.= ":$key";
				}
				else
				{  
					$fields			.= ",`$key`";
					$data_values	.= ",:$key";	 
				}
				$ctr++;
			} 
			$sql = $this->conn->prepare("INSERT INTO `$tableName` ( $fields ) VALUES ($data_values)");
			$sql->execute($values);

			return $this->conn->lastInsertId(); 
		}

		function update($tableName, $id, $idValue, $values=array()){ 
			$fields			= "";
			$data_values	= "";
			$ctr			= 0;
			foreach($values as $key=> $value)
			{ 
				if( $ctr == 0 ){ 
					$fields	.= "`$key` = :$key"; 
				}
				else{  
					$fields	.= ",`$key` = :$key"; 	
				}
				$ctr++;
			}  
			$values[$id] = $idValue; 
			$sql = $this->conn->prepare("UPDATE `$tableName` SET $fields WHERE `$id`=:$id");
			$sql->execute($values); 
		}

		function delete($tableName="", $id="", $idValue){ 
			$value[$id] = $idValue;
			$sql = $this->conn->prepare("DELETE FROM $tableName WHERE `$id` = :$id"); 
			$sql->execute($value); 
		}
 
		function rawQuery(?string $query){
			$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	   		$this->conn->query('SET NAMES utf8');

	   		$stmt = $this->conn->prepare($query);
			$stmt->execute();  
		}
		
		function rawResults($query){				   		
	   		$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	   		$this->conn->query('SET NAMES utf8');
	   		$stmt = $this->conn->prepare($query); 
			$result  = $stmt->fetchAll(PDO::FETCH_OBJ);
			return $result; 
		}

		/***********************/
		/**IPG INTERPRISE ONLY**/
		/***********************/
		function checkMerchant($merchantCode){ 
			$search = $this->conn->prepare('SELECT * FROM vw_merchant_entity_rates WHERE MERCHANT_CODE = :MERCHANT_CODE LIMIT 1');
	   		
	   		$search->execute(array('MERCHANT_CODE' => $merchantCode));	   	 
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
			$search = $this->conn->prepare('SELECT * FROM tbl_transactions_hdr WHERE MERCHANT_REF_NUM = :MERCHANT_REF_NUM AND MERCHANT_CODE = :MERCHANT_CODE AND
				CREATED_DATE = (SELECT MAX(TTH.`CREATED_DATE`) FROM tbl_transactions_hdr AS TTH WHERE 
				TTH.`MERCHANT_REF_NUM` = :MERCHANT_REF_NUM_2 AND 
				TTH.`MERCHANT_CODE` = :MERCHANT_CODE_2 ORDER BY MAX(TTH.`CREATED_DATE`) DESC LIMIT 1)
				
				AND ((TRANS_STATUS = "2" AND PM_CODE = "UFP") OR TRANS_STATUS = "3") LIMIT 1 ');

				$search->execute(array('MERCHANT_REF_NUM' => $merchantRefNo, 'MERCHANT_CODE' => $merchantCode, 'MERCHANT_REF_NUM_2' => $merchantRefNo, 'MERCHANT_CODE_2' => $merchantCode));
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

		function getUserEmail($TRN){
			$search = $this->conn->prepare('SELECT * FROM tbl_transactions_hdr WHERE TRN = :TRN LIMIT 1');
			$search->execute(array('TRN' => $TRN));
			$search  = $search->fetch(PDO::FETCH_OBJ); 
			return $search->REQUESTOR_EMAIL_ADDRESS;
		}	

	}
?>