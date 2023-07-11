<?php 
	session_start();
	include("../../../config.php");

 	$getMerchantCode = $_GET['MERCHANT_CODE'];
	$getSecureParam  = $_GET['SECUREPARAM'];
	$type  = $_GET['TYPE'];

	if($type == 'RETRY'){
		unset($_SESSION['IPG_PUBLIC']);
		$redirectUrl = BASE_URL."?secureparam=".$getSecureParam."&merchantcode=".$getMerchantCode;
		header('Location: '.$redirectUrl, true);
		exit();	
	}else{
		echo "Something went wrong, please call the Administrator";
		exit();	
	}
?>