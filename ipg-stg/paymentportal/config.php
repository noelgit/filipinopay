<?php
	date_default_timezone_set("Asia/Manila");
	$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/"; 

	/**************************/
	/* 		    STG 		  */
	/**************************/  
	define("BASE_URL"	,	$actual_link."ipg-stg/paymentportal/"); 
	define("ENTERPRISE_URL"	,	$actual_link."ipg-stg/enterprise/"); 
	define("CARD_URL_2C2P"	,	$actual_link."ipg-stg/creditdebit/"); 
	define("CARD_URL_DUMMY"	,	$actual_link."IPG_DUMMY_API/"); 
	
	//IPG_PUBLIC
	define("DB_USER_PUBLIC"	   	,	"ipg-stg-user");
	define("DB_PASSWORD_PUBLIC"	,	"ragMANOK2kx@djd");
	define("DB_NAME_PUBLIC"	   	,	"ipg_stg_public");
	define("DB_HOST_PUBLIC"		,	"localhost"); 

	//IPG_MERCHANT
	define("DB_USER_MERCHANT"	    ,	"ipg-stg-user");
	define("DB_PASSWORD_MERCHANT"	,	"ragMANOK2kx@djd");
	define("DB_NAME_MERCHANT"	   	,	"ipg_stg_merchant");
	define("DB_HOST_MERCHANT"		,	"localhost"); 

	//IPG_JV
	define("DB_USER_JV"	    ,	"ipg-stg-user");
	define("DB_PASSWORD_JV"	,	"ragMANOK2kx@djd");
	define("DB_NAME_JV"	   	,	"ipg_stg_jv");
	define("DB_HOST_JV"		,	"localhost"); 

	//IPG_ENTERPRISE
	define("DB_USER_ENTERPRISE"		,	"ipg-stg-user");
	define("DB_PASSWORD_ENTERPRISE"	,	"ragMANOK2kx@djd");
	define("DB_NAME_ENTERPRISE"		,	"ipg_stg_enterprise");
	define("DB_HOST_ENTERPRISE"		,	"localhost"); 
		
	//IPG_GATEWAY
	define("DB_USER_GATEWAY"	,	"ipg-stg-user");
	define("DB_PASSWORD_GATEWAY",	"ragMANOK2kx@djd");
	define("DB_NAME_GATEWAY"	,	"ipg_stg_gateway");
	define("DB_HOST_GATEWAY"	,	"localhost");
	
	//EMAIL CONFIGURATION
	define("MAIL_HOST"			,	"mail.filipinopay.com");
	define("MAIL_PORT"			,	587);
	define("MAIL_USERNAME"		,	"ipg_web_app@filipinopay.com");
	define("MAIL_PASSWORD"		,	"ragMANOK2kx@djd");
	define("CONTACT_EMAIL_FROM"	,	"ipg_web_app@filipinopay.com");
	
	define("COMPANY_NAME"	, "IPG Public");
 	define("COPYRIGHT_NAME"	, "Filipinopay");
 	define("SUPPORT_EMAIL"	, "support@filipinopay.com"); 
 	define("PAYMAYA_ENV"	, "SANDBOX"); //STG = SANDBOX / PROD = PRODUCTION
 	define("FOOTER"			, "Copyright &copy; ".date('Y')." ".COPYRIGHT_NAME.". All Rights Reserved.");
	
	define("CSS" , BASE_URL."CSS/");
	define("JS"	 , BASE_URL."JS/"); 
	define("IMG" , BASE_URL."IMAGES/"); 
	
?>