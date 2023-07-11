<?php   
	include("../../../../config.php");
	include("../../../../LIBRARIES/database.class.php");
	include("../../../../LIBRARIES/custom.class.php");
	$timeStamp 	  = date('Y-m-d G:i:s');
	$dbGateway	  = new db(DB_USER_GATEWAY, DB_PASSWORD_GATEWAY, DB_NAME_GATEWAY, DB_HOST_GATEWAY); 
	error_reporting(0);

	//each response params:
	$version 				= $_REQUEST["version"];
	$request_timestamp 		= $_REQUEST["request_timestamp"];
	$merchant_id 			= $_REQUEST["merchant_id"];
	$currency				= $_REQUEST["currency"];
	$order_id 				= $_REQUEST["order_id"];
	$amount 				= $_REQUEST["amount"];
	$invoice_no 			= $_REQUEST["invoice_no"];
	$transaction_ref 		= $_REQUEST["transaction_ref"];
	$approval_code 			= $_REQUEST["approval_code"];
	$eci 					= $_REQUEST["eci"];
	$transaction_datetime 	= $_REQUEST["transaction_datetime"];
	$payment_channel 		= $_REQUEST["payment_channel"];
	$payment_status 		= $_REQUEST["payment_status"];
	$channel_response_code 	= $_REQUEST["channel_response_code"];
	$channel_response_desc 	= $_REQUEST["channel_response_desc"];
	$masked_pan 			= $_REQUEST["masked_pan"];
	$stored_card_unique_id 	= $_REQUEST["stored_card_unique_id"];
	$backend_invoice 		= $_REQUEST["backend_invoice"];
	$paid_channel 			= $_REQUEST["paid_channel"];
	$recurring_unique_id 	= $_REQUEST["recurring_unique_id"];
	$paid_agent 			= $_REQUEST["paid_agent"];
	$payment_scheme 		= $_REQUEST["payment_scheme"];
	$user_defined_1 		= $_REQUEST["user_defined_1"];
	$user_defined_2 		= $_REQUEST["user_defined_2"];
	$user_defined_3 		= $_REQUEST["user_defined_3"];
	$user_defined_4 		= $_REQUEST["user_defined_4"];
	$user_defined_5 		= $_REQUEST["user_defined_5"];
	$browser_info 			= $_REQUEST["browser_info"];
	$ippPeriod 				= $_REQUEST["ippPeriod"];
	$ippInterestType 		= $_REQUEST["ippInterestType"];
	$ippInterestRate 		= $_REQUEST["ippInterestRate"];
	$ippMerchantAbsorbRate 	= $_REQUEST["ippMerchantAbsorbRate"];
	$payment_scheme 		= $_REQUEST["payment_scheme"];
	$process_by 			= $_REQUEST["process_by"];
	$sub_merchant_list		= $_REQUEST["sub_merchant_list"];
  	$hash_value 			= $_REQUEST["hash_value"];   


	$reponseTransInsert  = array();
	$reponseTransInsert['REQUEST_TIMESTAMP']		 = $request_timestamp;
	$reponseTransInsert['MERCHANT_ID'] 				 = $merchant_id;
	$reponseTransInsert['RESPCODE'] 			 	 = $channel_response_code;
	$reponseTransInsert['PAN'] 					 	 = $masked_pan;
	$reponseTransInsert['AMT'] 					  	 = $amount;
	$reponseTransInsert['UNIQUETRANSACTIONCODE']	 = $order_id;
	$reponseTransInsert['TRANREF']					 = $transaction_ref;
	$reponseTransInsert['APPROVAL_CODE']			 = $approval_code;
	$reponseTransInsert['ECI']						 = $eci;
	$reponseTransInsert['DATETIME']					 = $transaction_datetime;
	$reponseTransInsert['STATUS']					 = $payment_status;
	$reponseTransInsert['FAILREASON']				 = $channel_response_desc;
	$reponseTransInsert['USER_DEFINED_1']			 = $user_defined_1;
	$reponseTransInsert['USER_DEFINED_2']			 = $user_defined_2;
	$reponseTransInsert['USER_DEFINED_3'] 			 = $user_defined_3;
	$reponseTransInsert['USER_DEFINED_4']			 = $user_defined_4;
	$reponseTransInsert['USER_DEFINED_5']			 = $user_defined_5;
	$reponseTransInsert['STORE_CARD_UNIQUE_ID']		 = $stored_card_unique_id;
	$reponseTransInsert['IPP_PERIOD']				 = $ippPeriod;
	$reponseTransInsert['IPP_INTERESTTYPE']			 = $ippInterestType;
	$reponseTransInsert['IPP_INTEREST_RATE']		 = $ippInterestRate;
	$reponseTransInsert['IPP_MERCHANT_ABSORB_RATE']	 = $ippMerchantAbsorbRate;
	$reponseTransInsert['PAID_CHANNEL']				 = $paid_channel;
	//$reponseTransInsert['PAID_AGENT']				 = $PAID_AGENT;
	$reponseTransInsert['PAYMENT_CHANNEL']			 = $payment_channel;
	$reponseTransInsert['BACKEND_INVOICE']			 = $backend_invoice;
	//$reponseTransInsert['ISSUER_COUNTRY']			 = $ISSUER_COUNTRY;
	//$reponseTransInsert['BANK_NAME']				 = $BANK_NAME;
	$reponseTransInsert['PROCESS_BY']				 = $process_by;
	$reponseTransInsert['PAYMENT_SCHEME']			 = $payment_scheme;
	//$reponseTransInsert['RATE_QUOTE_ID']			 = $RATE_QUOTE_ID;
	$reponseTransInsert['ORIGINAL_AMOUNT']			 = $ORIGINAL_AMOUNT;
	//$reponseTransInsert['FX_RATE']					 = $FX_RATE;
	//$reponseTransInsert['CURENCY_CODE']				 = $CURENCY_CODE; 

	$insertData = $dbGateway->insert("tbl_2c2p_response_trans",$reponseTransInsert);

  	if($channel_response_code == '000' or $channel_response_code == '001'){
 		header("Location: ".BASE_URL."?PAYMENT_STATUS=1"); 
	}else{
 		header("Location: ".BASE_URL."?PAYMENT_STATUS=0"); 
	}

?>

 

