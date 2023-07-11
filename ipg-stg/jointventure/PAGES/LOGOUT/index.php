<?php 
	include('../../../config.php'); 

	/****************************
		INSERT tbl_audit_trail
	*****************************/			 
	$auditArr = array();
	$auditArr['COMPANY_CODE'] 	  = $_SESSION['JV']['COMPANY_CODE']; 
	$auditArr['EVENT_TYPE']    	  = 'LOGOUT';
	$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
	$auditArr['EVENT_REMARKS'] 	  = 'SUCCESSFULLY LOGOUT';
	$auditArr['CREATED_DATE']	  = $timeStamp;
	$auditArr['CREATED_BY']		  = $_SESSION['JV']['EMAILADDRESS'];

	$insertAudit = $dbJV->insert("tbl_audit_trail",$auditArr);

	unset($_SESSION["JV"]); 
	header("Location: ".BASE_URL);
?>