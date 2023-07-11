<?php 
	include('../../../config.php');
	include('../../../LIBRARIES/libraries.php');
	session_start();
	error_reporting(0);

	$timeStamp 	  	= date('Y-m-d G:i:s');
    $tableQueryData = array();
    $tableQueryData['COMPANY_CODE_1'] = $_SESSION['JV']['COMPANY_CODE'];
    $tableQueryData['COMPANY_CODE_2'] = $_SESSION['JV']['COMPANY_CODE'];

    $dateFrom        =  $_POST['dateFrom2'] ? $_POST['dateFrom2'] : false;  
    $dateToFormat    =  $_POST['dateTo2'] ? date('Y-m-d', strtotime($_POST['dateTo2'] . '+1 day')) : false; 
    $dateTo          =  $_POST['dateTo2'] ? $_POST['dateTo2'] : false; 
    $referenceNumber =  $_POST['referenceNumber2'] ? $_POST['referenceNumber2'] : false; 
    $transactionNumber =  $_POST['transactionNumber2'] ? $_POST['transactionNumber2'] : false; 
    $ORNumber        =  $_POST['ORNumber2'] ? $_POST['ORNumber2'] : false;
    $merchantCode    =  $_POST['merchantCode2'] ? $_POST['merchantCode2'] : false;


    $additionalQuery = "";
    if($_POST['dateFrom2'] AND $_POST['dateTo2']){ 
        $tableQueryData['DATEFROM'] = $dateFrom;
        $tableQueryData['DATETOFORMAT'] = $dateToFormat;
        $additionalQuery .= "(tth.`CREATED_DATE` BETWEEN :DATEFROM AND :DATETOFORMAT) AND ";
    }
    if($_POST['referenceNumber2']){
        $tableQueryData['MERCHANT_REF_NUM'] = $referenceNumber;
        $additionalQuery .= "tth.`MERCHANT_REF_NUM` = :MERCHANT_REF_NUM AND "; 
    }
    if($_POST['transactionNumber2']){
        $tableQueryData['TRN'] = $transactionNumber;
        $additionalQuery .= "tth.`TRN` = :TRN AND";
    }
    if($_POST['ORNumber2']){
        $tableQueryData['EOR'] = $ORNumber;
        $additionalQuery .= "te.`EOR` = :EOR AND "; 
    } 
    if($_POST['merchantCode2']){
        $tableQueryData['MERCHANT_CODE'] = $merchantCode;
        $additionalQuery .= "tth.`MERCHANT_CODE` = :MERCHANT_CODE AND "; 
    } 
  
    $tableData = $dbEnterprise->getResults("SELECT tth.`TRN`,tth.`CREATED_DATE`, tth.`MERCHANT_REF_NUM`, tth.`PM_CODE`, tth.`CONVENIENCE_FEE` - tth.`IPG_FEE` AS MDR, tth.`IPG_FEE`, tth.`COMPANY_CODE_1_FEE`, tth.`COMPANY_CODE_2_FEE`,
        te.`EOR`, trpe.`DESCRIPTION`, tijf.`COMPANY_CODE_1`, tijf.`COMPANY_CODE_2`,  
        (SELECT SUM(tt.`TRANSACTION_AMOUNT`) FROM tbl_transactions AS tt WHERE tth.`TRN` = tt.`TRN`) TOTALAMOUNT 

        FROM tbl_transactions_hdr AS tth 

        LEFT JOIN tbl_eor AS te 
        ON te.`TRN` = tth.`TRN` 

        LEFT JOIN tbl_ref_payment_entity AS trpe 
        ON trpe.`PE_CODE` = tth.`PE_CODE` 

        LEFT JOIN tbl_ipg_jv_fees AS tijf
        ON tijf.`COMPANY_CODE_1` = :COMPANY_CODE_1 OR tijf.`COMPANY_CODE_2` = :COMPANY_CODE_2
                
        WHERE tth.`JV_CODE` = tijf.`JV_CODE` AND 
        ".$additionalQuery."
        tth.`TRANS_STATUS` = '3' 
        GROUP BY tth.`TRN`
        ORDER BY tth.CREATED_DATE DESC", $tableQueryData); 

	$excelArray = array();
	$count = 0;
	foreach($tableData AS $data){    
        $totalAmount = $data->TOTALAMOUNT + $data->MDR + $data->IPG_FEE;

        $date = date_create($data->CREATED_DATE);
        $dateFormat = date_format($date,"M d, Y");   
      
        $excelArray[$count]['Payment Date'] = $dateFormat;
        $excelArray[$count]['Transaction Reference Number'] = $data->TRN; 
        $excelArray[$count]['OR Number'] = "'".$data->EOR;
        $excelArray[$count]['Mode of Payment'] = $data->DESCRIPTION;
        $excelArray[$count]['Amount'] = number_format($data->TOTALAMOUNT, 2);
        $excelArray[$count]['Convenience Fee/MDR'] = number_format($data->MDR,2);
        $excelArray[$count]['GAI Profit'] = number_format($data->COMPANY_CODE_1_FEE,2);
        $excelArray[$count]['DCI Profit'] = number_format($data->COMPANY_CODE_2_FEE,2);
        $excelArray[$count]['Total Amount'] = $totalAmount;
        $count++;
	} 
 
 	/****************************
		INSERT tbl_audit_trail
	*****************************/	
	$EVENT_REMARKS = '';	
    switch($_POST["ExportType"])
    {
        case "export-to-excel" :	
        	$EVENT_REMARKS = 'EXPORT TRANSACTION TABLE TO EXCEL';
        break;

   		case "export-to-csv" :    	
        	$EVENT_REMARKS = 'EXPORT TRANSACTION TABLE TO CSV';        
   		break;

        default : 
        	$EVENT_REMARKS = 'SOMETHING WENT WRONG';     
        break;
    } 
	$auditArr = array();
	$auditArr['COMPANY_CODE'] 	  = $_SESSION['JV']['COMPANY_CODE']; 
	$auditArr['EVENT_TYPE']    	  = 'EXPORT';
	$auditArr['ACCESSING_URL_IP'] = $custom->getUserIP();
	$auditArr['EVENT_REMARKS'] 	  = $EVENT_REMARKS;
	$auditArr['CREATED_DATE']	  = $timeStamp;
	$auditArr['CREATED_BY']		  = $_SESSION['JV']['EMAILADDRESS'];
	$insertAudit = $dbJV->insert("tbl_audit_trail",$auditArr);

	if(isset($_POST["ExportType"]))
	{
		 
	    switch($_POST["ExportType"])
	    {
	        case "export-to-excel" :
	            // Submission from
				$filename = date('YmdHis').$_SESSION['JV']['COMPANY_CODE'].".xls";		 
	            header("Content-Type: application/vnd.ms-excel");
				header("Content-Disposition: attachment; filename=\"$filename\"");
				ExportFile($excelArray);
				//$_POST["ExportType"] = '';				
				
	            exit();

       		case "export-to-csv" :
	            // Submission from
				$filename = date('YmdHis').$_SESSION['JV']['COMPANY_CODE']. ".csv";		 
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Content-type: text/csv");
				header("Content-Disposition: attachment; filename=\"$filename\"");
				ExportCSVFile($excelArray);
				//$_POST["ExportType"] = ''; 

            exit();

	        default :
	            die("Unknown action : ".$_POST["action"]);
	            break;
	    }
	}
   
	function ExportCSVFile($records) {
		// create a file pointer connected to the output stream
		$fh = fopen( 'php://output', 'w' );
		$heading = false;
			if(!empty($records))
			  foreach($records as $row) {
				if(!$heading) {
				  // output the column headings
				  fputcsv($fh, array_keys($row));
				  $heading = true;
				} 
   				fputcsv($fh, array_values($row));  
			  }
			  fclose($fh);
	}

	function ExportFile($records) {
		$heading = false;
			if(!empty($records))
			  foreach($records as $row) {
				if(!$heading) {
				  // display field/column names as a first row
				  echo implode("\t", array_keys($row)) . "\n";
				  $heading = true;
				}
				echo implode("\t", array_values($row)) . "\n";
			  }
			exit;
	} 
?>