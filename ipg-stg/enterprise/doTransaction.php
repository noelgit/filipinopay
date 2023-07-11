<?php 
	// include our OAuth2 Server object
	require_once __DIR__.'/server.php'; 

	// Handle a request to a resource and authenticate the access token
	if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
	    $server->getResponse()->send(); 
	    die;
	}else{ 
 
 		//Check if merchant code is valid 
 		$merchant 		= $dbEnterprise->checkMerchant($_POST['merchant_code']);
 		$subMerchant 	= $dbEnterprise->checkSubmerchant($_POST['merchant_code'], $_POST['transactions']); 
 		$emailAddress 	= $dbEnterprise->checkEmailAddress($_POST['requestor_email_address']);
 		$mobileNumber 	= $dbEnterprise->checkMobileNumber($_POST['requestor_mobile_no']);
		$amount 		= $dbEnterprise->checkAmount($_POST['transactions']);
 		$merchantRefNo  = $dbEnterprise->checkMerchantRefNo($_POST['merchant_ref_num'], $_POST['merchant_code']);

		$merchantCheck      = is_numeric($merchant) ? 1 : 0 ;
		$subMerchantCheck   = is_numeric($subMerchant) ? 1 : 0 ;
		$emailAddressCheck  = is_numeric($emailAddress) ? 1 : 0 ;
		$mobileNumberCheck  = is_numeric($mobileNumber) ? 1 : 0 ;
		$amountCheck  		= is_numeric($amount) ? 1 : 0 ;
		$merchantRefNoCheck = is_numeric($merchantRefNo) ? 1 : 0 ;
		
		$totalNoError 	= $merchantCheck + $subMerchantCheck + $emailAddressCheck + $mobileNumberCheck + $amountCheck + $merchantRefNoCheck; 
		if($totalNoError == 6){
			$jsonResponse = array();
			
			$date = date("ymd");
			$time = date("His"); 
			
			$countQueryData = array();
			$countQueryData['MERCHANT_CODE'] = $_POST['merchant_code'];
			$countQueryData['TRANS_STATUS'] = "1";
			$countQuery = $dbEnterprise->getRow("SELECT COUNT(*) AS countColumn FROM tbl_transactions_hdr 
				WHERE MERCHANT_CODE = :MERCHANT_CODE AND TRANS_STATUS = :TRANS_STATUS LIMIT 1",$countQueryData);

			//$count = $dbEnterprise->getCount("tbl_transactions_hdr", $countData); 
			$count = $countQuery->countColumn += 1; 

			//$reference_id = $date.$time.$count; 
			$reference_id = $date.$time; 

			//Encrypt reference_id for secureparam
			$token = $reference_id;  
			$getMerchantCode = $_POST['merchant_code'];
			
			$encryption_key = $getMerchantCode;
			$cryptor = new Cryptor($encryption_key);
			$secureparam = $cryptor->encrypt($token);
			unset($token);

			$jsonResponse['reference_id'] = $reference_id;
			$jsonResponse['status_code']  = "200";
			$jsonResponse['transaction_status'] = "1";
			$jsonResponse['transaction_status_desc'] = "PROCESSING";
			$jsonResponse['merchant_ref_num'] = $_POST['merchant_ref_num'];
			$jsonResponse['redirection_link'] = PUBLIC_URL."?secureparam=".$secureparam."&merchantcode=".$_POST['merchant_code']; 
			$ctr = 0;
 	
			foreach($_POST['transactions'] AS $dataVal){

				$jsonResponse['transactions'][$ctr]["'sub_merchant_code'"] = $dataVal["'sub_merchant_code'"];
				$jsonResponse['transactions'][$ctr]["'transaction_payment_for'"] = $dataVal["'transaction_payment_for'"];
				$jsonResponse['transactions'][$ctr]["'transaction_amount'"] = $dataVal["'transaction_amount'"]; 
				$ctr++;

			}
 
			/*****************************
				INSERT tbl_transactions
			*****************************/ 
			foreach($_POST['transactions'] AS $dataVal){
				$transactionArr = array();
				$transactionArr['TRN']					   = $reference_id;
				$transactionArr['SUB_MERCHANT_CODE']	   = $dataVal["'sub_merchant_code'"];
				$transactionArr['TRANSACTION_PAYMENT_FOR'] = $custom->upperCaseString($dataVal["'transaction_payment_for'"]);
				$transactionArr['TRANSACTION_AMOUNT'] 	   = $dataVal["'transaction_amount'"]; 
				$transactionArr['CREATED_DATE']			   = $timeStamp;
				$transactionArr['CREATED_BY']			   = $custom->upperCaseString($_POST['requestor_email_address']);
				$insertTransaction = $dbEnterprise->insert("tbl_transactions",$transactionArr);
			}
 
 			/***********************************
				INSERT tbl_transactions_hdr
			***********************************/
			$transactionhdrArr = array();
			$transactionhdrArr['TRN'] 					  = $reference_id;
			$transactionhdrArr['MERCHANT_CODE'] 		  = $_POST['merchant_code'];
			$transactionhdrArr['MERCHANT_REF_NUM'] 		  = $_POST['merchant_ref_num'];
			$transactionhdrArr['SUCCESS_RETURN_URL'] 	  = $_POST['success_return_url'];  
			$transactionhdrArr['FAILED_RETURN_URL'] 	  = $_POST['failed_return_url'];
			$transactionhdrArr['REQUESTOR_NAME'] 	 	  = $custom->upperCaseString($_POST['requestor_name']);
			$transactionhdrArr['REQUESTOR_FIRSTNAME']     = $custom->upperCaseString($_POST['requestor_fname']);
			$transactionhdrArr['REQUESTOR_MIDDLENAME']    = $custom->upperCaseString($_POST['requestor_mname']);
			$transactionhdrArr['REQUESTOR_LASTNAME'] 	  = $custom->upperCaseString($_POST['requestor_lname']);
			$transactionhdrArr['REQUESTOR_EMAIL_ADDRESS'] = $custom->upperCaseString($_POST['requestor_email_address']);
			$transactionhdrArr['REQUESTOR_MOBILE_NO'] 	  = $_POST['requestor_mobile_no'];
			$transactionhdrArr['PM_CODE'] 				  = "";
			$transactionhdrArr['PE_CODE'] 				  = "";
			$transactionhdrArr['TRANS_STATUS']			  = "1";
			$transactionhdrArr['CREATED_DATE']			  = $timeStamp;
			$transactionhdrArr['CREATED_BY']			  = $custom->upperCaseString($_POST['requestor_email_address']); 
			$insertTransactionHdr = $dbEnterprise->insert("tbl_transactions_hdr",$transactionhdrArr);
 
 			/****************************
				INSERT tbl_audit_trail
			*****************************/			 
			$auditArr = array();
			$auditArr['TRN'] 		 	  = $reference_id;
			$auditArr['MERCHANT_CODE']	  = $_POST['merchant_code'];
			$auditArr['EVENT_TYPE']    	  = 'ADD';
			$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
			$auditArr['EVENT_REMARKS'] 	  = 'DO TRANSACTION';
			$auditArr['CREATED_DATE']	  = $timeStamp;
			$auditArr['CREATED_BY']		  = $custom->upperCaseString($_POST['requestor_email_address']);
			$insertAudit = $dbEnterprise->insert("tbl_audit_trail",$auditArr);

			print_r(json_encode($jsonResponse)); 
 
		}else{ 
			$arrayVal = array();
			is_numeric($merchant) ? '' : array_push($arrayVal, json_decode($merchant));
			is_numeric($subMerchant) ? '' : array_push($arrayVal, json_decode($subMerchant));
			is_numeric($emailAddress) ? '' : array_push($arrayVal, json_decode($emailAddress));
			is_numeric($mobileNumber) ? '' : array_push($arrayVal, json_decode($mobileNumber));
			is_numeric($amount) ? '' : array_push($arrayVal, json_decode($amount));
			is_numeric($merchantRefNo) ? '' : array_push($arrayVal, json_decode($merchantRefNo));

			print_r(json_encode($arrayVal)); 
		}
	}
?>