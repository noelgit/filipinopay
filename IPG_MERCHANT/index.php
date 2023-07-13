<?php 
	session_start();  
	include("config.php");
	include("LIBRARIES/libraries.php");
	ini_set('display_errors', 1);
 	error_reporting(E_ALL);

	$timeStamp 	    = date('Y-m-d G:i:s');
	$firstPage		=	isset($_GET["val1"]) ? $_GET["val1"] : false;  
	$secondPage		=	isset($_GET["val2"]) ? $_GET["val2"] : false;  
	$thirdPage		=	isset($_GET["val3"]) ? $_GET["val3"] : false;  
	$fourthPage		=	isset($_GET["val4"]) ? $_GET["val4"] : false; 
	
	echo $firstPage;
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
				$file =	BASE_URL."PAGES/".strtoupper($firstPage)."/index.php"; 
			}else{ 
				if($secondPage == "" OR $firstPage == "DASHBOARD"){  
					$file =	BASE_URL."PAGES/".strtoupper($firstPage)."/index.php";  
				}else{  
					$file =	BASE_URL."PAGES/".strtoupper($firstPage)."/".strtoupper($secondPage)."/index.php";  
				} 
			}    
			print_r(@get_headers($file));
			print_r($file);
			$file_headers = @get_headers($file);  
			if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
				$exists = false;
				require_once("PAGES/404.php"); 
			}

			else { 
				$exists = true;
				if($firstPage == "login"){ 
					require_once("PAGES/".strtoupper($firstPage)."/index.php"); 
				}else{	
					if($secondPage == "" OR $firstPage == "DASHBOARD"){  
						require_once("PAGES/".strtoupper($firstPage)."/index.php"); 
					}else{
					   	require_once("PAGES/".strtoupper($firstPage)."/".strtoupper($secondPage)."/index.php"); 
					}
				}
			}
		?> 
	</body>
</html>

