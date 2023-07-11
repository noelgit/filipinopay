<?php   
	header('Content-type: application/json');
	include('../../config.php');
	include('../../LIB/libraries.php');
	session_start();  
	error_reporting(0);
 
	$GATEWAY_LINK = GATEWAY_SERVER.'postpayment.php';
	$OPTN 		  = $_POST['OPTN'];
	$AMOUNT       = $_POST['AMOUNT'];
 	$transDate    = date('Y-m-d H:i:s');
	$TRN 	  	  = $_POST['TRN'];
	$STATUS 	  = $_POST['STATUS'];
	$TYPEFEE 	  = $_POST['TYPEFEE'];
	$PROVIDER     = "DBP";   
	$TOKEN_ID 	  = TOKEN_ID;
	$DBP_TOKEN_ID = DBP_TOKEN_ID;

	$MOBILENUMBER = $_POST['MOBILENUMBER'];
	$EMAIL 		  = $_POST['EMAIL'];
	$NAME 		  = $_POST['NAME'];
	$response 	  = array();

	//Get Payment Details
	$gatewayLink = $GATEWAY_LINK."?refnum=".$OPTN."&amount=".$AMOUNT."&transactiondate=".$transDate."&transactionid=".$TRN."&type=".$TYPEFEE."&provider=".$PROVIDER."&tokenid=".$TOKEN_ID."&DBP_TOKEN_ID=".$DBP_TOKEN_ID; 
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
 		$response['MESSAGE']  = $resultGateway->message;

		$transactionData = array();
		$transactionData['TRN'] 		 = $custom->upperCaseString($TRN);
		$transactionData['OPTN'] 	 	 = $OPTN;
		$transactionData['MOBILENO'] 	 = $MOBILENUMBER; 
		$transactionData['STATUS'] 		 = $STATUS; 
		$transactionData['CREATED_DATE'] = $timeStamp;
		$transactionData['CREATED_BY'] 	 = $custom->upperCaseString($EMAIL);

		$result = $dbLGUDavao->insert('tbl_transactions', $transactionData);	

 
		/****************************
			INSERT tbl_audit_trails
		*****************************/			 
		$auditArr = array(); 
		$auditArr['TRN']			  = $TRN;
		$auditArr['EVENT_TYPE']    	  = 'INSERT';
		$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
		$auditArr['EVENT_REMARKS'] 	  = 'SUCCESS TRANSACTION';
		$auditArr['CREATED_DATE']	  = $timeStamp;
		$auditArr['CREATED_BY']		  = $custom->upperCaseString($EMAIL);

		$insertAudit = $dbLGUDavao->insert("tbl_audit_trails", $auditArr); 
 

 	}else{ //Failed
 		$response['STATUS']  = '0'; 
 		$response['MESSAGE']  = $resultGateway->message;
		
		$transactionData = array();
		$transactionData['TRN'] 		 = $custom->upperCaseString($TRN);
		$transactionData['OPTN'] 	 	 = $OPTN;
		$transactionData['MOBILENO'] 	 = $MOBILENUMBER; 
		$transactionData['STATUS'] 		 = $STATUS; 
		$transactionData['CREATED_DATE'] = $timeStamp;
		$transactionData['CREATED_BY'] 	 = $custom->upperCaseString($EMAIL);

		$result = $dbLGUDavao->insert('tbl_transactions', $transactionData);	

	 
		/****************************
			INSERT tbl_audit_trails
		*****************************/			 
		$auditArr = array(); 
		$auditArr['TRN']			  = $TRN;
		$auditArr['EVENT_TYPE']    	  = 'INSERT';
		$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
		$auditArr['EVENT_REMARKS'] 	  = 'FAILED TRANSACTION';
		$auditArr['CREATED_DATE']	  = $timeStamp;
		$auditArr['CREATED_BY']		  = $custom->upperCaseString($EMAIL);

		$insertAudit = $dbLGUDavao->insert("tbl_audit_trails", $auditArr); 
 
 	}   

 	unset($_SESSION[SESSION_NAME]);
	echo json_encode($response);  

?>