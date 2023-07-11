<?php
	include('../../config.php');
 	error_reporting(0);
 
?>

<!DOCTYPE html> 
<html lang="en">
	<head>      
		<title>POST RETRIGGER</title>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge"> 

		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<meta name="format-detection" content="telephone=no">
		<meta name="robots" content="noindex, nofollow"> 
		<link rel="shortcut icon" href="../../favicon.ico" type="image/x-icon" />    
		<link href="<?php echo CSS; ?>preloader.css" rel="stylesheet">  	
	
	</head>
	<body>
		<div id="preloader">
			<div> 
	 			<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
				<label>Please wait...</label>
			</div>
		</div>
		<!-- <div class="bodyBackground"></div> -->
		<div class="bodyBackgroundMask"></div>
		<div class="row minHeight100">
			<?php include('sections/form-post-payment.php') ?>
		</div> 
	</body>

<?php 
	include('../../REQUIRES/resources.php');
?>
<script>	

var BASE_URL = window.location.origin;

	$(document).ready(function(){
		verifyPayment();
	});
 
    
	function verifyPayment(){
		$('#frmVerifyPayment').validate({
			rules: {
			    TYPEFEE: { 
			    	required: true,
			    },  
			    OPTN: { 
			    	required: true,
			    },  
			    AMOUNT: { 
			    	required: true,
			    },  
			    TRN: { 
			    	required: true,
			    },  
			    TRANSACTION_DATE: { 
			    	required: true,
			    },  			   
			},
			messages: {  
			}, 
			submitHandler: function(form) {
				preloader(1);
				var fd = new FormData(document.getElementById("frmVerifyPayment"));
				$.ajax({
					url: BASE_URL+"/dbp/PHP/retrigger-page/requestPaymentDetailsRetrigger.php",
					type:"POST",
					data: fd,
					cache: false, 
		         	processData: false, // tell jQuery not to process the data
		     		contentType: false, // tell jQuery not to set contentType
					success: function(response) { 	
						preloader(0); 				
						if(response['STATUS'] == '1'){
							console.log("VERIFY SUCCESS: "+ JSON.stringify(response));

							//displayData
							$("#verificationModal .modal-body p").html('<strong>Message:</strong> '+response['MESSAGE']);
							$("#verificationModal .modal-body input[id=trn]").val(response['TRN']);
							$("#verificationModal .modal-body input[id=typeFee]").val(response['PAYMENT_FOR']);
							$("#verificationModal .modal-body input[id=amount]").val(response['AMOUNT']);
							$("#verificationModal .modal-body input[id=dueDate]").val(response['DUEDATE']);


							//formData
							$("#frmRetrigger input[name=TYPEFEE]").val(response['TYPE']);
							$("#frmRetrigger input[name=OPTN]").val(response['OPTN']);
							$("#frmRetrigger input[name=AMOUNT]").val(response['AMOUNT']);
							$("#frmRetrigger input[name=TRN]").val(response['TRN']);
							$("#frmRetrigger input[name=DUEDATE]").val(response['DUEDATE']);							
							$('#verificationModal').modal({
							    backdrop: 'static',
							    keyboard: false
							});								
								
						}
						else{
							console.log("VERIFY UNSUCCESSFUL: "+  JSON.stringify(response));
							$("#errorModal .modal-body p").html('<strong>Message:</strong> '+response['MESSAGE']);
							$("#errorModal .modal-body input[id=trn]").val(response['TRN']);
							$("#errorModal .modal-body input[id=typeFee]").val(response['PAYMENT_FOR']);
							$("#errorModal .modal-body input[id=amount]").val(response['AMOUNT']);
							$("#errorModal .modal-body input[id=dueDate]").val(response['DUEDATE']);

							$('#errorModal').modal({
							    backdrop: 'static',
							    keyboard: false
							});	 							
						}
					},
					error: function() {
						console.log("ERROR: "+ response);
						/* return false;  */
					}
				}); 
				
			} 
		});			
	}

	function postPayment(){
		$('#frmRetrigger').validate({
			rules: {   
			},
			messages: {  
			}, 
			submitHandler: function(form) {  
				preloader(1);
				var fd = new FormData(document.getElementById("frmRetrigger"));
				$.ajax({
					url: BASE_URL+"/dbp/PHP/retrigger-page/postPaymentDetailsRetrigger.php",
					type:"POST",
					data: fd,
					cache: false, 
		         	processData: false, // tell jQuery not to process the data
		     		contentType: false, // tell jQuery not to set contentType
					success: function(response) { 	
						preloader(0); 				
						if(response['STATUS'] == '1'){
							console.log("POSTING SUCCESS: "+ JSON.stringify(response));		
							if(!alert('Success!')){
								window.location.reload();
							}
						}
						else{
							console.log("POSTING UNSUCCESSFUL: "+  JSON.stringify(response));
							$("#verificationModal .modal-body p").html('<strong>Message:</strong> '+response['MESSAGE']);
							$('#verificationModal').modal({
							    backdrop: 'static',
							    keyboard: false
							});	 								
						}
					},
					error: function() {
						console.log("ERROR: "+ response);
						/* return false;  */
					}
				}); 
				
			} 
		});			
	}

</script>		
</html>

