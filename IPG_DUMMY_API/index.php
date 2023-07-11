<?php
	session_start();  
	include("config.php");
	include("lib/libraries.php");
 	error_reporting(0);

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
	</head>
	<body> 
		<div id="preloader">
 
		</div>
		<?php  
			if($firstPage == "" OR $firstPage == "login"){ 
				$file	=	BASE_URL."pages/".$firstPage."/index.php"; 
			}else{ 
				if($secondPage == ""){   
					$file	=	BASE_URL."pages/".$firstPage."/index.php";  
				}else{
					if($thirdPage == ""){   
						$file	=	BASE_URL."pages/".$firstPage."/".$secondPage."/index.php";  
					}else{ 
						$file	=	BASE_URL."pages/".$firstPage."/".$secondPage."/".$thirdPage."/index.php";  
					}
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
					require_once("pages/".$firstPage."/index.php"); 
				}else{	
					if($secondPage == ""){  
						require_once("pages/".$firstPage."/index.php"); 
					}else{
						if($thirdPage == ""){
					   		require_once("pages/".$firstPage."/".$secondPage."/index.php");
						}else{
					  		require_once("pages/".$firstPage."/".$secondPage."/".$thirdPage."/index.php");
						} 
					}
				}
			}
		?> 
	</body>
</html>

