<?php
	date_default_timezone_set("Asia/Manila");
	$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/"; 

	/**************************/
	/* 		    STG 		  */
	/**************************/  	
	define("BASE_URL"	,	$actual_link."ipg-stg/jointventure/"); 
	define("ENTERPRISE_URL"	,	$actual_link."ipg-stg/enterprise/"); 

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
 
	
 	//*****************************************************************//
 	define("COMPANY_NAME"	,	"IPG Joint Venture");
 	define("FOOTER"		,	"Copyright &copy; ".date('Y')." ".COMPANY_NAME.". All Rights Reserved.");

	define("CSS"		,	BASE_URL."CSS/");
	define("JS"			,	BASE_URL."JS/"); 
	define("IMG"		,	BASE_URL."IMAGES/"); 

?>