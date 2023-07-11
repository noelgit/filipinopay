<?php  

  if($firstPage != 'transaction'){
    $getMerchantCode = $_SESSION['IPG_PUBLIC']['MERCHANT']->merchant_code;
    $getSecureParam  = $_SESSION['IPG_PUBLIC']['SECUREPARAM'];
    $encryption_key = $getMerchantCode;
    $Cryptor = new Cryptor($encryption_key);
    $TRN = $Cryptor->decrypt($getSecureParam);   

    //CHeck if transaction number is already finish
    $transactionQueryData = array();
    $transactionQueryData['TRN'] = $TRN;
    $transactionQueryData['MERCHANT_CODE'] = $getMerchantCode;
    $checkDatabase = $dbEnterprise->getRow("SELECT * FROM tbl_transactions_hdr WHERE TRN = :TRN AND MERCHANT_CODE = :MERCHANT_CODE AND (TRANS_STATUS = '3' OR TRANS_STATUS = '4')", $transactionQueryData);
    
    if($checkDatabase){
      header('Location: '.BASE_URL.'?secureparam='.$getSecureParam.'&merchantcode='.$getMerchantCode.''); 
    }  
  }
?>
<div class="header">
  <div class="container custom_container">
    <div class="row">
      <div class="col-lg-2 col-md-3 text-center">
        <a class="navbar-brand" href="#">
          <img src="<?php echo IMG; ?>TERMS_AND_CONDITIONS/DEFAULT/FILPAY.png" alt="logo" class="logoHeader">
        </a>   
      </div> 
      <div class="col-lg-8 col-md-9 text-center">
        <h3 class="mt20 mb20"><?php echo $_SESSION['IPG_PUBLIC']['MERCHANT']->merchant_name; ?></h3>
      </div>
    </div>
  </div>
</div>

<?php 
  if($firstPage != 'transaction'){
?>
<ul class="navigationLink">
  <?php
    if($secondPage == ''){

    }else{
  ?>
  <li>
      <a href="<?php echo BASE_URL; ?>PAYMENT-HOME-PAGE">
        <i class="fa fa-home"></i> 
        <span>HOME</span>
      </a>
  </li>
  <?php 
    }
  ?>
  <li>
    <a href="#">
      <i class="fa fa-question-circle"></i>
      <span>FAQs</span>
    </a>
  </li>
  
  <li>
    <a href="#">
      <i class="fa fa-info-circle"></i>
      <span>ABOUT US</span>
    </a>
  </li>
</ul>

<?php } ?>