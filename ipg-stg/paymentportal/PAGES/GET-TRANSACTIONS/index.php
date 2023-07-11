<?php   
	if($_POST){ 
		include("../../config.php");
		session_start();  
		//$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/";  
	  	//define("BASE_URL"	,	$actual_link."IPG_PUBLIC/"); 
		//define("ENTERPRISE_URL"	,	$actual_link."IPG_ENTERPRISE/"); 
		$url = ENTERPRISE_URL."getTransaction.php";

		$paymentMethod  = $_POST['pmCode'];
		$paymentVal 	= $_POST['peCode'];
		$secureParam 	= $_POST['secureParam'];
		$merchantCode 	= $_POST['merchantCode'];

		$data = array("secureparam"=> $secureParam,
					   "merchant_code" => $merchantCode,
					   "pm_code" => $paymentMethod,
					   "pe_code" => $paymentVal ) ;
	 
		//  Initiate curl
		$curl = curl_init(); 
		$headr[] = 'Authorization:Basic dGVzdGNsaWVudDp0ZXN0cGFzcw=='; 
		$headr[] = 'Content-Type:multipart/form-data'; 
		// Set cURL options
		curl_setopt($curl, CURLOPT_URL,$url);
		curl_setopt($curl, CURLOPT_HTTPHEADER,$headr); 
		curl_setopt($curl, CURLOPT_POST, TRUE);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data );
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		//Run cURL
		$result = curl_exec($curl);
 		
		//Close cURL
		curl_close($curl);  
		$arrayVal = json_decode($result);   
	 	$_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP'] = $arrayVal; 

		if(!empty($_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP'])){ 
			include('../../PAGES/REQUIRES/resources.php');  
		}
		else{    
			die("Error Message.");
			include('../../PAGES/REQUIRES/resources.php');  
		}
	}else{ 
		//unset($_SESSION['IPG_PUBLIC']['TRANSACTIONS']); 
		 
		$url = ENTERPRISE_URL."getTransaction.php";

		$secureParam 	= $_SESSION['IPG_PUBLIC']['SECUREPARAM'];
		$merchantCode 	= $_SESSION['IPG_PUBLIC']['MERCHANT']->merchant_code; 
		
		$data = array("secureparam"=> $secureParam,
					   "merchant_code" => $merchantCode,
					   "pm_code" => $paymentMethod,
					   "pe_code" =>$paymentVal ) ;
 
		//  Initiate curl
		$curl = curl_init(); 
		$headr[] = 'Authorization:Basic dGVzdGNsaWVudDp0ZXN0cGFzcw=='; 
		$headr[] = 'Content-Type:multipart/form-data'; 
		// Set cURL options
		curl_setopt($curl, CURLOPT_URL,$url);
		curl_setopt($curl, CURLOPT_HTTPHEADER,$headr); 
		curl_setopt($curl, CURLOPT_POST, TRUE);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data );
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		//Run cURL
		$result = curl_exec($curl);
 		
		//Close cURL
		curl_close($curl);  
		$arrayVal = json_decode($result);   
	 	$_SESSION['IPG_PUBLIC']['TRANSACTIONS'] = $arrayVal; 
		if(!empty($_SESSION['IPG_PUBLIC']['TRANSACTIONS'])){  
			include('PAGES/REQUIRES/resources.php'); 
		}
		else{    
			die("Error Message."); 
			include('PAGES/REQUIRES/resources.php');  
		} 

	}
?>  