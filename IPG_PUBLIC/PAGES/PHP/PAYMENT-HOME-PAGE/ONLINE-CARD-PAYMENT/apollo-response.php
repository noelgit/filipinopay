<?php  
	include("../../../../vendor/autoload.php");
	include("../../../../config.php"); 
	include("../../../../LIBRARIES/libraries.php");
	error_reporting(0);
	$timeStamp 	  = date('Y-m-d G:i:s'); 
	$arrayFailed  = array('06','05');
				
	$referenceCode 		  = isset($_REQUEST['referenceCode']) ? $_REQUEST['referenceCode'] : 'N/A' ;
	$serviceType 		  = isset($_REQUEST['serviceType']) ? $_REQUEST['serviceType'] : 'N/A' ;
	$amount 			  = isset($_REQUEST['amount']) ? $_REQUEST['amount'] : 'N/A' ;
	$email 		 		  = isset($_REQUEST['email']) ? $_REQUEST['email'] : 'N/A' ;
	$serviceChargeFeeText = isset($_REQUEST['serviceChargeFeeText']) ? $_REQUEST['serviceChargeFeeText'] : 'N/A' ;
	$returnURL 			  = isset($_REQUEST['returnURL']) ? $_REQUEST['returnURL'] : 'N/A' ;
	$disableEmailClient   = isset($_REQUEST['disableEmailClient']) ? $_REQUEST['disableEmailClient'] : 'N/A' ;
	$merchantName 	      = isset($_REQUEST['merchantName']) ? $_REQUEST['merchantName'] : 'N/A' ;
	$serviceFeeLabel      = isset($_REQUEST['serviceFeeLabel']) ? $_REQUEST['serviceFeeLabel'] : 'N/A' ;
	$serviceChargeFee     = isset($_REQUEST['serviceChargeFee']) ? $_REQUEST['serviceChargeFee'] : 'N/A' ;
	$total 				  = isset($_REQUEST['total']) ? $_REQUEST['total'] : 'N/A' ;
	$interceptor 		  = isset($_REQUEST['interceptor']) ? $_REQUEST['interceptor'] : 'N/A';
	$responseCode 		  = isset($_REQUEST['responseCode']) ? $_REQUEST['responseCode'] : 'N/A';	
	$retrievalReferenceCode = isset($_REQUEST['retrievalReferenceCode']) ? $_REQUEST['retrievalReferenceCode'] : 'N/A';	
	$message 			  = isset($_REQUEST['message']) ? $_REQUEST['message'] : 'N/A' ;	 
 
	//Insert Data to IPG_GATEWAY
	$reponseTransInsert  = array();
	$reponseTransInsert['referenceCode']		= $referenceCode;
	$reponseTransInsert['serviceType'] 			= $serviceType;
	$reponseTransInsert['amount'] 				= $amount;
	$reponseTransInsert['serviceChargeFee']     = $serviceChargeFeeText;
	$reponseTransInsert['returnURL'] 			= $returnURL;
	$reponseTransInsert['email'] 				= $email;
	$reponseTransInsert['merchantName'] 		= $merchantName;
	$reponseTransInsert['serviceFeeLabel'] 		= $serviceFeeLabel;
	$reponseTransInsert['serviceChargeFee'] 	= $serviceChargeFee;
	$reponseTransInsert['total'] 				= $total;
	$reponseTransInsert['interceptor'] 			= $interceptor;
	$reponseTransInsert['responseCode'] 		= $responseCode;
	$reponseTransInsert['retrievalReferenceCode'] = $retrievalReferenceCode;
	$reponseTransInsert['message'] 		        = $message;
	$reponseTransInsert['transDate'] 		    = $timeStamp;
	
	$insertData = $dbGateway->insert("tbl_apollo_response_trans",$reponseTransInsert);	


	if($responseCode == "N/A"){//Success
		header("Location: ".BASE_URL."?PAYMENT_STATUS=1"); 	  
	}else{ 
 		header("Location: ".BASE_URL."?PAYMENT_STATUS=0"); 	 
	}
	/*
	if(in_array($_REQUEST['responseCode'], $arrayFailed)){ //Failed
 		header("Location: ".BASE_URL."?PAYMENT_STATUS=0"); 	 
 	}else{
		header("Location: ".BASE_URL."?PAYMENT_STATUS=1"); 	 
 	}
 	*/
?>