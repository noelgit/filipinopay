<?php 
	date_default_timezone_set("Asia/Manila"); 
	
	$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/"; 
	define("BASE_URL"	,	$actual_link."IPG_ENTERPRISE/"); 
	define("PUBLIC_URL"	,	$actual_link."IPG_PUBLIC/"); 

	//IPG_ENTERPRISE
	define("DB_USER_OAUTH"		,	"dennisjdizon03");
	define("DB_PASSWORD_OAUTH"	,	"ragMANOK2kx@djd");
	define("DB_NAME_OAUTH"		,	"ipg_oauth");
	define("DB_HOST_OAUTH"		,	"148.72.216.234"); 
	
	//IPG_PUBLIC
	define("DB_USER_PUBLIC"	   	,	"dennisjdizon03");
	define("DB_PASSWORD_PUBLIC"	,	"ragMANOK2kx@djd");
	define("DB_NAME_PUBLIC"	   	,	"ipg_public");
	define("DB_HOST_PUBLIC"		,	"148.72.216.234"); 

	//IPG_MERCHANT
	define("DB_USER_MERCHANT"	    ,	"dennisjdizon03");
	define("DB_PASSWORD_MERCHANT"	,	"ragMANOK2kx@djd");
	define("DB_NAME_MERCHANT"	   	,	"ipg_merchant");
	define("DB_HOST_MERCHANT"		,	"148.72.216.234"); 

	//IPG_JV
	define("DB_USER_JV"	    ,	"dennisjdizon03");
	define("DB_PASSWORD_JV"	,	"ragMANOK2kx@djd");
	define("DB_NAME_JV"	   	,	"ipg_jv");
	define("DB_HOST_JV"		,	"148.72.216.234"); 

	//IPG_ENTERPRISE
	define("DB_USER_ENTERPRISE"		,	"dennisjdizon03");
	define("DB_PASSWORD_ENTERPRISE"	,	"ragMANOK2kx@djd");
	define("DB_NAME_ENTERPRISE"		,	"ipg_enterprise");
	define("DB_HOST_ENTERPRISE"		,	"148.72.216.234"); 
	
	//EMAIL CONFIGURATION
	/*define("MAIL_HOST"			,	"smtp.gmail.com");
	define("MAIL_PORT"			,	587);
	define("MAIL_USERNAME"		,	"ipg.test.username@gmail.com");
	define("MAIL_PASSWORD"		,	"ipg.password");
	define("CONTACT_EMAIL_FROM"	,	"ipg.test.username@gmail.com");*/
	
	define("MAIL_HOST"			,	"mail.filipinopay.com");
	define("MAIL_PORT"			,	587);
	define("MAIL_USERNAME"		,	"ipg_web_app@filipinopay.com");
	define("MAIL_PASSWORD"		,	"ragMANOK2kx@djd");
	define("CONTACT_EMAIL_FROM"	,	"ipg_web_app@filipinopay.com"); 
?>