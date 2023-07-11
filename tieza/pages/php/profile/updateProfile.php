<?php 
	include('../../../config.php');
	include('../../../lib/libraries.php');
	error_reporting(0); 

	$subscriberID			= $_POST['subscriberID'];
	$emailAddress			= $_POST['emailAddress'];

	$contactCode	 		= $_POST['contactCode'];
	$contactNumber   		= $_POST['contactNumber'];
	$strNo	   	     		= $_POST['strNo'];
	$streetName 	 		= $_POST['streetName'];
	$region			 		= $_POST['region'];
	$province		 		= $_POST['province'];
	$municipality	 		= $_POST['municipality'];
	$barangay 				= $_POST['barangay'];
	$zipCode 				= $_POST['zipCode'];
	$passportNumber 		= $_POST['passportNumber'];
	$passportIssuingOffice  = $_POST['passportIssuingOffice'];
	$passportIssuedDate		= $_POST['passportIssuedDate'];
	$passportExpirationDate = $_POST['passportExpirationDate']; 
 
	//Passport Date Format
	$issuedDate = date_create($passportIssuedDate);
	$issuedDateFormat = date_format($issuedDate,"Y-m-d");  

	$expirationDate = date_create($passportExpirationDate);
	$expirationDateFormat = date_format($expirationDate,"Y-m-d"); 


	if($_POST['updateType'] == 'complete'){ 
		$response = array(); 
		$userData = array();
		$userData['COUNTRY_CODE_ID']	   = $contactCode;
		$userData['MOBILE_NO'] 			   = $contactNumber;
		$userData['UNIT_HOUSE']			   = $strNo;
		$userData['STREET'] 			   = $custom->upperCaseString($streetName);
		$userData['REGION'] 			   = $region;
		$userData['PROVINCE'] 			   = $province;
		$userData['MUNICIPALITY']		   = $municipality;
		$userData['BARANGAY']			   = $barangay;
		$userData['ZIPCODE'] 			   = $zipCode;
		$userData['PASSPORT_NO'] 		   = $passportNumber;
		$userData['PASSPORT_ISSUE_OFFICE'] = $custom->upperCaseString($passportIssuingOffice);
		$userData['PASSPORT_DUE_DATE'] 	   = $issuedDateFormat;
		$userData['PASSPORT_EXP_DATE'] 	   = $expirationDateFormat; 
		$userData['IS_DATA_COMPLETE'] 	   = '1'; 
	  	$userData['LAST_MODIFIED_DATE']    = $timeStamp;
	 	$userData['LAST_MODIFIED_BY']      = $custom->upperCaseString($emailAddress);
		$result = $dbTieza->update('tbl_account_profile', 'SUBSCRIBERS_ID', $subscriberID, $userData);	 
	 	  
	 	$infoStatus = 1;
	 	if($result){
	 		$infoStatus = 1; 
	 	}else{ 
	 		$infoStatus = 0;
	 	}

	 	$passportDIR = '../../../img/passport/'.$subscriberID.'';
	 	mkdir($passportDIR);

	 	$userPassportPicture = array(); 
		$uploadStatus = 1;
		if($_FILES['passportImg']['size'] != 0){    
			$targetDIR = $passportDIR.'/';
			$fileName   = date("Ymdhis");  
			$temp = explode(".", $_FILES["passportImg"]["name"]);
			$newfilename = round(microtime(true)) . '.' . end($temp);

			$target_file = $targetDIR . basename($_FILES["passportImg"]["name"]);
			$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
			
			// Check if image file is a actual image or fake image 
		    $check = getimagesize($_FILES["passportImg"]["tmp_name"]);
		    if($check !== false) { 
		        $uploadStatus = 1;
		    }else{ 
		        $uploadStatus = 0;
		    } 
			// Check if file already exists
			if (file_exists($target_file)) { 
			    $uploadStatus = 0;
			}  
			// Check if $uploadStatus is set to 0 by an error
			if ($uploadStatus == 0) {
			// if everything is ok, try to upload file
			} else {
			    if (move_uploaded_file($_FILES["passportImg"]["tmp_name"], $targetDIR.$newfilename)) { 
		           	$userPassportPicture['PASSPORT_IMAGE_PATH'] = $newfilename;  
			 		$resultUpdate = $dbTieza->update('tbl_account_profile', 'SUBSCRIBERS_ID', $subscriberID, $userPassportPicture);	 
			    }
			} 
		}
	 	 
	 	if($uploadStatus == 1 AND $infoStatus == 1){
			/****************************
				INSERT tbl_audit_trail
			*****************************/			 
			$auditArr = array();
			$auditArr['MODULE'] 	  	  = 'PROFILE'; 
			$auditArr['TRN']			  = 'N/A';
			$auditArr['EVENT_TYPE']    	  = 'UPDATE';
			$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
			$auditArr['EVENT_REMARKS'] 	  = 'PROFILE SUCCESSFULLY COMPLETED';
			$auditArr['CREATED_DATE']	  = $timeStamp;
			$auditArr['CREATED_BY']		  = $custom->upperCaseString($emailAddress);

			$insertAudit = $dbTieza->insert("tbl_audit_trail",$auditArr); 
		    
		    $response['STATUS']  = 'SUCCESS';
		    $response['TITLE']   = 'Successful';
		    $response['MESSAGE'] = 'Thank you for completing your profile information.';  	

	 	}else{ 
	 		if($uploaded == 0 AND $infoStatus == 1){
				/****************************
					INSERT tbl_audit_trail
				*****************************/			 
				$auditArr = array();
				$auditArr['MODULE'] 	  	  = 'PROFILE'; 
				$auditArr['TRN']			  = 'N/A';
				$auditArr['EVENT_TYPE']    	  = 'UPDATE';
				$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
				$auditArr['EVENT_REMARKS'] 	  = 'PASSPORT PICTURE NOT SUCCESSFULLY UPLOADED';
				$auditArr['CREATED_DATE']	  = $timeStamp;
				$auditArr['CREATED_BY']		  = $custom->upperCaseString($emailAddress);

				$insertAudit = $dbTieza->insert("tbl_audit_trail",$auditArr);

			    $response['STATUS']  = 'ERROR';
		    	$response['TITLE']   = 'Something went wrong';
			    $response['MESSAGE'] = 'Sorry, your file was not uploaded.'; 
	 		}elseif($infoStatus == 0 AND $uploaded == 1){				
	 			/****************************
					INSERT tbl_audit_trail
				*****************************/			 
				$auditArr = array();
				$auditArr['MODULE'] 	  	  = 'PROFILE'; 
				$auditArr['TRN']			  = 'N/A';
				$auditArr['EVENT_TYPE']    	  = 'UPDATE';
				$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
				$auditArr['EVENT_REMARKS'] 	  = 'PROFILE INFO NOT SUCCESSFULLY COMPLETED';
				$auditArr['CREATED_DATE']	  = $timeStamp;
				$auditArr['CREATED_BY']		  = $custom->upperCaseString($emailAddress);

				$insertAudit = $dbTieza->insert("tbl_audit_trail",$auditArr); 
			    
			    $response['STATUS']  = 'SUCCESS';
			    $response['TITLE']   = 'Something went wrong';
			    $response['MESSAGE'] = 'Sorry, your profile was not successfully updated. ';    
	 		}else{
	 			/****************************
					INSERT tbl_audit_trail
				*****************************/			 
				$auditArr = array();
				$auditArr['MODULE'] 	  	  = 'PROFILE'; 
				$auditArr['TRN']			  = 'N/A';
				$auditArr['EVENT_TYPE']    	  = 'UPDATE';
				$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
				$auditArr['EVENT_REMARKS'] 	  = 'PROFILE NOT SUCCESSFULLY UPDATED';
				$auditArr['CREATED_DATE']	  = $timeStamp;
				$auditArr['CREATED_BY']		  = $custom->upperCaseString($emailAddress);

				$insertAudit = $dbTieza->insert("tbl_audit_trail",$auditArr); 
			    
			    $response['STATUS']  = 'SUCCESS';
			    $response['TITLE']   = 'Something went wrong';
			    $response['MESSAGE'] = 'Sorry, your profile was not successfully updated. ';    
	 		}
	 	}

	}else{ //UPDATE
		$response = array(); 
		$userData = array();
		$userData['COUNTRY_CODE_ID']	   = $contactCode;
		$userData['MOBILE_NO'] 			   = $contactNumber;
		$userData['UNIT_HOUSE']			   = $strNo;
		$userData['STREET'] 			   = $custom->upperCaseString($streetName);
		$userData['REGION'] 			   = $region;
		$userData['PROVINCE'] 			   = $province;
		$userData['MUNICIPALITY']		   = $municipality;
		$userData['BARANGAY']			   = $barangay;
		$userData['ZIPCODE'] 			   = $zipCode;
		$userData['PASSPORT_NO'] 		   = $passportNumber;
		$userData['PASSPORT_ISSUE_OFFICE'] = $custom->upperCaseString($passportIssuingOffice);
		$userData['PASSPORT_DUE_DATE'] 	   = $issuedDateFormat;
		$userData['PASSPORT_EXP_DATE'] 	   = $expirationDateFormat;  
	  	$userData['LAST_MODIFIED_DATE']    = $timeStamp;
	 	$userData['LAST_MODIFIED_BY']      = $custom->upperCaseString($emailAddress);
		$result = $dbTieza->update('tbl_account_profile', 'SUBSCRIBERS_ID', $subscriberID, $userData);	 

	 	$infoStatus = 1;
	 	if($result){ 
	 		$infoStatus = 1; 
	 	}else{ 
	 		$infoStatus = 0;
	 	}

	 	$passportDIR = '../../../img/passport/'.$subscriberID.'/'; 

	 	$userPassportPicture = array(); 
		$uploadStatus = 1;
		if($_FILES['passportImg']['size'] != 0){    
			$targetDIR = $passportDIR;
			$fileName   = date("Ymdhis");  
			$temp = explode(".", $_FILES["passportImg"]["name"]);
			$newfilename = round(microtime(true)) . '.' . end($temp);

			$target_file = $targetDIR . basename($_FILES["passportImg"]["name"]);
			$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
			
			// Check if image file is a actual image or fake image 
		    $check = getimagesize($_FILES["passportImg"]["tmp_name"]);
		    if($check !== false) { 
		        $uploadStatus = 1;
		    }else{ 
		        $uploadStatus = 0;
		    } 
			// Check if file already exists
			if (file_exists($target_file)) { 
			    $uploadStatus = 0;
			}  
			// Check if $uploadStatus is set to 0 by an error
			if ($uploadStatus == 0) {
			    $response['STATUS']  = 'ERROR';
		    	$response['TITLE']   = 'Something went wrong';
			    $response['MESSAGE'] = 'Sorry, your file was not uploaded.';
			// if everything is ok, try to upload file
			} else { 
				unlink($targetDIR.$_POST['passportImageName']);
			    if (move_uploaded_file($_FILES["passportImg"]["tmp_name"], $targetDIR.$newfilename)) { 
		           	$userPassportPicture['PASSPORT_IMAGE_PATH'] = $newfilename;  
			 		$resultUpdate = $dbTieza->update('tbl_account_profile', 'SUBSCRIBERS_ID', $subscriberID, $userPassportPicture);	 
			    } else {
	    		    $response['STATUS']  = 'ERROR';
		    		$response['TITLE']   = 'Something went wrong';
			    	$response['MESSAGE'] = 'Sorry, your file was not uploaded.';
			    }
			} 
		}
	 	 
	 	if($uploadStatus == 1 AND $infoStatus == 1){
			/****************************
				INSERT tbl_audit_trail
			*****************************/			 
			$auditArr = array();
			$auditArr['MODULE'] 	  	  = 'PROFILE'; 
			$auditArr['TRN']			  = 'N/A';
			$auditArr['EVENT_TYPE']    	  = 'UPDATE';
			$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
			$auditArr['EVENT_REMARKS'] 	  = 'PROFILE SUCCESSFULLY COMPLETED';
			$auditArr['CREATED_DATE']	  = $timeStamp;
			$auditArr['CREATED_BY']		  = $custom->upperCaseString($emailAddress);

			$insertAudit = $dbTieza->insert("tbl_audit_trail",$auditArr); 
		    
		    $response['STATUS']  = 'SUCCESS';
		    $response['TITLE']   = 'Successful';
		    $response['MESSAGE'] = 'Thank you for completing your profile information.';  	

	 	}else{ 
	 		if($uploaded == 0 AND $infoStatus == 1){
				/****************************
					INSERT tbl_audit_trail
				*****************************/			 
				$auditArr = array();
				$auditArr['MODULE'] 	  	  = 'PROFILE'; 
				$auditArr['TRN']			  = 'N/A';
				$auditArr['EVENT_TYPE']    	  = 'UPDATE';
				$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
				$auditArr['EVENT_REMARKS'] 	  = 'PASSPORT PICTURE NOT SUCCESSFULLY UPLOADED';
				$auditArr['CREATED_DATE']	  = $timeStamp;
				$auditArr['CREATED_BY']		  = $custom->upperCaseString($emailAddress);

				$insertAudit = $dbTieza->insert("tbl_audit_trail",$auditArr);

			    $response['STATUS']  = 'ERROR';
		    	$response['TITLE']   = 'Something went wrong';
			    $response['MESSAGE'] = 'Sorry, your file was not uploaded.'; 
	 		}elseif($infoStatus == 0 AND $uploaded == 1){				
	 			/****************************
					INSERT tbl_audit_trail
				*****************************/			 
				$auditArr = array();
				$auditArr['MODULE'] 	  	  = 'PROFILE'; 
				$auditArr['TRN']			  = 'N/A';
				$auditArr['EVENT_TYPE']    	  = 'UPDATE';
				$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
				$auditArr['EVENT_REMARKS'] 	  = 'PROFILE INFO NOT SUCCESSFULLY COMPLETED';
				$auditArr['CREATED_DATE']	  = $timeStamp;
				$auditArr['CREATED_BY']		  = $custom->upperCaseString($emailAddress);

				$insertAudit = $dbTieza->insert("tbl_audit_trail",$auditArr); 
			    
			    $response['STATUS']  = 'SUCCESS';
			    $response['TITLE']   = 'Something went wrong';
			    $response['MESSAGE'] = 'Sorry, your profile was not successfully updated. ';    
	 		}else{
	 			/****************************
					INSERT tbl_audit_trail
				*****************************/			 
				$auditArr = array();
				$auditArr['MODULE'] 	  	  = 'PROFILE'; 
				$auditArr['TRN']			  = 'N/A';
				$auditArr['EVENT_TYPE']    	  = 'UPDATE';
				$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
				$auditArr['EVENT_REMARKS'] 	  = 'PROFILE NOT SUCCESSFULLY UPDATED';
				$auditArr['CREATED_DATE']	  = $timeStamp;
				$auditArr['CREATED_BY']		  = $custom->upperCaseString($emailAddress);

				$insertAudit = $dbTieza->insert("tbl_audit_trail",$auditArr); 
			    
			    $response['STATUS']  = 'SUCCESS';
			    $response['TITLE']   = 'Something went wrong';
			    $response['MESSAGE'] = 'Sorry, your profile was not successfully updated. ';    
	 		}
	 	}
	}
	echo json_encode($response);	
?>	