<?php 
	include('pages/requires/header.php');
	
	if($dataComplete == '0'){
		header('Location: '.BASE_URL.'profile/');	
	}   	

	//Birthday Format
	$birthday = date_create($userData->BIRTH_DATE);
	$birthdayFormat = date_format($birthday,"F d, Y");  
	$emailAddress = $userData->EMAIL_ADDRESS; 
 
	$mobileNumber	 = $userData->MOBILE_NO; 			  
	$streetNumber	 = $userData->UNIT_HOUSE;	
	$street  	 	 = $userData->STREET; 		 	 	 
	$zipCode 	  	 = $userData->ZIPCODE; 		
	$passportNumber  = $userData->PASSPORT_NO; 		 
	$passportOffice  = $userData->PASSPORT_ISSUE_OFFICE;


	$passportDueDate = $userData->PASSPORT_DUE_DATE;  
	$passportExpDate = $userData->PASSPORT_EXP_DATE;  

	//Passport Date Format
	$issuedDate = date_create($passportDueDate);
	$issuedDateFormat = date_format($issuedDate,"m/d/Y");  

	$expirationDate = date_create($passportExpDate);
	$expirationDateFormat = date_format($expirationDate,"m/d/Y"); 

	$passportImage   = IMG.'passport/'.$userData->SUBSCRIBERS_ID.'/'.$userData->PASSPORT_IMAGE_PATH;

	$countryCode  = $dbTieza->getResults("SELECT * FROM tbl_ref_country_codes AS trcc ORDER BY trcc.`COUNTRY_CODE` ASC");
	$barangay 	  = $dbTieza->getRow("SELECT PSGC_BRGY_DESC,PSGC_BRGY_CODE FROM tbl_ref_psgc_barangay WHERE PSGC_BRGY_CODE = '".$userData->BARANGAY."' LIMIT 1");
	$municipality = $dbTieza->getRow("SELECT PSGC_MUNC_DESC,PSGC_MUNC_CODE FROM tbl_ref_psgc_municipality WHERE PSGC_MUNC_CODE = '".$userData->MUNICIPALITY."' LIMIT 1");
	$province 	  = $dbTieza->getRow("SELECT PSGC_PROV_DESC,PSGC_PROV_CODE FROM tbl_ref_psgc_province WHERE PSGC_PROV_CODE = '".$userData->PROVINCE."' LIMIT 1"); 	
	$region 	  = $dbTieza->getResults("SELECT PSGC_REG_CODE, PSGC_REG_DESC FROM tbl_ref_psgc_region WHERE STATUS = '1'");
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
					<a href="<?php echo BASE_URL; ?>profile" class="link">Account Profile</a>
				</li> 
				<li>
					<label>Edit</label>
				</li>
			</ul>
		</div>

		<!--Profile-->
		<div class="col-12">
			<form method="post" id="frmProfile" class="row m0">
				<input type="hidden" name="updateType" value="update">
				<input type="hidden" name="subscriberID" value="<?php echo $userData->SUBSCRIBERS_ID; ?>">
				<input type="hidden" name="emailAddress" value="<?php echo $emailAddress; ?>">

				<div class="col-lg-7 pl0 profileDivider">
					<div class="form-group row text-left">
						<div class="col-md-6">
							<label>Hi <strong><?php echo $custom->upperCaseWords($fullName); ?></strong></label>
						</div>
						<div class="col-md-6">
							<small><strong>Birthday: </strong><?php echo $birthdayFormat; ?></small><br>
							<small><strong>Email: </strong><?php echo $emailAddress; ?></small>
						</div>
						<div class="col-12 mt10">
							<label>Contact Number*</label> 
						</div>
						<div class="col-lg-3 col-md-2 col-3 pr5">
							<select class="form-control" name="contactCode"> 
								<option value="">Select</option>
								<?php 
									foreach($countryCode as $data){
										if($data->ID == $userData->COUNTRY_CODE_ID){
											$selected = 'selected';
										}else{
											$selected = '';
										}
										echo "<option value='".$data->ID."' ".$selected.">(".$data->COUNTRY_CODE.") ".$data->DIALING_CODE."</option>";
									}
								?>
							</select>
						</div>
						<div class="col-lg-5 col-md-5 col-7 pl5"> 
							<input type="text" class="form-control" value="<?php echo $mobileNumber; ?>" placeholder="000 000 0000" name="contactNumber">
						</div>
					</div>

					<div class="form-group row text-left">
						<div class="col-12">
							<strong>Address</strong> 
						</div>
						<div class="col-4 pr5">
							<span>Street No.</span>
							<input type="text" class="form-control" value="<?php echo $streetNumber; ?>" name="strNo">
						</div>
						<div class="col-6 pl5">
							<span>Street Name</span>
							<input type="text" class="form-control" value="<?php echo $street; ?>" name="streetName">
						</div>
 
						<div class="col-6 pr5">
							<span>Region</span>
							<select class="form-control" id="region" value="<?php echo $streetNumber; ?>" name="region">
								<?php  
									echo "<option value=''>Select</option>";
									foreach ($region as $data) {
										if($data->PSGC_REG_CODE == $userData->REGION){
											$selected = 'selected';
										}else{
											$selected = '';
										}
										echo "<option value='".$data->PSGC_REG_CODE."' ".$selected.">".$data->PSGC_REG_DESC."</option>";
									}  
								?>
							</select>
						</div>

						<div class="col-6 pl5">
							<span>Province</span>
							<select class="form-control" id="province" name="province" readonly> 
								<?php echo "<option value='".$province->PSGC_PROV_CODE."'>".$province->PSGC_PROV_DESC."</option>"; ?>
							</select>
						</div>

						<div class="col-6 pr5">
							<span>Municipality</span>
							<select class="form-control" id="municipality" name="municipality" readonly> 
								<?php echo "<option value='".$municipality->PSGC_MUNC_CODE."'>".$municipality->PSGC_MUNC_DESC."</option>"; ?>
							</select>
						</div> 

						<div class="col-6 pl5">
							<span>Brgy.</span>
							<select class="form-control" id="barangay" name="barangay" readonly> 
								<?php echo "<option value='".$barangay->PSGC_BRGY_CODE."'>".$barangay->PSGC_BRGY_DESC."</option>"; ?>
							</select>
						</div>
						<div class="col-4 pr5">
							<span>Code</span>
							<input type="text" class="form-control" id="zipCode" value="<?php echo $zipCode; ?>" name="zipCode" readonly>
						</div>
					</div>

				</div>
				<div class="col-lg-5 row">
					<div class="col-md-6 pr5">
						<div class="form-group">
							<small>Passport Number*</small>
							<input type="text" class="form-control" value="<?php echo $passportNumber; ?>" name="passportNumber">
						</div>
						<div class="form-group">
							<div class="input-group">  
								<small class="full-width">Passport Issued Date*</small>
						        <input type="text" id="passportIssuedDate" class="form-control passportDate" value="<?php echo $issuedDateFormat; ?>"  name="passportIssuedDate"  data-date-end-date="0d">
						        <div class="input-group-append">
						        	<a href="#" class="input-group-text"><i class="fa fa-calendar-alt"></i></a>
						        </div>
						    </div>    
						    <label for="passportIssuedDate" class="error d-block" generated="true"></label>
						</div>
					</div>
					<div class="col-md-6 pl5">
						<div class="form-group">
							<small>Passport Issuing Office*</small>
							<input type="text" class="form-control" value="<?php echo $passportOffice; ?>" name="passportIssuingOffice">
						</div>
						<div class="form-group">
							<div class="input-group">  
								<small class="full-width">Passport Expiration Date*</small>
						        <input type="text" id="passportExpirationDate" class="form-control passportDate" value="<?php echo $expirationDateFormat; ?>" name="passportExpirationDate" data-date-start-date="0d">
						        <div class="input-group-append">
						        	<a href="#" class="input-group-text"><i class="fa fa-calendar-alt"></i></a>
						        </div>
						    </div>   
						    <label for="passportExpirationDate" class="error d-block" generated="true"></label>  
						</div>
					</div>
					<div class="col-12 mt10">
						<img src="<?php echo $passportImage; ?>" class="full-width" id="passportPicture" >  
						<input type="hidden" name="passportImageName" value="<?php echo $userData->PASSPORT_IMAGE_PATH; ?>"> 
						<input type="file" name="passportImg" class="full-width customfileInput" onchange="readURL(this);">  
						<small class="d-block">(Max size: 1mb)<br>(Allowed extensions: .png .jpeg .jpg)</small>
					</div>
					<div class="gap"></div> 
				</div>

				<div class="col-lg-12 text-center">
					<div class="gap gap-md"></div>
					<p class="m0"><a href="#"  data-toggle="modal" data-target="#PrivacyPolicyModal" class="link">Click Here</a> to read Our Privacy Policy</p>
					<input type="checkbox" name="updateCheckBox" id="updateCheckBox" 
					data-toggle="popover" data-placement="bottom" data-original-title="" data-content="Please check this box if you want to proceed.">
					<span class="updateCheckBoxText">I here by understand and agree with TIEZA OTTPS privacy policy.</span>
					<span class="checkbox-error-message"></span> 
					<br>
					<button class="btn btnUpdateProfile" id="updateButton">Save</button>
					<div class="gap gap-md"></div>
				</div>
			</form>
		</div>
	</div>
</div>
 
<div class="modal fade" id="PrivacyPolicyModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Our Privacy Policy</h5> 
			</div>
			<div class="modal-body privacyPolicyContent">
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			</div>
			<div class="modal-footer">
				<a href="#" type="button" class="btn btn-primary" data-dismiss="modal">Ok</a> 
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
				<a href="<?php echo BASE_URL; ?>profile" class="btnSignupError btn btn-secondary">Ok</a>
			</div>
		</div>
	</div>
</div> 
<?php 
	include('pages/requires/footer.php');
	include('pages/requires/resources.php');
?>

<script>
	function readURL(input) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader(); 
	        reader.onload = function (e) {
	            $('#passportPicture').attr('src', e.target.result);
	        } 
	        reader.readAsDataURL(input.files[0]);
	    }
	}

	$.validator.addMethod('filesize', function (value, element, param) {
	    return this.optional(element) || (element.files[0].size <= param)
	}, 'File size must be less than 1mb');
	
	$(".passportDate").datepicker({ 
        autoclose: true, 
        todayHighlight: true
	});

    $("#region").on('change',function () {
    	var REG_CODE = $(this).val(); 
    	if(REG_CODE == ''){
    		$("#province").attr('readonly', true);
            $('#province option:gt(0)').remove();
            $('#province').children('option:first').remove();

    		$("#municipality").attr('readonly', true);
            $('#municipality option:gt(0)').remove();
            $('#municipality').children('option:first').remove();

			$("#barangay").attr('readonly', true);
            $('#barangay option:gt(0)').remove();
            $('#barangay').children('option:first').remove();

            $('#zipCode').val('');
    	}else{ 
	    	$.ajax({
	            type: "POST",
	            url: BASE_URL+"pages/php/profile/changeProvince.php",  
	            cache: false, 
	            dataType:"json", 
	            data : {
	            	REG_CODE : REG_CODE, 
	            }, 
	          	beforeSend: function () { 
	            },
	            success: function (data) { 
	            	var $province_select = $("#province");
	            	$("#province").attr('readonly', false);
	                $('#province option:gt(0)').remove();
	                $('#province').children('option:first').remove();

            		$("#municipality").attr('readonly', true);
		            $('#municipality option:gt(0)').remove();
		            $('#municipality').children('option:first').remove();

					$("#barangay").attr('readonly', true);
		            $('#barangay option:gt(0)').remove();
		            $('#barangay').children('option:first').remove();

           			$('#zipCode').val('');

	        	 	$province_select.append($("<option value='' selected>Select Province</option>"));
	                $.each(data, function(key,value) { 
	                    $province_select.append($("<option value='"+value.PSGC_PROV_CODE+"'>"+value.PSGC_PROV_DESC+"</option>"));
	                });
	            }, 
	       });
	    }
    });	

    $("#province").on('change',function () {
      	var PSGC_PROV_CODE = $(this).val(); 
    	if(PSGC_PROV_CODE == ''){
    		$("#municipality").attr('readonly', true);
            $('#municipality option:gt(0)').remove();
            $('#municipality').children('option:first').remove();

			$("#barangay").attr('readonly', true);
            $('#barangay option:gt(0)').remove();
            $('#barangay').children('option:first').remove();

            $('#zipCode').val('');
    	}else{ 
	    	$.ajax({
	            type: "POST",
	            url: BASE_URL+"pages/php/profile/changeMunicipality.php",  
	            cache: false, 
	            dataType:"json", 
	            data : {
	            	PSGC_PROV_CODE : PSGC_PROV_CODE, 
	            }, 
	          	beforeSend: function () { 
	            },
	            success: function (data) { 
	            	var $municipality_select = $("#municipality");
	            	$("#municipality").attr('readonly', false);
	                $('#municipality option:gt(0)').remove();
	                $('#municipality').children('option:first').remove();

					$("#barangay").attr('readonly', true);
		            $('#barangay option:gt(0)').remove();
		            $('#barangay').children('option:first').remove();

           		 	$('#zipCode').val('');
	        	 	$municipality_select.append($("<option value='' selected>Select Municipality</option>"));
	                $.each(data, function(key,value) { 
	                    $municipality_select.append($("<option value='"+value.PSGC_MUNC_CODE+"'>"+value.PSGC_MUNC_DESC+"</option>"));
	                });
	            }, 
	       });  
	    } 
    });


    $("#municipality").on('change',function () {
      	var PSGC_MUNC_CODE = $(this).val(); 
    	if(PSGC_MUNC_CODE == ''){
    		$("#barangay").attr('readonly', true);
            $('#barangay option:gt(0)').remove();
            $('#barangay').children('option:first').remove();

            $('#zipCode').val('');
    	}else{ 
	    	$.ajax({
	            type: "POST",
	            url: BASE_URL+"pages/php/profile/changeBarangay.php",  
	            cache: false, 
	            dataType:"json", 
	            data : {
	            	PSGC_MUNC_CODE : PSGC_MUNC_CODE, 
	            }, 
	          	beforeSend: function () { 
	            },
	            success: function (data) { 
	            	var $barangay_select = $("#barangay");
	            	$("#barangay").attr('readonly', false);
	                $('#barangay option:gt(0)').remove();
	                $('#barangay').children('option:first').remove();

            		$('#zipCode').val('');
	        	 	$barangay_select.append($("<option value='' selected>Select Barangay</option>"));
	                $.each(data, function(key,value) { 
	                    $barangay_select.append($("<option value='"+value.PSGC_BRGY_CODE+"' postal-code='"+value.PSGC_ZIP_CODE+"'>"+value.PSGC_BRGY_DESC+"</option>"));
	                });
	            }, 
	       });  
       } 
    });

    $('#barangay').on('change',function () {
    	var PSGC_BRGY_CODE = $(this).val(); 
    	if(PSGC_BRGY_CODE == ''){
    		$("#zipCode").attr('readonly', true);
            $('#zipCode').val(''); 
    	}else{ 
	    	$.ajax({
	            type: "POST",
	            url: BASE_URL+"pages/php/profile/changePostalCode.php",  
	            cache: false, 
	            dataType:"json", 
	            data : {
	            	PSGC_BRGY_CODE : PSGC_BRGY_CODE, 
	            }, 
	          	beforeSend: function () { 
	            },
	            success: function (data) { 
	            	var $barangay_select = $("#zipCode"); 
	                $('#zipCode').val(data);   
	            }, 
	       });  
       } 
    });

    $("#frmProfile").submit(function(e){
        e.preventDefault();
    });

	$("#updateButton").click(function (){  
		if($('#updateCheckBox').is(':checked')){  
			$('#updateCheckBox').popover('hide'); 

			$('#frmProfile').validate({
				rules: { 
					contactCode: { 
						required  : true,    
					},
					contactNumber: {
						required  : true, 
					}, 
					strNo: {
						required  : true, 
					},  
					region: {
						required  : true, 
					}, 
					province: {
						required  : true, 
					}, 
					municipality: {
						required  : true, 
					}, 
					barangay: {
						required  : true, 
					}, 
					zipCode: {
						required  : true, 
					}, 
					passportNumber: {
						required  : true, 
					}, 
					passportIssuingOffice: {
						required  : true, 
					}, 
					passportIssuedDate: {
						required  : true, 
					}, 
					passportExpirationDate: {
						required  : true, 
					},  					
					passportImg: { 
				    	extension: "jpg,jpeg,png",
		                filesize: 1000000,
					}, 
				},
				messages: {  
				},
				submitHandler: function(form) {  
					var fd = new FormData(document.getElementById("frmProfile"));
					$.ajax({
						url: BASE_URL+"pages/php/profile/updateProfile.php",
						type:"POST",
						data: fd, 
						cache: false, 
			         	processData: false, // tell jQuery not to process the data
			     		contentType: false, // tell jQuery not to set contentType
						success: function(results) {    
								var results = JSON.parse(results); 
								preloader(0); 
								if(results.STATUS == 'success'){    
									$('#modalSuccess .modal-title').text(results.TITLE); 
									$('#modalSuccess .modal-body p').text(results.MESSAGE); 
									$('#modalSuccess').modal({
									    backdrop: 'static',
									    keyboard: false
									});
								}else{    
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
				}
			}); 


		}else{  
			$('#updateCheckBox').popover('show'); 
		}
	});
</script>