<?php 
	include('../../../config.php');
	include('../../../lib/libraries.php');
	error_reporting(0); 
	$municipality = $dbTieza->getResults("SELECT * FROM tbl_ref_psgc_municipality WHERE PSGC_PROV_CODE = '".$_POST['PSGC_PROV_CODE']."' AND STATUS = '1' ORDER BY PSGC_MUNC_DESC ASC"); 
	print_r(json_encode($municipality));
 
?>