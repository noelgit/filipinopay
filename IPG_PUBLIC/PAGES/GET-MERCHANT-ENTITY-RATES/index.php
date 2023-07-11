<?php   
	//session_destroy();   
 	$getMerchantCode = $_GET['merchantcode'];
	$getSecureParam  = $custom->getSecureParam($_GET['secureparam'],"GET");
 	$encryption_key = $getMerchantCode;
	$Cryptor = new Cryptor($encryption_key);
	$TRN = $Cryptor->decrypt($getSecureParam);  
	
 	$transQueryData = array();
 	$transQueryData['TRN'] = $TRN;
 	$transQueryData['MERCHANT_CODE'] = $getMerchantCode;

	$checkDatabase = $dbEnterprise->getRow("SELECT * FROM tbl_transactions_hdr WHERE TRN = :TRN AND MERCHANT_CODE = :MERCHANT_CODE AND (TRANS_STATUS = '3' OR TRANS_STATUS = '4')", $transQueryData);
	if(!$checkDatabase){
		if($TRN == $_SESSION['IPG_PUBLIC']['SESSION_ID'] OR empty($_SESSION['IPG_PUBLIC']['SESSION_ID'])){  

			unset($_SESSION['IPG_PUBLIC']['MERCHANT']); 
			$_SESSION['IPG_PUBLIC']['SECUREPARAM'] = $getSecureParam;

			if(empty($_SESSION['IPG_PUBLIC']['MERCHANT'])){
			 
				$url = ENTERPRISE_URL."getMerchantEntityRates.php?secureparam=".$getSecureParam."&merchantcode=".$getMerchantCode."";

				//  Initiate curl
				$curl = curl_init();
				
				$headr[] = 'Authorization:Basic dGVzdGNsaWVudDp0ZXN0cGFzcw==';
				// Set cURL options
				curl_setopt($curl, CURLOPT_URL,$url);
				curl_setopt($curl, CURLOPT_HTTPHEADER,$headr);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				
				//Run cURL
				$result = curl_exec($curl);
		 
				//Close cURL
				curl_close($curl); 
				$arrayVal = json_decode($result); 
			 	$_SESSION['IPG_PUBLIC']['MERCHANT'] = $arrayVal;
 
				if(!empty($_SESSION['IPG_PUBLIC']['MERCHANT'])){ 
					header('Location: '.BASE_URL.'TERMS-AND-CONDITIONS');	 
					include('PAGES/REQUIRES/resources.php'); 
				}
				else{    
					die("Error Message."); 
					include('PAGES/REQUIRES/resources.php'); 
				}  
			}else{
				//header('Location: '.BASE_URL.'TERMS-AND-CONDITIONS');	
				include('PAGES/REQUIRES/resources.php'); 
			}
		}else{  
			$topLogo = array(); 
			$topLogo['PAGE_ID'] = '1';
			$topLogo['POSITION_ID'] = '1';
			$topLogo['STATUS'] = '1';
			$selectTopLogo = $dbPublic->getRow("SELECT * FROM tbl_ref_global_images WHERE PAGE_ID = :PAGE_ID AND POSITION_ID = :POSITION_ID AND STATUS = :STATUS LIMIT 1", $topLogo);	
		?>
			<div class="container">
				<div class="gap-sm"></div>
				<div class="row">
					<div class="col-md-8 offset-md-2">
						<div class="text-center"> 
							<img src="<?php echo IMG.$selectTopLogo->FILE_NAME_LOCATION.$selectTopLogo->FILE_NAME; ?>" alt="logo" class="full-width" style="width: 200px;"> 
							<div class="gap-sm"></div>
						</div>
						<div class="TaCBox"> 
							<h1><strong>Oops!</strong></h1>
							<p>Other transaction is still on process.</p> 
							<div class="text-right">
								<button class="btn btn-primary" id="btnRetry">Retry</button>
								<button class="btn btn-success" id="btnContinue">Continue</button>
							</div>
						</div>
					</div>  
				</div>
			</div> 
		<?php
			include('PAGES/REQUIRES/resources.php');  
		}
	}else{ 
		unset($_SESSION['IPG_PUBLIC']['SESSION_ID']); 
		$topLogo = array(); 
		$topLogo['PAGE_ID'] = '1';
		$topLogo['POSITION_ID'] = '1';
		$topLogo['STATUS'] = '1'; 
		$selectTopLogo = $dbPublic->getRow("SELECT * FROM tbl_ref_global_images WHERE PAGE_ID = :PAGE_ID AND POSITION_ID = :POSITION_ID AND STATUS = :STATUS LIMIT 1", $topLogo);	
	 
		?>
		<div class="container">
			<div class="gap-sm"></div>
			<div class="row">
				<div class="col-md-8 offset-md-2">
					<div class="text-center"> 
						<img src="<?php echo IMG.$selectTopLogo->FILE_NAME_LOCATION.$selectTopLogo->FILE_NAME; ?>" alt="logo" class="full-width" style="width: 200px;"> 
						<div class="gap-sm"></div>
					</div>
					<div class="TaCBox"> 
						<h1><strong>Oops!</strong></h1>
						<p>Transaction number is already used and complete.</p> 
					</div>
				</div>  
			</div>
		</div> 
	<?php
		include('PAGES/REQUIRES/resources.php');  		
	}
	
?>  
<script>
	$( "#btnRetry" ).click(function() { 
	  	var response = confirm("Create new session?");
		if (response == true) {
			window.location.href = "<?php echo BASE_URL; ?>PAGES/PHP/SESSION/transaction.php?MERCHANT_CODE=<?php echo $getMerchantCode; ?>&SECUREPARAM=<?php echo $getSecureParam ?>&TYPE=RETRY"; 
		} else { 

		} 
	});
	$( "#btnContinue" ).click(function() {
		var response = confirm("Continue last transaction?");
		if (response == true) {
			window.location.href = "<?php echo BASE_URL; ?>terms-and-conditions"; 
		} else { 

		} 		 
	});
</script>