<?php    
	require ('cryptor.class.php');    	
	require ('helper.class.php');
	require ('hash.crypt.class.php');
	require ('database.class.php'); 
	require ('custom.class.php'); 
 	
	$IPGGateway = new db(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
	$helper = new Helper;	  
	$custom = new custom;	
?>