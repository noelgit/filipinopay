<?php  
	// include('pages/requires/header.php');
 	// if(!isset($_SESSION['IPG_DEBIT_CREDIT'])){
 	// 	die();
 	// }
?>
 	<div class="container mt20" style="max-width: 500px">
 		<div class="row"> 
 			<div class="col-12 text-center"> 
	 			<label class="labelHeader"><i class="far fa-credit-card"></i> <span>Credit/Debit Cards</span></label> 
 			</div>
 			<div class="col-12 text-left mt20 mb20">
 				<label class="labelSecondary"><span>Accepted Cards:</span> <i class="fab fa-cc-visa"></i> <i class="fab fa-cc-mastercard"></i></label>
 			</div>
 			<div class="col-12">
 				<form id="2c2p-payment-form"  method="POST">
 					<input type="hidden" name="paymentFor" value="<?php echo $_SESSION['IPG_DEBIT_CREDIT']['TRANSACTION']['payment_for']; ?>">
 					<input type="hidden" name="TRN" value="<?php echo $_SESSION['IPG_DEBIT_CREDIT']['TRANSACTION']['TRN']; ?>">
 					<input type="hidden" name="amount" value="<?php echo sprintf("%012d", str_replace('.', '', $_SESSION['IPG_DEBIT_CREDIT']['TRANSACTION']['amount'])); ?>">  
 					<input type="hidden" name="email" value="<?php echo $_SESSION['IPG_DEBIT_CREDIT']['TRANSACTION']['email']; ?>">  
 					<div class="form-group">
	 					<input type="text" data-encrypt="cardnumber" maxlength="16" class="form-control" placeholder="Card Number (Required)" required>
	 				</div>
	 				<div class="form-group row">
	 					<div class="pr5 col-4">
	 						<label class="labelSecondary"><span>Expiry Date:</span></label>
	 					</div>
	 					<div class="pr5 pl5 col-4">
	 						<select data-encrypt="month" class="form-control" required>
	 							<option value="">Month</option>
	 							<?php  
	 								for ($i=1; $i <= 12; $i++) { 
	 									echo "<option value='".sprintf("%02d", $i)."'>".sprintf("%02d", $i)."</option>";
	 								} 
	 							?>
	 						</select>
	 					</div>
	 					<div class="pl5 col-4">
	 						<select data-encrypt="year" maxlength="4" class="form-control" required>
	 							<option value="">Year</option>
	 							<?php 
	 								$yearToday = DATE('Y'); 
	 								$yearEnd   = $yearToday+35;
	 								for ($yearToday; $yearToday <= $yearEnd ; $yearToday++) { 
	 									echo "<option value='".$yearToday."'>".$yearToday."</option>";
	 								}

	 							?>
	 						</select> 
	 					</div>
	 				</div>
 					<div class="form-group row">
 						<div class="pr5 col-8">
		 					<input type="password" data-encrypt="cvv" maxlength="4" class="form-control" placeholder="CVV/CVV2" required>
		 				</div>
 						<div class="pl5 col-4">
 							<label class="labelThird">
			 					<i class="far fa-question-circle"></i>
			 					<a href="#" data-toggle="modal" data-target="#cvvModal">What is This?</a>
			 				</label>
		 				</div>
	 				</div>

 					<div class="form-group">
	 					<input type="text" name="cardName" class="form-control" placeholder="Card Name (Required)" required="">
	 				</div>

 					<!--<div class="form-group">
	 					<input type="text" name="bank" class="form-control" placeholder="Issuing Bank Name (Required)">
	 				</div>-->

	 				<div class="form-group row">
	 					<div class="pr5 col-6">
		 					<button type="submit" class="btn btn-primary full-width">Continue Payment</button>
		 				</div>
	 					<div class="pl5 col-6">
	 						<button id="cancelPayment" class="btn btn-secondary full-width">Cancel Payment</button>
	 					</div>
	 				</div>
 				</form>
 			</div>
 		</div> 
 	</div>


	<!-- The Modal -->
	<div class="modal" id="cvvModal">
		<div class="modal-dialog">
			<div class="modal-content"> 

				<!-- Modal body -->
				<div class="modal-body text-center">
			        <button type="button" class="close" data-dismiss="modal"><i class="far fa-times-circle"></i></button>
					<p class="mt30 text-justify">
						The Card Verification Code, or CVC*, is an extra code printed on your debit or credit card. With American Express the CVC appears as a separate 4-digit code printed on the front of your card. With all other cards (Visa, MasterCard, bank cards etc.) it is the final three digits of the number printed on the signature strip on the reverse of your card. As the CVC is not embossed (like the card number), the CVC is not printed on any receipts, hence it is not likely to be known by anyone other than the actual card owner. 
					</p>
					<p class="text-justify">
						We ask you to fill out the CVC here to verify beyond any doubt that you actually hold the card you are using for this transaction, and to avoid anyone other than you from shopping with your card number. All information you submit is transferred over secure SSL connections. 
					</p>
					<p class="text-justify">
						* The name of this code differs per card company. You may also know it as the Card Verification Value (CVV), the Card Security Code or the Personal Security Code. All names cover the same type of information.
					</p>
					<img src="<?php echo IMG.'CVC.png'; ?>" class="img">
				</div>
 

			</div>
		</div>
	</div>
<?php 
	include('pages/requires/footer.php');  
	include('pages/requires/resources.php');  
?>

<!--Importing 2c2p JSLibrary-->
<script type="text/javascript" src="https://demo2.2c2p.com/2C2PFrontEnd/SecurePayment/api/my2c2p.1.6.9.min.js"></script>
<script type="text/javascript">
    My2c2p.onSubmitForm("2c2p-payment-form", function(errCode,errDesc){
        if(errCode!=0){
            alert(errDesc+" ("+errCode+")");
        }
    }); 
	
	$("#2c2p-payment-form").submit(function(e){
        e.preventDefault();
    });
 
	var goBack = window.open('','IPGPublicWindow');
	$('#2c2p-payment-form').submit(function(){
		//rules: {  },
		//messages: { },
		//submitHandler: function(form) {    
			preloader(1);	
			var fd = new FormData(document.getElementById("2c2p-payment-form"));
			$.ajax({
				url: BASE_URL+"pages/php/payment/payment_non3d.php",
				type:"POST",
				data: fd, 
				cache: false, 
	         	processData: false, // tell jQuery not to process the data
	     		contentType: false, // tell jQuery not to set contentType
				success: function(results) {    

					if(results['status_code'] == '201'){ 
					 	//alert(results['message']+'.');    
						goBack.location.href = results['redirection_link'];
            			goBack.focus(); 
            			window.top.close();
					 }else{
					 	//alert(results['message']+'.');  
					 	goBack.location.href = results['redirection_link'];
            			goBack.focus();
            			window.top.close();
					 }
				},
				error: function() {
					preloader(0);
					return false; 
				}
			});
		//}
	}); 
	 
  	$('#cancelPayment').click(function(){
	 	goBack.location.href = "<?php echo $_SESSION['IPG_DEBIT_CREDIT']['TRANSACTION']['response_url'].'?PAYMENT_STATUS=0'; ?>";
		goBack.focus();
		window.top.close();		
 	});
</script>