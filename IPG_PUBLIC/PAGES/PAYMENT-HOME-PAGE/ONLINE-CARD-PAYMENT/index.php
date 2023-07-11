<?php   
	
	if($_SESSION['IPG_PUBLIC']['TermsAndCondition'] != "1"){
		header('Location: '.BASE_URL);	
	}    
	include('PAGES/REQUIRES/header.php');  
	$payeeQueryData = array();
	$payeeQueryData['TRN'] = $TRN;
	$payeeInfo = $dbEnterprise->getRow("SELECT * FROM tbl_transactions_hdr WHERE TRN = :TRN LIMIT 1", $payeeQueryData); 

	$paymentMethod = 'OCP';
	
	$paymentLogo = array(); 
	$paymentLogo['PE_CODE'] = $paymentVal; 
	$paymentLogo['STATUS'] = '1';
	$selectPaymentLogo = $dbPublic->getRow("SELECT * FROM tbl_ref_entity_images WHERE PE_CODE = :PE_CODE AND STATUS = :STATUS" ,$paymentLogo);	

	if(!$selectPaymentLogo){
		header('Location: '.BASE_URL.'PAYMENT-HOME-PAGE');	
	}else{ 
		include('PAGES/GET-TRANSACTIONS/index.php'); 
		$totalFee = 0;
	} 

	$paymentEntity = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->payment_entity;
	$paymentLogo = $selectPaymentLogo->FILE_NAME_LOCATION.$selectPaymentLogo->FILE_NAME;

?>

<div class="div_content mt20">
	<div class="gap gap-md"></div>
	<div class="gap gap-md"></div>
		<div class="container custom_container"> 
			<div class="col-lg-12 customRow">  
				<div class="col-xl-6 col-lg-8 offset-xl-3 offset-lg-2">
					<div class="optionsDiv2 customRow"> 
						<label>TRN: <?php echo $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->trn ?></label> 
						 <table class="table paymentTable">
						 	<tr>
						 		<th>Payment for</th>
						 		<th style="text-align: right;">Amount</th>
						 	</tr>
						 	<?php  
						 		foreach($_SESSION['IPG_PUBLIC']['TRANSACTIONS']->transactions as $transactionData){
									echo"<tr>
									 		<td>".$transactionData->transaction_payment_for."</td>
									 		<td style='text-align: right;'>".number_format($transactionData->transaction_amount,2)."</td>
									 	</tr>
								 	"; 
								 	$totalFee += $transactionData->transaction_amount;
								 } 

						 		if($_SESSION['IPG_PUBLIC']['TRANSACTIONS']->er_type == 'PERCENTAGE'){
									$entityRate   = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->entity_rate_amount;
									$ipgFee 	  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->ipg_fee;
									//MDR
									//v1
									//$totalAddedAmount = (($totalFee + $ipgFee) * $entityRate) + $ipgFee;   
									//$totalFee	+= $totalAddedAmount; 

									//v2
									$amountWithIPG = $totalFee + $ipgFee; 
									$totalAddedAmount = $amountWithIPG / (1.00 - $entityRate);   
									$totalAddedAmount = $totalAddedAmount - $amountWithIPG;
									$totalFee	= $amountWithIPG + $totalAddedAmount; 
									$convenienceFee = $totalAddedAmount + $ipgFee;
																		
									$RateDisplay = $totalAddedAmount;
							 	}else{
							 		$entityRate  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->entity_rate_amount;
									$ipgFee 	 = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->ipg_fee;
								 	$totalFee	 += $entityRate + $ipgFee; 
								 	$RateDisplay = $entityRate + $ipgFee;
							 	}  
						 	?>
						 </table> 				
					</div>

					<div class="optionsDiv2 customRow">  
						<div class="col-3 p0">
							<img src="<?php echo IMG.$paymentLogo; ?>" style="height: 60px; max-width:100%;">
						</div>
						<div class="col-9 p0">
						 <table class="table paymentTable"> 
						 	<tr>
						 		<td><strong>Convenience Fee:</strong></td>
						 		<td style='text-align: right;'><strong><?php echo number_format($RateDisplay,2); ?></strong></td>
						 	</tr>
						 	<tr>
						 		<td><strong>Total:</strong></td>
						 		<td style='text-align: right;'><strong><?php echo number_format($totalFee,2); ?></strong></td>
						 	</tr>
						 </table> 	
						</div>			
					</div>

					<div class="paymentFeesDiv">
					 	<table class="table paymentTable"> 
						 	<tr>
								<td><strong>Email address: </strong></td>
								<td><input type="text" value="<?php echo $payeeInfo->REQUESTOR_EMAIL_ADDRESS; ?>" class="full-width" id="emailAddress" name="emailAddress" data-toggle="popover" data-placement="right" data-original-title="" data-content="This field is required."></td>
							</tr>
						 	<tr>
								<td><strong>Mobile Number: </strong></td>
								<td><input type="text" value="<?php echo $payeeInfo->REQUESTOR_MOBILE_NO; ?>" class="full-width" id="contactNo" name="contactNo" data-toggle="popover" data-placement="right" data-original-title="" data-content="This field is required."></td>
							</tr>
						</table>
						<p><strong>
							Please be advised that confirmation of payment will be sent to the above 
							contact details, check before proceeding to payment to avoid any 
							inconvenience. Thank you!
						</strong></p>
						<p><strong>
							<?php 
								if($paymentVal == "OCP001" AND $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->partner_code == "APOLLO"){
									echo "Your Transaction will be Charged a Convenience Fee of Php 25";
								}elseif($paymentVal == "OCP005" AND $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->partner_code == "PMAYA-DAV"){
									echo "Your Transaction will be Charged a Convenience Fee of 2% of the Transaction Amount";
								}else{
									echo "This Payment Transaction will be charge an Convenience Fee on top of your total payment.";
								}
							?>
						</strong></p>
					</div> 
				</div> 
 
				<div class="col-12 text-center">
					<div class="gap"></div>
					<input type="checkbox" name="paymentCheckBox" id="paymentCheckBox" 
					data-toggle="popover" data-placement="bottom" data-original-title="" data-content="Please check this box if you want to proceed.">
					<span class="paymentCheckBoxText">I here by certify that the aboved information is true and correct.</span>
					<span class="checkbox-error-message"></span> 
					<br>
					<a href="<?php echo BASE_URL; ?>PAYMENT-HOME-PAGE" class="btnBackPayment">Back</a>
					<button class="btnProceedPayment" id="PaymentButton">Proceed to payment </button>
				</div>
			</div>
		</div>
	<div class="gap gap-lg"></div>
</div>	
<?php 
	include('PAGES/REQUIRES/footer.php'); 
	include('PAGES/REQUIRES/resources.php'); 

	if($_SESSION['IPG_PUBLIC']['TRANSACTIONS']->payment_entity == "VISA"){
		$paymentMode = '1';
	}elseif($_SESSION['IPG_PUBLIC']['TRANSACTIONS']->payment_entity == "MASTERCARD"){
		$paymentMode = '2';
	}else{
		$paymentMode = '3';
	}
?>
<script>       

	$("#PaymentButton").click(function (){  
		if($('#paymentCheckBox').is(':checked')){ 
			$('#paymentCheckBox').popover('hide');  
		} 
		if($('#emailAddress').val().length != 0){ 
			$('#emailAddress').popover('hide');  
		}
		if($('#contactNo').val().length != 0){ 
			$('#contactNo').popover('hide'); 
		} 

		if($('#paymentCheckBox').is(':checked') && $('#emailAddress').val().length != 0 && $('#contactNo').val().length != 0){    
			preloader(1); 
			

			<?php
				/*-----------IF NOT APOLLO-------------*/ 
				if($_SESSION['IPG_PUBLIC']['TRANSACTIONS']->partner_code != 'APOLLO'){	        
			?>
				var paymentAPI = window.open('','_blank', 'width=1000, height=600');
				window.history.pushState(null, "", window.location.href);        
		        window.onpopstate = function() {
		            window.history.pushState(null, "", window.location.href); 
		            alert('Payment is on process please wait.'); 
		        };
        	<?php 
        		}  
        	?>


 			var emailAddress = $('#emailAddress').val();
			var contactNo = $('#contactNo').val();
			$.ajax({
				url: BASE_URL+"PAGES/PHP/EMAIL-TEMPLATE/session.php",
    			type:"POST",
				data: { 
					emailAddress:emailAddress,
					contactNo:contactNo
				},
				cache: false,      
			}).done(function(){
				$.ajax({ 
				<?php 
						//2C2P PGW
						if($paymentVal == 'OCP004'){ 
				?>
							url: "<?php echo CARD_URL_2C2P; ?>pages/php/payment/authentication.php",  
							data: { 
								merchant_ref_code:"<?php echo $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->merchant_ref_num; ?>",
								amount:"<?php echo number_format($totalFee,2,'.',''); ?>",
								payment_for:"<?php echo $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->transactions[0]->transaction_payment_for; ?>",
								payment_mode:"<?php echo $paymentMode; ?>",
								response_url:BASE_URL,
								email: "<?php echo $payeeInfo->REQUESTOR_EMAIL_ADDRESS; ?>",
								TRN: "<?php echo $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->trn; ?>",
							},					
				<?php
						//Paymaya Credit Debit
						}elseif($paymentVal == 'OCP005'){
				?>
							url: "<?php echo BASE_URL; ?>PAGES/PHP/PAYMENT-HOME-PAGE/ONLINE-CARD-PAYMENT/paymaya-checkout.php",  
							data: { 
								merchant_ref_code:"<?php echo $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->merchant_ref_num; ?>",
								convenienceFee:"<?php echo number_format($convenienceFee,2,'.',''); ?>",
								transactionDetails:'<?php echo json_encode($_SESSION['IPG_PUBLIC']['TRANSACTIONS']->transactions); ?>',
								payment_mode:"<?php echo $paymentMode; ?>",
								response_url:BASE_URL,
								name: "<?php echo $payeeInfo->REQUESTOR_NAME; ?>",
								contactNo: "<?php echo $payeeInfo->REQUESTOR_MOBILE_NO; ?>",
								email: "<?php echo $payeeInfo->REQUESTOR_EMAIL_ADDRESS; ?>",
								TRN: "<?php echo $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->trn; ?>",
								partnerCode: "<?php echo $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->partner_code; ?>",
							},	

				<?php			
						}else{
							if($_SESSION['IPG_PUBLIC']['TRANSACTIONS']->partner_code == 'APOLLO'){
				?>
								url: "<?php echo BASE_URL; ?>PAGES/PHP/PAYMENT-HOME-PAGE/ONLINE-CARD-PAYMENT/apollo-request.php", 
								data: {  
									referenceCode:"<?php echo $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->trn; ?>",
									amount:"<?php echo number_format($totalFee,2,'.',''); ?>",
									serviceType:"<?php echo $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->transactions[0]->transaction_payment_for; ?>",
									returnURL:"<?php echo BASE_URL.'PAGES/PHP/PAYMENT-HOME-PAGE/ONLINE-CARD-PAYMENT/apollo-response.php'; ?>", 
									partnerCode: "<?php echo $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->partner_code; ?>",
								},
				<?php 
							}else{
				?>
								url: "<?php echo CARD_URL_DUMMY; ?>pages/php/payment/authentication.php", 
								data: {
									mid:"10001",   
									merchant_ref_code:"<?php echo $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->merchant_ref_num; ?>",
									amount:"<?php echo number_format($totalFee,2,'.',''); ?>",
									payment_for:"<?php echo $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->transactions[0]->transaction_payment_for; ?>",
									security_token:"E443169117A184F91186B401133B20BE670C7C0896F9886075E5D9B81E9D076B",
									payment_mode:"<?php echo $paymentMode; ?>",
									response_url:BASE_URL,
								},
				<?php 
							}
						}
				?> 
	    			type:"POST",
					cache: false,   
					crossDomain : true, 

				}).done(function(results){  
					 if(results['status_code'] == '201'){ 
						<?php
							/*-----------IF APOLLO-------------*/ 
							if($_SESSION['IPG_PUBLIC']['TRANSACTIONS']->partner_code == 'APOLLO'){	        
						?>
    						window.location.href = results['redirection_link']; 
		   				<?php 
			        		}else{
			        	?> 
							$(document).keydown(function (e) {  
					            return (e.which || e.keyCode) != 116;  
					        });   
							window.name = "IPGPublicWindow"; 
							paymentAPI.location.href = results['redirection_link'];
	            			paymentAPI.focus();    
			        	<?php
			        		}  
			        	?>
					 }else{
					 	alert(results['message']);
					 	preloader(0);
					 }
				}); 

			});

		}else{ 

			if(!$('#paymentCheckBox').is(':checked')){ 
				$('#paymentCheckBox').popover('show');  
			} 
			if($('#emailAddress').val().length == 0){ 
				$('#emailAddress').popover('show');  
			}
			if($('#contactNo').val().length == 0){ 
				$('#contactNo').popover('show'); 
			} 
		}
	});

</script>