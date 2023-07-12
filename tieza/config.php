<?php
	date_default_timezone_set("Asia/Manila");
	$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/"; 

	/**************************/
	/* 		    DEV 		  */
	/**************************/  
	/*define("BASE_URL"  ,	$actual_link.'GAI_PROJECTS/TIEZA/');  
	//IPG CREDENTIALS 
	define("IPG_ENTERPRISE_URL"	 ,  "http://localhost/GAI_PROJECTS/IPG_ENTERPRISE/"); 
	define("IPG_OAUTH_URL"		 ,  "http://localhost/GAI_PROJECTS/IPG_OAUTH/"); 
	define("IPG_OAUTH_USERNAME"  ,  "TIE_USER");
	define("IPG_OAUTH_PASSWORD"  ,  "password");
	define("IPG_MERCHANT_CODE" 	 ,  "TIE0001");
	define("IPG_SUB_MERCHANT_CODE" , "TIE0001B1");
	define("SUCCESS_PAGE"  		 ,  BASE_URL);
	define("FAILED_PAGE"  	     ,  BASE_URL);

	//DATABASE CONFIGURATION
	define("DB_TIEZA_USER"	  ,	"root");
	define("DB_TIEZA_PASSWORD"  ,	"");
	define("DB_TIEZA_NAME"	  ,	"tieza_online_payment");
	define("DB_TIEZA_HOST"	  ,	"localhost");

	//EMAIL CONFIGURATION 
	define("MAIL_HOST"		  ,	"smtp.gmail.com");
	define("MAIL_PORT"		  ,	587);
	define("MAIL_USERNAME"	  ,	"ipg.test.username@gmail.com");
	define("MAIL_PASSWORD"	  ,	"ipg.password");
	define("CONTACT_EMAIL_FROM" ,	"ipg.test.username@gmail.com");  

	//GOOGLE RECAPTCHA
	define("SITE_KEY", "6LdXdlsUAAAAANWhqD8Sep8l2sNaXGgIoq4rdkz5");
	define("SECRET_KEY", "6LdXdlsUAAAAABe6dVranUftsNcgM6WlpRDCJir3"); 
	*/

	/**************************/
	/* 		    STG 		  */
	/**************************/
	// define("BASE_URL"  ,	$actual_link);
	// //IPG CREDENTIALS
	// define("IPG_ENTERPRISE_URL"		,  "http://filipinopay.com/IPG_ENTERPRISE/"); 
	// define("IPG_OAUTH_URL"			,  "http://filipinopay.com/IPG_OAUTH/");  
	// define("IPG_OAUTH_USERNAME"  	,  "TIE_USER");
	// define("IPG_OAUTH_PASSWORD"  	,  "password");
	// define("IPG_MERCHANT_CODE" 	  	,  "TIE0001");
	// define("IPG_SUB_MERCHANT_CODE"  ,  "TIE0001B1");
	// define("SUCCESS_PAGE"  		    ,  BASE_URL); 
	// define("FAILED_PAGE"  			,  BASE_URL);
	
	// //DATABASE CONFIGURATION
	// define("DB_TIEZA_USER"	,	"dennisjdizon03");
	// define("DB_TIEZA_PASSWORD",	"ragMANOK2kx@djd");
	// define("DB_TIEZA_NAME"	,	"tieza_online_payment");
	// define("DB_TIEZA_HOST"	,	"148.72.216.234");

	// //EMAIL CONFIGURATION 
	// define("MAIL_HOST"			,	"mail.filipinopay.com");
	// define("MAIL_PORT"			,	587);
	// define("MAIL_USERNAME"		,	"tieza_web_app@filipinopay.com");
	// define("MAIL_PASSWORD"		,	"KQ*qpsp(x+=c");
	// define("CONTACT_EMAIL_FROM"	,	"tieza_web_app@filipinopay.com");  

	// //GOOGLE RECAPTCHA
	// define("SITE_KEY", "6Lc8w5oUAAAAAL9nAfJEoYL1xxC5laAu818idbTn");
	// define("SECRET_KEY", "6Lc8w5oUAAAAAFnlV6jeD_kNkHAYLopIFjGqg542");
	
	/**************************/
	/* 		    PROD 		  */
	/**************************/
	define("BASE_URL"  ,	$actual_link);
	//IPG CREDENTIALS
	// define("IPG_ENTERPRISE_URL"		,  $actual_link . "http://enterprise.filipinopay.com/"); 
	// define("IPG_OAUTH_URL"			,  $actual_link . "http://oauth.filipinopay.com/");  
	define("IPG_ENTERPRISE_URL"		,  $actual_link . "enterprise/"); 
	define("IPG_OAUTH_URL"			,  $actual_link . "oauth/");  
	define("IPG_OAUTH_USERNAME"  	,  "TIE_USER");
	define("IPG_OAUTH_PASSWORD"  	,  "password");
	define("IPG_MERCHANT_CODE" 	  	,  "TIE0001");
	define("IPG_SUB_MERCHANT_CODE"  ,  "TIE0001B1");
	define("SUCCESS_PAGE"  		    ,  BASE_URL); 
	define("FAILED_PAGE"  			,  BASE_URL);
	
	//DATABASE CONFIGURATION
	define("DB_TIEZA_USER"	,	"root");
	define("DB_TIEZA_PASSWORD",	"@dminP4y");
	define("DB_TIEZA_NAME"	,	"tieza_online_payment");
	define("DB_TIEZA_HOST"	,	"localhost");

	//EMAIL CONFIGURATION 
	define("MAIL_HOST"			,	"mail.filipinopay.com");
	define("MAIL_PORT"			,	587);
	define("MAIL_USERNAME"		,	"tieza_web_app@filipinopay.com");
	define("MAIL_PASSWORD"		,	"KQ*qpsp(x+=c");
	define("CONTACT_EMAIL_FROM"	,	"tieza_web_app@filipinopay.com");  

	//GOOGLE RECAPTCHA
	define("SITE_KEY", "6Lc8w5oUAAAAAL9nAfJEoYL1xxC5laAu818idbTn");
	define("SECRET_KEY", "6Lc8w5oUAAAAAFnlV6jeD_kNkHAYLopIFjGqg542");
 	
 	//*****************************************************************//
 	define("COMPANY_NAME" , "Tieza");
 	define("FOOTER"		,	"Copyright &copy; ".date('Y')." ".COMPANY_NAME.". All Rights Reserved.");
	define("CSS"		,	BASE_URL."css/");
	define("JS"			,	BASE_URL."js/"); 
	define("IMG"		,	BASE_URL."img/"); 
?>