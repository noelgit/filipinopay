<?php
	date_default_timezone_set("Asia/Manila");
	$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/"; 

	/**************************/
	/* 		    DEV 		  */
	/**************************/  	
	// define("BASE_URL"	,	$actual_link."GAI_PROJECTS/IPG_DEBIT_CREDIT/"); 
	// define("BASE_URL"	,	$actual_link."filipinopay/IPG_DEBIT_CREDIT/"); 
	// //DATABASE CONFIGURATION
	// define("DB_USER"	,	"root");
	// define("DB_PASSWORD",	"");
	// define("DB_NAME"	,	"ipg_gateway");
	// define("DB_HOST"	,	"localhost");
	// //EMAIL CONFIGURATION
	// define("MAIL_HOST"			,	"smtp.gmail.com");
	// define("MAIL_PORT"			,	587);
	// define("MAIL_USERNAME"		,	"username@gmail.com");
	// define("MAIL_PASSWORD"		,	"password");
	// define("CONTACT_EMAIL_FROM"	,	"username@gmail.com"); 

	// define("COMPANY_NAME"	,	"IPG Debit Credit API"); 
	// define("CSS"	,	BASE_URL."css/");
	// define("JS"		,	BASE_URL."js/"); 
	// define("IMG"	,	BASE_URL."img/"); 
	// define("COUNTRY_CODE", "PH"); 

	
	/**************************/
	/* 		    STG 		  */
	/**************************/  	
	// define("BASE_URL"	,	$actual_link."IPG_DEBIT_CREDIT/"); 
	// //DATABASE CONFIGURATION
	// define("DB_USER"	  ,  "dennisjdizon03");
	// define("DB_PASSWORD"  ,  "ragMANOK2kx@djd");
	// define("DB_NAME"	  ,  "ipg_gateway");
	// define("DB_HOST"	  ,  "148.72.216.234");
	// //EMAIL CONFIGURATION
	// define("MAIL_HOST"			,	"smtp.filipinopay.com");
	// define("MAIL_PORT"			,	587);
	// define("MAIL_USERNAME"		,	"ipg_web_app@filipinopay.com");
	// define("MAIL_PASSWORD"		,	"ragMANOK2kx@djd");
	// define("CONTACT_EMAIL_FROM"	,	"ipg_web_app@filipinopay.com"); 
	
	
 	// //*****************************************************************//
	// define("COMPANY_NAME"	,	"IPG Debit Credit API"); 
	// define("CSS"	,	BASE_URL."css/");
	// define("JS"		,	BASE_URL."js/"); 
	// define("IMG"	,	BASE_URL."img/"); 
	// define("COUNTRY_CODE", "PH"); 

	/**************************/
	/* 		    PROD 		  */
	/**************************/  	
	// define("BASE_URL"	,	$actual_link."GAI_PROJECTS/IPG_DEBIT_CREDIT/"); 
	define("BASE_URL"	,	$actual_link."IPG_DEBIT_CREDIT/"); 
	//DATABASE CONFIGURATION
	define("DB_USER"	,	"root");
	define("DB_PASSWORD",	"@dminP4y");
	define("DB_NAME"	,	"ipg_gateway");
	define("DB_HOST"	,	"localhost");
	//EMAIL CONFIGURATION
	define("MAIL_HOST"			,	"smtp.gmail.com");
	define("MAIL_PORT"			,	587);
	define("MAIL_USERNAME"		,	"username@gmail.com");
	define("MAIL_PASSWORD"		,	"password");
	define("CONTACT_EMAIL_FROM"	,	"username@gmail.com"); 

	define("COMPANY_NAME"	,	"IPG Debit Credit API"); 
	define("CSS"	,	BASE_URL."css/");
	define("JS"		,	BASE_URL."js/"); 
	define("IMG"	,	BASE_URL."img/"); 
	define("COUNTRY_CODE", "PH"); 

?>