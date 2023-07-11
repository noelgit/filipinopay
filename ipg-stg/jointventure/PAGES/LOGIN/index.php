<?php  
	if($_SESSION['JV']['LOGGED'] == 'true'){
		header('Location: '.BASE_URL.'DASHBOARD/');	
	}   

	
?> 
<div class="gap gap-lg"></div>
<div class="container">
	<div class="row"> 
		<div class="col-md-6 col-lg-4 col-xs-12 col-md-offset-3 col-lg-offset-4">  
			<div class="divLogin">
				<h1><i class="fa fa-lock"></i></h1>
				<label>Please sign-in to start session</label>
				<div class="gap"></div>
				<form role="form" id="frmLogin" method="post" action=""> 
					<div class="form-group text-left">
						<input type="text" class="form-control" placeholder="Username" name="username" required>
					</div>
					<div class="form-group text-left">
						<div class="input-group"> 
					        <input type="password" id="password" class="form-control" placeholder="Password" name="password" required>
					        <div class="input-group-addon showPassword">
					        	<i class="fa fa-eye"></i>
					        </div>
					    </div>   
					</div>
					<div class="form-group text-left">
						<input type="submit" class="form-control btn btnLogin" value="Login">
						<p class="login-output-message mt10"></p>
					</div>
					<div class="form-group text-left">
						<a href="#" class="forgotPassword">Forgot Password?</a> 
					</div>
				</form>  
			</div>
		</div> 
	</div>
</div> 
<?php 
	include('PAGES/REQUIRES/resources.php');
?>
<script type="text/javascript">
	$('.showPassword').on('mousedown', function() {
	    $('#password').prop('type', 'text');
	}).on('mouseup mouseleave', function() {
	    $('#password').prop('type', 'password');
	});

	$(document).ready(function(){ 
		$('#frmLogin').validate({
			  rules: { 
				  username:{ 
					  required	:	true,   
				  },
				  password:{
					  required	:	true, 
				  } 
			  },
			  messages: { 
				  username:{
					  required	:	"Please enter your Username."
				  },
				  password:{
					  required	:	"Please enter your Password."
				  }
			  },
			  submitHandler: function(form) { 
				$(".login-output-message").text('Validating...').fadeIn(1000); 
				var formData = new FormData(form); 
				$.ajax({
					url: BASE_URL+"PAGES/PHP/LOGIN/loginAuth.php",
					type:"POST",
					data: formData,
					cache: false,
					contentType: false,
					processData: false, 
				}).done(function(results){
					var results = JSON.parse(results); 
					switch(results.result){
						case "error": 
							var error_message = results.error_message; 
							$(".login-output-message").fadeTo(200,0.1,function()  { 
								$(this).html(error_message).fadeTo(900,1,function() {  
								});	
							});
						break;
						case "success": 
							document.location='<?php echo BASE_URL; ?>DASHBOARD/'; 
						break;
					} 
					return false;
				}); 
				return false; //Not to post the form physically 
			}
		}); 
	});
</script>