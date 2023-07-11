<?php 
	// include our OAuth2 Server object
	require_once __DIR__.'/server.php';  

	if (!isset($_SERVER['PHP_AUTH_USER'])) {
	    header('WWW-Authenticate: Basic realm="Authentication System"');
	    header('HTTP/1.0 401 Unauthorized'); 
	     die ("Not authorized");
	} else { 
 
    	$getPaymentMethod = $_POST['pm_code'];
    	$getPaymentEntity = $_POST['pe_code'];
	 	$getMerchantCode  = $_POST['merchant_code'];
		$getSecureParam   = $custom->getSecureParam($_POST['secureparam'], "POST");

		$encryption_key = $getMerchantCode;
		$Cryptor = new Cryptor($encryption_key);
		$TRN = $Cryptor->decrypt($getSecureParam);    
		
		$merchantcodeExist = $dbEnterprise->checkMerchant($getMerchantCode);
		$TRNExist 		   = $dbEnterprise->checkTRN($TRN); 
		
		$merchantcodeExistCheck  = is_numeric($merchantcodeExist) ? 1 : 0 ;
		$TRNExistCheck  		 = is_numeric($TRNExist) ? 1 : 0 ;

		$totalNoError 	   = $merchantcodeExistCheck + $TRNExistCheck; 
		
		if($totalNoError == 2){  
			$modePaymentArr = array(); 
			$modePaymentArr['TRN'] = $TRN;
			$modePaymentArr['MERCHANT_CODE'] = $getMerchantCode; 
			$modePaymentArr['TRANS_STATUS'] = '2'; 

			$transaction = $dbEnterprise->getRow("SELECT * FROM tbl_transactions_hdr WHERE TRN = :TRN AND MERCHANT_CODE = :MERCHANT_CODE AND TRANS_STATUS = :TRANS_STATUS LIMIT 1", $modePaymentArr);
  			
  			if($transaction){ 	
				$transactionHDR = $dbEnterprise->rawQuery("UPDATE tbl_transactions_hdr SET 
					PM_CODE = '".$getPaymentMethod."', 
					PE_CODE = '".$getPaymentEntity."', 
					LAST_MODIFIED_DATE = '".$timeStamp."',
					LAST_MODIFIED_BY = '".$transaction->REQUESTOR_EMAIL_ADDRESS."' 
					WHERE TRN = '".$TRN."' AND MERCHANT_CODE = '".$getMerchantCode."' AND TRANS_STATUS = '2'"); 

					//Audit Trail
					$userEmailAdd = $dbEnterprise->getUserEmail($TRN); 
					$auditArr = array();
					$auditArr['TRN'] = $TRN;
					$auditArr['MERCHANT_CODE'] = $getMerchantCode;
					$auditArr['EVENT_TYPE'] = 'UPDATE';
					$auditArr['EVENT_REMARKS'] = 'New selected payment processor';
					$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP(); 
					$auditArr['CREATED_DATE'] = $timeStamp;
					$auditArr['CREATED_BY'] = $custom->upperCaseString($userEmailAdd);

					$insertAudit = $dbEnterprise->insert("tbl_audit_trail",$auditArr);
  			}else{  
  				$transactionHDR = $dbEnterprise->rawQuery("
					INSERT INTO tbl_transactions_hdr (TRN,	MERCHANT_CODE,	MERCHANT_REF_NUM, SUCCESS_RETURN_URL, FAILED_RETURN_URL, REQUESTOR_NAME, REQUESTOR_EMAIL_ADDRESS, REQUESTOR_MOBILE_NO, PM_CODE, PE_CODE, TRANS_STATUS, CREATED_DATE, CREATED_BY)

					SELECT TRN, MERCHANT_CODE, MERCHANT_REF_NUM, SUCCESS_RETURN_URL, FAILED_RETURN_URL, REQUESTOR_NAME, REQUESTOR_EMAIL_ADDRESS, REQUESTOR_MOBILE_NO, '".$getPaymentMethod."', '".$getPaymentEntity."', '2', '".$timeStamp."', CREATED_BY
					FROM tbl_transactions_hdr WHERE TRN = '".$TRN."'");

				//Audit Trail
				$userEmailAdd = $dbEnterprise->getUserEmail($TRN); 
				$auditArr = array();
				$auditArr['TRN'] = $TRN;
				$auditArr['MERCHANT_CODE'] = $getMerchantCode;
				$auditArr['EVENT_TYPE'] = 'ADD';
				$auditArr['EVENT_REMARKS'] = '';
				$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
				$auditArr['EVENT_REMARKS'] = 'Get Transaction';
				$auditArr['CREATED_DATE'] = $timeStamp;
				$auditArr['CREATED_BY'] = $custom->upperCaseString($userEmailAdd);

				$insertAudit = $dbEnterprise->insert("tbl_audit_trail",$auditArr); 
  			}
  	
  			$transHDRQueryData = array();
  			$transHDRQueryData['TRN'] = $TRN;
  			$transHDRQueryData['MERCHANT_CODE'] = $getMerchantCode;
  			$transHDRQueryData['TRANS_STATUS'] = '2';

			$transactionHDRData = $dbEnterprise->getRow("SELECT 
				th.`TRANS_STATUS`, th.`MERCHANT_REF_NUM`, th.`PM_CODE`, th.`PE_CODE`, th.`CREATED_DATE`,
				trts.`TRANS_DESC`, 
				vmas.`MERCHANT_NAME`, 
				tper.`ENTITY_TYPE`, tper.`ER_TYPE`, tper.`ENTITY_RATE_AMOUNT`, tper.`IPG_FEE`,tper.`PARTNER_CODE`,
				vmer.`MP_DESCRIPTION`,vmer.`PE_DESCRIPTION`,
				tijf.`JV_CODE`, tijf.`COMPANY_CODE_1`, tijf.`COMPANY_CODE_1_PERCENT`, tijf.`COMPANY_CODE_2`, tijf.`COMPANY_CODE_2_PERCENT`

				FROM tbl_transactions_hdr AS th

				INNER JOIN tbl_ref_transaction_status AS trts
				ON th.`TRANS_STATUS` = trts.`TRANS_STATUS`

				INNER JOIN vw_merchant_and_submerchant AS vmas
				ON th.`MERCHANT_CODE` = vmas.`MERCHANT_CODE`

				LEFT JOIN tbl_payment_entity_rate AS tper
				ON tper.`MERCHANT_CODE` = th.`MERCHANT_CODE` AND tper.`PE_CODE` = th.`PE_CODE` 

				LEFT JOIN vw_merchant_entity_rates AS vmer
				ON th.`PE_CODE` = vmer.`PE_CODE` 
				AND th.`MERCHANT_CODE` = vmer.`MERCHANT_CODE`

				LEFT JOIN tbl_ipg_jv_fees AS tijf
				ON tijf.`MERCHANT_CODE` = th.`MERCHANT_CODE`

				WHERE th.`TRN` = :TRN AND th.`MERCHANT_CODE` = :MERCHANT_CODE AND th.`TRANS_STATUS` = :TRANS_STATUS LIMIT 1", $transHDRQueryData);
				
			$jsonResponse = array();

			$jsonResponse['trn']		   			= $TRN;
			$jsonResponse['status_code']   			= '200';
			$jsonResponse['transaction_status'] 	= $transactionHDRData->TRANS_STATUS;
			$jsonResponse['merchant_ref_num']		= $transactionHDRData->MERCHANT_REF_NUM;
 			$jsonResponse['pm_code'] 				= $transactionHDRData->PM_CODE;    
 			$jsonResponse['pe_code'] 				= $transactionHDRData->PE_CODE;   
			$jsonResponse['transaction_status_desc']= $transactionHDRData->TRANS_DESC;
			$jsonResponse['merchant_code'] 			= $getMerchantCode;  
			$jsonResponse['merchant_name'] 			= $transactionHDRData->MERCHANT_NAME;   
 			$jsonResponse['payment_mode'] 			= $transactionHDRData->MP_DESCRIPTION;  
 			$jsonResponse['payment_entity'] 		= $transactionHDRData->PE_DESCRIPTION;   
 			$jsonResponse['entity_type'] 			= $transactionHDRData->ENTITY_TYPE;   
 			$jsonResponse['er_type'] 				= $transactionHDRData->ER_TYPE;    
 			$jsonResponse['entity_rate_amount'] 	= $transactionHDRData->ENTITY_RATE_AMOUNT;   
 			$jsonResponse['ipg_fee'] 				= $transactionHDRData->IPG_FEE;   
 			$jsonResponse['jv_code'] 				= $transactionHDRData->JV_CODE;  
 			$jsonResponse['partner_code']		    = $transactionHDRData->PARTNER_CODE;
 			$jsonResponse['cc1'] 					= $transactionHDRData->COMPANY_CODE_1;  
 			$jsonResponse['cc1p'] 					= $transactionHDRData->COMPANY_CODE_1_PERCENT; 
 			$jsonResponse['cc2'] 					= $transactionHDRData->COMPANY_CODE_2;  
 			$jsonResponse['cc2p'] 					= $transactionHDRData->COMPANY_CODE_2_PERCENT; 
 			$jsonResponse['created_date'] 			= $transactionHDRData->CREATED_DATE; 

 			$transactionsArr = array(); 
			$transactionsArr['TRN'] = $TRN;
			$transactionData = $dbEnterprise->getResults("SELECT * FROM tbl_transactions WHERE TRN = :TRN" ,$transactionsArr);

			$counter = 0;
			foreach($transactionData AS $transactionData){
				$jsonResponse['transactions'][$counter]['sub_merchant_code'] = $transactionData->SUB_MERCHANT_CODE;
				$jsonResponse['transactions'][$counter]['transaction_payment_for'] = $transactionData->TRANSACTION_PAYMENT_FOR;
				$jsonResponse['transactions'][$counter]['transaction_amount'] = $transactionData->TRANSACTION_AMOUNT;
				$counter++;
			} 
			print_r(json_encode($jsonResponse));  
		}else{
			$arrayVal = array();
			is_numeric($merchantcodeExist) ? '' : array_push($arrayVal, json_decode($merchantcodeExist));
			is_numeric($TRNExist) ? '' : array_push($arrayVal, json_decode($TRNExist));

			print_r(json_encode($arrayVal));
		}
	}
	
 
?>