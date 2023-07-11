<?php  
	include('pages/requires/header.php'); 
?>
 	<div class="container" style="width: 500px">
 		<div class="row">
 			<div class="col-3">
 				<img src="<?php echo IMG; ?>visa.png" class="full-width">
 				
 			</div>
 			<div class="col-9">
 				<p class="text-justify"><small>Your details will be sent to and processed by <strong>The VISA Internet Gateway Service</strong> and will not be disclosed to the merchant</small></p>
 			</div>
 			<div class="col-12">
 				<small>Internet Gateway Service</small>
 				<small class="float-right"><strong>TEST MODE</strong></small>
 			</div>
 			<div class="col-12">
 				<img src="<?php echo IMG; ?>banner.png" class="full-width mt10">
 			</div>
 			<div class="col-1"></div>
 			<div class="col-10 mt10 row">
	 			<div class="col-4">
	 				<img src="<?php echo IMG; ?>visalock.png" class="full-width">
	 			</div>
	 			<div class="col-8">
	 				<label style="font-size:10px;" class="text-justify">You have chosen VISA as your method of payment. Please enter your card details into the form below and click "pay" to complete your purchase</label>
	 			</div> 
 				
 				<div class="col-12">
 					<form class="text-right" id="formPaymentCredentials" method="post"> 

 						<!--Card Number-->
 						<div class="formDivLeft">
							<label style="font-size: 12px;">Card Number ::</label> 
						</div> 

 						<div class="formDivRight">
	                    	<input name="txt1" type="text" maxlength="4" id="txt1" class="formFieldNumber"> 
		                    <label>-</label>
	                    	<input name="txt2" type="text" maxlength="4" id="txt2" class="formFieldNumber"> 
		                    <label>-</label>
	                    	<input name="txt3" type="text" maxlength="4" id="txt3" class="formFieldNumber"> 
		                    <label>-</label>
	                    	<input name="txt4" type="text" maxlength="4" id="txt4" class="formFieldNumber" required> 
	                    </div> 
	                    <!--Expiry Date-->
 						<div class="formDivLeft">
							<label>Expiry Date ::</label>  
						</div>

 						<div class="formDivRight">
	                    	<input name="txtExpiry1" type="text" maxlength="4" id="txtExpiry1" class="formFieldNumber"> 
		                    <label>/</label>
	                    	<input name="txtExpiry2" type="text" maxlength="4" id="txtExpiry2" class="formFieldNumber" required> 
		                    <label>month/year</label>
		                </div> 

 						<!--Security Code-->
 						<div class="formDivLeft">
		                    <label>Security Code ::</label>
		                </div> 
 						<div class="formDivRight">
	                    	<input name="txtCode" type="text" maxlength="4" id="txtCode" class="formFieldNumber" required> 
	                    </div>

	                    <!--Notice-->
 						<div class="formDivLeft"></div>
 						<div class="formDivRight">
 							<label style="font-size:10px;">The 3 digits after the card number on the signature panel of your card</label>
 						</div>

 						<!--Total Amount-->
 						<div class="formDivLeft">
 							<label>Purchase Amount ::</label>
 						</div>

 						<div class="formDivRight">
 							<strong>PHP <?php echo $_SESSION['DUMMY']['TRANSACTION']['amount']; ?></strong>
 						</div>

 						<!--Pay-->
 						<div class="formDivLeft">
 							<img src="<?php echo IMG; ?>verifiedvisa.jpg" class="full-width">
 						</div>
 						<div class="formDivRight text-right">
 							<button type="submit" class="formButton" id="frmButton">
 								<img src="<?php echo IMG; ?>Pay.png">
 							</button>
 						</div>
 					</form>
 				</div>
 			</div>
 			<div class="col-1"></div>
 			<div class="col-12 text-center mt10">
 				<small style="font-size:10px;">I hereby authorise the debit to my MasterCard Account in favour of MasterCard Test 1</small>
 				<img src="<?php echo IMG; ?>mastercardfooter.png" class="full-width">
 			</div>
 		</div>
 	</div>


<?php 
	include('pages/requires/footer.php');  
	include('pages/requires/resources.php');  
?>

<script>
$("#frmButton").click(function (){  
	var goBack = window.open('','IPGPublicWindow');
	$('#formPaymentCredentials').validate({
		rules: {  },
		messages: { },
		submitHandler: function(form) {    
			var fd = new FormData(document.getElementById("formPaymentCredentials"));
			$.ajax({
				url: BASE_URL+"pages/php/payment/validateCredentials.php",
				type:"POST",
				data: fd, 
				cache: false, 
	         	processData: false, // tell jQuery not to process the data
	     		contentType: false, // tell jQuery not to set contentType
				success: function(results) {    			 
					if(results['status_code'] == '201'){ 
					 	alert(results['message']+'.');    
						goBack.location.href = results['redirection_link'];
            			goBack.focus(); 
            			window.top.close();
					 }else{
					 	alert(results['message']+'.');  
					 	goBack.location.href = results['redirection_link'];
            			goBack.focus();
            			window.top.close();
					 }
				},
				error: function() {
					return false; 
				}
			});
		}
	}); 
});
</script>