<?php 
	include('REQUIRES/header.php'); 
	$refType = $dbLGUDavao->getResults("SELECT FEE_TYPE_ID, FEE_TYPE FROM tbl_ref_fee_type WHERE DELETED != '1'");
?>
<div class="bodyBackground"></div>
<div class="bodyBackgroundMask"></div>
<div class="row minHeight100">
	<div class="col-md-5 col-lg-4 divLeft">
		<div class="full-width mb20 mt20 text-center">
			<img src="<?php echo IMG."LGU-Davao-logo.png"; ?>" class="LGULogo">
		</div>
		<div class="mobileTitle d-block d-md-none">
			<label>LGU Davao Payment Portal</label>
		</div>
		<form method="post" id="frmRequestPayment" action="">
			<div class="input-group">
				<div class="input-group-prepend">
					<span class="input-group-text">Type of Fee</span>
				</div>
				<select class="form-control" name="TYPEFEE" required>
			  		<option value="">SELECT</option>
				  	<?php 
				  		foreach($refType AS $type){
							echo "<option value='".$type->FEE_TYPE."'>".$type->FEE_TYPE."</option>";
				  		}
				 	?> 
				</select>	
			</div>
			<label for="TYPEFEE" class="error" generated="true"></label>

			<div class="input-group mt15">
			 	<div class="input-group-prepend">
					<span class="input-group-text">OPTN</span>
			  	</div>
			  	<input type="text" class="form-control" name="OPTN">
			</div>
			<label for="OPTN" class="error" generated="true"></label>
			
			<div class="input-group mt15">
			  	<div class="input-group-prepend">
					<span class="input-group-text">Payor Name</span>
			  	</div>
			  	<input type="text" class="form-control" name="NAME">
			</div>
			<label for="NAME" class="error" generated="true"></label>

			<div class="input-group mt15">
			  	<div class="input-group-prepend">
					<span class="input-group-text">Email Address</span>
			  	</div>
			  	<input type="text" class="form-control" name="EMAIL">
			</div>
			<label for="EMAIL" class="error" generated="true"></label>

			<div class="input-group mt15">
			  	<div class="input-group-prepend">
					<span class="input-group-text">Mobile Number</span>
			  	</div>
			  	<input type="text" class="form-control" name="MOBILENUMBER">
			</div>
			<label for="MOBILENUMBER" class="error" generated="true"></label>

			<div class="form-group mt15 mb0">
				<button type="submit" class="btn btnSubmit full-width" id="btnSubmit">Submit</button>
			</div>	
		</form> 
 
		<div class="mobileLogoDiv d-block d-md-none">
			<ul>
				<li>
					<img src="<?php echo IMG."dbp-logo.png"; ?>" class="full-width">
				</li>
				<li>
					<img src="<?php echo IMG."dci-logo.png"; ?>" class="full-width">
				</li>
			</ul>
		</div> 
		<div class="full-width text-center">
			<div class="copyright">
				<small><?php echo FOOTER; ?></small>
			</div>
		</div>
	</div>
	<div class="col-md-7 col-lg-8 d-none d-md-block">
		<div class="titleDiv">
			<label class="label1">Welcome to</label>
			<label class="label2">LGU Davao Payment Portal</label> 
		</div>

		<div class="logoDiv">
			<ul>
				<li>
					<img src="<?php echo IMG."dbp-logo.png"; ?>" class="full-width">
				</li>
				<li>
					<img src="<?php echo IMG."dci-logo.png"; ?>" class="full-width">
				</li>
			</ul>
		</div>
	</div>
</div>  


<!-- Modal -->
<div class="modal fade" id="payLoadModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
	      	<div class="modal-header">
	        	<h5 class="modal-title" id="optn">OPTN: <strong>M23</strong></h5>
	       		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          		<span aria-hidden="true">&times;</span>
	        	</button>
	      	</div>
	      	
	      	<div class="modal-body">  
				<div class="input-group">
				 	<div class="input-group-prepend">
						<span class="input-group-text font-black">Type of Fee:</span>
				  	</div> 
				  	<input type="text" value="<?php echo $_SESSION[SESSION_NAME]['PAYMENT_DETAILS']->TYPE; ?>" id="typeFee" readonly class="form-control  font-black readonlyCustom">
				</div>
				
				<div class="input-group mt20">
				 	<div class="input-group-prepend">
						<span class="input-group-text font-black">Due Date:</span>
				  	</div> 
				  	<input type="text" value="2019-08-15" id="dueDate" readonly class="form-control  font-black readonlyCustom">
				</div>

				<div class="input-group mt20">
				 	<div class="input-group-prepend">
						<span class="input-group-text font-black">Amount:</span>
				  	</div> 
				  	<input type="text" value="<?php echo $_SESSION[SESSION_NAME]['PAYMENT_DETAILS']->AMOUNT; ?>" id="amount" readonly class="form-control  font-black readonlyCustom">
				</div>
  			
	      	</div>
	      	
	      	<div class="modal-footer"> 
				<form id="frmIPG" method="post">
					<input type="hidden" class="form-control" name="name" value="">
					<input type="hidden" class="form-control" name="email" value="">
					<input type="hidden" class="form-control" name="contact" value=""> 
					<input type="hidden" class="form-control" name="optn" value=""> 
        			
        			<input type="hidden" name="transaction[0][sub_merchant_code]" value="<?php echo IPG_SUB_MERCHANT_CODE; ?>">
        			<input type="hidden" name="transaction[0][transaction_payment_for]" id="paymentFor" value="">
					<input type="hidden" name="transaction[0][transaction_amount]" id="transAmount" value="">

	        		<button type="submit" class="btn btn-secondary btn-block" id="btnProceed">Proceed to Payment</button>
				</form>
	      	</div>
	    </div>
	</div>
</div>


<!-- Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
	      	<div class="modal-header">
	        	<h5 class="modal-title">Something Went Wrong</h5> 
	      	</div>
	      	
	      	<div class="modal-body">  
	      		<p></p>
	      	</div>
	      	
	      	<div class="modal-footer">
	        	<a href="<?php echo BASE_URL; ?>" class="btn btn-secondary">OK</a>
	      	</div>
	    </div>
	</div>
</div>

<?php 
	if($transactionStatus == 'success' OR $transactionStatus == 'failed'){
?>
	<!-- Modal -->
	<div class="modal fade" id="transactionStatusModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
		      	<div class="modal-header">
		        	<h5 class="modal-title"><?php echo $transactionLabel; ?></h5> 
		      	</div>
		      	
		      	<div class="modal-body text-center">  
		      		<?php echo $transactionIcon; ?>
		      		<p class="mb0"><?php echo $transactionMessage; ?></p>
		      	</div>
		      	
		      	<div class="modal-footer">
		        	<a href="<?php echo BASE_URL; ?>" class="btn btn-secondary">Back to Home</a>
		      	</div>
		    </div>
		</div>
	</div>		
<?php 
	}else{
		//None
	}

	include('REQUIRES/footer.php');
	include('REQUIRES/resources.php');
?>
<script> 
	$(document).ready(function(){ 
		$('#frmRequestPayment').validate({
			rules: {   
			    TYPEFEE: { 
			    	required: true,
			    },  
			    OPTN: { 
			    	required: true,
			    },  
			    NAME: { 
			    	required: true,
			    },  
			    EMAIL: { 
			    	required: true,
			    	email:true
			    },  
			    MOBILENUMBER: { 
			    	required: true,
			    	digits: true,
			    	maxlength: 15,
			    },  
			},
			messages: {  
			}, 
			submitHandler: function(form) {    
				preloader(1);
				var fd = new FormData(document.getElementById("frmRequestPayment")); 
				$.ajax({
					url: BASE_URL+"PHP/payment-page/requestPaymentDetails.php",
					type:"POST",
					data: fd, 
					cache: false, 
		         	processData: false, // tell jQuery not to process the data
		     		contentType: false, // tell jQuery not to set contentType
					success: function(results) {     
						var results = JSON.parse(results); 
						preloader(0); 
						if(results['STATUS'] == '1'){
							$("#optn strong").text(results['OPTN']);
							$("#typeFee").val(results['TYPE']);
							$("#dueDate").val(results['DUEDATE']);
							$("#amount").val(results['AMOUNT']);

							$("input[name=name]").val(results['NAME']);
							$("input[name=email]").val(results['EMAIL']);
							$("input[name=contact]").val(results['NUMBER']);
							$("input[name=optn]").val(results['OPTN']);
							$("#paymentFor").val(results['PAYMENT_FOR']);
							$("#transAmount").val(results['TRANSACTION_AMOUNT']);

							$('#payLoadModal').modal({
							    backdrop: 'static',
							    keyboard: false
							});		 
						}else{
							$("#errorModal .modal-body p").html('<strong>Message:</strong> '+results['MESSAGE']);
							$('#errorModal').modal({
							    backdrop: 'static',
							    keyboard: false
							});	 
						}
					},
					error: function() {
						return false; 
					}
				}); 
				
			} 
		});

		$('#frmIPG').submit( function (){ 
			preloader(1);
			var fd = new FormData(document.getElementById("frmIPG"));   
			$('#payLoadModal').modal('toggle');
			$.ajax({
				url: BASE_URL+"PHP/payment-page/getIPGLink.php",
				type:"POST",
				data: fd, 
				cache: false, 
	         	processData: false, // tell jQuery not to process the data
	     		contentType: false, // tell jQuery not to set contentType
				success: function(result) {    
		 			if(result['status_code'] == '200'){  
		 				location.href = result['redirection_link'];
		 			}else{ 
						//console.log(result);
		 				preloader(0);
						var text = '';
						var arrLength = result.length - 1;  
		 				for (i = 0; i <= arrLength; i++) { 
					  		text += "<li>"+result[i]['error_description'] + "</li>";
						}
						$("#errorModal .modal-body p").html("<ul class='ulError'>"+text+"</ul>");
						$('#errorModal').modal({
						    backdrop: 'static',
						    keyboard: false
						});	 
		 			}
				},
				error: function() {
					return false; 
				}
			}); 
			return false; 
		});
	}); 
	
<?php 
	if($transactionStatus == 'success' OR $transactionStatus == 'failed'){
		echo "
			$('#transactionStatusModal').modal({
		    	backdrop: 'static',
		    	keyboard: false
			});";
	}
?>	 
</script>
