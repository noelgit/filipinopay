<?php 
	session_start();
	include("../../../vendor/autoload.php");
	include("../../../config.php");	  
	include("../../../LIBRARIES/libraries.php");
 	error_reporting(0); 
	
	$timeStamp 	  = date('Y-m-d G:i:s'); 

	echo $_SESSION['IPG_PUBLIC']['TermsAndCondition'] = '1';

 	$getMerchantCode = $_SESSION['IPG_PUBLIC']['MERCHANT']->merchant_code;
	$getSecureParam  = $_SESSION['IPG_PUBLIC']['SECUREPARAM']; 
	$encryption_key = $getMerchantCode;
	$Cryptor = new Cryptor($encryption_key);
	$TRN = $Cryptor->decrypt($getSecureParam);   
	$payeeQueryData = array();
	$payeeQueryData['TRN'] = $TRN;
	$payeeInfo = $dbEnterprise->getRow("SELECT * FROM tbl_transactions_hdr WHERE TRN = :TRN LIMIT 1", $payeeQueryData);
	 
	$auditArr = array();
	$auditArr['TRN'] = $TRN;
	$auditArr['MERCHANT_CODE'] = $_SESSION['IPG_PUBLIC']['MERCHANT']->merchant_code;
	$auditArr['EVENT_TYPE'] = 'SELECT';
	$auditArr['EVENT_REMARKS'] = '';
	$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
	$auditArr['EVENT_REMARKS'] = 'TERMS AND CONDITIONS';
	$auditArr['CREATED_DATE'] = $timeStamp;
	$auditArr['CREATED_BY'] = $payeeInfo->REQUESTOR_EMAIL_ADDRESS;

	$insertAudit = $dbEnterprise->insert("tbl_audit_trail",$auditArr); 
	 
	if($insertAudit){
		echo true;
	}else{
		echo false; 
	}	
?>