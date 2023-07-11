<?php 
	session_start();
	header('Content-type: application/json');
	include("../../../config.php");
	include("../../../lib/libraries.php");
   
  	$cardNumber = $_POST['txt1'].$_POST['txt2'].$_POST['txt3'].$_POST['txt4'];
  	$expiryData = $_POST['txtExpiry1'].'-'.$_POST['txtExpiry2'];
  	$code	    = $_POST['txtCode'];

  	$cardCredentials = array();
	$cardCredentials['CARD_NO'] = $cardNumber; 
	$cardCredentials['SECURITY_CODE'] = $code;

	$validCardCredentials = $IPGDummyAPI->getRow("SELECT * FROM tbl_card AS tc WHERE tc.`CARD_NO` = :CARD_NO AND tc.`SECURITY_CODE` =:SECURITY_CODE LIMIT 1",$cardCredentials);
 
	if($validCardCredentials){
		$updateData = array();
		$updateData['PAYMENT_STATUS'] = '1';
		$transactionID = $_SESSION['DUMMY']['TRANSACTION']['TRANSACTION_ID'];
		$updateStatus = $IPGDummyAPI->update("tbl_transactions","TRANS_ID",$transactionID,$updateData);
		
		$jsonObj = array();
	 	$jsonObj['status_code'] = "201";
	 	$jsonObj['redirection_link'] = $_SESSION['DUMMY']['TRANSACTION']['response_url']."?RRN=".$_SESSION['DUMMY']['TRANSACTION']['RRN']."&PAYMENT_STATUS=1";
	 	$jsonObj['message'] = "Your payment was successfully processed.";
		echo json_encode($jsonObj);
	}else{ 
		$jsonObj = array(); 
	 	$jsonObj['redirection_link'] = $_SESSION['DUMMY']['TRANSACTION']['response_url']."?RRN=".$_SESSION['DUMMY']['TRANSACTION']['RRN']."&PAYMENT_STATUS=0";
	 	$jsonObj['message'] = "Invalid credentials";
		echo json_encode($jsonObj);
	}
?>