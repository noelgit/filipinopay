<?php 
	//Paymaya Ewallet
	//header('Content-type: application/json');
	session_start();
	include("../../../../vendor/autoload.php");
	include("../../../../config.php"); 
	include("../../../../LIBRARIES/libraries.php");
	error_reporting(0);


	use PayMaya\PayMayaSDK; 
	$timeStamp 	  = date('Y-m-d G:i:s'); 
	$pgwInfo = $dbGateway->getRow("SELECT * FROM tbl_pgw_info WHERE PARTNER_CODE = '".$_POST['partnerCode']."' LIMIT 1");
	$pgwInfoProcessor = $dbGateway->getRow("SELECT * FROM tbl_pgw_processors WHERE PARTNER_CODE = '".$_POST['partnerCode']."' LIMIT 1");
	$response = array();
	if($pgwInfo){  

		$paymayaLink = $pgwInfoProcessor->API_URI;
		$username = $pgwInfo->PUBLIC_KEY;
		$password = ""; 

		$authorizationCode = base64_encode($username.":".$password);
		PayMayaSDK::getInstance()->initCheckout($pgwInfo->PUBLIC_KEY, $pgwInfo->SECRET_KEY, PAYMAYA_ENV);
		  
		$email = $_POST['email']; 
		$merchantRefCode = $_POST['merchant_ref_code']; 
		$referenceNumber = $_POST['TRN'];
		$amount = $_POST['amount'];
	 	$successURL = $_POST['response_url']."PAGES/PHP/PAYMENT-HOME-PAGE/E-WALLET/paymaya-response.php?STATUS=1";
	 	$failureURL = $_POST['response_url']."PAGES/PHP/PAYMENT-HOME-PAGE/E-WALLET/paymaya-response.php?STATUS=0";
	 	$cancelURL = $_POST['response_url']."PAGES/PHP/PAYMENT-HOME-PAGE/E-WALLET/paymaya-response.php?STATUS=0";
	 	$currency = "PHP";

	 	$ewalletPayment = array();

	 	$ewalletPayment['totalAmount']['currency'] = $currency; 
	 	$ewalletPayment['totalAmount']['value'] = $amount;
 
	 	$ewalletPayment['redirectUrl']['success'] = $successURL; 
	 	$ewalletPayment['redirectUrl']['failure'] = $failureURL;
	 	$ewalletPayment['redirectUrl']['cancel'] = $cancelURL;

	 	$ewalletPayment['requestReferenceNumber'] = $referenceNumber; 
	 	$ewalletPayment['metadata']['subMerchantRequestReferenceNumber'] = $referenceNumber;
	 	/*
		$ewalletPayment['metadata']['pf']['smi'] = 'SUB034221'; 
	 	$ewalletPayment['metadata']['pf']['smn'] = 'FP'; 
	 	$ewalletPayment['metadata']['pf']['mci'] = 'MANILA'; 
	 	$ewalletPayment['metadata']['pf']['mpc'] = '608'; 
	 	$ewalletPayment['metadata']['pf']['mco'] = 'PHL'; 
	 	$ewalletPayment['metadata']['pf']['mcc'] = '3415'; 
	 	$ewalletPayment['metadata']['pf']['postalCode'] = '1001'; 
	 	$ewalletPayment['metadata']['pf']['contactNo'] = '+6329112345'; 
	 	$ewalletPayment['metadata']['pf']['state'] = 'Metro Manila'; 
	 	$ewalletPayment['metadata']['pf']['addressLine1'] = 'Quezon Boulevard, Quiapo'; 
		*/
		
	 	$jsonEwalletPayment = json_encode($ewalletPayment);
		
		$headers = [
			'Authorization:Basic '.$authorizationCode,
			'Content-Type:application/json' 
		];

		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $paymayaLink);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonEwalletPayment);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$paymayaResponse = json_decode(curl_exec($ch)); 
		curl_close($ch);   

		$_SESSION['IPG_PUBLIC']['PAYMAYA']['CHECKOUT_ID'] = $paymayaResponse->paymentId;
		$_SESSION['IPG_PUBLIC']['PAYMAYA']['CHECKOUT_URL'] = $paymayaResponse->redirectUrl;
		$_SESSION['IPG_PUBLIC']['PAYMAYA']['IPG_URL'] = $_POST['response_url'];
		$response['status_code'] = '201';
		$response['redirection_link'] = $paymayaResponse->redirectUrl;
	}else{ 
		$response['message'] = "Something went wrong.";
	}
 
	echo json_encode($response); 
?>