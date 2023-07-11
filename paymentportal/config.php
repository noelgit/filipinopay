<?php
	date_default_timezone_set("Asia/Manila");
	$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/"; 
	
	/**************************/
	/* 		    DEV 		  */
	/**************************/  
	// define("BASE_URL"		, $actual_link."public_html/paymentportal/"); 
	// define("ENTERPRISE_URL"	, $actual_link."public_html/enterprise/");
	// define("CARD_URL_2C2P"	, $actual_link."public_html/creditdebit/"); 
	// define("CARD_URL_DUMMY"	, ""); 
	
	// //IPG_PUBLIC
	// define("DB_USER_PUBLIC"	   	, "root");
	// define("DB_PASSWORD_PUBLIC"	, "");
	// define("DB_NAME_PUBLIC"	   	, "ipg_prod_public");
	// define("DB_HOST_PUBLIC"		, "localhost"); 

	// //IPG_MERCHANT
	// define("DB_USER_MERCHANT"	    , "root");
	// define("DB_PASSWORD_MERCHANT"	, "");
	// define("DB_NAME_MERCHANT"	   	, "ipg_prod_merchant");
	// define("DB_HOST_MERCHANT"		, "localhost"); 

	// //IPG_JV
	// define("DB_USER_JV"	    , "root");
	// define("DB_PASSWORD_JV"	, "");
	// define("DB_NAME_JV"	   	, "ipg_prod_jv");
	// define("DB_HOST_JV"		, "localhost"); 

	// //IPG_ENTERPRISE
	// define("DB_USER_ENTERPRISE"		, "root");
	// define("DB_PASSWORD_ENTERPRISE"	, "");
	// define("DB_NAME_ENTERPRISE"		, "ipg_prod_enterprise");
	// define("DB_HOST_ENTERPRISE"		, "localhost"); 
		
	// //IPG_GATEWAY
	// define("DB_USER_GATEWAY"	 , "root");
	// define("DB_PASSWORD_GATEWAY" , "");
	// define("DB_NAME_GATEWAY"	 , "ipg_prod_gateway");
	// define("DB_HOST_GATEWAY"	 , "localhost");
	
	// //EMAIL CONFIGURATION
	// define("MAIL_HOST"			, "mail.filipinopay.com");
	// define("MAIL_PORT"			, 587);
	// define("MAIL_USERNAME"		, "ipg_web_app@filipinopay.com");
	// define("MAIL_PASSWORD"		, "ragMANOK2kx@djd");
	// define("CONTACT_EMAIL_FROM"	, "ipg_web_app@filipinopay.com");
	
 
 	// define("COMPANY_NAME"	, "IPG Public");
 	// define("COPYRIGHT_NAME"	, "Filipinopay");
 	// define("SUPPORT_EMAIL"	, "support@filipinopay.com"); 
 	// define("PAYMAYA_ENV"	, "PRODUCTION"); //STG = SANDBOX / PROD = PRODUCTION
 	// define("FOOTER"			, "Copyright &copy; ".date('Y')." ".COPYRIGHT_NAME.". All Rights Reserved.");
 
	// define("CSS" , BASE_URL."CSS/");
	// define("JS"	 , BASE_URL."JS/"); 
	// define("IMG" , BASE_URL."IMAGES/"); 
	
	/**************************/
	/* 		    STG 		  */
	/**************************/  
	/*define("BASE_URL"	,	$actual_link."IPG_PUBLIC/"); 
	define("ENTERPRISE_URL"	,	$actual_link."IPG_ENTERPRISE/"); 
	define("CARD_URL_2C2P"	,	$actual_link."IPG_DEBIT_CREDIT/"); 
	define("CARD_URL_DUMMY"	,	$actual_link."IPG_DUMMY_API/"); 
	
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
		
	//IPG_GATEWAY
	define("DB_USER_GATEWAY"	,	"dennisjdizon03");
	define("DB_PASSWORD_GATEWAY",	"ragMANOK2kx@djd");
	define("DB_NAME_GATEWAY"	,	"ipg_gateway");
	define("DB_HOST_GATEWAY"	,	"148.72.216.234");
	
	//EMAIL CONFIGURATION
	define("MAIL_HOST"			,	"mail.filipinopay.com");
	define("MAIL_PORT"			,	587);
	define("MAIL_USERNAME"		,	"ipg_web_app@filipinopay.com");
	define("MAIL_PASSWORD"		,	"ragMANOK2kx@djd");
	define("CONTACT_EMAIL_FROM"	,	"ipg_web_app@filipinopay.com");*/   
	
	/**************************/
	/* 		    PROD 		  */
	/**************************/  
	define("BASE_URL"		, $actual_link."paymentportal/"); 
	define("ENTERPRISE_URL"	, $actual_link."enterprise/");
	define("CARD_URL_2C2P"	, $actual_link."creditdebit/"); 
	define("CARD_URL_DUMMY"	, ""); 
	
	//IPG_PUBLIC
	define("DB_USER_PUBLIC"	   	, "root");
	define("DB_PASSWORD_PUBLIC"	, "@dminP4y");
	define("DB_NAME_PUBLIC"	   	, "ipg_prod_public");
	define("DB_HOST_PUBLIC"		, "localhost"); 

	//IPG_MERCHANT
	define("DB_USER_MERCHANT"	    , "root");
	define("DB_PASSWORD_MERCHANT"	, "@dminP4y");
	define("DB_NAME_MERCHANT"	   	, "ipg_prod_merchant");
	define("DB_HOST_MERCHANT"		, "54.255.135.27"); 

	//IPG_JV
	define("DB_USER_JV"	    , "root");
	define("DB_PASSWORD_JV"	, "@dminP4y");
	define("DB_NAME_JV"	   	, "ipg_prod_jv");
	define("DB_HOST_JV"		, "54.255.135.27"); 

	//IPG_ENTERPRISE
	define("DB_USER_ENTERPRISE"		, "root");
	define("DB_PASSWORD_ENTERPRISE"	, "@dminP4y");
	define("DB_NAME_ENTERPRISE"		, "ipg_prod_enterprise");
	define("DB_HOST_ENTERPRISE"		, "54.255.135.27"); 
		
	//IPG_GATEWAY
	define("DB_USER_GATEWAY"	 , "root");
	define("DB_PASSWORD_GATEWAY" , "@dminP4y");
	define("DB_NAME_GATEWAY"	 , "ipg_prod_gateway");
	define("DB_HOST_GATEWAY"	 , "54.255.135.27");
	
	//EMAIL CONFIGURATION
	define("MAIL_HOST"			, "mail.filipinopay.com");
	define("MAIL_PORT"			, 587);
	define("MAIL_USERNAME"		, "ipg_web_app@filipinopay.com");
	define("MAIL_PASSWORD"		, "ragMANOK2kx@djd");
	define("CONTACT_EMAIL_FROM"	, "ipg_web_app@filipinopay.com");
	
 
 	define("COMPANY_NAME"	, "IPG Public");
 	define("COPYRIGHT_NAME"	, "Filipinopay");
 	define("SUPPORT_EMAIL"	, "support@filipinopay.com"); 
 	define("PAYMAYA_ENV"	, "PRODUCTION"); //STG = SANDBOX / PROD = PRODUCTION
 	define("FOOTER"			, "Copyright &copy; ".date('Y')." ".COPYRIGHT_NAME.". All Rights Reserved.");
 
	define("CSS" , BASE_URL."CSS/");
	define("JS"	 , BASE_URL."JS/"); 
	define("IMG" , BASE_URL."IMAGES/"); 

?>