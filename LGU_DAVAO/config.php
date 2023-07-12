<?php
	date_default_timezone_set("Asia/Manila");
	$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/"; 
	
	/***************************
	 			DEV           
	***************************/
	// define("BASE_URL"	,	$actual_link."public_html/LGU_DAVAO/"); 
	// define("GATEWAY_SERVER" , "http://122.2.27.43/"); 
	// define("TOKEN_ID" 		, "489a6ac47fb47d1ddea52b39fac1bb14");
	// define("DBP_TOKEN_ID" 	, "489a6ac47fb47d1ddea52b39fac1bb14");
	
	// define("DB_USER"		,	"root");
	// define("DB_PASSWORD"	,	"");
	// define("DB_NAME"		,	"lgu_prod_davao");
	// define("DB_HOST"		,	"localhost");

	// define("ENTERPRISE_URL"			, BASE_URL. "public_html/IPG_ENTERPRISE/"); 
	// define("OAUTH_URL"				,  BASE_URL. "public_html/IPG_OAUTH/"); 
	// define("IPG_OAUTH_USERNAME"  		,  "TST_USER");
	// define("IPG_OAUTH_PASSWORD"  		,  "password");
	// define("IPG_MERCHANT_CODE" 	  	,  "TST0001");
	// define("IPG_SUB_MERCHANT_CODE"  	,  "TST0001B1");
	// define("SUCCESS_PAGE"  		    ,  BASE_URL);
	// define("FAILED_PAGE"  			,  BASE_URL);	

 
	// //--------------------------------------------------------------------------------------
	// define("SESSION_NAME"	, "LGU_DAVAO");
 	// define("COMPANY_NAME" , "LGU Davao");
 	// define("ALT_IMG"      , "LGU Davao");
 	// define("FOOTER"		  ,	"Copyright &copy; 2019 ".COMPANY_NAME.". All Rights Reserved.");
 
  	// define("WEBSITE_VERSION" 	, '2.0');

	// define("CSS"		,	BASE_URL."CSS/");
	// define("JS"			,	BASE_URL."JS/"); 
	// define("IMG"		,	BASE_URL."IMG/");  

	/**************************/
	/* 		    STG 		  */
	/**************************/  
 	// define("BASE_URL"	,	$actual_link."LGU_DAVAO/"); 
	// define("GATEWAY_SERVER" , "http://122.2.27.43/"); 
	// define("TOKEN_ID" 		, "489a6ac47fb47d1ddea52b39fac1bb14");
	// define("DBP_TOKEN_ID" 	, "489a6ac47fb47d1ddea52b39fac1bb14");
	
	// define("DB_USER"		,	"dennisjdizon03");
	// define("DB_PASSWORD"	,	"ragMANOK2kx@djd");
	// define("DB_NAME"		,	"lgu_davao");
	// define("DB_HOST"		,	"148.72.216.234");

	// define("ENTERPRISE_URL"			,  "https://filipinopay.com/IPG_ENTERPRISE/"); 
	// define("OAUTH_URL"				,  "https://filipinopay.com/IPG_OAUTH/"); 
	// define("IPG_OAUTH_USERNAME"  		,  "TST_USER");
	// define("IPG_OAUTH_PASSWORD"  		,  "password");
	// define("IPG_MERCHANT_CODE" 	  	,  "TST0001");
	// define("IPG_SUB_MERCHANT_CODE"  	,  "TST0001B1");
	// define("SUCCESS_PAGE"  		    ,  BASE_URL);
	// define("FAILED_PAGE"  			,  BASE_URL);	

 
	// //--------------------------------------------------------------------------------------
	// define("SESSION_NAME"	, "LGU_DAVAO");
 	// define("COMPANY_NAME" , "LGU Davao");
 	// define("ALT_IMG"      , "LGU Davao");
 	// define("FOOTER"		  ,	"Copyright &copy; 2019 ".COMPANY_NAME.". All Rights Reserved.");
 
  	// define("WEBSITE_VERSION" 	, '2.0');

	// define("CSS"		,	BASE_URL."CSS/");
	// define("JS"			,	BASE_URL."JS/"); 
	// define("IMG"		,	BASE_URL."IMG/");  

	/***************************
	 			PROD           
	***************************/
	define("BASE_URL"	,	$actual_link."LGU_DAVAO/"); 
	define("GATEWAY_SERVER" , "http://122.2.27.43/"); 
	define("TOKEN_ID" 		, "489a6ac47fb47d1ddea52b39fac1bb14");
	define("DBP_TOKEN_ID" 	, "489a6ac47fb47d1ddea52b39fac1bb14");
	
	define("DB_USER"		,	"root");
	define("DB_PASSWORD"	,	"@dminP4y");
	define("DB_NAME"		,	"lgu_prod_davao");
	define("DB_HOST"		,	"localhost");

	define("ENTERPRISE_URL"			, BASE_URL. "IPG_ENTERPRISE/"); 
	define("OAUTH_URL"				,  BASE_URL. "IPG_OAUTH/"); 
	define("IPG_OAUTH_USERNAME"  		,  "TST_USER");
	define("IPG_OAUTH_PASSWORD"  		,  "password");
	define("IPG_MERCHANT_CODE" 	  	,  "TST0001");
	define("IPG_SUB_MERCHANT_CODE"  	,  "TST0001B1");
	define("SUCCESS_PAGE"  		    ,  BASE_URL);
	define("FAILED_PAGE"  			,  BASE_URL);	

 
	//--------------------------------------------------------------------------------------
	define("SESSION_NAME"	, "LGU_DAVAO");
 	define("COMPANY_NAME" , "LGU Davao");
 	define("ALT_IMG"      , "LGU Davao");
 	define("FOOTER"		  ,	"Copyright &copy; 2019 ".COMPANY_NAME.". All Rights Reserved.");
 
  	define("WEBSITE_VERSION" 	, '2.0');

	define("CSS"		,	BASE_URL."CSS/");
	define("JS"			,	BASE_URL."JS/"); 
	define("IMG"		,	BASE_URL."IMG/");  
?>