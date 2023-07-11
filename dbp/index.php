<?php
	session_start();  
	include("config.php");
	include("./LIB/libraries.php");
 	error_reporting(E_ALL);

 	if(isset($_GET['TRN']) AND isset($_GET['STATUS'])){ 
 		if($_GET['STATUS'] == '1' OR $_GET['STATUS'] == '2' OR $_GET['STATUS'] == '3'){
 			$transactionStatus = 'success';
 		}else{
 			$transactionStatus = 'failed';
 		}
		
		if($_GET['STATUS'] == "3"){
			$transactionLabel = "Payment Success";
			$transactionMessage = "Thank you, your Payment was successful.";
			$transactionIcon = "<i class='fa fa-check-circle successIcon'></i>";
		}elseif($_GET['STATUS'] == "1" OR $_GET['STATUS'] == "2"){
			$transactionLabel = "Payment Request Success";
			$transactionMessage = "Thank you, your Payment Request was successful.";
			$transactionIcon = "<i class='fa fa-check-circle successIcon'></i>";
		}elseif($_GET['STATUS'] == "5"){
			$transactionLabel = "Payment Expired";
			$transactionMessage = "Sorry, your Payment was unsuccessful.";
			$transactionIcon = "<i class='fa fa-times-circle failedIcon'></i> ";
		}else{
			$transactionLabel = "Payment Failed";
			$transactionMessage = "Sorry, your Payment was unsuccessful.";
			$transactionIcon = "<i class='fa fa-times-circle failedIcon'></i> "; 
		}
		$firstPage = 'payment-page';
 	}else{
		if(!empty($_GET["val1"])){ 
			$firstPage = $_GET["val1"] ? $_GET["val1"] : false;   
		}else{  
			$firstPage = 'payment-page';
		}  
 	} 

	
 	
	$secondPage = isset($_GET["val2"]) ? $_GET["val2"] : false;
	$thirdPage = isset($_GET["val3"]) ? $_GET["val3"] : false;
	$fourthPage = isset($_GET["val4"]) ? $_GET["val4"] : false;


	$title = $secondPage != "" ? str_replace("-", " ", $firstPage)." - ".str_replace("-", " ", $secondPage) : str_replace("-", " ", $firstPage) ; 
?>

<!DOCTYPE html> 
<html lang="en">
	<head>      
		<title><?php echo COMPANY_NAME." | ".ucwords($title); ?></title>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge"> 

		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<meta name="format-detection" content="telephone=no">
		<meta name="robots" content="noindex, nofollow"> 
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />    
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
		 	
			if($firstPage == "" OR $firstPage == "home"){ 
				$file	=	BASE_URL."PAGES/".$firstPage."/index.php"; 
			}else{ 
				if($secondPage == ""){   
					$file	=	BASE_URL."PAGES/".$firstPage."/index.php";  
				}else{ 
					if($thirdPage == ""){   
						$file	=	BASE_URL."PAGES/".$firstPage."/".$secondPage."/index.php";  
					}else{ 
						$file	=	BASE_URL."PAGES/".$firstPage."/".$secondPage."/".$thirdPage."/index.php";  
					}
				} 
			}

			$file_headers = @get_headers($file);  
			// print_r($file_headers);
			if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
				$exists = false;
				require_once("PAGES/404.php"); 
			}
			

			else { 
				$exists = true;
				if($firstPage == "" OR $firstPage == "payment-page"){ 
					require_once("PAGES/".$firstPage."/index.php"); 
				}else{	
					if($secondPage == ""){  
						require_once("PAGES/".$firstPage."/index.php"); 
					}else{
						if($thirdPage == ""){
					   		require_once("PAGES/".$firstPage."/".$secondPage."/index.php");
						}else{
					  		require_once("PAGES/".$firstPage."/".$secondPage."/".$thirdPage."/index.php");
						} 
					}
				}
			}
			
		?> 
	</body>
</html>

