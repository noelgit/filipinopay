<?php 
	require_once __DIR__ . '/vendor/autoload.php';
	include ("config.php");
	include ("LIBRARIES/libraries.php");  
	//include __DIR__.'/LIBRARIES/libraries.php'; 

	ini_set('display_errors',1); error_reporting(E_ALL);
	require_once('OAuth2/Autoloader.php');
	
	OAuth2\Autoloader::register();

	$timeStamp 	  = date('Y-m-d G:i:s');
	$DataSourceName      = 'mysql:dbname='.DB_NAME_OAUTH.';host='.DB_HOST_OAUTH;
	$storage = new OAuth2\Storage\Pdo(array('dsn' => $DataSourceName, 'username' => DB_USER_OAUTH, 'password' => DB_PASSWORD_OAUTH));

	// Pass a storage object or array of storage objects to the OAuth2 server class
	$server = new OAuth2\Server($storage);

	// Add the "Client Credentials" grant type (it is the simplest of the grant types)
	$server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
	$server->addGrantType(new OAuth2\GrantType\RefreshToken($storage));
	// Add the "Authorization Code" grant type (this is where the oauth magic happens)
	$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));

?>