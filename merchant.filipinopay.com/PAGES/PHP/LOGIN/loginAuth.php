<?php
	include('../..config.php');
	include('../..LIBRARIES/libraries.php');
	session_start();
	error_reporting(E_ALL);
	$timeStamp 	  	= date('Y-m-d G:i:s');
	$result = array(); 
	if( isset($_POST['username']) and isset($_POST['password']) ){

		$username = $_REQUEST['username'];
		$password 	  = $_REQUEST['password'];

		$userQueryInfo = array();
		$userQueryInfo['USERNAME'] = $username;
		$userQueryInfo['STATUS']   = '2';
		$userInfo = $dbMerchant->getRow("SELECT tma.`MERCHANT_CODE`,tma.`PASSWORD`,tma.`VARKEY`, tmi.`MERCHANT_EMAIL_ADDRESS` FROM tbl_merchant_account AS tma 

			INNER JOIN tbl_merchant_info AS tmi
			ON tmi.`MERCHANT_CODE` = tma.`MERCHANT_CODE`

			WHERE tma.`USERNAME` = :USERNAME AND STATUS != :STATUS LIMIT 1",$userQueryInfo); 
 
		if($userInfo){ 
			$passwordcrypt 	= new hash_encryption($userInfo->VARKEY);
			$dbpassword = $passwordcrypt->decrypt($userInfo->PASSWORD);      
			if($dbpassword == $password){  
				$_SESSION['MERCHANT']['LOGGED']  = 'true';   
				$_SESSION['MERCHANT']['MERCHANT_CODE'] 	 = $userInfo->MERCHANT_CODE;  
				$_SESSION['MERCHANT']['EMAILADDRESS'] 	 = $userInfo->MERCHANT_EMAIL_ADDRESS;  
				$result["result"] = "success";   	 			

				/****************************
					INSERT tbl_audit_trail
				*****************************/			 
				$auditArr = array();
				$auditArr['MERCHANT_CODE'] 	  = $userInfo->MERCHANT_CODE; 
				$auditArr['EVENT_TYPE']    	  = 'LOGIN';
				$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
				$auditArr['EVENT_REMARKS'] 	  = 'SUCCESSFULLY LOGIN';
				$auditArr['CREATED_DATE']	  = $timeStamp;
				$auditArr['CREATED_BY']		  = $userInfo->MERCHANT_EMAIL_ADDRESS;

				$insertAudit = $dbMerchant->insert("tbl_audit_trail",$auditArr);
			}else{ 
				$result["result"] = "error";
				$result["error_message"] = "Invalid user credentials"; 
			} 
		}else{ 
			$result["result"] = "error";
			$result["error_message"] = "User does not exist or not activated"; 
		}
	} 
	echo json_encode($result);	

?> 