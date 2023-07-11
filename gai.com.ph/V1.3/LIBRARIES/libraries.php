<?php  
error_reporting(0); 
require ('cryptor.class.php' );

require ('database.class.php'); 
require ('custom.class.php'); 
require ('email-template.php');   

//PHPMailer 
require ('phpmailer/src/PHPMailer.php'); 
require ('phpmailer/src/SMTP.php');
require ('phpmailer/src/Exception.php');

$dbPublic 	  = new db(DB_USER_PUBLIC, DB_PASSWORD_PUBLIC, DB_NAME_PUBLIC, DB_HOST_PUBLIC); 
$dbMerchant   = new db(DB_USER_MERCHANT, DB_PASSWORD_MERCHANT, DB_NAME_MERCHANT, DB_HOST_MERCHANT); 
$dbEnterprise = new db(DB_USER_ENTERPRISE, DB_PASSWORD_ENTERPRISE, DB_NAME_ENTERPRISE, DB_HOST_ENTERPRISE); 
$dbJV 		  = new db(DB_USER_JV, DB_PASSWORD_JV, DB_NAME_JV, DB_HOST_JV); 
	
$custom = new custom;
$emailTemplate = new EmailTemplate(); 
?>