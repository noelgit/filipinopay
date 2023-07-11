<?php 
	include('../../../config.php'); 				
	/****************************
		INSERT tbl_audit_trail
	*****************************/			 
	$auditArr = array();
	$auditArr['MERCHANT_CODE'] 	  = $_SESSION['MERCHANT']['MERCHANT_CODE']; 
	$auditArr['EVENT_TYPE']    	  = 'LOGOUT';
	$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
	$auditArr['EVENT_REMARKS'] 	  = 'SUCCESSFULLY LOGOUT';
	$auditArr['CREATED_DATE']	  = $timeStamp;
	$auditArr['CREATED_BY']		  = $_SESSION['MERCHANT']['EMAILADDRESS'];

	$insertAudit = $dbMerchant->insert("tbl_audit_trail",$auditArr);

	unset($_SESSION["MERCHANT"]);
	//session_destroy();
	header("Location: ".BASE_URL);
?>