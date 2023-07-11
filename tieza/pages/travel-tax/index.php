<?php 
	include('pages/requires/header.php');	
	if($dataComplete == '0'){
		header('Location: '.BASE_URL.'profile/');	
	}   
	
	$pointExit 	 = $dbTieza->getResults("SELECT trpe.`EXIT_POINT_ID`, trpe.`EXIT_POINT_DESC` FROM tbl_ref_point_exit AS trpe WHERE  trpe.`DELETED` = '0' ORDER BY trpe.`EXIT_POINT_DESC`");
	$passage 	 = $dbTieza->getResults("SELECT trp.`PASSAGE_ID`, trp.`PASSAGE_DESC`, trp.`AMOUNT` FROM tbl_ref_passage AS trp WHERE trp.`DELETED` = '0' ORDER BY trp.`PASSAGE_DESC` ASC");
	$destination = $dbTieza->getResults("SELECT trcd.`DESTINATION_ID`, trcd.`COUNTRY_NAME`, trcd.`REGION` FROM tbl_ref_country_destination AS trcd WHERE trcd.`DELETED` = '0' ORDER BY trcd.`COUNTRY_NAME` ASC");
	$emailAddress = $userData->EMAIL_ADDRESS; 
?>

<div class="container">
	<div class="row">
		<div class="col-12 text-center">
			<div class="gap"></div>
			<h3>Online Travel Tax Payment System</h3>
			<div class="gap"></div>
		</div>

		<!--Breadcrumb-->
		<div class="col-12 text-left">
			<ul class="breadCrumbs">
				<li>
					<a href="<?php echo BASE_URL; ?>home" class="link">Home</a>
				</li>
				<li>
					<label>Full Travel Tax</label>
				</li> 
			</ul> 
		</div>
		<!--Profile-->
		<div class="col-12">
			<form method="post" id="frmTravelTax" class="row m0"> 
				<input type="hidden" name="emailAddress" value="<?php echo $emailAddress; ?>">
				<input type="hidden" name="subscriberID" value="<?php echo $subscriberID; ?>"> 
				<div class="col-lg-7 pl0 profileDivider">
					<div class="form-group row text-left"> 
						<div class="col-12">
							<label>Ticket or Confirmation Number*</label> 
						</div> 
						<div class="col-lg-8 col-md-5 col-7"> 
							<input type="text" class="form-control" name="ticketNumber">
						</div>
					</div>

 					<div class="form-group mb0 row text-left"> 
						<div class="col-6 pr5">
							<span>Passage*</span>
							<select name="passage" class="form-control" id="passage">
								<?php 
									foreach($passage AS $data){
										echo "<option value='".$data->PASSAGE_ID."' amount='".$data->AMOUNT."'>".$data->PASSAGE_DESC."</option>";
									}
								?>
							</select> 
						</div>
						<input type="hidden" name="amount" id="amount">
						<div class="col-6 pl5"> 
							<div class="form-group">
								<div class="input-group">  
									<span class="full-width">Date of Travel*</span>
							        <input type="text" id="dateTravel" class="form-control dateTravel readonly" data-date-start-date="0d"  name="dateTravel" readonly>
							        <div class="input-group-append">
							        	<a href="#" class="input-group-text"><i class="fa fa-calendar-alt"></i></a>
							        </div>
							    </div> 
						        <label for="dateTravel" class="error d-block" generated="true"></label>     
							</div>
						</div>
					</div>		

					<div class="form-group row text-left">
						<div class="col-6 pr5">
							<span>Point of Exit*</span>
							<select name="pointExit" class="form-control">
								<?php 
									foreach($pointExit AS $data){
										echo "<option value='".$data->EXIT_POINT_ID."'>".$data->EXIT_POINT_DESC."</option>";
									}
								?> 
							</select> 
						</div>
						<div class="col-6 pl5"> 
							<span>Destination*</span>
							<select name="destination" class="form-control">								
								<?php 
									foreach($destination AS $data){
										echo "<option value='".$data->DESTINATION_ID."'>".$data->COUNTRY_NAME."</option>";
									}
								?> 
							</select>  
						</div>						
					</div>	

					<div class="form-group row text-left"> 
						<div class="col-12">
							<label>Destination City/Town/Province*</label> 
						</div> 
						<div class="col-lg-8 col-md-5 col-7"> 
							<input type="text" class="form-control" name="destinationCity">
						</div>
					</div> 
				</div>
				<div class="col-lg-5">  
					<div class="travelTaxDiv mb20">  
						<span>TAX FEE</span>
						<h4><small class="mr20">PHP</small> <strong id="amountText">0.00</strong></h4>
					</div> 
 
					<label class="mb0"><strong>GENTLE REMINDER ON YOUR FLIGHT:</strong></label>
					<p><small>Please print two(2) copies of the Acknowledgement Receipt (AR) confirming "Successful Payment Transaction". Ensure that both the Requestor Name and Ticket/Confirmation Number on the AR matches the information on your passport and ticket.</small></p>
					<p><small>Please bring BOTH copies on your scheduled date of travel. They shall be collected at the check-in counter at the airport or port of exit.</small></p>
				
					<div class="text-center">
						<div class="travelTaxCheckbox">
							<input type="checkbox" name="paymentCheckBox" id="paymentCheckBox" 
							data-toggle="popover" data-placement="bottom" data-original-title="" data-content="Please check this box if you want to proceed.">
							<span><small class="paymentCheckBoxText">I UNDERSTAND</small></span>
						</div>
						<button class="btn btnLogin" id="paymentBtn">PROCEED TO PAYMENT</button>
					</div>
				</div> 
			</form>
		</div>
	</div>	
</div>

<div class="modal fade" id="modalError" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Something went wrong</h5> 
			</div>
			<div class="modal-body">
				<p>Sorry, something went wrong. Please try again later.</p>
			</div>
			<div class="modal-footer"> 
				<a href="<?php echo BASE_URL; ?>travel-tax" class="btnSignupError btn btn-primary">Ok</a>
			</div>
		</div>
	</div>
</div> 


<div class="modal fade" id="modalSuccess" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"></h5> 
			</div>
			<div class="modal-body">
				<p></p>
			</div>
			<div class="modal-footer">
				<a href="<?php echo BASE_URL; ?>home/" type="button" class="btn btn-primary">Ok</a> 
			</div>
		</div>
	</div>
</div>

<?php 
	include('pages/requires/footer.php');
	include('pages/requires/resources.php');
?>
 
<script> 

	$(".dateTravel").datepicker({ 
        autoclose: true, 
        todayHighlight: true
	});

	var amount = $('option:selected', '#passage').attr('amount');
	$('#amountText').text(amount).digits();
	$('#amount').val(amount);

	$('#passage').on('change',function () {
		var amount = $('option:selected', this).attr('amount');
		$('#amountText').text(amount).digits();
		$('#amount').val(amount);
	});

    $("#frmTravelTax").submit(function(e){
        e.preventDefault();
    });	

	$("#paymentBtn").click(function (){  
		if($('#paymentCheckBox').is(':checked')){   
			var amount = $('option:selected', '#passage').attr('amount');
			$('#paymentCheckBox').popover('hide'); 
			$('#frmTravelTax').validate({
				rules: { 
					ticketNumber: { 
						required  : true,   
     		 			maxlength: 20 
					},
					passage: {
						required  : true, 
					}, 
					dateTravel: {
						required  : true, 
					},  
					pointExit: {
						required  : true, 
					},  
					destination: {
						required  : true, 
					},  
					destinationCity: {
						required  : true, 
     		 			maxlength: 150 
					},   
				},
				messages: {  
				},
				submitHandler: function(form) {   
					preloader(1);  
					var fd = new FormData(document.getElementById("frmTravelTax"));
					$.ajax({
						url: BASE_URL+"pages/php/travel-tax/transaction.php",
						type:"POST",
						data: fd, 
						cache: false, 
			         	processData: false, // tell jQuery not to process the data
			     		contentType: false, // tell jQuery not to set contentType
						success: function(transactionResults) {    
								var transactionResults = JSON.parse(transactionResults); 
							 
								if(transactionResults.STATUS == 'SUCCESS'){     

									var formData = new FormData(); 
									formData.append('name', transactionResults.NAME);
									formData.append('email', transactionResults.EMAIL);
									formData.append('contact', transactionResults.CONTACT);
									formData.append('transactions', JSON.stringify(transactionResults.TRANSACTION)); 
									$.ajax({
										url: BASE_URL+"pages/php/travel-tax/getIPGLinkAPI.php",
										type:"POST",
										dataType: 'json',
										data: formData, 
										cache: false,  
			         					processData: false, // tell jQuery not to process the data
							     		contentType: false, // tell jQuery not to set contentType
										success: function(IPGResult) {    
								 			if(IPGResult['status_code'] == '200'){ 

								 				var formUpdateData = new FormData(); 
												formUpdateData.append('TYPE', "UPDATE"); 
								 				formUpdateData.append('IPG_TRN', IPGResult['reference_id']);
								 				formUpdateData.append('TIEZA_TRN', transactionResults.TIEZA_TRN);
								 				formUpdateData.append('TRANS_ID', transactionResults.TRANS_ID);
												formUpdateData.append('EMAIL', transactionResults.EMAIL);
												$.ajax({
													url: BASE_URL+"pages/php/travel-tax/updateTransaction.php",
													type:"POST",
													dataType: 'json',
													data: formUpdateData, 
													cache: false,  
						         					processData: false, // tell jQuery not to process the data
										     		contentType: false, // tell jQuery not to set contentType
													success: function(updateResult) {    
											 			if(updateResult.STATUS == 'SUCCESS'){  
								 							location.href = IPGResult['redirection_link']; 
											 			} 
											 		},
													error: function() {
														preloader(0); 
														return false; 
													}
												});  

								 			}else{ 
												preloader(0); 
								 				alert(JSON.stringify(IPGResult)); 
								 			}
										},
										error: function() {
											preloader(0); 
											return false; 
										}
									}); 

								}else{     
									preloader(0); 
									$('#modalError').modal({
									    backdrop: 'static',
									    keyboard: false
									});
								} 
						},
						error: function() {
							preloader(0); 
							return false; 
						}
					}); 
				}
			}); 
		}else{  
			$('#paymentCheckBox').popover('show'); 
		}
	});
	<?php  
		if(isset($_GET['TRN']) AND isset($_GET['STATUS'])){ 
			if($_GET['STATUS'] == '3' OR $_GET['STATUS'] == '2'){
	?>
				$('#modalSuccess .modal-title').text('Successful Payment!'); 
				$('#modalSuccess .modal-body p').text('Thank you for paying your Travel Tax.'); 
		<?php 
			}else{
		?>
				$('#modalSuccess .modal-title').text('Transaction Failed'); 
				$('#modalSuccess .modal-body p').text('Something went wrong. Please try again.'); 
		<?php
			}
		?>	 
			$('#modalSuccess').modal({
			    backdrop: 'static',
			    keyboard: false
			});

			var formUpdateData = new FormData(); 
			formUpdateData.append('TYPE',"INSERT"); 
			formUpdateData.append('IPG_TRN', "<?php echo $_GET['TRN']; ?>"); 
			formUpdateData.append('STATUS',  "<?php echo $_GET['STATUS']; ?>"); 
			$.ajax({
				url: BASE_URL+"pages/php/travel-tax/updateTransaction.php",
				type:"POST",
				dataType: 'json',
				data: formUpdateData, 
				cache: false,  
					processData: false, // tell jQuery not to process the data
	     		contentType: false, // tell jQuery not to set contentType
				success: function(updateResult) {    
		 			if(updateResult.STATUS == 'SUCCESS'){  
		 			} 
		 		},
				error: function() {
					return false; 
				}
			});   
	<?php 
		}
	?>
</script>