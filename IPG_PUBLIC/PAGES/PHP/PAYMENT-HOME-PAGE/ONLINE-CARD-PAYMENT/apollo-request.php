<?php 
	header('Content-type: application/json');
	include("../../../../vendor/autoload.php");
	include("../../../../config.php");  
	include("../../../../LIBRARIES/libraries.php");
	error_reporting(0);
	$timeStamp 	  = date('Y-m-d G:i:s'); 
	$pgwInfo = $dbGateway->getRow("SELECT * FROM tbl_pgw_info WHERE PARTNER_CODE = '".$_POST['partnerCode']."' LIMIT 1");
	$pgwInfoProcessor = $dbGateway->getRow("SELECT * FROM tbl_pgw_processors WHERE PARTNER_CODE = '".$_POST['partnerCode']."' LIMIT 1");
	
	$response = array();
	if($pgwInfo){
		$terminalID = $pgwInfo->PARTNER_ID;
		$transactionKey = $pgwInfo->PARTNER_KEY;
		$referenceCode = $_POST['referenceCode'];
		$amount = $_POST['amount'];
		$serviceType = $custom->invalidParam($_POST['serviceType']);
		$returnURL = $_POST['returnURL'];

		$requestToken = sha1($terminalID . $referenceCode . "{" . $transactionKey . "}");
		$responseToken = sha1($requestToken . "{" . $transactionKey . "}");

		$securityToken = $requestToken;

		$ApolloUrl = $pgwInfoProcessor->API_URI;

		$redirectUrl = $ApolloUrl.'?terminalID='.$terminalID.'&referenceCode='.$referenceCode.'&amount='.$amount.'&serviceType='.$serviceType.'&securityToken='.$securityToken.'&returnURL='.$returnURL;

		$response['status_code'] = '201';
		$response['redirection_link'] = $redirectUrl;
		echo json_encode($response);
	}else{
 		$response['message'] = "Something went wrong.";
		echo json_encode($jsonObj);
	}
?>