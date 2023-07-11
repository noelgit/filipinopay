<?php
	date_default_timezone_set("Asia/Manila");
	$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/"; 
 
	/**************************/
	/* 		    STG 		  */
	/**************************/  
	define("BASE_URL"	,	$actual_link."ipg-stg/samplemerchant/"); 
	define("ENTERPRISE_URL"	, $actual_link."ipg-stg/enterprise/"); 
	define("OAUTH_URL"	,	$actual_link."ipg-stg/oauth/"); 
 
	define("DB_USER"	,	"");
	define("DB_PASSWORD",	"");
	define("DB_NAME"	,	"");
	define("DB_HOST"	,	""); 
	
	define("IPG_OAUTH_USERNAME"  	,  "TST_USER");
	define("IPG_OAUTH_PASSWORD"  	,  "password");
	define("IPG_MERCHANT_CODE" 	  	,  "TST0001");
	define("IPG_SUB_MERCHANT_CODE"  ,  "TST0001B1");
	define("SUCCESS_PAGE"  		    ,  BASE_URL.'success');
	define("FAILED_PAGE"  			,  BASE_URL.'failed');
	 

 	//*****************************************************************//	
 	define("COMPANY_NAME"	,	"Template");
 	define("FOOTER"		,	"Copyright &copy; ".date('Y')." ".COMPANY_NAME.". All Rights Reserved.");

	define("CSS"		,	BASE_URL."css/");
	define("JS"			,	BASE_URL."js/"); 
	define("IMG"		,	BASE_URL."img/"); 
?>