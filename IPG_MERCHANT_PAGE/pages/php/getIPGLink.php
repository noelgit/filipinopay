<?php 
	session_start();
	header('Content-type: application/json');
	include("../../config.php"); 
	error_reporting(0); 

 	$username = IPG_OAUTH_USERNAME;
 	$password = IPG_OAUTH_PASSWORD;

 	$authorizationBasic = base64_encode(IPG_OAUTH_USERNAME.":".IPG_OAUTH_PASSWORD);

	//Get authorization Code
	$authoizationUrl = OAUTH_URL."authorize.php?response_type=code&client_id=".$username."&state=xyzsdsdsds";
	$headr[] = 'Authorization:Basic '.$authorizationBasic;
	$curlauthorization = curl_init();
	// Set cURL options
	curl_setopt($curlauthorization, CURLOPT_URL,$authoizationUrl);
	curl_setopt($curlauthorization, CURLOPT_HTTPHEADER,$headr);
	curl_setopt($curlauthorization, CURLOPT_USERPWD, $username . ":" . $password);  
	curl_setopt($curlauthorization, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curlauthorization, CURLOPT_RETURNTRANSFER, true);
	
	//Run cURL 
	$result = curl_exec($curlauthorization);

	//Close cURL
	curl_close($curlauthorization); 
	$authorizationVal= json_decode($result); 

	if($authorizationVal->Code){
		$dataToken = array("grant_type"=> 'authorization_code',
					   "code" => $authorizationVal->Code) ;
	 	 
		//  Initiate curl
		$curlToken 	= curl_init(); 
		$tokenUrl = OAUTH_URL."getToken.php"; 
		$headrToken[] = 'Authorization: Basic '.$authorizationBasic; 
		$headrToken[] = 'Content-Type:multipart/form-data'; 
		// Set cURL options
		curl_setopt($curlToken, CURLOPT_URL,$tokenUrl);
		curl_setopt($curlToken, CURLOPT_HTTPHEADER,$headrToken); 
		curl_setopt($curlToken, CURLOPT_USERPWD, $username . ":" . $password);  
		curl_setopt($curlToken, CURLOPT_POST, TRUE);
		curl_setopt($curlToken, CURLOPT_POSTFIELDS, $dataToken);
		curl_setopt($curlToken, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curlToken, CURLOPT_RETURNTRANSFER, true);

		$result = curl_exec($curlToken);

		curl_close($curlToken);  
		$tokenVal = json_decode($result);   

		if($tokenVal->access_token){
			$date = date("Ymd");
			$time = date("His"); 
			$dataDoTransaction = array(
						   "merchant_code" => IPG_MERCHANT_CODE,
						   "merchant_ref_num" => "REF-".$time."-".$date,
						   "requestor_name" => $_POST['name'],
						   "requestor_email_address" => $_POST['email'],
						   "requestor_mobile_no" => $_POST['contact'],
						   "success_return_url" => SUCCESS_PAGE,
						   "failed_return_url" => FAILED_PAGE,
						   //"transactions" => $_POST['transaction']
						);
			$count = 0;
			foreach($_POST['transaction'] as $key => $value){
				foreach($_POST['transaction'][$key] AS $key => $data){
					$dataDoTransaction["transactions['".$count."']['".$key."']"] = $data; 
				} 
				$count++;
			} 
			$curlDoTransaction = curl_init(); 

			$urlDoTransaction = ENTERPRISE_URL."doTransaction.php";
			$headrDoTransaction[] = 'Authorization: Bearer '.$tokenVal->access_token;
			$headrDoTransaction[] = 'Content-Type: multipart/form-data'; 		

			// Set cURL options
			curl_setopt($curlDoTransaction, CURLOPT_URL,$urlDoTransaction);
			curl_setopt($curlDoTransaction, CURLOPT_HTTPHEADER, $headrDoTransaction);  
			curl_setopt($curlDoTransaction, CURLOPT_POST, 1);
			curl_setopt($curlDoTransaction, CURLOPT_POSTFIELDS, $dataDoTransaction);
			curl_setopt($curlDoTransaction, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curlDoTransaction, CURLOPT_RETURNTRANSFER, true);

			$result = curl_exec($curlDoTransaction);

			curl_close($curlDoTransaction);  
			
			$transactionVal = json_decode($result);    
  			//print_r($transactionVal);
			print_r(json_encode($transactionVal));
		} 
	} 
?>
