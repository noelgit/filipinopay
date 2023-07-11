<?php  
	if($_SESSION['IPG_PUBLIC']['TermsAndCondition'] != "1"){
		header('Location: '.BASE_URL.'TERMS-AND-CONDITIONS');	
	}  

	$requestPMArr = array(); 
	foreach($_SESSION['IPG_PUBLIC']['MERCHANT']->payment_mode AS $paymentMethod){
		array_push($requestPMArr, $paymentMethod->pm_code); 
		foreach($paymentMethod->payment_entities AS $paymentEntity){ 
			$requestPEArr[$paymentMethod->pm_code][] = $paymentEntity->pe_code;
		}
	} 
 	$requestPM = "'" . implode("','", $requestPMArr) . "'";  
 	$requestMerchantCode = $_SESSION['IPG_PUBLIC']['MERCHANT']->merchant_code;

 	//SELECT AVAILABLE MODE OF PAYMENT FOR THE MERCHANT
 	$pmQueryData = array();
 	$pmQueryData['MERCHANT_CODE'] = $requestMerchantCode;
	$SelectPM = $dbEnterprise->getResults("SELECT TRPM.`PM_CODE`, TRPM.`DESCRIPTION`, TRPM.`SORT`, TRPM.`STATUS`, VMMOP.`MERCHANT_CODE`, VMMOP.`MERCHANT_NAME` FROM tbl_ref_payment_mode AS TRPM 

		INNER JOIN vw_merchant_mode_of_payment AS VMMOP
		ON VMMOP.`PM_CODE` = TRPM.`PM_CODE`

		WHERE TRPM.`PM_CODE` IN(".$requestPM.") AND  VMMOP.`MERCHANT_CODE` = :MERCHANT_CODE AND TRPM.`STATUS` = '1' ORDER BY TRPM.SORT ASC", $pmQueryData);

	include('PAGES/REQUIRES/header.php'); 
 	 
?> 
<div class="div_content">
	<div class="gap gap-md"></div>
		<div class="container custom_container"> 
			<div class="col-xl-8 col-lg-10 col-md-12 offset-xl-2 offset-lg-1 customRow"> 
				
				<div class="col-md-12 text-center mb20 mt20">
					<h3>Select Mode of Payment</h3>
				</div>
  				<?php
  					foreach($SelectPM AS $SelectPMVal){ 
  						switch ($SelectPMVal->PM_CODE) {
  							case 'OCP'://Online Card Payment
  								$link  = "";
  								$divID = "OnlineCardPaymentBtn";
  								$contentID = "OnlineCardPaymentContent";
  								$contentLabel = "Online Card Payment Options";
  								$image = IMG."PAYMENT_HOME_PAGE/DEFAULT/ONLINE_PAYMENT.png";
  								$PELink = BASE_URL."PAYMENT-HOME-PAGE/ONLINE-CARD-PAYMENT";

  								break;
  							case 'EWP'://E-WALLET PAYMENT
  								$link  = "";
  								$divID = "EWalletPaymentBtn";
  								$contentID = "EWalletPaymentContent";
  								$contentLabel = "eWallet Payment Options";
  								$image = IMG."PAYMENT_HOME_PAGE/DEFAULT/E-WALLET_PAYMENT.png"; 
  								$PELink = BASE_URL."PAYMENT-HOME-PAGE/EWALLET-PAYMENT";

  								break;
  							case 'UFP'://Up-front Payment 
  								$link  = "href='".BASE_URL."PAYMENT-HOME-PAGE/UP-FRONT-PAYMENT'"; 
  								$divID = "";
  								$contentID = "";
  								$contentLabel = "";
  								$image = IMG."PAYMENT_HOME_PAGE/DEFAULT/UP-FRONT_PAYMENT.png";
  								$PELink = "";

  								break;
  							default:
  								# code...
  								break; 
  						}  

						echo "				
							<div class='col-lg-4 col-sm-6 mt20'>
									<div class='text-center paymentMethod' id='".$divID."' data-toggle='popover' data-placement='bottom' tabindex='1' >
										<a ".$link." class='defaultLink'>
											<img src='".$image."' alt='logo' class='full-width'>
											<span>".$SelectPMVal->DESCRIPTION."</span>
										</a>
									</div>
							</div>
						";

				?> 
				<div class="onlineCardOptions" id="<?php echo $contentID; ?>" style="display: none">
					<div class="optionsDiv customRow mb20"> 
						<label><?php echo $contentLabel; ?></label> 
						<?php 
							$requestPE = "";
							$requestPE = "'" . implode("','", $requestPEArr[$SelectPMVal->PM_CODE]) . "'"; 

							$queryData = array();
							$queryData['MERCHANT_CODE'] = $requestMerchantCode;
							$queryData['PM_CODE'] = $SelectPMVal->PM_CODE;
							
							$query = $dbEnterprise->getResults("SELECT TRPE.`PE_CODE`,TRPE.`DESCRIPTION` FROM tbl_payment_entity_rate AS TPER 

								INNER JOIN tbl_ref_payment_entity AS TRPE
								ON TRPE.`PE_CODE` = TPER.`PE_CODE`
							 
								WHERE TPER.`MERCHANT_CODE` = :MERCHANT_CODE AND TPER.`PM_CODE` = :PM_CODE AND TPER.`PE_CODE` IN(".$requestPE.") AND TPER.`STATUS` = '1' AND TRPE.STATUS = '1' 
								ORDER BY TRPE.`SORT` ASC", $queryData);

							foreach($query AS $paymentEntity){  
								$entityIMGArr = '';
								$entityIMGArr = array();  
								$entityIMGArr['PE_CODE'] = $paymentEntity->PE_CODE;
								$entityIMGArr['STATUS'] = '1';
								$entityIMG = $dbPublic->getRow("SELECT * FROM tbl_ref_entity_images WHERE PE_CODE = :PE_CODE AND STATUS = :STATUS" ,$entityIMGArr);	 
								if($paymentEntity->PE_CODE == 'OCP004'){
							 		$imageLink = IMG.$entityIMG->FILE_NAME_LOCATION.'GENERICLOGO.png';
							 	}elseif($paymentEntity->PE_CODE == 'OCP001'){
							 		$imageLink = IMG.$entityIMG->FILE_NAME_LOCATION.'VISA.png';
							 	}else{
							 		$imageLink = IMG.$entityIMG->FILE_NAME_LOCATION.$entityIMG->FILE_NAME;
							 	}
								echo " 
									<div class='col-6 col-sm-6 col-md-6 mt20'>
										<a href='".$PELink."/".$paymentEntity->PE_CODE."' class='optionsLink'>  
											<img src='".$imageLink."' class='full-width'> 
										</a>
									</div>  
								";
							}
						?>
						 

					</div> 
				</div> 
				<?php 
  					}
  				?>   
 
				<!--EWALLET PAYMENT-->
				<div class="onlineCardOptions" id="EWalletPaymentContent" style="display: none">
					<div class="optionsDiv customRow mb20"> 
						<label>eWallet Payment Options</label> 
						<div class="col-6 col-sm-4 col-md-6 mt20">
							<a href="<?php echo BASE_URL ?>PAYMENT-HOME-PAGE/EWALLET-PAYMENT" class="optionsLink">  
								<img src="<?php echo IMG; ?>EWALLET-PAYMENT/PAYMENT-OPTIONS/GCASH.png" class="full-width"> 
							</a>
						</div> 
						<div class="col-6 col-sm-4 col-md-6 mt20">
							<a href="<?php echo BASE_URL ?>PAYMENT-HOME-PAGE/EWALLET-PAYMENT" class="optionsLink">  
								<img src="<?php echo IMG; ?>EWALLET-PAYMENT/PAYMENT-OPTIONS/PAYMAYA.png" class="full-width"> 
							</a>	
						</div>  				
					</div> 
				</div>
				<div class="gap gap-md"></div>
			</div>
		</div>
	<div class="gap gap-md"></div>
</div>



<?php  
?>

<?php 
	include('PAGES/REQUIRES/footer.php'); 
	include('PAGES/REQUIRES/resources.php'); 
?>


<script>
	var OCPC = {
	    'html':true,    
	    content: function(){
	        return $('#OnlineCardPaymentContent').html();
	    }
	};
	var EWPC = {
	    'html':true,    
	    content: function(){
	        return $('#EWalletPaymentContent').html();
	    }
	};

	$(function(){
	    $('#OnlineCardPaymentBtn').popover(OCPC);
	    $('#EWalletPaymentBtn').popover(EWPC); 
	}); 
 
</script>