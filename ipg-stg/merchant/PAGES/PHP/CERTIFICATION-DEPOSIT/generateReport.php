<?php 
	include('../../../config.php');
	include('../../../LIBRARIES/libraries.php');
	session_start();
	error_reporting(0);

	$response = array();

	$merchantCode = $_SESSION['MERCHANT']['MERCHANT_CODE'];
	$undepositedDate = $_POST['UNDEPOSITED_DATE'];
	$undepositedAmount = $_POST['UNDEPOSITED_AMOUNT'];
	$fundTransferDate = $_POST['FUND_TRANSFER_DATE'];
	$fundTransferAmount = $_POST['FUND_TRANSFER_AMOUNT'];
 

	$merchantData = array();
	$merchantData['MERCHANT_CODE'] = $merchantCode;
	$merchantAccount = $dbMerchant->getRow("SELECT * FROM tbl_merchant_info 
		WHERE MERCHANT_CODE = :MERCHANT_CODE LIMIT 1 ", $merchantData);
 
	if($merchantAccount){
		$response['STATUS'] = 'SUCCESS';
		$response['REDIRECT_URL'] = ENTERPRISE_URL."generateCoD.php?mchCode=".$merchantCode."&ud=".$undepositedDate."&ua=".$undepositedAmount."&ftd=".$fundTransferDate."&fta=".$fundTransferAmount;
		
	}else{
		$response['STATUS'] = 'ERROR';
	
	}
	echo json_encode($response);	
?>