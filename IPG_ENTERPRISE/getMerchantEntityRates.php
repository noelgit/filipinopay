<?php 
	// include our OAuth2 Server object
	require_once __DIR__.'/server.php'; 
	
	if (!isset($_SERVER['PHP_AUTH_USER'])) {
	    header('WWW-Authenticate: Basic realm="Authentication System"');
	    header('HTTP/1.0 401 Unauthorized'); 
	     die ("Not authorized");
	} else { 
	 	    
	 	$getMerchantCode = $_GET['merchantcode'];
		$getSecureParam  = $custom->getSecureParam($_GET['secureparam'], "GET");
  	
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
			$modePaymentArr['MERCHANT_CODE'] = $getMerchantCode;
			$modePayment 	= $dbEnterprise->getResults("SELECT * FROM vw_merchant_mode_of_payment WHERE MERCHANT_CODE = :MERCHANT_CODE" ,$modePaymentArr);	

			$modePaymentRow = $dbEnterprise->getRow("SELECT * FROM vw_merchant_mode_of_payment WHERE MERCHANT_CODE = :MERCHANT_CODE LIMIT 1" ,$modePaymentArr);
			
			$jsonResponse = array();
			$jsonResponse['merchant_code'] = $getMerchantCode; 
			$jsonResponse['merchant_name'] = $modePaymentRow->MERCHANT_NAME;

			$counter = 0;
			foreach($modePayment as $modePaymentVal){
				$jsonResponse['payment_mode'][$counter]['pm_code'] = $modePaymentVal->PM_CODE;
				$jsonResponse['payment_mode'][$counter]['mp_description'] = $modePaymentVal->MP_DESCRIPTION;
				
				$PEArr = array(); 
				$PEArr['MERCHANT_CODE'] = $getMerchantCode; 
				$PEArr['PM_CODE'] = $modePaymentVal->PM_CODE;

				$PE	= $dbEnterprise->getResults("SELECT * FROM vw_merchant_entity_rates WHERE MERCHANT_CODE = :MERCHANT_CODE AND PM_CODE = :PM_CODE" ,$PEArr);	  
				$counter2 = 0;	
				foreach($PE as $PEVal){
					$jsonResponse['payment_mode'][$counter]['payment_entities'][$counter2]['pe_code'] = $PEVal->PE_CODE;
					$jsonResponse['payment_mode'][$counter]['payment_entities'][$counter2]['pe_description'] = $PEVal->PE_DESCRIPTION;
					$jsonResponse['payment_mode'][$counter]['payment_entities'][$counter2]['entity_type'] = $PEVal->ENTITY_TYPE;
					$jsonResponse['payment_mode'][$counter]['payment_entities'][$counter2]['er_type'] = $PEVal->ER_TYPE;
					$jsonResponse['payment_mode'][$counter]['payment_entities'][$counter2]['entity_rate_amount'] = $PEVal->ENTITY_RATE_AMOUNT;	
					$counter2++;
				}
				$counter++;
			}

			$userEmailAdd = $dbEnterprise->getUserEmail($TRN);

			$auditArr = array();
			$auditArr['TRN'] = $TRN;
			$auditArr['MERCHANT_CODE'] = $getMerchantCode;
			$auditArr['EVENT_TYPE'] = 'SELECT';
			$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
			$auditArr['EVENT_REMARKS'] = 'GET MERCHANT ENTITY RATES';
			$auditArr['CREATED_DATE'] = $timeStamp;
			$auditArr['CREATED_BY'] = $custom->upperCaseString($userEmailAdd);

			$insertAudit = $dbEnterprise->insert("tbl_audit_trail",$auditArr);
			print_r(json_encode($jsonResponse)); 
		} else{
			$arrayVal = array();
			is_numeric($merchantcodeExist) ? '' : array_push($arrayVal, json_decode($merchantcodeExist));
			is_numeric($TRNExist) ? '' : array_push($arrayVal, json_decode($TRNExist));
			print_r(json_encode($arrayVal));
		}
	}
	
 
?>