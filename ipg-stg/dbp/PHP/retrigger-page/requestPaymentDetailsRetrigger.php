<?php    
	header('Content-type: application/json');
	include('../../config.php');
	error_reporting(0);

	$GATEWAY_LINK = GATEWAY_SERVER.'verify.php';
	$TYPEFEE 	  = $_POST['TYPEFEE'];
	$OPTN 		  = $_POST['OPTN'];
	$TRN 		  = $_POST['TRN'];
	$AMOUNT 	  = $_POST['AMOUNT'];
	$PROVIDER     = "DBP";  
	$TOKEN_ID 	  = TOKEN_ID;	
	$response 	  = array();

	//Get Payment Details
	$gatewayLink = $GATEWAY_LINK."?refnum=".$OPTN."&type=".$TYPEFEE."&provider=".$PROVIDER."&tokenid=".$TOKEN_ID; 	
	
	$curlVerify = curl_init(); 
	curl_setopt($curlVerify, CURLOPT_URL,$gatewayLink);  
	curl_setopt($curlVerify, CURLOPT_RETURNTRANSFER, true);  
	
	//Run cURL 
	$results = curl_exec($curlVerify); 

	curl_close($curlVerify); 
	$result = json_decode($results); 
	
 	if($result->Result == '1'){ // Success
		$origformatamount = $result->amount;
 		$response['STATUS']  = '1';
 		$response['DUEDATE'] = $result->duedate;
 		$response['AMOUNT']  = $origformatamount; //$result->amount;
 		$response['TRANSACTION_AMOUNT']  = $origformatamount; //$result->amount;
 		$response['TYPE'] 	 = $TYPEFEE;
 		$response['OPTN'] 	 = $OPTN;
 		$response['TRN'] 	 = $TRN; 		
		$response['MESSAGE'] = $result->message; 		 
 		$response['PAYMENT_FOR'] = $TYPEFEE." | ".$OPTN;	


 	}else{ //Failed
		$origformatamount = $result->amount;
 		$response['STATUS']  = '0';
 		$response['DUEDATE'] = $result->duedate;
 		$response['AMOUNT']  = number_format((float)$AMOUNT, 2, '.', ','); //$result->amount;
 		$response['TRANSACTION_AMOUNT']  = number_format((float)$origformatamount, 2, '.', ''); //$result->amount;
 		$response['TRN'] 	 = $TRN; 	 		
		$response['MESSAGE'] = $result->message;
 		$response['PAYMENT_FOR'] = $TYPEFEE." | ".$OPTN;	
 	}  
 
	// $_SESSION[SESSION_NAME]['PAYMENT_DETAILS'] = json_decode(json_encode($response));

	error_log($response);
	echo json_encode($response);  

?>