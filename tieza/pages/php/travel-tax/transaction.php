<?php 
	include('../../../config.php');
	include('../../../lib/libraries.php');
	error_reporting(0); 

	$subscriberID 			= $_POST['subscriberID'];

	$ticketNumber			= $_POST['ticketNumber'];
	$passage				= $_POST['passage'];
	$dateTravel 	 		= $_POST['dateTravel'];
	$pointExit 		  		= $_POST['pointExit'];
	$destination	   		= $_POST['destination'];
	$destinationCity   		= $_POST['destinationCity'];
	$amount 		   		= $_POST['amount'];
	$emailAddress 			= $_POST['emailAddress'];
 
 	$dateTravel  = date_create($dateTravel);
	$dateTravelFormat = date_format($dateTravel,"Y-m-d");  

	$transactionData = array();
	$transactionData['TICKET_NO'] 		= $custom->upperCaseString($ticketNumber);
	$transactionData['PASSAGE_ID'] 	 	= $passage;
	$transactionData['DATE_TRAVEL'] 	= $dateTravelFormat;
	$transactionData['EXIT_POINT_ID'] 	= $pointExit;
	$transactionData['DESTINATION_ID'] 	= $destination;
	$transactionData['CITY_TOWN_PROV'] 	= $custom->upperCaseString($destinationCity);
	$transactionData['TRANS_AMOUNT'] 	= $amount;
	$transactionData['IPG_TRN'] 		= '';
	$transactionData['TRANS_STATUS_ID'] = '1'; //PROCESSING
	$transactionData['CREATED_DATE'] 	= $timeStamp;
	$transactionData['CREATED_BY'] 		= $custom->upperCaseString($emailAddress);

	$result = $dbTieza->insert('tbl_transaction', $transactionData);	

	$response = array();
	if($result){
		/****************************
			INSERT tbl_audit_trail
		*****************************/			 
		$auditArr = array();
		$auditArr['MODULE'] 	  	  = 'TRAVEL-TAX'; 
		$auditArr['TRN']			  = 'N/A';
		$auditArr['EVENT_TYPE']    	  = 'ADD';
		$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
		$auditArr['EVENT_REMARKS'] 	  = 'TRANSACTION IN-PROCESS';
		$auditArr['CREATED_DATE']	  = $timeStamp;
		$auditArr['CREATED_BY']		  = $custom->upperCaseString($emailAddress);

		$insertAudit = $dbTieza->insert("tbl_audit_trail", $auditArr); 

		$data = array();
		$data['SUBSCRIBERS_ID'] = $subscriberID;
		$userData = $dbTieza->getRow("SELECT CONCAT(tap.`FIRST_NAME`, ' ', tap.`LAST_NAME`) AS FULL_NAME, tap.`EMAIL_ADDRESS`, CONCAT(trcc.`DIALING_CODE`,tap.`MOBILE_NO`) AS CONTACT_NUMBER FROM tbl_account_profile AS tap 

			LEFT JOIN tbl_ref_country_codes AS trcc
			ON trcc.`ID` = tap.`COUNTRY_CODE_ID`

			WHERE tap.`SUBSCRIBERS_ID` = :SUBSCRIBERS_ID", $data);
		$passageData = $dbTieza->getRow("SELECT PASSAGE_DESC FROM tbl_ref_passage WHERE PASSAGE_ID = '".$passage."' ");

	    $response['STATUS']   	  = 'SUCCESS'; 
	    $response['TRANS_ID']	  = $result;
	    $response['TIEZA_TRN']    = $ticketNumber;
	    $response['NAME']  	  	  = $userData->FULL_NAME; 
	    $response['EMAIL']    	  = $userData->EMAIL_ADDRESS; 	
	    $response['CONTACT']  	  = $userData->CONTACT_NUMBER; 	  
	    $response['TRANSACTION'][0]['sub_merchant_code']  	   = IPG_SUB_MERCHANT_CODE; 	
	    $response['TRANSACTION'][0]['transaction_payment_for'] = $passageData->PASSAGE_DESC; 	
	    $response['TRANSACTION'][0]['transaction_amount']  	   = $amount; 	    
	}else{
	    $response['STATUS']  = 'ERROR'; 		
	}

	echo json_encode($response);	

?>