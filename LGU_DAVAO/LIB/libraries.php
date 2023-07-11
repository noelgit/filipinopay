<?php    
	require ('cryptor.class.php');    	
	require ('helper.class.php');
	require ('hash.crypt.class.php');
	require ('database.class.php'); 
	require ('custom.class.php'); 
	require ('email-template.php');   
 	
	//PHPMailer 
	require ('phpmailer/src/PHPMailer.php'); 
	require ('phpmailer/src/SMTP.php');
	require ('phpmailer/src/Exception.php');

 	$timeStamp = date('Y-m-d G:i:s'); 
	$mail = new PHPMailer\PHPMailer\PHPMailer(true); 
	$dbLGUDavao = new db(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
	$helper = new Helper;	  
	$custom = new custom;	
	$emailTemplate = new EmailTemplate(); 
?>