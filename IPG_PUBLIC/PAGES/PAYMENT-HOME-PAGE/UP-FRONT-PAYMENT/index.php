<?php  
	if($_SESSION['IPG_PUBLIC']['TermsAndCondition'] != "1"){
		header('Location: '.BASE_URL);	
	}    

	foreach($_SESSION['IPG_PUBLIC']['MERCHANT']->payment_mode AS $paymentMethod){ 
		if($paymentMethod->pm_code == 'UFP'){ 
 			$requestPEArr = $paymentMethod->payment_entities;
		}
	}   
	
	$nonBanks = array();
	$banks 	  = array();
	foreach($requestPEArr as $paymentOption){
		if($paymentOption->entity_type == 'NON-BANK'){
			array_push($nonBanks, $paymentOption->pe_code); 
		}elseif($paymentOption->entity_type == 'BANK'){
			array_push($banks, $paymentOption->pe_code); 
		} 
	}	

	$nonBanks = "'" . implode("','", $nonBanks) . "'"; 
	$banks = "'" . implode("','", $banks) . "'";  

	include('PAGES/REQUIRES/header.php'); 
 
	$payeeQueryData = array();
	$payeeQueryData['TRN'] = $TRN;
	$payeeInfo = $dbEnterprise->getRow("SELECT * FROM tbl_transactions_hdr WHERE TRN = :TRN LIMIT 1", $payeeQueryData); 

	$paymentMethod = 'UFP'; 
	$paymentVal = $_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->pe_code ? $_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->pe_code : "" ;
	include('PAGES/GET-TRANSACTIONS/index.php'); 
	$totalFee = 0;
 	 
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
						 		<th style='text-align: right;'>Amount</th>
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
								if($_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->er_type == 'PERCENTAGE'){
									$entityRate   = $_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->entity_rate_amount;
									$ipgFee	  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->ipg_fee;
 						
 									//MDR
 									//$totalAddedAmount = (($totalFee + $ipgFee) * $entityRate) + $ipgFee;   
									 
									$amountWithIPG = $totalFee + $ipgFee;  
									$totalAddedAmount = $amountWithIPG / (1.00 - $entityRate);   
									$totalAddedAmount = $totalAddedAmount - $amountWithIPG;

									$totalFee	= $amountWithIPG + $totalAddedAmount; 
									$RateDisplay = $totalAddedAmount;
  									$totalDisplayFee = $totalFee;
									
							 	}elseif($_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->er_type == 'FEE'){
							 		$entityRate  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->entity_rate_amount;
									$ipg_fee	  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->ipg_fee;
								 	$totalFee	+= $entityRate + $ipg_fee;
								 	$RateDisplay = $entityRate + $ipg_fee;
  									$totalDisplayFee = $totalFee;
							 	}else{
							 		$entityRate  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->entity_rate_amount;
									$ipg_fee	  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->ipg_fee;
								 	$totalFee	+= $entityRate + $ipg_fee;
								 	$RateDisplay = $entityRate + $ipg_fee;							 		
							 	}   
						 	?>
						 </table> 				
					</div>

					<div class="optionsDiv2 customRow">  
						<div class="col-3 p0">
							<img src="<?php echo IMG; ?>PAYMENT_HOME_PAGE/DEFAULT/UP-FRONT_PAYMENT.png"  style="height: 60px; max-width:100%;">
						</div>
						<div class="col-9 p0">
						 <table class="table paymentTable"> 
						 	<tr>
						 		<td><strong>Convenience Fee:</strong></td>
						 		<td style='text-align: right;'><strong><?php echo number_format($RateDisplay,2); ?></strong></td>
						 	</tr>
						 	<tr>
						 		<td><strong>Total:</strong></td>
						 		<td style='text-align: right;'><strong><?php echo number_format($totalDisplayFee,2); ?></strong></td>
						 		<input type="hidden" value="<?php echo $totalDisplayFee; ?>" id="checkAmount">
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
 
				<div class="col-md-12 upFrontOptions mt20">
					<div class="optionsDiv customRow"> 
						<label>Up-front Payment Options</label> 
						<div class="col-md-6 customRow">
							<div class="col-12 mt20">
								<strong>Non-Bank</strong>
							</div>
							<?php   
								$nonBankQuery = $dbPublic->getResults("SELECT * FROM tbl_ref_entity_images WHERE PE_CODE IN(".$nonBanks.")");
								foreach($nonBankQuery AS $nonBank){
						  			$checked = $_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->pe_code == $nonBank->PE_CODE ? "checked" : "" ;
								echo "
									<div class='col-6 col-lg-4 mt10'>
										<div class='optionsLink optionPE'> 
											<input type='radio' value='".$nonBank->PE_CODE."' name='paymentOptionsValue' class='paymentOptionsCheck' ".$checked.">
											<img src='".IMG.$nonBank->FILE_NAME_LOCATION.$nonBank->FILE_NAME."' style='max-width:100%; height:80px;'> 
										</div>
									</div> 
								";
								} 
							?>
							 
						</div>
						<div class="col-md-6 customRow">
							<div class="col-12 mt20">
								<strong>Bank</strong>
							</div>
							<?php  
								$bankQuery = $dbPublic->getResults("SELECT * FROM tbl_ref_entity_images WHERE PE_CODE IN(".$banks.")");
								foreach($bankQuery AS $bank){  
									$checked = $_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->pe_code == $bank->PE_CODE ? "checked" : "" ;

								echo "
									<div class='col-6 col-lg-4 mt10'>
										<div class='optionsLink optionPE'> 
											<input type='radio' value='".$bank->PE_CODE."' name='paymentOptionsValue' class='paymentOptionsCheck' ".$checked.">
											<img src='".IMG.$bank->FILE_NAME_LOCATION.$bank->FILE_NAME."' style='max-width:100%; height:80px;'> 
										</div>
									</div> 
								";
								} 
							?>
						</div>
					</div>

					<div class="paymentFeesDiv">
						<p> 
							Subject to Php <strong><?php echo number_format($RateDisplay,2); ?> Convenience fee</strong>
						</p>
					</div>

				</div> 
				<div class="col-12 text-center">
					<div class="gap"></div>
					<input type="checkbox" name="paymentCheckBox" id="paymentCheckBox" 
					data-toggle="popover" data-placement="bottom" data-original-title="" data-content="Please check this box if you want to proceed.">
					<span class="paymentCheckBoxText">I here by certify that the aboved information is true and correct.</span>
					<span class="checkbox-error-message"></span> 
					<br>
					<button class="btnProceedPayment" id="PaymentButton">Proceed to Payment </button>
				</div>
			</div>
		</div>
	<div class="gap gap-lg"></div>
</div>	


<!--SECUREPAYAPM--> 
<form action='https://demo2.2c2p.com/2C2PFrontEnd/SecurePayment/PaymentAuth.aspx' method='POST' name='paymentRequestForm'> 
	<input type="hidden" name="paymentRequest" value="">
</form>

<!--
Payment Options Channel
<form id="myform" method="post" action="">
	<input type="hidden" name="version" value="">
	<input type="hidden" name="merchant_id" value=""/>
	<input type="hidden" name="currency" value=""/>
	<input type="hidden" name="result_url_1" value=""/>
	<input type="hidden" name="enable_store_card" value=""/>
	<input type="hidden" name="request_3ds" value=""/>
	<input type="hidden" name="payment_option" value=""/>
	<input type="hidden" name="hash_value" value=""/>
	<input type="hidden" name="payment_description" value="" />
	<input type="hidden" name="order_id" value=""/>
	<input type="hidden" name="amount" value=""/> 
</form>  
-->

<?php  

	include('PAGES/REQUIRES/footer.php'); 
	include('PAGES/REQUIRES/resources.php'); 
 
	$amountValue =  strval(str_replace(",","",str_replace(".","",number_format($totalFee,2)))); 					 
?>

<script>
	$(".optionPE").click(function(){
		preloader(1);
	    var radioValue = $("input[name='paymentOptionsValue']:checked"). val();  
	   	
	   	$.ajax({
            url: BASE_URL+"PAGES/GET-TRANSACTIONS/index.php",
            type:"POST",
            data: { 
            	pmCode		: "<?php echo $paymentMethod; ?>", 
            	peCode		: radioValue,
            	merchantCode: "<?php echo $_SESSION['IPG_PUBLIC']['MERCHANT']->merchant_code; ?>",
            	secureParam : "<?php echo $_SESSION['IPG_PUBLIC']['SECUREPARAM']; ?>",
            },
            cache: false,			
            success: function(result) {     
				location.reload()
			},   
        }); 

	});

	$("#PaymentButton").click(function (){ 

		if($('#paymentCheckBox').is(':checked') && $('#emailAddress').val().length != 0 && $('#contactNo').val().length != 0 && $('#checkAmount').val().length != 0){   

			preloader(1);

			var emailAddress = $('#emailAddress').val();
			var contactNo = $('#contactNo').val();
			$.ajax({
				url: BASE_URL+"PAGES/PHP/EMAIL-TEMPLATE/session.php",
    			type:"POST",
				data: { 
					emailAddress: emailAddress,
					contactNo: contactNo, 
					paymentEntity: "<?php echo $_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->pe_code; ?>",  
					partnerCode: "<?php echo $_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->partner_code; ?>",  
				},
				cache: false, 						
				success: function(results) {   
					var results = JSON.parse(results); 
				
					if(results.REDIRECT == '2C2P'){
						$.ajax({
							url: results.REDIRECT_URL,
			    			type:"POST",
							data: { 
								partnerCode: "<?php echo $_SESSION['IPG_PUBLIC']['TRANSACTIONS_UFP']->partner_code; ?>",
								paymentOption: "1",
								emailAddress: emailAddress,
								contactNo: contactNo.replace(/[^0-9]/, ''),
								TRN: "<?php echo $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->trn; ?>",
								amount: "<?php echo sprintf("%012d", $amountValue); ?>",
								transactionDesc: "<?php echo $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->transactions[0]->transaction_payment_for; ?>",
							},
							cache: false, 	
							success: function(paymentDetails) {     
							
								//---Payment Options Channel---//
								/*
									var paymentDetails = JSON.parse(paymentDetails); 
									$('#myform').attr('action', paymentDetails.payment_url);
									$("input[name=version]").val(paymentDetails.version); 
									$("input[name=merchant_id]").val(paymentDetails.merchant_id); 
									$("input[name=currency]").val(paymentDetails.currency); 
									$("input[name=result_url_1]").val(paymentDetails.result_url_1); 
									$("input[name=enable_store_card]").val(paymentDetails.enable_store_card); 
									$("input[name=request_3ds]").val(paymentDetails.request_3ds); 
									$("input[name=payment_option]").val(paymentDetails.payment_option); 
									$("input[name=hash_value]").val(paymentDetails.hash_value); 
									$("input[name=payment_description]").val(paymentDetails.payment_description); 
									$("input[name=order_id]").val(paymentDetails.order_id); 
									$("input[name=amount]").val(paymentDetails.amount); 
									document.forms.myform.submit();
								*/

								//---SECUREPAYAPM---//
								$("input[name=paymentRequest]").val(paymentDetails); 
								document.paymentRequestForm.submit();	//submit form to 2c2p PGW
							},
							error: function() {
								return false; 
							}     
						});
					}else{
						window.location = results.REDIRECT_URL;
					} 
				},
				error: function() {
					return false; 
				}     
			});
		}else{ 

			if($('#paymentCheckBox').is(':checked')){ 
				$('#paymentCheckBox').popover('hide');  
				if($('#checkAmount').val().length == 0){
					alert('Please select a Payment Option.');
				}
			}else{
				$('#paymentCheckBox').popover('show');  
			}

			if($('#emailAddress').val().length != 0){ 
				$('#emailAddress').popover('hide');  
			}else{
				$('#emailAddress').popover('show');   
			}
			
			if($('#contactNo').val().length != 0){ 
				$('#contactNo').popover('hide'); 
			}else{
				$('#contactNo').popover('show');  
			}
		}
	});
</script>