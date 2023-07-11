<?php 
	include('../../../config.php');
	include('../../../LIBRARIES/libraries.php');
	session_start();
	error_reporting(E_ALL);
 
	$dateFrom 		 =	$_POST['dateFrom'] ? $_POST['dateFrom'] : false;  
	$dateTo 		 =	$_POST['dateTo'] ? $_POST['dateTo'] : false; 
	$referenceNumber =	$_POST['referenceNumber'] ? $_POST['referenceNumber'] : false; 
	$ORNumber 		 =	$_POST['ORNumber'] ? $_POST['ORNumber'] : false; 

	$additionalQuery = "";
	if($_POST['dateFrom'] AND $_POST['dateTo']){
		$additionalQuery .= "(tth.CREATED_DATE BETWEEN '".$dateFrom."' AND '".$dateTo."') AND ";
	}
	if($_POST['referenceNumber']){
		$additionalQuery .= "AND  tth.`MERCHANT_REF_NUM` = '".$referenceNumber."'";
	}
	if($_POST['ORNumber']){
		$additionalQuery .= "AND  te.`EOR` = '".$ORNumber."'";
	}


    $getGraphData = $dbEnterprise->query("SELECT tth.TRN,tth.CREATED_DATE, tth.MERCHANT_REF_NUM, te.EOR, tth.PM_CODE, tth.CONVENIENCE_FEE,
                (SELECT SUM(tt.TRANSACTION_AMOUNT) 
                    FROM tbl_transactions AS tt
                    WHERE tth.`TRN` = tt.`TRN`) TOTALAMOUNT
                
                FROM tbl_transactions_hdr AS tth

                LEFT JOIN tbl_eor AS te
                ON te.TRN = tth.TRN

                WHERE tth.MERCHANT_CODE = '".$_SESSION['MERCHANT']['MERCHANT_CODE']."' AND 
                
                ".$additionalQuery."
 
                tth.`TRANS_STATUS` = '3'");

 	
 	$dataTableArray = array();
 	$count = 0;
 	foreach($getGraphData AS $data){
        $totalAmount = $data->TOTALAMOUNT + $data->CONVENIENCE_FEE;
        $date = date_create($data->CREATED_DATE);
        $dateFormat = date_format($date,"M d, Y");   

        $dataTableArray[$count]['paymentDate'] 	= $dateFormat;
        $dataTableArray[$count]['merchantRefNum'] = $data->MERCHANT_REF_NUM;
        $dataTableArray[$count]['EOR'] 			= $data->EOR;
        $dataTableArray[$count]['pmCode'] 		= $data->PM_CODE;
        $dataTableArray[$count]['totalAmount'] 	= number_format($data->TOTALAMOUNT, 2);
        $dataTableArray[$count]['ConvenienceFee'] = number_format($data->CONVENIENCE_FEE,2);
        $dataTableArray[$count]['totalAmountNet'] = number_format($totalAmount,2); 
 		
 		$count++;
 	}
 	$info['data'] = array_values($dataTableArray);

    return print_r(json_encode($info));

?>