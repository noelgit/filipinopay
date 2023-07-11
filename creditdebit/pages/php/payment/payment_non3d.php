<?php 
	include("../../../config.php");
	include("../../../lib/libraries.php");
	header('Content-type: application/json');
	session_start();  

	$pgwInfo = $IPGGateway->getRow("SELECT * FROM tbl_pgw_info WHERE PARTNER_CODE = '2C2P' LIMIT 1");

	//Merchant's account information
	$merchantID = $pgwInfo->PARTNER_ID;		//Get MerchantID when opening account with 2C2P
	$secretKey = $pgwInfo->PARTNER_KEY;	//Get SecretKey from 2C2P PGW Dashboard

	//Transaction Information
	$desc = $_POST['paymentFor'];
	$uniqueTransactionCode = $_POST['TRN'];//"C" . time();
	$amt = sprintf("%012d", $_POST['amount']);
	$currencyCode = $pgwInfo->CURRENCY_CODE;
	$panCountry = COUNTRY_CODE;

	//Customer Information
	$cardholderName = $_POST['cardName'];
	$customer_email = $_POST['email'];
 
	//Encrypted card data 
	$encCardData = $_POST['encryptedCardInfo'];

	//Retrieve card information for merchant use if needed
	$maskedCardNo = $_POST['maskedCardInfo'];
	$expMonth = $_POST['expMonthCardInfo'];
	$expYear = $_POST['expYearCardInfo'];

	//Payment Options
	$storeCard = "Y";		//Enable / Disable Tokenization
	
	//Request Information 
	$version = "9.9";  
	
	//Construct payment request message
	$xml = "<PaymentRequest>
		<merchantID>$merchantID</merchantID>
		<uniqueTransactionCode>$uniqueTransactionCode</uniqueTransactionCode>
		<desc>$desc</desc>
		<amt>$amt</amt>
		<currencyCode>$currencyCode</currencyCode>  
		<panCountry>$panCountry</panCountry> 
		<cardholderName>$cardholderName</cardholderName>
		<cardholderEmail>$customer_email</cardholderEmail>
		<storeCard>$storeCard</storeCard>
		<encCardData>$encCardData</encCardData>
		</PaymentRequest>"; 
	$paymentPayload = base64_encode($xml); //Convert payload to base64
	$signature = strtoupper(hash_hmac('sha256', $paymentPayload, $secretKey, false));
	$payloadXML = "<PaymentRequest>
           <version>$version</version>
           <payload>$paymentPayload</payload>
		   <signature>$signature</signature>
           </PaymentRequest>"; 
	$data = base64_encode($payloadXML); //Convert payload to base64
    $payload = urlencode($data);        //encode with base64
	
	include_once('HTTP.php');
	
	//Send authorization request
	$http = new HTTP();
	$response = $http->post("https://s.2c2p.com/SecurePayment/Payment.aspx","paymentRequest=".$payload);
	
	//decode response with base64
	$reponsePayLoadXML = base64_decode($response);
	
	//Parse ResponseXML
	$xmlObject =simplexml_load_string($reponsePayLoadXML) or die("Error: Cannot create object");
	
	//decode payload with base64 to get the Reponse
	$payloadxml = base64_decode($xmlObject->payload); 
 

	//echo "Response) ? : '' :<br/><textarea style='width:100%;height:80px'>". $payloadxml."</textarea>"; 
	$dom = new DOMDocument;
	$dom->preserveWhiteSpace = FALSE;
	$dom->loadXML($payloadxml);

	//Save XML as a file
	$dom->save('sitemap.xml');

	$xml = simplexml_load_file('sitemap.xml');
	$payloadData = json_decode(json_encode($xml)); 
 
	unlink('sitemap.xml');

	//Insert Data to ipg_gateway  
	$REQUEST_TIMESTAMP 	   	   = isset($payloadData->timeStamp) ? !is_object($payloadData->timeStamp) ? $payloadData->timeStamp : '' : '' ;
	$MERCHANT_ID 	   	       = isset($payloadData->merchantID) ? !is_object($payloadData->merchantID) ? $payloadData->merchantID : '' : '' ;
	$RESPCODE 			  	   = isset($payloadData->respCode) ? !is_object($payloadData->respCode) ? $payloadData->respCode : '' : '' ;
	$PAN 				  	   = isset($payloadData->pan) ? !is_object($payloadData->pan) ? $payloadData->pan : '' : '' ;
	$AMT 				  	   = isset($payloadData->amt) ? !is_object($payloadData->amt) ? $payloadData->amt : '' : '' ;
	$UNIQUETRANSACTIONCODE     = isset($payloadData->uniqueTransactionCode) ? !is_object($payloadData->uniqueTransactionCode) ? $payloadData->uniqueTransactionCode : '' : '' ;
	$TRANREF 			       = isset($payloadData->tranRef) ? !is_object($payloadData->tranRef) ? $payloadData->tranRef : '' : '' ;
	$APPROVAL_CODE 		   	   = isset($payloadData->approvalCode) ? !is_object($payloadData->approvalCode) ? $payloadData->approvalCode : '' : '' ;
	$ECI 		   		  	   = isset($payloadData->eci) ? !is_object($payloadData->eci) ? $payloadData->eci : '' : '' ;
	$DATETIME 		  		   = isset($payloadData->dateTime) ? !is_object($payloadData->dateTime) ? $payloadData->dateTime : '' : '' ;
	$STATUS 		   		   = isset($payloadData->status) ? !is_object($payloadData->status) ? $payloadData->status : '' : '' ;
	$FAILREASON 		   	   = isset($payloadData->failReason) ? !is_object($payloadData->failReason) ? $payloadData->failReason : '' : '' ;
	$USER_DEFINED_1 		   = isset($payloadData->userDefined1) ? !is_object($payloadData->userDefined1) ? $payloadData->userDefined1 : '' : '' ;
	$USER_DEFINED_2 		   = isset($payloadData->userDefined2) ? !is_object($payloadData->userDefined2) ? $payloadData->userDefined2 : '' : '' ;
	$USER_DEFINED_3 		   = isset($payloadData->userDefined3) ? !is_object($payloadData->userDefined3) ? $payloadData->userDefined3 : '' : '' ;
	$USER_DEFINED_4 		   = isset($payloadData->userDefined4) ? !is_object($payloadData->userDefined4) ? $payloadData->userDefined4 : '' : '' ;
	$USER_DEFINED_5 		   = isset($payloadData->userDefined5) ? !is_object($payloadData->userDefined5) ? $payloadData->userDefined5 : '' : '' ;
	$STORE_CARD_UNIQUE_ID 	   = isset($payloadData->storeCardUniqueID) ? !is_object($payloadData->storeCardUniqueID) ? $payloadData->storeCardUniqueID : '' : '' ;
	$IPP_PERIOD 		   	   = isset($payloadData->ippPeriod) ? !is_object($payloadData->ippPeriod) ? $payloadData->ippPeriod : '' : '' ;
	$IPP_INTERESTTYPE 		   = isset($payloadData->ippInterestType) ? !is_object($payloadData->ippInterestType) ? $payloadData->ippInterestType : '' : '' ;
	$IPP_INTEREST_RATE 		   = isset($payloadData->ippInterestRate) ? !is_object($payloadData->ippInterestRate) ? $payloadData->ippInterestRate : '' : '' ;
	$IPP_MERCHANT_ABSORB_RATE  = isset($payloadData->ippMerchantAbsorbRate) ? !is_object($payloadData->ippMerchantAbsorbRate) ? $payloadData->ippMerchantAbsorbRate : '' : '' ;
	$PAID_CHANNEL 		   	   = isset($payloadData->paidChannel) ? !is_object($payloadData->paidChannel) ? $payloadData->paidChannel : '' : '' ;
	$PAID_AGENT 		   	   = isset($payloadData->paidAgent) ? !is_object($payloadData->paidAgent) ? $payloadData->paidAgent : '' : '' ;
	$PAYMENT_CHANNEL 		   = isset($payloadData->paymentChannel) ? !is_object($payloadData->paymentChannel) ? $payloadData->paymentChannel : '' : '' ;
	$BACKEND_INVOICE 		   = isset($payloadData->backendInvoice) ? !is_object($payloadData->backendInvoice) ? $payloadData->backendInvoice : '' : '' ;
	$ISSUER_COUNTRY 		   = isset($payloadData->issuerCountry) ? !is_object($payloadData->issuerCountry) ? $payloadData->issuerCountry : '' : '' ;
	$BANK_NAME 		  		   = isset($payloadData->bankName) ? !is_object($payloadData->bankName) ? $payloadData->bankName : '' : '' ;
	$PROCESS_BY 		   	   = isset($payloadData->processBy) ? !is_object($payloadData->processBy) ? $payloadData->processBy : '' : '' ;
	$PAYMENT_SCHEME 		   = isset($payloadData->paymentScheme) ? !is_object($payloadData->paymentScheme) ? $payloadData->paymentScheme : '' : '' ;
	$RATE_QUOTE_ID 		   	   = isset($payloadData->rateQuoteID) ? !is_object($payloadData->rateQuoteID) ? $payloadData->rateQuoteID : '' : '' ;
	$ORIGINAL_AMOUNT 		   = isset($payloadData->originalAmount) ? !is_object($payloadData->originalAmount) ? $payloadData->originalAmount : '' : '' ;
	$FX_RATE 		  		   = isset($payloadData->fxRate) ? !is_object($payloadData->fxRate) ? $payloadData->fxRate : '' : '' ;
	$CURENCY_CODE 		   	   = isset($payloadData->currencyCode) ? !is_object($payloadData->currencyCode) ? $payloadData->currencyCode : '' : '' ;


	$reponseTransInsert  = array();
	$reponseTransInsert['REQUEST_TIMESTAMP']		 = $REQUEST_TIMESTAMP;
	$reponseTransInsert['MERCHANT_ID'] 				 = $MERCHANT_ID;
	$reponseTransInsert['RESPCODE'] 			 	 = $RESPCODE;
	$reponseTransInsert['PAN'] 					 	 = $PAN;
	$reponseTransInsert['AMT'] 					  	 = $AMT;
	$reponseTransInsert['UNIQUETRANSACTIONCODE']	 = $UNIQUETRANSACTIONCODE;
	$reponseTransInsert['TRANREF']					 = $TRANREF;
	$reponseTransInsert['APPROVAL_CODE']			 = $APPROVAL_CODE;
	$reponseTransInsert['ECI']						 = $ECI;
	$reponseTransInsert['DATETIME']					 = $DATETIME;
	$reponseTransInsert['STATUS']					 = $STATUS;
	$reponseTransInsert['FAILREASON']				 = $FAILREASON;
	$reponseTransInsert['USER_DEFINED_1']			 = $USER_DEFINED_1;
	$reponseTransInsert['USER_DEFINED_2']			 = $USER_DEFINED_2;
	$reponseTransInsert['USER_DEFINED_3'] 			 = $USER_DEFINED_3;
	$reponseTransInsert['USER_DEFINED_4']			 = $USER_DEFINED_4;
	$reponseTransInsert['USER_DEFINED_5']			 = $USER_DEFINED_5;
	$reponseTransInsert['STORE_CARD_UNIQUE_ID']		 = $STORE_CARD_UNIQUE_ID;
	$reponseTransInsert['IPP_PERIOD']				 = $IPP_PERIOD;
	$reponseTransInsert['IPP_INTERESTTYPE']			 = $IPP_INTERESTTYPE;
	$reponseTransInsert['IPP_INTEREST_RATE']		 = $IPP_INTEREST_RATE;
	$reponseTransInsert['IPP_MERCHANT_ABSORB_RATE']	 = $IPP_MERCHANT_ABSORB_RATE;
	$reponseTransInsert['PAID_CHANNEL']				 = $PAID_CHANNEL;
	$reponseTransInsert['PAID_AGENT']				 = $PAID_AGENT;
	$reponseTransInsert['PAYMENT_CHANNEL']			 = $PAYMENT_CHANNEL;
	$reponseTransInsert['BACKEND_INVOICE']			 = $BACKEND_INVOICE;
	$reponseTransInsert['ISSUER_COUNTRY']			 = $ISSUER_COUNTRY;
	$reponseTransInsert['BANK_NAME']				 = $BANK_NAME;
	$reponseTransInsert['PROCESS_BY']				 = $PROCESS_BY;
	$reponseTransInsert['PAYMENT_SCHEME']			 = $PAYMENT_SCHEME;
	$reponseTransInsert['RATE_QUOTE_ID']			 = $RATE_QUOTE_ID;
	$reponseTransInsert['ORIGINAL_AMOUNT']			 = $ORIGINAL_AMOUNT;
	$reponseTransInsert['FX_RATE']					 = $FX_RATE;
	$reponseTransInsert['CURENCY_CODE']				 = $CURENCY_CODE; 

	$insertData = $IPGGateway->insert("tbl_2c2p_response_trans",$reponseTransInsert);

 
	if($STATUS == 'A' or $STATUS == 'a'){
		$responseData = array(); 
	 	$responseData['status_code'] = "201";
	 	$responseData['redirection_link'] = $_SESSION['IPG_DEBIT_CREDIT']['TRANSACTION']['response_url']."?PAYMENT_STATUS=1";
	 	$responseData['message'] = $FAILREASON;
		echo json_encode($responseData);
	}else{
		$responseData = array();  
	 	$responseData['redirection_link'] = $_SESSION['IPG_DEBIT_CREDIT']['TRANSACTION']['response_url']."?PAYMENT_STATUS=0";
	 	$responseData['message'] = $FAILREASON;
		echo json_encode($responseData);

	}
?>
 