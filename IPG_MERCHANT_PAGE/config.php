<?php
	date_default_timezone_set("Asia/Manila");
	$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/"; 
	
	/**************************/
	/* 		    DEV 		  */
	/**************************/  
	// define("BASE_URL"	,	$actual_link."GAI_PROJECTS/IPG_MERCHANT_PAGE/"); 
	// define("BASE_URL"	,	$actual_link."public_html/IPG_MERCHANT_PAGE/"); 
 
	// define("DB_USER"	,	"root");
	// define("DB_PASSWORD",	"");
	// define("DB_NAME"	,	"");
	// define("DB_HOST"	,	""); 
	
	// define("ENTERPRISE_URL"	,	$actual_link."public_html/IPG_ENTERPRISE/"); 
	// define("OAUTH_URL"	,	$actual_link."GAI_PROJECTS/IPG_OAUTH/"); 
	// define("IPG_OAUTH_USERNAME"  	,  "TST_USER");
	// define("IPG_OAUTH_PASSWORD"  	,  "password");
	// define("IPG_MERCHANT_CODE" 	  	,  "TST0001");
	// define("IPG_SUB_MERCHANT_CODE"  ,  "TST0001B1");
	// define("SUCCESS_PAGE"  		    ,  BASE_URL.'success');
	// define("FAILED_PAGE"  			,  BASE_URL.'failed');

	// define("CSS"		,	BASE_URL."css/");
	// define("JS"			,	BASE_URL."js/"); 
	// define("IMG"		,	BASE_URL."img/"); 

	/**************************/
	/* 		    STG 		  */
	/**************************/  
	// define("BASE_URL"	,	$actual_link."IPG_MERCHANT_PAGE/"); 
 
	// define("DB_USER"	,	"");
	// define("DB_PASSWORD",	"");
	// define("DB_NAME"	,	"");
	// define("DB_HOST"	,	""); 
	
	// define("ENTERPRISE_URL"	,	$actual_link."IPG_ENTERPRISE/"); 
	// define("OAUTH_URL"	,	$actual_link."IPG_OAUTH/"); 
	// define("IPG_OAUTH_USERNAME"  	,  "TST_USER");
	// define("IPG_OAUTH_PASSWORD"  	,  "password");
	// define("IPG_MERCHANT_CODE" 	  	,  "TST0001");
	// define("IPG_SUB_MERCHANT_CODE"  ,  "TST0001B1");
	// define("SUCCESS_PAGE"  		    ,  BASE_URL.'success');
	// define("FAILED_PAGE"  			,  BASE_URL.'failed');

 	// //*****************************************************************//	
 	// define("COMPANY_NAME"	,	"Template");
 	// define("FOOTER"		,	"Copyright &copy; ".date('Y')." ".COMPANY_NAME.". All Rights Reserved.");

	// define("CSS"		,	BASE_URL."css/");
	// define("JS"			,	BASE_URL."js/"); 
	// define("IMG"		,	BASE_URL."img/"); 

	/**************************/
	/* 		    PROD 		  */
	/**************************/  
	// define("BASE_URL"	,	$actual_link."GAI_PROJECTS/IPG_MERCHANT_PAGE/"); 
	define("BASE_URL"	,	$actual_link."IPG_MERCHANT_PAGE/"); 
 
	define("DB_USER"	,	"root");
	define("DB_PASSWORD",	"@dminP4y");
	define("DB_NAME"	,	"");
	define("DB_HOST"	,	"localhost"); 
	
	define("ENTERPRISE_URL"	,	$actual_link."public_html/IPG_ENTERPRISE/"); 
	define("OAUTH_URL"	,	$actual_link."GAI_PROJECTS/IPG_OAUTH/"); 
	define("IPG_OAUTH_USERNAME"  	,  "TST_USER");
	define("IPG_OAUTH_PASSWORD"  	,  "password");
	define("IPG_MERCHANT_CODE" 	  	,  "TST0001");
	define("IPG_SUB_MERCHANT_CODE"  ,  "TST0001B1");
	define("SUCCESS_PAGE"  		    ,  BASE_URL.'success');
	define("FAILED_PAGE"  			,  BASE_URL.'failed');

	define("CSS"		,	BASE_URL."css/");
	define("JS"			,	BASE_URL."js/"); 
	define("IMG"		,	BASE_URL."img/"); 
?>