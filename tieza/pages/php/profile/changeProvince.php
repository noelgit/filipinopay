<?php 
	include('../../../config.php');
	include('../../../lib/libraries.php');
	error_reporting(0); 
	$Province = $dbTieza->getResults("SELECT * FROM tbl_ref_psgc_province WHERE PSGC_REG_CODE = '".$_POST['REG_CODE']."' ORDER BY PSGC_PROV_DESC ASC"); 
	print_r(json_encode($Province));
 
?>