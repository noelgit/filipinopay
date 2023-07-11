<?php 
	include('../../../config.php');
	include('../../../lib/libraries.php');
	session_start();
	error_reporting(0);
	
	$email 	    = $_POST['email'];
	$firstName  = $_POST['firstName'];
	$middleName = $_POST['middleName'];
	$lastName   = $_POST['lastName'];
	$birthday   = $_POST['birthday'];
	$password   = $_POST['password'];

	$fullName   = $firstName.' '.$lastName;
	$data = array();
	$data['EMAIL_ADDRESS'] = $email;   
	$response = array();
	if($custom->validateAge($birthday)){ 
		$checkEmail = $dbTieza->getRow("SELECT COUNT(*) AS COUNT FROM tbl_account_profile AS tap WHERE tap.`EMAIL_ADDRESS` = :EMAIL_ADDRESS LIMIT 1",$data);
		if($checkEmail->COUNT >= 1){
			$response['STATUS']  = 'error';
			$response['TITLE']   = 'Information';
			$response['MESSAGE'] = 'Email Address already in use.';
		}else{ 
			$vardumkey 	   = $helper->varkeydump();
			$passwordcrypt = new hash_encryption($vardumkey);  
			
			//Birthday Format
			$date = date_create($birthday);
			$birthdayFormat = date_format($date,"Y-m-d"); 

			//TABLE ACCOUNT PROFILE
			$tbl_account_profile = array();  
		    $tbl_account_profile['FIRST_NAME']     = $custom->upperCaseString($firstName);
		    $tbl_account_profile['MIDDLE_NAME']	   = $custom->upperCaseString($middleName);
		    $tbl_account_profile['LAST_NAME']	   = $custom->upperCaseString($lastName);
			$tbl_account_profile['EMAIL_ADDRESS']  = $custom->upperCaseString($email);
			$tbl_account_profile['PASSWORD'] 	   = $passwordcrypt->encrypt($password); 
		    $tbl_account_profile['IS_PASSWORD_VERIFIED'] = '0';
		    $tbl_account_profile['BIRTH_DATE']     = $birthdayFormat;
			$tbl_account_profile['VARKEY']	   	   = $vardumkey; 
		 	$tbl_account_profile['IS_DATA_COMPLETE'] = '0'; 
		 	$tbl_account_profile['CREATED_DATE']  = $timeStamp;
		 	$tbl_account_profile['CREATED_BY']    = $custom->upperCaseString($email); 
		  	
		 	$SUBSCRIBERS_ID = $dbTieza->insert('tbl_account_profile', $tbl_account_profile);  

		 	if($SUBSCRIBERS_ID){ 
		 		$link = BASE_URL.'?verificationCode='.$tbl_account_profile['PASSWORD']; 
		 		$emailSent = $emailTemplate->verificationEmail($email, $fullName, $link);

		 		/****************************
					INSERT tbl_audit_trail
				*****************************/			 
				$auditArr = array();
				$auditArr['MODULE'] 	  	  = 'SIGN UP'; 
				$auditArr['TRN']			  = 'N/A';
				$auditArr['EVENT_TYPE']    	  = 'ADD';
				$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
				$auditArr['EVENT_REMARKS'] 	  = 'SUCCESSFULLY SIGN UP';
				$auditArr['CREATED_DATE']	  = $timeStamp;
				$auditArr['CREATED_BY']		  = $custom->upperCaseString($email);

				$insertAudit = $dbTieza->insert("tbl_audit_trail",$auditArr);

				$response['STATUS']  = 'success';
				$response['TITLE']   = 'Sign up Successful';
				$response['MESSAGE'] = 'Please check your email and activate your account.';	 		 
		 	}else{
				$response['STATUS']  = 'error';
				$response['TITLE']   = 'Information';
				$response['MESSAGE'] = 'Something went wrong.';	 		
		 	}
		}
	}else{
		$response['STATUS']  = 'error';
		$response['TITLE']   = 'Information';
		$response['MESSAGE'] = 'You must be 18 years old and above to continue.';	 			
	}
 
	echo json_encode($response);	
?>