<?php
	session_start(); 
	require_once __DIR__ . '/vendor/autoload.php';
	include("config.php");
	include("LIBRARIES/libraries.php");
 	error_reporting(0);

	$timeStamp 	  = date('Y-m-d G:i:s');
 	$RRN 			= $_GET['RRN'];
 	$PAYMENT_STATUS = $_GET['PAYMENT_STATUS']; 
 	
 	if($PAYMENT_STATUS == '0' OR $PAYMENT_STATUS == '1'){
 		$PAYMENT_STATUS = TRUE;
 	}else{
 		$PAYMENT_STATUS = FALSE;
 	}
 	if($_GET['secureparam']){
 		$firstPage = 'get-merchant-entity-rates';
 	}elseif((!empty($RRN) OR $PAYMENT_STATUS) AND $_SESSION['IPG_PUBLIC']['SECUREPARAM']){   
		$firstPage = 'transaction';	
 	}else{
 		if(!empty($_GET["val1"])){ 
 			$firstPage = $_GET["val1"] ? $_GET["val1"] : false;   
  		}else{  
  			$firstPage = 'terms-and-conditions';
  		} 
  	}   
 
	$secondPage		=	$_GET["val2"] ? $_GET["val2"] : false;  
	$paymentVal		=	$_GET["val3"] ? $_GET["val3"] : false;   
	$fourthPage		=	$_GET["val4"] ? $_GET["val4"] : false; 

	$title = $secondPage != "" ? str_replace("-", " ", $firstPage)." - ".str_replace("-", " ", $secondPage) : str_replace("-", " ", $firstPage) ; 

?> 
<!DOCTYPE html> 
<html lang="en">
	<head>      
		<title><?php echo COMPANY_NAME." | ".ucwords($title); ?></title>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="description" content=""/>
		<meta name="keywords" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<meta name="format-detection" content="telephone=no"> 
		<link href="<?php echo CSS; ?>preloader.css" rel="stylesheet"> 
	</head>
	<body> 
		<div id="preloader">
			<div>
	 			<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
				<label>Please wait...</label>
			</div>
		</div>
		<?php 

			if($firstPage == "" OR $firstPage == "get-merchant-entity-rates"){ 
				$file =	BASE_URL."PAGES/".strtoupper("get-merchant-entity-rates")."/index.php"; 
			}else{ 
				if($secondPage == ""){  
					$file =	BASE_URL."PAGES/".strtoupper($firstPage)."/index.php";  
				}else{  
					$file =	BASE_URL."PAGES/".strtoupper($firstPage)."/".strtoupper($secondPage)."/index.php";  
				} 
			}
			           
			$file_headers = @get_headers($file);  
			if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
				$exists = false;
				require_once("PAGES/404.php"); 
			}

			else{ 
				$exists = true;
				if($firstPage == "" OR $firstPage == "get-merchant-entity-rates"){ 
					require_once("PAGES/".strtoupper("get-merchant-entity-rates")."/index.php"); 
				}else{	
					if($secondPage == ""){  
						require_once("PAGES/".strtoupper($firstPage)."/index.php"); 
					}else{
					   require_once("PAGES/".strtoupper($firstPage)."/".strtoupper($secondPage)."/index.php"); 
					}
				}
			}
		?> 
	</body>
</html>

