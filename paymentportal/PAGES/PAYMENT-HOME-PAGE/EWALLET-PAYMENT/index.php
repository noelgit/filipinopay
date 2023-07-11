<?php   
	
	if($_SESSION['IPG_PUBLIC']['TermsAndCondition'] != "1"){
		header('Location: '.BASE_URL);	
	}    
	include('PAGES/REQUIRES/header.php');  
	$payeeQueryData = array();
	$payeeQueryData['TRN'] = $TRN;
	$payeeInfo = $dbEnterprise->getRow("SELECT * FROM tbl_transactions_hdr WHERE TRN = :TRN LIMIT 1", $payeeQueryData); 

	$paymentMethod = 'EWP'; 
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
	$paymentLogo   = $selectPaymentLogo->FILE_NAME_LOCATION.$selectPaymentLogo->FILE_NAME;
 	 
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
									$amountWithIPG = $totalFee + $ipgFee; 
									$totalAddedAmount = $amountWithIPG / (1.00 - $entityRate);   
									$totalAddedAmount = $totalAddedAmount - $amountWithIPG; 
									$totalFee	= $amountWithIPG + $totalAddedAmount; 
									
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
							This payment transaction will be charged a convenience fee.
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
					<button class="btnProceedPayment" id="PaymentButton">Proceed to payment </button>
				</div>
			</div>
		</div>
	<div class="gap gap-lg"></div>
</div>

<?php 
	include('PAGES/REQUIRES/footer.php'); 
	include('PAGES/REQUIRES/resources.php'); 
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
					//Paymaya Ewallet
					if($paymentVal == 'EWP001'){ 
				?>
						url: "<?php echo BASE_URL; ?>PAGES/PHP/PAYMENT-HOME-PAGE/E-WALLET/paymaya-gcash-request.php", 
						data: { 
  
							merchant_ref_code:"<?php echo $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->merchant_ref_num; ?>",
							convenienceFee:"<?php echo number_format($RateDisplay, 2,'.',''); ?>",
							transactionDetails:'<?php echo json_encode($_SESSION['IPG_PUBLIC']['TRANSACTIONS']->transactions); ?>',
							amount:"<?php echo number_format($totalFee,2,'.',''); ?>",
							response_url:BASE_URL,
							name: "<?php echo $payeeInfo->REQUESTOR_NAME; ?>",
							fname: "<?php echo $payeeInfo->REQUESTOR_FIRSTNAME; ?>",
							mname: "<?php echo $payeeInfo->REQUESTOR_MIDDLENAME; ?>",
							lname: "<?php echo $payeeInfo->REQUESTOR_LASTNAME; ?>",					
							contactNo: "<?php echo $payeeInfo->REQUESTOR_MOBILE_NO; ?>",
							email: "<?php echo $payeeInfo->REQUESTOR_EMAIL_ADDRESS; ?>",
							TRN: "<?php echo $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->trn; ?>",
							partnerCode: "<?php echo $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->partner_code; ?>",
						},	 
				<?php  
					}else{ 

					}
				?> 
	    			type:"POST",
					cache: false,   
					crossDomain : true, 

				}).done(function(results){  
					 if(results['status_code'] == '201'){  
						$(document).keydown(function (e) {  
				            return (e.which || e.keyCode) != 116;  
				        });   
						window.name = "IPGPublicWindow"; 
						paymentAPI.location.href = results['redirection_link'];
            			paymentAPI.focus();    
			         
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