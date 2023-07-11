<?php  
	header('Content-type: application/json');
	session_start();
	include("../../../../vendor/autoload.php");
	include("../../../../config.php"); 
	include("../../../../LIBRARIES/libraries.php");
	error_reporting(0);

	use PayMaya\PayMayaSDK;
	use PayMaya\API\Checkout;  
	use PayMaya\Model\Checkout\Item;  
	use PayMaya\Model\Checkout\Contact;
	use PayMaya\Model\Checkout\ItemAmount; 
	use PayMaya\Model\Checkout\ItemAmountDetails;
	$timeStamp 	  = date('Y-m-d G:i:s'); 
	$pgwInfo = $dbGateway->getRow("SELECT * FROM tbl_pgw_info WHERE PARTNER_CODE = '".$_POST['partnerCode']."' LIMIT 1");
	
	$response = array();
	if($pgwInfo){ 

		PayMayaSDK::getInstance()->initCheckout($pgwInfo->PUBLIC_KEY, $pgwInfo->SECRET_KEY, PAYMAYA_ENV);

		$firstName = $_POST['fname'];
		$middleName = $_POST['mname'];
		$lastName = $_POST['lname'];
		$contact = $_POST['contactNo']; 
		$email = $_POST['email']; 
		$referenceNumber = $_POST['TRN'];
	 	$successURL = $_POST['response_url']."PAGES/PHP/PAYMENT-HOME-PAGE/ONLINE-CARD-PAYMENT/paymaya-response.php?STATUS=1";
	 	$failureURL = $_POST['response_url']."PAGES/PHP/PAYMENT-HOME-PAGE/ONLINE-CARD-PAYMENT/paymaya-response.php?STATUS=0";
	 	$cancelURL = $_POST['response_url']."?PAGES/PHP/PAYMENT-HOME-PAGE/ONLINE-CARD-PAYMENT/paymaya-response.php?STATUS=0";
	 	$currency = "PHP";

	 	$itemData = array();
	 	$i = 0;
	 	foreach(json_decode($_POST['transactionDetails']) as $data){
	 		$itemData[$i]['NAME'] = $data->transaction_payment_for; 
	 		$itemData[$i]['VALUE'] = $data->transaction_amount;

	 		$i++;
	 	}	
 
	 
		$itemAmount = new ItemAmount();
		$itemCheckout = new Checkout(); 
		$buyerContact->contact = new Contact(); 
		
		
		// Contact	
		$buyerContact->contact->phone = $contact;
		$buyerContact->contact->email = $email;
		$buyerData->contact = $buyerContact;

		$buyerData->firstName = $firstName;
		$buyerData->middleName = $middleName;
		$buyerData->lastName = $lastName; 
	 
		$i = 0;  
		$itemAmount->currency = $currency;	
		$subTotal;
		$items = array();
		foreach($itemData as $data){ 
			$itemAm[$i]->currency = $currency;
			$itemAm[$i]->value = $data['VALUE']; 

			$item[$i]->name = $data['NAME'];
			$item[$i]->quantity = $data['QTY']; 
			$item[$i]->amount = $itemAm[$i]; 
			$item[$i]->totalAmount = $itemAm[$i];
	 
			$itemAmount->value += $data['VALUE']; //Total Amount 
			$subTotal += $data['VALUE'];
			$i++;
		} 
	 	$itemAmount->value += $_POST['convenienceFee']; 
	 
		$itemAmountDetails->serviceCharge = $_POST['convenienceFee']; 
		//$itemAmountDetails->subtotal = $subTotal;
		//$itemAmount->details = $itemAmountDetails; 

		// Checkout 
		$itemCheckout->buyer = $buyerData;
		//$itemCheckout->items = $item;
		$itemCheckout->totalAmount = $itemAmount;
		$itemCheckout->requestReferenceNumber = $referenceNumber;
		$itemCheckout->redirectUrl = array(
			"success" => $successURL,
			"failure" => $failureURL,
			"cancel" => $cancelURL
		);
	 
		$itemCheckout->execute();
		$itemCheckout->retrieve();

		//echo "Checkout ID: ".$itemCheckout->id."<br>";
		//echo "Checkout URL: ".$itemCheckout->url;
		$_SESSION['IPG_PUBLIC']['PAYMAYA']['CHECKOUT_ID'] = $itemCheckout->id;
		$_SESSION['IPG_PUBLIC']['PAYMAYA']['CHECKOUT_URL'] = $itemCheckout->url;
		$_SESSION['IPG_PUBLIC']['PAYMAYA']['IPG_URL'] = $_POST['response_url'];
		$response['status_code'] = '201';
		$response['redirection_link'] = $itemCheckout->url;
	}else{
		$response['message'] = "Something went wrong.";
	}

	echo json_encode($response); 
?>