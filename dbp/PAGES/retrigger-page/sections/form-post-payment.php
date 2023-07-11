<?php 
	date_default_timezone_set("Asia/Manila");
	print_r($_SESSION);
?>

<div class="col-md-5 col-lg-4 divLeft">
	<div class="full-width mb20 mt20 text-center">
		<img src="<?php echo IMG."LGU-Davao-logo.png"; ?>" class="LGULogo">
	</div>				
	<div class="mobileTitle d-block d-md-none">
		<label>City Government of Davao Payment Portal</label>
	</div>

	<form method="post" id="frmVerifyPayment" action="">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text">Type of Fee</span>
			</div>
			<select class="form-control" name="TYPEFEE" required>
				<option value="">SELECT</option>
				<option value="BUSINESS">BUSINESS</option>
				<option value="MISCELLANEOUS">MISCELLANEOUS</option>
				<option value="REALPROPERTY">REALPROPERTY</option>
			</select>	
		</div>
		<label for="TYPEFEE" class="error" generated="true"></label>

		<div class="input-group mt15">
			<div class="input-group-prepend">
				<span class="input-group-text">OPTN</span>
			</div>
			<input type="text" class="form-control" name="OPTN" required>
		</div>
		<label for="OPTN" class="error" generated="true"></label>
		
		<div class="input-group mt15">
			<div class="input-group-prepend">
				<span class="input-group-text">Amount</span>
			</div>
			<input type="text" class="form-control" name="AMOUNT" required>
		</div>
		<label for="AMOUNT" class="error" generated="true"></label>

		<div class="input-group mt15">
			<div class="input-group-prepend">
				<span class="input-group-text">TRN</span>
			</div>
			<input type="text" class="form-control" name="TRN" required>
		</div>
		<label for="TRN" class="error" generated="true"></label>

<!-- 		<div class="input-group mt15">
			<div class="input-group-prepend">
				<span class="input-group-text">Transaction Date</span>
			</div>
			<input type="text" class="form-control" name="TRANSACTION_DATE" required>
		</div>
		<label for="TRANSACTION_DATE" class="error" generated="true"></label> -->

		<div class="input-group mt15">
			<div class="input-group-prepend">
				<span class="input-group-text">Transaction Date</span>
			</div>
			<input type="date" class="form-control" name="TRANSACTION_DATE" width="250"  required />
		</div>
		<label for="TRANSACTION_DATE" class="error" generated="true"></label>

		<div class="input-group mt15">
			<div class="input-group-prepend">
				<span class="input-group-text">Transaction Time</span>
			</div>
			<input type="time" step="1" class="form-control" name="TRANSACTION_TIME" width="250"  required />
		</div>
		<label for="TRANSACTION_TIME" class="error" generated="true"></label>


		<div class="form-group mt15 mb0">
			<button type="submit" class="btn btnSubmit full-width" id="btnSubmit">Submit</button>
		</div>
	</form> 
</div>

<!-- Modal -->
<div class="modal fade" id="verificationModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
	      	<div class="modal-header">
	        	<h5 class="modal-title">POST PAYMENT</h5> 
	      	</div>
	      	
	      	<div class="modal-body">  
	      		<p></p>
				<div class="input-group">
				 	<div class="input-group-prepend">
						<span class="input-group-text font-black">TRN:</span>
				  	</div> 
				  	<input type="text" value="<?php echo $_SESSION[SESSION_NAME]['PAYMENT_DETAILS']->TRN; ?>" id="trn"  readonly class="form-control  font-black readonlyCustom">
				</div>

				<div class="input-group">
				 	<div class="input-group-prepend">
						<span class="input-group-text font-black">Type | OPTN:</span>
				  	</div> 
				  	<input type="text" value="<?php echo $_SESSION[SESSION_NAME]['PAYMENT_DETAILS']->PAYMENT_FOR; ?>" id="typeFee" readonly class="form-control  font-black readonlyCustom">
				</div>	      			

				<div class="input-group">
				 	<div class="input-group-prepend">
						<span class="input-group-text font-black">Amount:</span>
				  	</div> 
				  	<input type="text" value="<?php echo $_SESSION[SESSION_NAME]['PAYMENT_DETAILS']->AMOUNT; ?>" id="amount" readonly class="form-control  font-black readonlyCustom">
				</div>		

				<div class="input-group">
				 	<div class="input-group-prepend">
						<span class="input-group-text font-black">Due Date:</span>
				  	</div> 
				  	<input type="text" value="<?php echo $_SESSION[SESSION_NAME]['PAYMENT_DETAILS']->DUEDATE; ?>" id="dueDate" readonly class="form-control  font-black readonlyCustom">
				</div>	

				<div class="input-group">
				 	<div class="input-group-prepend">
						<span class="input-group-text font-black">Transaction Date & Time:</span>
				  	</div> 
				  	<input type="text" value="<?php echo $_SESSION[SESSION_NAME]['PAYMENT_DETAILS']->TRANS_DATE_TIME; ?>" id="transDateTime" readonly class="form-control  font-black readonlyCustom">
				</div>	

	      	</div>
	      	
	      	<div class="modal-footer">
	      		<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

				<form id="frmRetrigger" method="post" action="">
					<input type="hidden" class="form-control" name="TYPEFEE" value="">
					<input type="hidden" class="form-control" name="OPTN" value="">
					<input type="hidden" class="form-control" name="AMOUNT" value=""> 
					<input type="hidden" class="form-control" name="TRN" value=""> 
					<input type="hidden" class="form-control" name="DUEDATE" value="">    
					<input type="hidden" class="form-control" name="TRANS_DATE_TIME" value="">        			

        			<button onclick="postPayment()" class="btn btn-primary">Retrigger</button>	
				</form>      		
	        	<!-- <a href="<?php echo BASE_URL; ?>" class="btn btn-secondary">OK</a> -->
	      	</div>
	    </div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
	      	<div class="modal-header">
	        	<h5 class="modal-title">POST PAYMENT</h5>
				<?php print_r($_SESSION); ?>	        	 
	      	</div>
	      	
	      	<div class="modal-body">  
	      		<p></p>

				<div class="input-group">
				 	<div class="input-group-prepend">
						<span class="input-group-text font-black">TRN:</span>
				  	</div> 
				  	<input type="text" value="<?php echo $_SESSION[SESSION_NAME]['PAYMENT_DETAILS']->TRN; ?>" id="trn" readonly class="form-control  font-black readonlyCustom">
				</div>

				<div class="input-group">
				 	<div class="input-group-prepend">
						<span class="input-group-text font-black">Type | OPTN:</span>
				  	</div> 
				  	<input type="text" value="<?php echo $_SESSION[SESSION_NAME]['PAYMENT_DETAILS']->PAYMENT_FOR; ?>" id="typeFee" readonly class="form-control  font-black readonlyCustom">
				</div>	      			

				<div class="input-group">
				 	<div class="input-group-prepend">
						<span class="input-group-text font-black">Amount:</span>
				  	</div> 
				  	<input type="text" value="<?php echo $_SESSION[SESSION_NAME]['PAYMENT_DETAILS']->AMOUNT; ?>" id="amount" readonly class="form-control  font-black readonlyCustom">
				</div>		

				<div class="input-group">
				 	<div class="input-group-prepend">
						<span class="input-group-text font-black">Due Date:</span>
				  	</div> 
				  	<input type="text" value="<?php echo $_SESSION[SESSION_NAME]['PAYMENT_DETAILS']->DUEDATE; ?>" id="dueDate" readonly class="form-control  font-black readonlyCustom">
				</div>													

	      	</div>
	      	
	      	<div class="modal-footer">
	      		<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>      		
	        	<!-- <a href="<?php echo BASE_URL; ?>" class="btn btn-secondary">OK</a> -->
	      	</div>
	    </div>
	</div>
</div>

