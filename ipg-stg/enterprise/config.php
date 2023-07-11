<?php 
	date_default_timezone_set("Asia/Manila"); 
	$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/"; 
		
	/**************************/
	/* 		    STG 		  */
	/**************************/   
	define("BASE_URL"	, $actual_link."ipg-stg/enterprise/"); 
	define("PUBLIC_URL"	, $actual_link."ipg-stg/paymentportal/"); 

	//IPG_ENTERPRISE
	define("DB_USER_OAUTH"		,	"ipg-stg-user");
	define("DB_PASSWORD_OAUTH"	,	"ragMANOK2kx@djd");
	define("DB_NAME_OAUTH"		,	"ipg_stg_oauth");
	define("DB_HOST_OAUTH"		,	"localhost"); 
	
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
	
	//TIEZA DATABASE
	define("DB_USER_TIEZA"		,	"dennisjdizon03");
	define("DB_PASSWORD_TIEZA"	,	"ragMANOK2kx@djd");
	define("DB_NAME_TIEZA"		,	"tieza_online_payment");
	define("DB_HOST_TIEZA"		,	"148.72.216.234"); 
	
	//EMAIL CONFIGURATION
	define("MAIL_HOST"			,	"mail.filipinopay.com");
	define("MAIL_PORT"			,	587);
	define("MAIL_USERNAME"		,	"ipg_web_app@filipinopay.com");
	define("MAIL_PASSWORD"		,	"ragMANOK2kx@djd");
	define("CONTACT_EMAIL_FROM"	,	"ipg_web_app@filipinopay.com");

 	define("SUPPORT_EMAIL"	, "support@filipinopay.com"); 
	define("COA_AR_EMAILS" , "mrdballesteros@dci.ph");
	define("COA_AR_EMAILS_CC" , "");
	define("COA_AR_EMAILS_BCC" , "");
?>