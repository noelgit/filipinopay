<?php
	include('../../../config.php');
	include('../../../LIBRARIES/libraries.php');
	session_start();
	error_reporting(0);
	$timeStamp 	  	= date('Y-m-d G:i:s');
	
	$result = array(); 
	if(isset($_POST['username']) and isset($_POST['password']) ){

		$username = $_REQUEST['username'];
		$password = $_REQUEST['password'];

		$userQueryInfo = array();
		$userQueryInfo['USERNAME'] = $username;
		$userQueryInfo['STATUS']   = '2';
		$userInfo = $dbJV->getRow("SELECT tca.`ACCOUNT_ID`,tca.`COMPANY_CODE`,tca.`PASSWORD`, tca.`VARKEY`, tci.`COMPANY_EMAIL_ADDRESS`, trut.`USER_TYPE_CODE`
			FROM tbl_company_account AS tca 
			
			INNER JOIN tbl_company_info AS tci
			ON tci.`COMPANY_CODE` = tca.`COMPANY_CODE`
			
			LEFT JOIN tbl_user_assigned_access AS tuaa
			ON tuaa.`USERNAME` = tca.`USERNAME`
			
			LEFT JOIN tbl_ref_user_type AS trut
			ON trut.`USER_TYPE_ID` = tuaa.`USER_TYPE_ID`
			
			WHERE tca.`USERNAME` = :USERNAME  AND tca.`STATUS` != :STATUS LIMIT 1",$userQueryInfo); 
 
		if($userInfo){ 
			$passwordcrypt 	= new hash_encryption($userInfo->VARKEY);
			$dbpassword = $passwordcrypt->decrypt($userInfo->PASSWORD);      
			if($dbpassword == $password){  
				$_SESSION['JV']['LOGGED']  		 = 'true';   
				$_SESSION['JV']['COMPANY_CODE']  = $userInfo->COMPANY_CODE;  
				$_SESSION['JV']['USER_CODE']  	 = $userInfo->USER_TYPE_CODE;  
				$_SESSION['JV']['EMAILADDRESS']  = $userInfo->COMPANY_EMAIL_ADDRESS;  
				$result["result"] = "success";   

	 			/****************************
					INSERT tbl_audit_trail
				*****************************/			 
				$auditArr = array();
				$auditArr['COMPANY_CODE'] 	  = $userInfo->COMPANY_CODE; 
				$auditArr['EVENT_TYPE']    	  = 'LOGIN';
				$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
				$auditArr['EVENT_REMARKS'] 	  = 'SUCCESSFULLY LOGIN';
				$auditArr['CREATED_DATE']	  = $timeStamp;
				$auditArr['CREATED_BY']		  = $userInfo->COMPANY_EMAIL_ADDRESS;

				$insertAudit = $dbJV->insert("tbl_audit_trail",$auditArr);
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