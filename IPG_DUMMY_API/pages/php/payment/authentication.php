<?php  
	session_start();
	header('Content-type: application/json');
	include("../../../config.php");
	include("../../../lib/libraries.php");
  
	$credentials = array();
	$credentials['STATUS'] = '1';
	$credentials['MID'] = $_POST['mid'];
	$credentials['TRANSACTION_KEY'] = $_POST['security_token'];

	$validCredentials = $IPGDummyAPI->getRow("SELECT * FROM tbl_client_credentials AS tcc WHERE tcc.`MID` = :MID AND tcc.`TRANSACTION_KEY` = :TRANSACTION_KEY AND tcc.`STATUS` = :STATUS LIMIT 1",$credentials);
	unset($_SESSION['DUMMY']);
	if($validCredentials){
		$data = array(); 
		$data['MID'] = $_POST['mid'];
		$data['MERCHANT_REF_CODE'] = $_POST['merchant_ref_code'];
		$checkMerchant = $IPGDummyAPI->getRow("SELECT * FROM tbl_transactions AS tt WHERE tt.`MID` = :MID AND tt.`MERCHANT_REF_CODE` = :MERCHANT_REF_CODE LIMIT 1", $data);

		if(!$checkMerchant){	 
			$RRN = $custom->generateRandomString(15);
			$insertData = array(); 
			$insertData['MID'] = $_POST['mid'];
			$insertData['MERCHANT_REF_CODE'] = $_POST['merchant_ref_code'];
			$insertData['AMOUNT'] = $_POST['amount'];
			$insertData['PAYMENT_FOR'] = $_POST['payment_for'];
			$insertData['SECURITY_TOKEN'] = $_POST['security_token'];
			$insertData['PAYMENT_MODE'] = $_POST['payment_mode'];
			$insertData['RRN'] = $RRN;
			$insertData['PAYMENT_STATUS'] = "0";
			$insertData['RESPONSE_URL'] = $_POST['response_url'];
			$insertData['CREATED_DATE'] = "now";
			$insertData['CREATED_BY'] = "TEST APP";

			$insertData = $IPGDummyAPI->insert("tbl_transactions",$insertData);
			if($insertData){
				$jsonObj = array();
			 	$jsonObj['status_code'] = "201";
			 	$jsonObj['redirection_link'] = BASE_URL."payment";
				echo json_encode($jsonObj);
				$_SESSION['DUMMY']['TRANSACTION'] = $_POST;  
				$_SESSION['DUMMY']['TRANSACTION']['TRANSACTION_ID'] = $insertData;
				$_SESSION['DUMMY']['TRANSACTION']['RRN'] = $RRN;
			}else{
				$jsonObj = array(); 
			 	$jsonObj['message'] = "Something went wrong.";
				echo json_encode($jsonObj); 
			} 
		}else{
			$jsonObj = array(); 
		 	$jsonObj['message'] = "Merchant Reference Code already existed.";
			echo json_encode($jsonObj);
		} 
	}else{
		$jsonObj = array(); 
	 	$jsonObj['message'] = "Invalid credentials";
		echo json_encode($jsonObj);
	} 

?>