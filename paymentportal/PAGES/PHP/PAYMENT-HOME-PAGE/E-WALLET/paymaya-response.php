<?php
	session_start(); 
	include("../../../../vendor/autoload.php");
	include("../../../../config.php"); 
	include("../../../../LIBRARIES/libraries.php");
	error_reporting(0);
 
	$timeStamp 	  = date('Y-m-d G:i:s');
	$status = $_GET['STATUS'];

	$responseData = array();
	if($status == '1'){ //SUCCESS
		$responseData['IPG_REF_NUM']  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->trn;
		$responseData['CHECK_OUT_ID'] = $_SESSION['IPG_PUBLIC']['PAYMAYA']['CHECKOUT_ID'];
		$responseData['REDIRECT_URL'] = $_SESSION['IPG_PUBLIC']['PAYMAYA']['CHECKOUT_URL'];
		$responseData['CREATED_DATE'] = $timeStamp; 
	}else{
		$responseData['IPG_REF_NUM']  = $_SESSION['IPG_PUBLIC']['TRANSACTIONS']->trn;
		$responseData['CHECK_OUT_ID'] = $_SESSION['IPG_PUBLIC']['PAYMAYA']['CHECKOUT_ID'];
		$responseData['REDIRECT_URL'] = $_SESSION['IPG_PUBLIC']['PAYMAYA']['CHECKOUT_URL'];
		$responseData['CREATED_DATE'] = $timeStamp; 
	}

	$insertData = $dbGateway->insert("tbl_paymaya_response_trans",$responseData);
?>
<script>
	var goBack = window.open('','IPGPublicWindow');
	
	<?php if($status == '1'){ ?>    
		goBack.location.href = "<?php echo $_SESSION['IPG_PUBLIC']['PAYMAYA']['IPG_URL']; ?>?PAYMENT_STATUS=1";
		goBack.focus(); 
		window.top.close();
	
	<?php }else{ ?>    
		goBack.location.href = "<?php echo $_SESSION['IPG_PUBLIC']['PAYMAYA']['IPG_URL']; ?>?PAYMENT_STATUS=0";
		goBack.focus();
		window.top.close();
	 <?php } ?>
</script>