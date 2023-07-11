<?php 
	// include our OAuth2 Server object
	require_once __DIR__.'/server.php'; 
	 
	if (!isset($_SERVER['PHP_AUTH_USER'])) {
	    header('WWW-Authenticate: Basic realm="Authentication System"');
	    header('HTTP/1.0 401 Unauthorized'); 
	     die ("Not authorized");
	} else { 
	 	  
    	$TRN 			   = $_POST['TRN'];
    	$getPartnerCode    = $_POST['PARTNER_CODE'];
	 	$getTransDateTime  = $_POST['TRANS_DATE_TIME'];  
 
		$TRNExist 		   = $dbEnterprise->checkTRN($TRN);  
		$TRNExistCheck     = is_numeric($TRNExist) ? 1 : 0 ; 
		$totalNoError 	   = $TRNExistCheck; 
		
		if($totalNoError == 1){  
			$modePaymentArr = array(); 
			$modePaymentArr['TRN'] = $TRN;
			$modePaymentArr['PARTNER_CODE'] = $getPartnerCode; 
			$modePaymentArr['TRANS_STATUS'] = '2'; 
			$modePaymentArr['PM_CODE'] = 'UFP';

			$transaction = $dbEnterprise->getRow("SELECT * FROM tbl_transactions_hdr WHERE TRN = :TRN AND PARTNER_CODE = :PARTNER_CODE AND TRANS_STATUS = :TRANS_STATUS AND PM_CODE = :PM_CODE LIMIT 1", $modePaymentArr);

  			if($transaction){ 	
	  			$transactionDate = $transaction->CREATED_DATE;
	  			$dateToday = date('Y-m-d H:s:i');  
	  			$timediff = strtotime($dateToday) - strtotime($transactionDate); 

				if($timediff > 86400) { //more than 24 hours  

					$transQueryHDRData = array(); 
					$transQueryHDRData['TRN'] = $TRN;
					$transQueryHDRData['PARTNER_CODE'] = $getPartnerCode;  

					$transactionHDRData = $dbEnterprise->getRow("SELECT th.`TRN`, th.`MERCHANT_CODE`,  th.`TRANS_STATUS`, trts.`TRANS_DESC` 
 
						FROM tbl_transactions_hdr AS th

						INNER JOIN tbl_ref_transaction_status AS trts
						ON th.`TRANS_STATUS` = trts.`TRANS_STATUS`

						WHERE th.`TRN` = :TRN AND th.`PARTNER_CODE` = :PARTNER_CODE AND (th.`TRANS_STATUS` = '5' OR th.`TRANS_STATUS` = '3') LIMIT 1", $transQueryHDRData);

					if($transactionHDRData){ 
						$jsonResponse = array();   
						$jsonResponse['trn'] = $TRN;
						$jsonResponse['status_code'] = '200';
						$jsonResponse['trasaction_status'] = $transactionHDRData->TRANS_STATUS; 
						$jsonResponse['trasaction_status_desc'] = $transactionHDRData->TRANS_DESC;
						print_r(json_encode($jsonResponse));  
					}else{
						$transactionHDR = $dbEnterprise->rawQuery("
							INSERT INTO tbl_transactions_hdr (TRN,	MERCHANT_CODE,	MERCHANT_REF_NUM, SUCCESS_RETURN_URL, FAILED_RETURN_URL, REQUESTOR_NAME, REQUESTOR_EMAIL_ADDRESS, REQUESTOR_MOBILE_NO, PM_CODE, PE_CODE, CONVENIENCE_FEE, IPG_FEE, COMPANY_CODE_1_FEE, COMPANY_CODE_2_FEE, JV_CODE, PARTNER_CODE, TRANS_STATUS, CREATED_DATE, CREATED_BY)

							SELECT TRN, MERCHANT_CODE, MERCHANT_REF_NUM, SUCCESS_RETURN_URL, FAILED_RETURN_URL, REQUESTOR_NAME, REQUESTOR_EMAIL_ADDRESS, REQUESTOR_MOBILE_NO, PM_CODE, PE_CODE, CONVENIENCE_FEE, IPG_FEE, COMPANY_CODE_1_FEE, COMPANY_CODE_2_FEE, JV_CODE, PARTNER_CODE, '5', '".$getTransDateTime."', CREATED_BY
							FROM tbl_transactions_hdr
							WHERE TRN = '".$TRN."' AND TRANS_STATUS = '2'");
 
						$transactionHDRData = $dbEnterprise->getRow("SELECT th.`TRN`, th.`MERCHANT_CODE`,  th.`TRANS_STATUS`, trts.`TRANS_DESC` 
	 
							FROM tbl_transactions_hdr AS th

							INNER JOIN tbl_ref_transaction_status AS trts
							ON th.`TRANS_STATUS` = trts.`TRANS_STATUS`

							WHERE th.`TRN` = :TRN AND th.`PARTNER_CODE` = :PARTNER_CODE AND (th.`TRANS_STATUS` = '5' OR th.`TRANS_STATUS` = '3') LIMIT 1", $transQueryHDRData);

						//Audit Trail
						$userEmailAdd = $dbEnterprise->getUserEmail($TRN); 
						$auditArr = array();
						$auditArr['TRN'] = $TRN;
						$auditArr['MERCHANT_CODE'] = $transactionHDRData->MERCHANT_CODE;
						$auditArr['EVENT_TYPE'] = 'UPDATE';
						$auditArr['EVENT_REMARKS'] = 'TRANSACTION EXPIRED';
						$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP(); 
						$auditArr['CREATED_DATE'] = $timeStamp;
						$auditArr['CREATED_BY'] = $custom->upperCaseString($userEmailAdd); 
						$insertAudit = $dbEnterprise->insert("tbl_audit_trail", $auditArr); 
 						
 						//JsonResponse
						$jsonResponse = array();   
						$jsonResponse['trn'] = $TRN;
						$jsonResponse['status_code'] = '200';
						$jsonResponse['trasaction_status'] = $transactionHDRData->TRANS_STATUS; 
						$jsonResponse['trasaction_status_desc'] = $transactionHDRData->TRANS_DESC;
						print_r(json_encode($jsonResponse));  
					}
				} else {    
					$transQueryHDRData = array(); 
					$transQueryHDRData['TRN'] = $TRN;
					$transQueryHDRData['PARTNER_CODE'] = $getPartnerCode;  

					$transactionHDRData = $dbEnterprise->getRow("SELECT th.`TRN`, th.`MERCHANT_CODE`, th.`TRANS_STATUS`, trts.`TRANS_DESC` 
 
						FROM tbl_transactions_hdr AS th

						INNER JOIN tbl_ref_transaction_status AS trts
						ON th.`TRANS_STATUS` = trts.`TRANS_STATUS`

						WHERE th.`TRN` = :TRN AND th.`PARTNER_CODE` = :PARTNER_CODE AND (th.`TRANS_STATUS` = '3' OR th.`TRANS_STATUS` = '5') LIMIT 1", $transQueryHDRData);
					if($transactionHDRData){ 
						$jsonResponse = array();   
						$jsonResponse['trn'] = $TRN;
						$jsonResponse['status_code'] = '200';
						$jsonResponse['trasaction_status'] 	= $transactionHDRData->TRANS_STATUS; 
						$jsonResponse['trasaction_status_desc'] = $transactionHDRData->TRANS_DESC; 
						print_r(json_encode($jsonResponse));   
					}else{
						$transactionHDR = $dbEnterprise->rawQuery("
							INSERT INTO tbl_transactions_hdr (TRN,	MERCHANT_CODE,	MERCHANT_REF_NUM, SUCCESS_RETURN_URL, FAILED_RETURN_URL, REQUESTOR_NAME, REQUESTOR_EMAIL_ADDRESS, REQUESTOR_MOBILE_NO, PM_CODE, PE_CODE, CONVENIENCE_FEE, IPG_FEE, COMPANY_CODE_1_FEE, COMPANY_CODE_2_FEE, JV_CODE, PARTNER_CODE, TRANS_STATUS, CREATED_DATE, CREATED_BY)

							SELECT TRN, MERCHANT_CODE, MERCHANT_REF_NUM, SUCCESS_RETURN_URL, FAILED_RETURN_URL, REQUESTOR_NAME, REQUESTOR_EMAIL_ADDRESS, REQUESTOR_MOBILE_NO, PM_CODE, PE_CODE, CONVENIENCE_FEE, IPG_FEE, COMPANY_CODE_1_FEE, COMPANY_CODE_2_FEE, JV_CODE, PARTNER_CODE, '3', '".$getTransDateTime."', CREATED_BY
							FROM tbl_transactions_hdr
							WHERE TRN = '".$TRN."' AND TRANS_STATUS = '2'");
						
						$transactionHDRData = $dbEnterprise->getRow("SELECT th.`TRN`, th.`MERCHANT_CODE`, th.`TRANS_STATUS`, trts.`TRANS_DESC` 
	 
							FROM tbl_transactions_hdr AS th

							INNER JOIN tbl_ref_transaction_status AS trts
							ON th.`TRANS_STATUS` = trts.`TRANS_STATUS`

							WHERE th.`TRN` = :TRN AND th.`PARTNER_CODE` = :PARTNER_CODE AND (th.`TRANS_STATUS` = '3' OR th.`TRANS_STATUS` = '5') LIMIT 1", $transQueryHDRData);

						$jsonResponse = array();   
						$jsonResponse['trn'] = $TRN;
						$jsonResponse['status_code'] = '200';
						$jsonResponse['trasaction_status'] 	= $transactionHDRData->TRANS_STATUS; 
						$jsonResponse['trasaction_status_desc'] = $transactionHDRData->TRANS_DESC; 
						print_r(json_encode($jsonResponse));   
	 
						$userEmailAdd = $dbEnterprise->getUserEmail($TRN); 

						$countData = array();
						$countData['MERCHANT_CODE'] = $transactionHDRData->MERCHANT_CODE;
						$countData['TRANS_STATUS'] = "3";

						$count = $dbEnterprise->getRow("SELECT COUNT(*) AS countColumn FROM tbl_transactions_hdr WHERE MERCHANT_CODE = :MERCHANT_CODE AND TRANS_STATUS =:TRANS_STATUS", $countData); 
						$count = $count->countColumn += 1;  
						$eorDateTime  = date('YmdHis').$count;
	
						//EOR 
						$EORARR = array();
						$EORARR['TRN'] = $TRN;
						$EORARR['MERCHANT_CODE'] = $transactionHDRData->MERCHANT_CODE;
						$EORARR['EOR'] = $eorDateTime; 
						$EORARR['CREATED_DATE'] = $timeStamp;
						$EORARR['CREATED_BY'] = $custom->upperCaseString($userEmailAdd);

						$insertEOR = $dbEnterprise->insert("tbl_eor",$EORARR); 

						//Audit Trail
						$auditArr = array();
						$auditArr['TRN'] = $TRN;
						$auditArr['MERCHANT_CODE'] = $transactionHDRData->MERCHANT_CODE;
						$auditArr['EVENT_TYPE'] = 'UPDATE';
						$auditArr['EVENT_REMARKS'] = 'CONFIRM PAYMENT';
						$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP(); 
						$auditArr['CREATED_DATE'] = $timeStamp;
						$auditArr['CREATED_BY'] = $custom->upperCaseString($userEmailAdd); 
						$insertAudit = $dbEnterprise->insert("tbl_audit_trail",$auditArr); 
					}
				} 
  			}else{   
				$jsonResponse = array();   
				$jsonResponse['error_code'] 	   = "-1010";
				$jsonResponse['error'] 			   = "Invalid Transaction Details";
				$jsonResponse['error_description'] = "No transaction found."; 
				print_r(json_encode($jsonResponse));   
  			} 
  			
		}else{
			$arrayVal = array(); 
			is_numeric($TRNExist) ? '' : array_push($arrayVal, json_decode($TRNExist)); 
			print_r(json_encode($arrayVal));
		}
	}
	
 
?>