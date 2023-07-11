<?php 
	// include our OAuth2 Server object
	require_once __DIR__.'/server.php'; 

	$ufpData = $dbEnterprise->getResults("SELECT * FROM tbl_transactions_hdr WHERE PM_CODE = 'UFP' AND TRANS_STATUS = '2'");

	foreach($ufpData AS $data){
		$transactionDate = $data->CREATED_DATE;
		$dateToday = date('Y-m-d H:s:i');  
		$timediff = strtotime($dateToday) - strtotime($transactionDate); 

		if($timediff > 86400) { //more than 24 hours  		
			$transQueryHDRData = array(); 
			$transQueryHDRData['TRN'] = $data->TRN; 

			//Check if transaction is already updated 
			$transactionHDRData = $dbEnterprise->getRow("SELECT th.`TRN`, th.`MERCHANT_CODE`,  th.`TRANS_STATUS`, trts.`TRANS_DESC` 

				FROM tbl_transactions_hdr AS th

				INNER JOIN tbl_ref_transaction_status AS trts
				ON th.`TRANS_STATUS` = trts.`TRANS_STATUS`

				WHERE th.`TRN` = :TRN AND (th.`TRANS_STATUS` = '5' OR th.`TRANS_STATUS` = '3' OR th.`TRANS_STATUS` = '4') LIMIT 1", $transQueryHDRData);
			
			if($transactionHDRData){

			}else{
				$transactionHDR = $dbEnterprise->rawQuery("
					INSERT INTO tbl_transactions_hdr (TRN,	MERCHANT_CODE,	MERCHANT_REF_NUM, SUCCESS_RETURN_URL, FAILED_RETURN_URL, REQUESTOR_NAME, REQUESTOR_EMAIL_ADDRESS, REQUESTOR_MOBILE_NO, PM_CODE, PE_CODE, CONVENIENCE_FEE, IPG_FEE, COMPANY_CODE_1_FEE, COMPANY_CODE_2_FEE, JV_CODE, PARTNER_CODE, TRANS_STATUS, CREATED_DATE, CREATED_BY)

					SELECT TRN, MERCHANT_CODE, MERCHANT_REF_NUM, SUCCESS_RETURN_URL, FAILED_RETURN_URL, REQUESTOR_NAME, REQUESTOR_EMAIL_ADDRESS, REQUESTOR_MOBILE_NO, PM_CODE, PE_CODE, CONVENIENCE_FEE, IPG_FEE, COMPANY_CODE_1_FEE, COMPANY_CODE_2_FEE, JV_CODE, PARTNER_CODE, '5', '".$timeStamp."', CREATED_BY
					FROM tbl_transactions_hdr
					WHERE TRN = '".$data->TRN."' AND TRANS_STATUS = '2'");
			}
		}else{

		}

	}

?>