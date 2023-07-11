<?php  
	header('Access-Control-Allow-Origin: *');
	header('Content-type: application/json');
	header('Access-Control-Allow-Method: POST');
	session_start();
	include("../../../config.php");
	include("../../../lib/libraries.php");
 
	unset($_SESSION['IPG_DEBIT_CREDIT']); 
	$_SESSION['IPG_DEBIT_CREDIT']['TRANSACTION'] = $_POST;   
	$jsonObj = array();
 	$jsonObj['status_code'] = "201";
 	$jsonObj['redirection_link'] = BASE_URL."payment";
	echo json_encode($jsonObj); 
?> 