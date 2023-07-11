<?php  
	require_once __DIR__.'/server.php';   
	$getTRN = $_GET['TRN'];
	$getAR  = $_GET['AR'];
	$getMCHCode  = $_GET['MCH_CODE']; 
 
	$transactionArr = array(); 
	$transactionArr['TRN'] = $getTRN;
	$transactionArr['AR_REF'] = $getAR; 
	$transactionArr['MERCHANT_CODE'] = $getMCHCode; 
	$transactionArr['TRANS_STATUS'] = '3';  
	$transaction = $dbEnterprise->getRow("SELECT * 
		FROM tbl_transactions_hdr 
		WHERE TRN = :TRN AND AR_REF = :AR_REF AND MERCHANT_CODE = :MERCHANT_CODE AND TRANS_STATUS = :TRANS_STATUS LIMIT 1", $transactionArr);

	if($transaction){ 	
		echo "
			<html>
				<head>
					<title>IPG Acknowledgement Receipt</title>
				</head>
				<body style='font-family: arial, sans-serif; background: #f1f1f1;'>
					<div style='text-align: center; display: table; overflow: hidden; height: 100%; width: 100%; background: white; border-radius: 15px;'>
						<div style='display: table-cell; vertical-align: middle;'>
							<div style='text-align: left; width: 100%; position: absolute; left: 30px; top: 30px;'>
								<img src='".PUBLIC_URL."IMAGES/DBP-Logo.png' style='width:75px;'>
							</div>
							<h2 style='text-decoration: underline;'>AR No.: ".$transaction->AR_REF."</h2>
							<label><small>This AR No. is generated by the electronic payment facility and this is correct and valid.</small></label>
						</div>
					</div>
				</body>
			</html>";  
	}else{  
		echo "
			<html>
				<head>
					<title>IPG Acknowledgement Receipt</title>
				</head>
				<body style='font-family: arial, sans-serif; background: #f1f1f1;'>
					<div style='text-align: center; display: table; overflow: hidden; height: 100%; width: 100%; background: white; border-radius: 15px;'>
						<div style='display: table-cell; vertical-align: middle;'>
							<div style='text-align: left; width: 100%; position: absolute; left: 30px; top: 30px;'>
								<img src='".PUBLIC_URL."IMAGES/DBP-Logo.png' style='width:75px;'>
							</div>
							<h1>Invalid AR No.</h1>
							<label>No Data Found.</label>
						</div>
					</div>
				</body>
			</html>";  
 	}
?>