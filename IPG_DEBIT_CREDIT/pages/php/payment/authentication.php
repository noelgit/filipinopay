<?php  
	session_start();
	header('Content-type: application/json');
	include("../../../config.php");
	include("../../../lib/libraries.php");

	unset($_SESSION['IPG_DEBIT_CREDIT']);
	$jsonObj = array();
 	$jsonObj['status_code'] = "201";
 	$jsonObj['redirection_link'] = BASE_URL."payment";
	echo json_encode($jsonObj);
	$_SESSION['IPG_DEBIT_CREDIT']['TRANSACTION'] = $_POST;   
?>