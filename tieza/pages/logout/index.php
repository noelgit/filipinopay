<?php 
	include('../../../config.php');  
	/****************************
		INSERT tbl_audit_trail
	*****************************/			 
	$auditArr = array();
	$auditArr['MODULE'] 	  	  = 'LOGOUT'; 
	$auditArr['TRN']			  = 'N/A';
	$auditArr['EVENT_TYPE']    	  = 'SELECT';
	$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
	$auditArr['EVENT_REMARKS'] 	  = 'SUCCESSFULLY LOGOUT';
	$auditArr['CREATED_DATE']	  = $timeStamp;
	$auditArr['CREATED_BY']		  = $custom->upperCaseString($_SESSION['TIEZA']['EMAIL_ADDRESS']);
	$insertAudit = $dbTieza->insert("tbl_audit_trail",$auditArr);
	
	unset($_SESSION["TIEZA"]); 
	header("Location: ".BASE_URL);
?>