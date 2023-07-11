<?php  
	include("../../../../config.php");
	include("../../../../LIBRARIES/database.class.php");
	include("../../../../LIBRARIES/custom.class.php"); 	
	$timeStamp 	  = date('Y-m-d G:i:s');
	$dbGateway	  = new db(DB_USER_GATEWAY, DB_PASSWORD_GATEWAY, DB_NAME_GATEWAY, DB_HOST_GATEWAY); 
	error_reporting(0);
 
	$pgwInfo = $dbGateway->getRow("SELECT * FROM tbl_pgw_info WHERE PARTNER_CODE = '".$_POST['partnerCode']."' LIMIT 1");
	
	//Merchant's account information
	$merchant_id   = $pgwInfo->PARTNER_ID;	//Get MerchantID when opening account with 2C2P
	$secret_key    = $pgwInfo->PARTNER_KEY;	//Get SecretKey from 2C2P PGW Dashboard
	$currency  	   = $pgwInfo->CURRENCY_CODE;
	
	//Transaction information
	$payment_description  = $_POST['transactionDesc'];
	$order_id  = $_POST['TRN'];//time();
	$amount  = $_POST['amount'];
	$result_url_1 = BASE_URL."PAGES/PHP/PAYMENT-HOME-PAGE/UP-FRONT-PAYMENT/2c2p-response.php";
	
	//Payment Options
	$enable_store_card = "N"; //Enable / Disable Tokenization
	$request_3ds = "Y";	//Enable / Disable 3DS
	$payment_option = $_POST['paymentOption'];	//Customer Payment Options
	
	//Other params
	$customer_email='';
	$invoice_no ='';
	$pay_category_id = '';
	$promotion ='';
	$user_defined_1 ='';
	$user_defined_2 ='';
	$user_defined_3 ='';
	$user_defined_4 ='';
	$user_defined_5 ='';
	$result_url_2 ='';
	$stored_card_unique_id ='';
	$recurring='';
	$order_prefix='';
	$recurring_amount='';
	$allow_accumulate ='';
	$max_accumulate_amount ='';
	$recurring_interval ='';
	$recurring_count ='';
	$charge_next_date ='';
	$charge_on_date='';
	$ipp_interest_type ='';
	$payment_expiry ='';
	$default_lang ='';
	$statement_descriptor ='';
	$use_storedcard_only = '';
	$tokenize_without_authorization ='';
	$product ='';
	$ipp_period_filter ='';
	$sub_merchant_list ='';
	$qr_type = '';
	$custom_route_id ='';
	$airline_transaction ='';
	$airline_passenger_list ='';
	$address_list ='';
	
	
	//Request information
	$version = "8.5";	
	$payment_url = "https://demo2.2c2p.com/2C2PFrontEnd/RedirectV3/payment";
	//Construct signature string
	$params = $version . $merchant_id . $payment_description . $order_id . $invoice_no . 
	$currency . $amount . $customer_email . $pay_category_id . $promotion . $user_defined_1 . 
	$user_defined_2 . $user_defined_3 . $user_defined_4 . $user_defined_5 . $result_url_1 . 
	$result_url_2 . $enable_store_card . $stored_card_unique_id . $request_3ds . $recurring . 
	$order_prefix . $recurring_amount . $allow_accumulate . $max_accumulate_amount . 
	$recurring_interval . $recurring_count . $charge_next_date. $charge_on_date . $payment_option . 
	$ipp_interest_type . $payment_expiry . $default_lang . $statement_descriptor . $use_storedcard_only .
	$tokenize_without_authorization . $product . $ipp_period_filter . $sub_merchant_list . $qr_type .
	$custom_route_id . $airline_transaction . $airline_passenger_list . $address_list;

	$hash_value = hash_hmac('sha256',$params, $secret_key,false);	//Compute hash value
	
	$response = array();
	$response['payment_url'] = $payment_url;
	$response['version'] = $version;
	$response['merchant_id'] = $merchant_id;
	$response['currency'] = $currency;
	$response['result_url_1'] = $result_url_1;
	$response['enable_store_card'] = $enable_store_card;
	$response['request_3ds'] = $request_3ds;
	$response['payment_option'] = $payment_option;
	$response['hash_value'] = $hash_value;
	$response['payment_description'] = $payment_description;
	$response['order_id'] = $order_id;
	$response['amount'] = $amount;
	echo json_encode($response);
?>