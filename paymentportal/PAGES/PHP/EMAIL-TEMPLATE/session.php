<?php 
	session_start();
	include("../../../config.php");

	$_SESSION['IPG_PUBLIC']['EMAILADDRESS'] = $_POST['emailAddress'];
	$_SESSION['IPG_PUBLIC']['CONTACTNO']	= $_POST['contactNo'];

	$response = array();
	if($_POST['partnerCode'] == "2C2P"){

		if($_POST['paymentEntity'] == 'UFP005'){  
			$response['REDIRECT'] = '2C2P';
			//$response['REDIRECT_URL'] = BASE_URL."PAGES/PHP/PAYMENT-HOME-PAGE/UP-FRONT-PAYMENT/2c2p-paymentoptionschannel.php"; 
			$response['REDIRECT_URL'] = BASE_URL."PAGES/PHP/PAYMENT-HOME-PAGE/UP-FRONT-PAYMENT/2c2p-securepayapm.php"; 
		}
		elseif($_POST['paymentEntity'] == 'EWP002'){ 
			$response['REDIRECT'] = '2C2P';
			$response['REDIRECT_URL'] = BASE_URL."PAGES/PHP/PAYMENT-HOME-PAGE/UP-FRONT-PAYMENT/2c2p-paymentoptionschannel.php"; 
		}else{ 
			$response['REDIRECT'] = 'FP';
			$response['REDIRECT_URL'] = BASE_URL."?PAYMENT_STATUS=1"; 
		} 
	}else{
		$response['REDIRECT'] = 'FP';
		$response['REDIRECT_URL'] = BASE_URL."?PAYMENT_STATUS=1"; 
	}

	echo json_encode($response);	
?>