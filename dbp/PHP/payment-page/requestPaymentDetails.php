<?php    
	include('../../config.php');
	include('../../LIB/libraries.php');
	session_start();  
	error_reporting(E_ALL);

	$TYPEFEE 	  = $_POST['TYPEFEE'];
	$OPTN 		  = $_POST['OPTN'];
	$MOBILENUMBER = $_POST['MOBILENUMBER'];
	$EMAIL 		  = $_POST['EMAIL'];
	$FNAME 		  = $_POST['FNAME'];
	$MNAME 		  = $_POST['MNAME'];
	$LNAME 		  = $_POST['LNAME'];
	$GATEWAY_LINK = GATEWAY_SERVER.'verify.php';
	$TOKEN_ID 	  = TOKEN_ID;
	$PROVIDER     = "DBP";  
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
 		$response['AMOUNT']  = number_format((float)$origformatamount, 2, '.', ','); //$result->amount;
 		$response['TRANSACTION_AMOUNT']  = number_format((float)$origformatamount, 2, '.', ''); //$result->amount;
 		$response['TYPE'] 	 = $TYPEFEE;
 		$response['OPTN'] 	 = $OPTN; 
 		$response['NUMBER']  = $MOBILENUMBER;	
 		$response['EMAIL']   = $EMAIL;	
 		$response['FNAME']   = $FNAME;	
 		$response['MNAME']   = $MNAME;	
 		$response['LNAME']   = $LNAME;	
 		$response['PAYMENT_FOR'] = $TYPEFEE." | OPTN:".$OPTN;	


 	}else{ //Failed
		$origformatamount = $result->amount;
 		$response['STATUS']  = '0';
 		$response['DUEDATE'] = $result->duedate;
 		$response['AMOUNT']  = number_format((float)$origformatamount, 2, '.', ','); //$result->amount;
 		$response['TRANSACTION_AMOUNT']  = number_format((float)$origformatamount, 2, '.', ''); //$result->amount;
 		$response['TYPE'] 	 = $TYPEFEE;
 		$response['OPTN'] 	 = $OPTN;
 		$response['NUMBER']  = $MOBILENUMBER;	
 		$response['EMAIL']   = $EMAIL;	
 		$response['FNAME']   = $FNAME;	
 		$response['MNAME']   = $MNAME;	
 		$response['LNAME']   = $LNAME;	
		$response['MESSAGE'] = $result->message;
 		$response['PAYMENT_FOR'] = $TYPEFEE." | OPTN:".$OPTN;	
 	}  

	$_SESSION[SESSION_NAME]['PAYMENT_DETAILS'] = json_decode(json_encode($response));
	echo json_encode($response);  

?>