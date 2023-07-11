<?php 
	include('../../../config.php');
	include('../../../lib/libraries.php');
	error_reporting(0); 
	$Barangay = $dbTieza->getRow("SELECT PSGC_ZIP_CODE FROM tbl_ref_psgc_barangay WHERE PSGC_BRGY_CODE = '".$_POST['PSGC_BRGY_CODE']."' AND STATUS = '1' LIMIT 1"); 
	echo $Barangay->PSGC_ZIP_CODE;
 
?>