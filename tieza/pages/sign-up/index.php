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
				<form role="form" id="frmSignUp" method="post" action="">
					<div class="form-group text-left">
						<span><a href="<?php echo BASE_URL; ?>login" class="link">Login</a> here, if you already have an account.</span>
					</div>
					
					<div class="form-group text-left"> 
						<input type="text" class="form-control" placeholder="First name" name="firstName"> 
					</div>
 
					<div class="form-group text-left"> 
						<div class="input-group"> 
					        <div class="input-group-append">
					        	<a  class="input-group-text">
					        		<input type="checkbox" id="middleNameCheckbox">
					        	</a>
					        </div>
					        <input type="text" id="middleName" class="form-control" placeholder="Middle Name" name="middleName" required>
					    </div>   
					    <label for="middleName" class="error" generated="true"></label> 
					    <small >(Check if not applicable)</small>
					</div>

					<div class="form-group text-left">
						<input type="text" class="form-control" placeholder="Last name" name="lastName">
					</div>

					<div class="form-group text-left">
						<input type="text" class="form-control" placeholder="Email Address" name="email">
					</div>
					
					<div class="form-group text-left"> 
						<div class="input-group"> 
					        <input type="text" id="birthday" class="form-control" placeholder="Birthday" name="birthday" data-date-end-date="0d" readonly> 
					        <div class="input-group-append">
					        	<a href="#" class="input-group-text"><i class="far fa-calendar-alt"></i></a>
					        </div>
					    </div>   
					    <label for="birthday" class="error d-block" generated="true"></label> 
					</div>

					<div class="form-group text-left">
						<div class="input-group"> 
					        <input type="password" id="password" class="form-control" placeholder="Password" name="password">
					        <div class="input-group-append showPassword">
					        	<a href="#" class="input-group-text"><i class="fa fa-eye"></i></a>
					        </div>
					    </div>   
					    <label for="password" class="error d-block" generated="true"></label>
					</div>

					<div class="form-group text-left">
						<input type="hidden" id="secret_code" value="<?php echo SECRET_KEY; ?>" />
						<div class="g-recaptcha" id="g-recaptcha-response" name="g-recaptcha-response" data-sitekey="<?php echo SITE_KEY; ?>"></div>
					</div>

					<div class="form-group text-left">
						<input type="submit" class="form-control btn btnLogin" value="Sign Up">
						<p class="login-output-message mt10"></p> 
					</div>  

				</form>  
			</div>
		</div> 
	</div>
</div> 

<div class="modal fade" id="modalSuccess" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Sign Up Successful</h5> 
			</div>
			<div class="modal-body">
				<p>Click continue to login to your account.</p>
			</div>
			<div class="modal-footer">
				<a href="<?php echo BASE_URL; ?>login" class="btnSignupSuccess btn btn-primary" >Back to Login</button> 
				<a href="<?php echo BASE_URL; ?>sign-up" class="btnSignupError btn btn-secondary">Ok</a>
			</div>
		</div>
	</div>
</div> 
<div class="gap gap-md"></div>
<?php 
	//include('pages/requires/footer.php');
	include('pages/requires/resources.php');
?>
<script>  
	$(document).ready(function(){  
		$("#birthday").datepicker({ 
	        autoclose: true, 
	        todayHighlight: true
		});

		$('#middleNameCheckbox').click(function (){
			if($(this).prop("checked") == true){
				$("#middleName").prop('disabled', true);
				$("#middleName").prop('required', false);
			}else{
				$("#middleName").prop('disabled', false);
				$("#middleName").prop('required', true);
			}
		});

		$('#frmSignUp').validate({
			rules: {  
			    firstName: { 
			    	required: true,
			    }, 
			    lastName: { 
			    	required: true,
			    }, 
			    birthday: {
			    	required: true,
			    },
			    email: { 
			    	required: true,
			    	email: true
			    }, 
			    password: { 
			    	required: true,
			    }, 
			},
			messages: { 
	 
			},
			submitHandler: function(form) {   
				var g_recaptcha_response = String($("#g-recaptcha-response").val());
				var secret_code = $("#secret_code").val();
				$.post(BASE_URL+"pages/php/check-captcha.php",{
					secret: secret_code,
					response: g_recaptcha_response
				}, function(result){	
					if(result == "true"){
						preloader(1);
						var fd = new FormData(document.getElementById("frmSignUp"));
						$.ajax({
							url: BASE_URL+"pages/php/sign-up/sign-up.php",
							type:"POST",
							data: fd, 
							cache: false, 
				         	processData: false, // tell jQuery not to process the data
				     		contentType: false, // tell jQuery not to set contentType
							success: function(results) {    
								var results = JSON.parse(results); 
								preloader(0); 
								if(results.STATUS == 'success'){   
									$('.btnSignupError').hide();
									$('#modalSuccess .modal-title').text(results.TITLE); 
									$('#modalSuccess .modal-body p').text(results.MESSAGE); 
									$('#modalSuccess').modal({
									    backdrop: 'static',
									    keyboard: false
									});
								}else{   
									$('.btnSignupSuccess').hide();
									$('#modalSuccess .modal-title').text(''+results.TITLE+''); 
									$('#modalSuccess .modal-body p').text(''+results.MESSAGE+''); 
									$('#modalSuccess').modal({
									    backdrop: 'static',
									    keyboard: false
									});
								} 
							},
							error: function() {
								return false; 
							}
						});
					}else{ 					
						alert("Captcha error. Please try again.");
					}
				});
			} 
		});
 

		$('.showPassword').on('mousedown', function() {
		    $('#password').prop('type', 'text');
		}).on('mouseup mouseleave', function() {
		    $('#password').prop('type', 'password');
		});
	}); 
</script>