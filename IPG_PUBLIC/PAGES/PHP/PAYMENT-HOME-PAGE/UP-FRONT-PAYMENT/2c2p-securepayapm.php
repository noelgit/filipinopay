<?php  
	include("../../../../config.php");
	include("../../../../LIBRARIES/database.class.php");
	include("../../../../LIBRARIES/custom.class.php"); 	
	$timeStamp 	  = date('Y-m-d G:i:s');
	$dbGateway	  = new db(DB_USER_GATEWAY, DB_PASSWORD_GATEWAY, DB_NAME_GATEWAY, DB_HOST_GATEWAY); 
	error_reporting(0);
 
	$pgwInfo = $dbGateway->getRow("SELECT * FROM tbl_pgw_info WHERE PARTNER_CODE = '".$_POST['partnerCode']."' LIMIT 1");

	//Merchant's account information
	$merchantID   = $pgwInfo->PARTNER_ID;	//Get MerchantID when opening account with 2C2P
	$secretKey    = $pgwInfo->PARTNER_KEY;	//Get SecretKey from 2C2P PGW Dashboard
	$currencyCode = $pgwInfo->CURRENCY_CODE;

	//Transaction Information
	$desc 				   = $_POST['transactionDesc'];
	$uniqueTransactionCode = $_POST['TRN'];//'2019051421';
	$amt  			 	   = $_POST['amount'];
	$mobileNo 		 	   = $_POST['contactNo']; //customer mobile number
	$cardholderEmail 	   = $_POST['emailAddress'];	//customer email address
 

	//Payment Options
	$paymentChannel  = "123"; //Set transaction as Alternative Payment Method
	$agentCode 		 = "BAYAD"; //APM agent code
	$channelCode 	 = "OVERTHECOUNTER"; //APM channel code


  
	$paymentExpiry = date("Y-m-d H:i:s", strtotime('+23 hours'));
 
	//Request Information 
	$version = "9.9";
	
	//Construct payment request message
	$xml = "<PaymentRequest>
		<merchantID>$merchantID</merchantID>
		<uniqueTransactionCode>$uniqueTransactionCode</uniqueTransactionCode>
		<desc>$desc</desc>
		<amt>$amt</amt>
		<currencyCode>$currencyCode</currencyCode>  
		<panCountry></panCountry> 
		<cardholderName></cardholderName>
		<paymentChannel>$paymentChannel</paymentChannel>
		<agentCode>$agentCode</agentCode>
		<channelCode>$channelCode</channelCode>
		<paymentExpiry>$paymentExpiry</paymentExpiry>
		<mobileNo>$mobileNo</mobileNo>
		<cardholderEmail>$cardholderEmail</cardholderEmail>
		<encCardData></encCardData>
		</PaymentRequest>"; 

	$paymentPayload = base64_encode($xml); //Convert payload to base64
	$signature = strtoupper(hash_hmac('sha256', $paymentPayload, $secretKey, false));
	$payloadXML = "<PaymentRequest>
           <version>$version</version>
           <payload>$paymentPayload</payload>
           <signature>$signature</signature>
           </PaymentRequest>"; 
	$payload = base64_encode($payloadXML); //encode with base64
 	 
 	echo $payload;
?>
 