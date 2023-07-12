<?php
	date_default_timezone_set("Asia/Manila");
	$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/"; 
	
	/***************************
	 			DEV           
	***************************/
	// define("BASE_URL"	,	$actual_link."filipinopay/dbp/"); 
	// define("GATEWAY_SERVER" , "http://122.2.27.43/"); 
	// define("TOKEN_ID" 	,   "489a6ac47fb47d1ddea52b39fac1bb14");
	// define("DBP_TOKEN_ID" 	,   "489a6ac47fb47d1ddea52b39fac1bb14");

	// //DATABASE CONFIGURATION
	// define("DB_USER"	,	"root");
	// define("DB_PASSWORD",	"");
	// define("DB_NAME"	,	"lgu_prod_davao");
	// define("DB_HOST"	,	"localhost"); 

	// define("ENTERPRISE_URL"			,  $actual_link."filipinopay/IPG_ENTERPRISE/"); 
	// define("OAUTH_URL"				,  $actual_link."filipinopay/IPG_OAUTH/"); 
	// define("IPG_OAUTH_USERNAME"  	,  "TST_USER");
	// define("IPG_OAUTH_PASSWORD"  	,  "password");
	// define("IPG_MERCHANT_CODE" 	  	,  "TST0001");
	// define("IPG_SUB_MERCHANT_CODE"  ,  "TST0001B1");
	// define("SUCCESS_PAGE"  		    ,  BASE_URL);
	// define("FAILED_PAGE"  			,  BASE_URL);

	// define("SESSION_NAME"	, "LGU_DAVAO");
 	// define("COMPANY_NAME" , "City Government of Davao");
 	// define("ALT_IMG"      , "City Government of Davao");
 	// define("FOOTER"		  ,	"Copyright &copy; 2020 ".COMPANY_NAME.". All Rights Reserved.");
 
  	// define("WEBSITE_VERSION" 	, '2.0');

	// define("CSS"		,	BASE_URL."CSS/");
	// define("JS"			,	BASE_URL."JS/"); 
	// define("IMG"		,	BASE_URL."IMG/");  

	/**************************/
	/* 		    STG 		  */
	/**************************/  
 	/*define("BASE_URL"	,	$actual_link."dbp/"); 
	define("GATEWAY_SERVER" , "http://122.2.27.43/"); 
	define("TOKEN_ID" 		, "489a6ac47fb47d1ddea52b39fac1bb14");
	define("DBP_TOKEN_ID" 	, "489a6ac47fb47d1ddea52b39fac1bb14");
	
	define("DB_USER"		,	"dennisjdizon03");
	define("DB_PASSWORD"	,	"ragMANOK2kx@djd");
	define("DB_NAME"		,	"lgu_davao");
	define("DB_HOST"		,	"148.72.216.234");

	define("ENTERPRISE_URL"			,  "https://filipinopay.com/IPG_ENTERPRISE/"); 
	define("OAUTH_URL"				,  "https://filipinopay.com/IPG_OAUTH/"); 
	define("IPG_OAUTH_USERNAME"  		,  "TST_USER");
	define("IPG_OAUTH_PASSWORD"  		,  "password");
	define("IPG_MERCHANT_CODE" 	  	,  "TST0001");
	define("IPG_SUB_MERCHANT_CODE"  	,  "TST0001B1");
	define("SUCCESS_PAGE"  		    ,  BASE_URL);
	define("FAILED_PAGE"  			,  BASE_URL);*/	

    /**************************/
	/* 		    PROD 		  */
	/**************************/  
 	define("BASE_URL"	,	$actual_link."dbp/"); 
	define("GATEWAY_SERVER" , "http://122.2.27.43/"); 
	define("TOKEN_ID" 		, "489a6ac47fb47d1ddea52b39fac1bb14");
	define("DBP_TOKEN_ID" 	, "489a6ac47fb47d1ddea52b39fac1bb14");
	
	define("DB_USER"		,	"root");
	define("DB_PASSWORD"	,	"@dminP4y");
	define("DB_NAME"		,	"lgu_prod_davao");
	define("DB_HOST"		,	"localhost");

	//define("ENTERPRISE_URL"	    ,  "http://enterprise.filipinopay.com/"); 
	define("ENTERPRISE_URL"			,  $actual_link."enterprise/");
	//define("OAUTH_URL"			,  "http://oauth.filipinopay.com/"); 
	define("OAUTH_URL"				,  $actual_link."oauth/");
	define("IPG_OAUTH_USERNAME"  	,  "LGUDAVAO");
	define("IPG_OAUTH_PASSWORD"  	,  "lgu@davao");
	define("IPG_MERCHANT_CODE" 	  	,  "LGUDAV0001");
	define("IPG_SUB_MERCHANT_CODE"  ,  "DAV0001B1");
	define("SUCCESS_PAGE"  		    ,  BASE_URL);
	define("FAILED_PAGE"  			,  BASE_URL);	
 
	//--------------------------------------------------------------------------------------
	define("SESSION_NAME"	, "LGU_DAVAO");
 	define("COMPANY_NAME" , "City Government of Davao");
 	define("ALT_IMG"      , "City Government of Davao");
 	define("FOOTER"		  ,	"Copyright &copy; 2020 ".COMPANY_NAME.". All Rights Reserved.");
 
  	define("WEBSITE_VERSION" 	, '2.0');

	define("CSS"		,	BASE_URL."CSS/");
	define("JS"			,	BASE_URL."JS/"); 
	define("IMG"		,	BASE_URL."IMG/");  
?>