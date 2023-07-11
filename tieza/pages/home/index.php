<?php 
	include('pages/requires/header.php');	
	header('Location: '.BASE_URL.'travel-tax/');	
	//if($dataComplete == '0'){
		//header('Location: '.BASE_URL.'profile/');	
	//} 
?>

<div class="container">
	<div class="row">
		<div class="col-12 text-center">
			<div class="gap"></div>
			<h3>Online Travel Tax Payment System</h3>
			<div class="gap"></div>
		</div>

		<div class="col-12 text-center">
			<div class="col-lg-4 offset-lg-4">
				<div class="p50">
					<a href="<?php echo BASE_URL; ?>travel-tax">
						<img src="<?php echo IMG; ?>sample.png" alt="Icon" class="full-width">
						<h5 class="mt10">Full Travel Tax</h5>
					</a>
				</div>
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