<?php     
	//require ('pdo.class.php');    
	require ('cryptor.class.php');
	require ('email-template.php');   

	require ('database.class.php'); 
	require ('custom.class.php'); 

	//PHPMailer 
	require ('phpmailer/src/PHPMailer.php'); 
	require ('phpmailer/src/SMTP.php');
	require ('phpmailer/src/Exception.php');

	require ('mpdf/src/Mpdf.php');     
 
	$dbPublic 	  = new db(DB_USER_PUBLIC, DB_PASSWORD_PUBLIC, DB_NAME_PUBLIC, DB_HOST_PUBLIC); 
	$dbMerchant   = new db(DB_USER_MERCHANT, DB_PASSWORD_MERCHANT, DB_NAME_MERCHANT, DB_HOST_MERCHANT); 
	$dbEnterprise = new db(DB_USER_ENTERPRISE, DB_PASSWORD_ENTERPRISE, DB_NAME_ENTERPRISE, DB_HOST_ENTERPRISE); 
	$dbJV 		  = new db(DB_USER_JV, DB_PASSWORD_JV, DB_NAME_JV, DB_HOST_JV); 
	$dbGateway	  = new db(DB_USER_GATEWAY, DB_PASSWORD_GATEWAY, DB_NAME_GATEWAY, DB_HOST_GATEWAY); 
	 
	$mpdf = new mPDF\mPDF();  
	$emailTemplate = new EmailTemplate(); 
	$custom = new custom;	

?>