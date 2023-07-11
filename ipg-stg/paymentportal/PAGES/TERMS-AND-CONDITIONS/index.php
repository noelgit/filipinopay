<?php    
	unset($_SESSION['IPG_PUBLIC']['TermsAndCondition']); 
	if(empty($_SESSION['IPG_PUBLIC']['MERCHANT'])){ 
		header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
		die();
	} 
	
 	$getMerchantCode = $_SESSION['IPG_PUBLIC']['MERCHANT']->merchant_code;
	$getSecureParam  = $_SESSION['IPG_PUBLIC']['SECUREPARAM'];
 	$encryption_key = $getMerchantCode;
	$Cryptor = new Cryptor($encryption_key);
	$TRN = $Cryptor->decrypt($getSecureParam);   
 
  	//Check if transaction number is already finish
    $transactionQueryData = array();
    $transactionQueryData['TRN'] = $TRN;
    $transactionQueryData['MERCHANT_CODE'] = $getMerchantCode;

  	$checkDatabase = $dbEnterprise->getRow("SELECT * FROM tbl_transactions_hdr WHERE TRN = :TRN AND MERCHANT_CODE = :MERCHANT_CODE AND (TRANS_STATUS = '3' OR TRANS_STATUS = '4') LIMIT 1", $transactionQueryData);
 	
  	if($checkDatabase){
   		header('Location: '.BASE_URL.'?secureparam='.$getSecureParam.'&merchantcode='.$getMerchantCode.''); 
  	} 

	$_SESSION['IPG_PUBLIC']['SESSION_ID'] = $TRN;  
 	
	$topLogo = array(); 
	$topLogo['PAGE_ID'] = '1';
	$topLogo['POSITION_ID'] = '1';
	$topLogo['STATUS'] = '1';
	$selectTopLogo = $dbPublic->getRow("SELECT * FROM tbl_ref_global_images WHERE PAGE_ID = :PAGE_ID AND POSITION_ID = :POSITION_ID AND STATUS = :STATUS LIMIT 1", $topLogo);	 

	$bottomLogo = array(); 
	$bottomLogo['PAGE_ID'] = '1';
	$bottomLogo['POSITION_ID'] = '2';
	$bottomLogo['STATUS'] = '1'; 
	$selectBottomLogo = $dbMerchant->getResults("SELECT * FROM tbl_ref_global_images WHERE PAGE_ID = :PAGE_ID AND POSITION_ID = :POSITION_ID AND STATUS = :STATUS AND MERCHANT_CODE = '".$getMerchantCode."'", $bottomLogo);	

	$TAC = array();  
	$TAC['STATUS'] = '1';
	$selectTAC = $dbPublic->getRow("SELECT * FROM tbl_ref_terms_conditions WHERE STATUS = :STATUS LIMIT 1", $TAC);	
 
?>

<div class="gap gap-md"></div>

<div class="container">
	<div class="row text-center m0">
			<div class="col-lg-6 col-md-8 col-sm-10 col-12 offset-lg-3 offset-md-2 offset-sm-1 offset-0"> 
				
				<!--Logo
				<div class="col-md-6 col-sm-8 col-xs-12 offset-md-3 offset-sm-2">
					<img src="<?php echo IMG.$selectTopLogo->FILE_NAME_LOCATION.$selectTopLogo->FILE_NAME; ?>" alt="logo" class="full-width">
				</div>-->

				<!--Text-->
				<div class="col-md-12 TaCBox mt20 mb20"> 
					<h2>Terms and Conditions</h2>
					<a href="#" class="TaCClose"><i class="fa fa-times-circle"></i></a>
					<div class="TaCContent" id="TaCScrollContent">
					<?php 
						$myfile = fopen($selectTAC->FILE_NAME_LOCATION.$selectTAC->FILE_NAME, "r");
						if($myfile){
							echo fread($myfile,filesize($selectTAC->FILE_NAME_LOCATION.$selectTAC->FILE_NAME));
							fclose($myfile); 
				 	?>
					</div>
				
					<button class="btn btn-success mb10 mt10" id="btnAgreeTAC">I Accept</button>
					<!--<a class="TaCScrollBTN"><span>Scroll Down  <i class="fa fa-arrow-down"></i></span></a>--> 
					
					<?php }else{ ?>
						
						<div class="gap"></div>
						<p class="text-center"> Something went wrong from the text file.</p>
						<div class="gap"></div> 
					</div>
					
					<?php } ?>
					
				</div> 

				<!--Logo-->
				<div class="row m0">
					<?php  
						foreach($selectBottomLogo AS $value){ 
					?>
					<div class="col-md-4 col-sm-4 col-4">

						<!--<img src="<?php echo IMG.$value->FILE_NAME_LOCATION.$value->FILE_NAME; ?>" alt="logo" class="full-width">-->
						<img src="<?php echo IMG.$value->FILE_NAME_LOCATION.$value->FILE_NAME; ?>" alt="logo" style="width:100px">
						
					</div>
					
					<?php 
						}
					?> 
				</div>
				<br>
				<br>
			</div> 

		</div>
	</div>
</div>
<?php 
	include('PAGES/REQUIRES/resources.php');  
?>  