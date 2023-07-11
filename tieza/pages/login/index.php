<?php  
	if($_SESSION['TIEZA']['LOGGED'] == 'true'){
		header('Location: '.BASE_URL.'home/');	
	}   	
?> 
<div class="gap gap-md"></div>
<div class="container">
	<div class="row"> 
		<div class="col-xl-6 col-lg-8 col-md-8 offset-xl-3 offset-lg-2 offset-md-2 row">  
			<div class="divLoginHeader col-l2">
				<img src="<?php echo IMG; ?>logo.png">
				<h2>Welcome to the</h2>
				<h1>Online Travel Tax Payment System</h1>
				<p>The OTTPS can be used to process your travel tax payment online. For more information, please click <a href="#" class="link">here</a></p>
			</div>
			<div class="divLoginForm col-lg-10 offset-lg-1">   
				<form role="form" id="frmLogin" method="post"> 
					<div class="form-group text-left">
						<span><a href="<?php echo BASE_URL; ?>sign-up" class="link">Sign up</a> here, if you don't have an account.</span>
					</div>
					<div class="form-group text-left">
						<input type="text" class="form-control" placeholder="Email Address" name="email">
					</div>
					<div class="form-group text-left">
						<div class="input-group"> 
					        <input type="password" id="password" class="form-control" placeholder="Password" name="password">
					        <div class="input-group-append showPassword">
					        	<a href="#" class="input-group-text"> <i class="fa fa-eye"></i></a>
					        </div>
					    </div> 
					    <label for="password" class="error d-block" generated="true"></label>
					</div>

					<div class="form-group text-left">
						<button type="submit" class="form-control btn btnLogin">Login</button>
						<p class="login-output-message mt10"></p>
						<label>or Login with</label>
						<button class="form-control btn btn-danger mt10">
							<i class="fab fa-google-plus-g"></i> Google
						</button>
					</div>  

				</form>  
			</div>
		</div> 
	</div>
</div> 
 
<?php 
	//include('pages/requires/footer.php');
	include('pages/requires/resources.php');
?>
<script>
	$(document).ready(function(){
	    $("#frmLogin").submit(function(e){
	        e.preventDefault();
	    });

		$('.showPassword').on('mousedown', function() {
		    $('#password').prop('type', 'text');
		}).on('mouseup mouseleave', function() {
		    $('#password').prop('type', 'password');
		});

		$('#frmLogin').validate({
			rules: { 
				email: { 
					required  : true,   
					email  	  : true,
				},
				password: {
					required  : true, 
				} 
			},
			messages: {  
			},
			submitHandler: function(form) { 
				$(".login-output-message").text('Validating...').fadeIn(1000); 
				var fd = new FormData(document.getElementById("frmLogin"));
				$.ajax({
					url: BASE_URL+"pages/php/login/authentication.php",
					type:"POST",
					data: fd, 
					cache: false, 
		         	processData: false, // tell jQuery not to process the data
		     		contentType: false, // tell jQuery not to set contentType
					success: function(results) {    
						var results = JSON.parse(results); 
						preloader(0); 
						switch(results.STATUS){
							case "error": 
								var error_message = results.MESSAGE; 
								$(".login-output-message").fadeTo(200,0.1,function()  { 
									$(this).html(error_message).fadeTo(900,1,function() { }); 
								});
							break;
							case "success": 
								document.location='<?php echo BASE_URL; ?>home/'; 
							break;
						} 
						return false;
					},
					error: function() {
						return false; 
					}
				}); 
			}
		}); 


	}); 

</script>