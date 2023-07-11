<?php   

	include('PAGES/EMAIL-TEMPLATE/index.php');
	include('PAGES/REQUIRES/header.php');  
 
?>
	<div class="div_content"> 
		<div class="gap gap-lg"></div>
		<div class="container custom_container">  
			<div class="col-lg-8 col-md-12 offset-lg-2" style="text-align: center;border-radius: 10px;border: 1px solid #dedede;">  
				<div class="gap gap-lg"></div>
				<h4><?php echo $label; ?></h4>
				<label><?php echo $_GET['PAYMENT_STATUS'] == '1' ? $emailLabel : '' ; ?></label>
				<div class="gap gap-lg"></div>
			</div> 
		</div> 
		<div class="gap gap-lg"></div>
	</div>


<?php 
	unset($_SESSION['IPG_PUBLIC']);
	include('PAGES/REQUIRES/footer.php'); 
	include('PAGES/REQUIRES/resources.php');  

?>

<script>
	$(window).ready(function(){  
		function disable_f5(e)
	{
		if ((e.which || e.keyCode) == 116){
			e.preventDefault();
		}
	}

	$(document).ready(function(){
	    $(document).bind("keydown", disable_f5);    
	});

        window.history.pushState(null, "", window.location.href);        
        window.onpopstate = function() {
            window.history.pushState(null, "", window.location.href);
        };
		
		setTimeout(function () {
	       window.location.href = "<?php echo $redirectLink; ?>?TRN=<?php echo $TRN ?>&STATUS=<?php echo $transactionStatus ?>";  
	    }, 3000);
	});
	
</script>