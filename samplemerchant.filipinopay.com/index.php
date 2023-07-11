<?php
	//session_start();  
	include("config.php");
	//include("lib/libraries.php");
 	error_reporting(0);

 	$ipgLink 		=   'http://localhost/IPG_PUBLIC/?secureparam=61a14fae07ad48c2aa3f164724c76d0fRDjVDb85DzzpYxlMectpl1k3AuA=&merchantcode=GAI0001';
	$firstPage		=	$_GET["val1"] ? $_GET["val1"] : false;  
	$secondPage		=	$_GET["val2"] ? $_GET["val2"] : false;  
	$thirdPage		=	$_GET["val3"] ? $_GET["val3"] : false;  
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
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />    
		<link href="<?php echo CSS; ?>preloader.css" rel="stylesheet"> 
		<style>
			#preloader{
				height: 100vh;
				width: 100%;
				background: white;
				position: fixed;
				z-index: 9999;
				text-align: center;
			}
		</style>
	</head>
	<body> 
		<div id="preloader">
		    <div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
		</div>
		<?php 

			if($firstPage == "" OR $firstPage == "login"){ 
				$file	=	BASE_URL."pages/payment-page/index.php"; 
			}else{ 
				if($secondPage == ""){   
					$file	=	BASE_URL."pages/".$firstPage."/index.php";  
				}else{   
					$file	=	BASE_URL."pages/".$firstPage."/".$secondPage."/index.php";  
				} 
			}           
			$file_headers = @get_headers($file);  
			if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
				$exists = false;
				require_once("pages/404.php"); 
			}

			else { 
				$exists = true;
				if($firstPage == "" OR $firstPage == "login"){ 
					require_once("pages/payment-page/index.php"); 
				}else{	
					if($secondPage == ""){  
						require_once("pages/".$firstPage."/index.php"); 
					}else{
					   require_once("pages/".$firstPage."/".$secondPage."/index.php"); 
					}
				}
			}
		?> 
	</body>
</html>

