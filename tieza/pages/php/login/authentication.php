<?php 
	include('../../../config.php');
	include('../../../lib/libraries.php');
	session_start();
	error_reporting(0);
	
	$result = array(); 
	if( isset($_POST['email']) and isset($_POST['password']) ){
		$email = $_POST['email'];
		$password = $_POST['password'];

		$data = array();
		$data['EMAIL_ADDRESS'] = $email;
		$checkEmail = $dbTieza->getRow("SELECT tap.`SUBSCRIBERS_ID`, tap.`PASSWORD`, tap.`VARKEY` FROM tbl_account_profile AS tap 
			WHERE tap.`EMAIL_ADDRESS` = :EMAIL_ADDRESS AND tap.`IS_PASSWORD_VERIFIED` = '1' LIMIT 1", $data);
 
		if($checkEmail){ 
			$passwordcrypt 	= new hash_encryption($checkEmail->VARKEY);
			$dbpassword = $passwordcrypt->decrypt($checkEmail->PASSWORD);      
			if($dbpassword == $password){  
				$_SESSION['TIEZA']['LOGGED'] = 'true';     
				$_SESSION['TIEZA']['SUBSCRIBERS_ID'] = $checkEmail->SUBSCRIBERS_ID;   
				$_SESSION['TIEZA']['EMAIL_ADDRESS'] = $email;
				$result["STATUS"] = "success";  

		 		/****************************
					INSERT tbl_audit_trail
				*****************************/			 
				$auditArr = array();
				$auditArr['MODULE'] 	  	  = 'LOGIN'; 
				$auditArr['TRN']			  = 'N/A';
				$auditArr['EVENT_TYPE']    	  = 'SELECT';
				$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
				$auditArr['EVENT_REMARKS'] 	  = 'SUCCESSFULLY LOGGED IN';
				$auditArr['CREATED_DATE']	  = $timeStamp;
				$auditArr['CREATED_BY']		  = $email;

				$insertAudit = $dbTieza->insert("tbl_audit_trail",$auditArr); 
			}else{ 
				$result["STATUS"] = "error";
				$result["MESSAGE"] = "Invalid user credentials"; 
			} 
		}else{
			$result["STATUS"] = "error";
			$result["MESSAGE"] = "User does not exist or not activated"; 
		}
	} 
	echo json_encode($result);
?>