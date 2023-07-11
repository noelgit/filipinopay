<?php 
	include('../../../config.php');
	include('../../../lib/libraries.php');
	error_reporting(0); 
	$Barangay = $dbTieza->getResults("SELECT * FROM tbl_ref_psgc_barangay WHERE PSGC_MUNC_CODE = '".$_POST['PSGC_MUNC_CODE']."' AND STATUS = '1' ORDER BY PSGC_BRGY_DESC ASC"); 
	print_r(json_encode($Barangay));
 
?>