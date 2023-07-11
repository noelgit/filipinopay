<?php 
	$verificationCode  = $custom->getVerificationCode($_GET['verificationCode'], "GET"); 

	$data = array();
	$data['PASSWORD'] = $verificationCode;
	$checkAccount = $dbTieza->getRow("SELECT COUNT(*) AS COUNT, SUBSCRIBERS_ID, EMAIL_ADDRESS, IS_PASSWORD_VERIFIED  FROM tbl_account_profile WHERE PASSWORD = :PASSWORD LIMIT 1", $data);
	if($checkAccount->COUNT >= 1){
		if($checkAccount->IS_PASSWORD_VERIFIED == '0'){
			$updateData = array();
			$updateData['IS_PASSWORD_VERIFIED'] = '1';
			$updateData['LAST_MODIFIED_DATE']	= $timeStamp;
			$updateData['LAST_MODIFIED_BY'] 	= $custom->upperCaseString($checkAccount->EMAIL_ADDRESS); 
			$verifyAccount = $dbTieza->update("tbl_account_profile",'SUBSCRIBERS_ID',$checkAccount->SUBSCRIBERS_ID, $updateData); 
			$class = 'divVerificationSuccess';
			$message = 'Your account successfully verified.';

			/****************************
				INSERT tbl_audit_trail
			*****************************/			 
			$auditArr = array();
			$auditArr['MODULE'] 	  	  = 'VERIFY ACCOUNT'; 
			$auditArr['TRN']			  = 'N/A';
			$auditArr['EVENT_TYPE']    	  = 'UPDATE';
			$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
			$auditArr['EVENT_REMARKS'] 	  = 'ACCOUNT SUCCESSFULLY VERIFIED';
			$auditArr['CREATED_DATE']	  = $timeStamp;
			$auditArr['CREATED_BY']		  = $custom->upperCaseString($checkAccount->EMAIL_ADDRESS);

			$insertAudit = $dbTieza->insert("tbl_audit_trail",$auditArr); 

		}else{
			$class = 'divVerificationError';
			$message = 'This account is already verified.';
		}
	}else{ 
		$class = 'divVerificationError';
		$message = 'No record was found.';
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
		</div> 
		<div class="<?php echo $class; ?> col-12 text-center">  
			 <h2><?php echo $message; ?></h2>
			<div class="text-right">
			 	<a href="<?php echo BASE_URL; ?>login" class="btn btnLogin">Back to login</a>
			</div>
		</div>
	</div>
</div> 
 

<?php 
	//include('pages/requires/footer.php');
	include('pages/requires/resources.php');
?> 