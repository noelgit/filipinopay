<?php   
	header('Content-type: application/json');
	include('../../config.php');
	include('../../LIB/libraries.php');
	error_reporting(0); 
	
	$GATEWAY_LINK = GATEWAY_SERVER.'postpayment.php';
	$OPTN 		  = $_POST['OPTN'];
	$AMOUNT       = $_POST['AMOUNT'];
 	$transDate    = $_POST['TRANSACTION_DATE'];
 	$transTime    = $_POST['TRANSACTION_TIME'];
	$TRN 	  	  = $_POST['TRN'];
	$TYPEFEE 	  = $_POST['TYPEFEE'];
	$PROVIDER     = "DBP";   
	$TOKEN_ID 	  = TOKEN_ID;
	$DBP_TOKEN_ID = DBP_TOKEN_ID;	
	$response 	  = array();

	$transactionDateTime = $transDate." ".$transTime;
	$toSeconds = strtotime($transactionDateTime); 
	$reformatDateTime = date("Y-m-d H:i:s", $toSeconds); 

	//Get Payment Details
	$gatewayLink = $GATEWAY_LINK."?refnum=".$OPTN."&amount=".$AMOUNT."&transactiondate=".$reformatDateTime."&transactionid=".$TRN."&type=".$TYPEFEE."&provider=".$PROVIDER."&tokenid=".$TOKEN_ID."&DBP_TOKEN_ID=".$DBP_TOKEN_ID; 
	$curlPost = curl_init(); 
	curl_setopt($curlPost, CURLOPT_URL, str_replace(' ', '%20', $gatewayLink));  
	curl_setopt($curlPost, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curlPost, CURLOPT_RETURNTRANSFER, true); 
	
	//Run cURL 
	$results = curl_exec($curlPost);	
	curl_close($curlPost);  
	$resultGateway = json_decode($results);  
 	if($resultGateway->Result == '1'){ // Success 
 		$response['STATUS']  = '1';
	 	$response['OPTN']  = $OPTN;
	 	$response['AMOUNT']  = $AMOUNT;	
	 	$response['TRANSACTION_DATE']  = $transDate;
	 	$response['TRANSACTION_TIME']  = $transTime;
	 	$response['TRN']  = $TRN;				
 		$response['MESSAGE']  = $resultGateway->message;


		$auditArr = array(); 
		$auditArr['TRN']			  = $TRN;
		$auditArr['EVENT_TYPE']    	  = 'INSERT';
		$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
		$auditArr['EVENT_REMARKS'] 	  = 'TRANSACTION WAS SUCCESSFULLY POSTED FROM THE GATEWAY SERVER - REPOSTING';
		$auditArr['CREATED_DATE']	  = $timeStamp;
		$auditArr['CREATED_BY']		  = "SYSAD";

		$insertAudit = $dbLGUDavao->insert("tbl_audit_trails", $auditArr); 

 	}else{ //Failed
 		$response['STATUS']  = '0'; 
	 	$response['OPTN']  = $OPTN;
	 	$response['AMOUNT']  = $AMOUNT;	
	 	$response['TRANSACTION_DATE']  = $transDate;
	 	$response['TRANSACTION_TIME']  = $transTime;	 	
	 	$response['TRN']  = $TRN;	
 		$response['MESSAGE']  = $resultGateway->message;
 		
 		$auditArr = array(); 
		$auditArr['TRN']			  = $TRN;
		$auditArr['EVENT_TYPE']    	  = 'INSERT';
		$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
		$auditArr['EVENT_REMARKS'] 	  = 'TRANSACTION WAS NOT SUCCESSFULLY POSTED FROM THE GATEWAY SERVER - REPOSTING. MESSAGE:'. $resultGateway->message;
		$auditArr['CREATED_DATE']	  = $timeStamp;
		$auditArr['CREATED_BY']		  = "SYSAD";
		$insertAudit = $dbLGUDavao->insert("tbl_audit_trails", $auditArr); 
		
			
 	}  

	
	error_log($response);
	echo json_encode($response);  

?>