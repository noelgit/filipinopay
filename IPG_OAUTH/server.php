<?php   
	date_default_timezone_set("Asia/Manila");
	/**************************/
	/* 		    DEV 		  */
	/**************************/  	
	//$dsn      = 'mysql:dbname=ipg_oauth;host=localhost';
	//$username = 'root';
	//$password = '';
	
	/**************************/
	/* 		    STG 		  */
	/**************************/  	
	$dsn      = 'mysql:dbname=ipg_oauth;host=148.72.216.234';
	$username = 'dennisjdizon03';
	$password = 'ragMANOK2kx@djd';
 
	ini_set('display_errors',1);error_reporting(E_ALL);
 
	require_once('OAuth2/Autoloader.php');
	OAuth2\Autoloader::register();

	// $dsn is the Data Source Name for your database
	$storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));

	// Pass a storage object or array of storage objects to the OAuth2 server class
	$server = new OAuth2\Server($storage);

	// Add the "Client Credentials" grant type (it is the simplest of the grant types)
	$server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
	$server->addGrantType(new OAuth2\GrantType\RefreshToken($storage));
	// Add the "Authorization Code" grant type (this is where the oauth magic happens)
	$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
?>