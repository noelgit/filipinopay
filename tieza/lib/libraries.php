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
	$dbTieza = new db(DB_TIEZA_USER, DB_TIEZA_PASSWORD, DB_TIEZA_NAME, DB_TIEZA_HOST);
	$helper = new Helper;	  
	$custom = new custom;	 
	$emailTemplate = new EmailTemplate(); 
?>