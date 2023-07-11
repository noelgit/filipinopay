<?php 
	// include our OAuth2 Server object
	require_once __DIR__.'/server.php'; 
	 
	if (!isset($_SERVER['PHP_AUTH_USER'])) {
	    header('WWW-Authenticate: Basic realm="Authentication System"');
	    header('HTTP/1.0 401 Unauthorized'); 
	     die ("Not authorized");
	} else {  
    	$TRN 			   = $_POST['TRN'];    
		$TRNExist 		   = $dbEnterprise->checkTRN($TRN);  
		$TRNExistCheck     = is_numeric($TRNExist) ? 1 : 0 ; 
		$totalNoError 	   = $TRNExistCheck; 
		
		if($totalNoError == 1){   
			$transHDRQueryData = array(); 
			$transHDRQueryData['TRN'] = $TRN; 

			$transactionHDR = $dbEnterprise->getRow("SELECT  th.`MERCHANT_CODE`, th.`PM_CODE`, th.`CREATED_DATE`, 
					tper.`ENTITY_TYPE`, tper.`ER_TYPE`, tper.`ENTITY_RATE_AMOUNT`, tper.`IPG_FEE`

					FROM tbl_transactions_hdr AS th
				  
					LEFT JOIN tbl_payment_entity_rate AS tper
					ON tper.`MERCHANT_CODE` = th.`MERCHANT_CODE` AND tper.`PE_CODE` = th.`PE_CODE`  
					
					WHERE th.`TRN` = :TRN AND th.`TRANS_STATUS` = '2' AND (th.`TRANS_STATUS` != '3' OR th.`TRANS_STATUS` != '5')  AND th.`PM_CODE` = 'UFP' LIMIT 1", $transHDRQueryData); 
  			
  			if($transactionHDR){ 	 
	 			$transactionsArr = array(); 
				$transactionsArr['TRN'] = $TRN; 
				$transactionData = $dbEnterprise->getResults("SELECT * FROM tbl_transactions WHERE TRN = :TRN", $transactionsArr);
 
				$transationDataArr = array();
				$counter = 0;
				$totalAmountDue = 0;
				foreach($transactionData AS $data){
					$transationDataArr[$counter]['sub_merchant_code'] = $data->SUB_MERCHANT_CODE;
					$transationDataArr[$counter]['transaction_payment_for'] = $data->TRANSACTION_PAYMENT_FOR;
					$transationDataArr[$counter]['transaction_amount'] = $data->TRANSACTION_AMOUNT;
					$counter++; 

					$totalAmountDue += $data->TRANSACTION_AMOUNT; 
				} 

				if($transactionHDR->ER_TYPE == 'PERCENTAGE'){
					$entityRate   = $transactionHDR->ENTITY_RATE_AMOUNT; 
					$ipgFee 	  = $transactionHDR->IPG_FEE;

					//MDR
					//$totalAddedAmount = (($totalAmountDue + $ipgFee) * $entityRate) + $ipgFee; 
					//$totalFee	 = $totalAmountDue + $totalAddedAmount;
						
					//v2 
					$amountWithIPG = $totalAmountDue + $ipgFee; 
					$totalAddedAmount = $amountWithIPG / (1.00 - $entityRate);   
					$totalAddedAmount = $totalAddedAmount - $amountWithIPG; 
					$totalFee	= $amountWithIPG + $totalAddedAmount; 

					$RateDisplay = $totalAddedAmount;  
			 	}else{ 
			 		$entityRate  = $transactionHDR->ENTITY_RATE_AMOUNT;
					$ipgFee 	 = $transactionHDR->IPG_FEE;
				 	$totalFee	 = $totalAmountDue + $entityRate + $ipgFee;
				 	$RateDisplay = number_format($entityRate + $ipgFee,2);
			 	} 

			 	$jsonResponse = array();
				$jsonResponse['TRN'] = $TRN;
				$jsonResponse['TRANSACTION_DETAILS'] = $transationDataArr;
				$jsonResponse['CONVENIENCE_FEE'] 	 = $RateDisplay;
				$jsonResponse['TRANSACTION_DATE'] 	 = $transactionHDR->CREATED_DATE;
				$jsonResponse['TOTAL_AMOUNT'] 		 = $totalFee;
				

				$userEmailAdd = $dbEnterprise->getUserEmail($TRN); 
				//Audit Trail
				$auditArr = array();
				$auditArr['TRN'] = $TRN;
				$auditArr['MERCHANT_CODE'] = $transactionHDR->MERCHANT_CODE;
				$auditArr['EVENT_TYPE'] = 'SELECT';
				$auditArr['EVENT_REMARKS'] = 'GET TRANSACTION DETAILS';
				$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP(); 
				$auditArr['CREATED_DATE'] = $timeStamp;
				$auditArr['CREATED_BY'] = $custom->upperCaseString($userEmailAdd); 
				$insertAudit = $dbEnterprise->insert("tbl_audit_trail",$auditArr); 
				
				print_r(json_encode($jsonResponse)); 
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