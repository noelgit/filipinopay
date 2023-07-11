<?php  
    $dateTime     = date('D M j g:i A'); 
    $dateToday    = date('Y-m-d'); 

    //*********************************
    //TABLE QUERY DATA
    //*********************************
    $tableQueryData = array();
    $tableQueryData['COMPANY_CODE_1'] = $companyCode;
    $tableQueryData['COMPANY_CODE_2'] = $companyCode;
    
    $dateFrom        =  $_POST['dateFrom'] ? $_POST['dateFrom'] : false;  
    $dateToFormat    =  $_POST['dateTo'] ? date('Y-m-d', strtotime($_POST['dateTo'] . '+1 day')) : false; 
    $dateTo          =  $_POST['dateTo'] ? $_POST['dateTo'] : false; 
    $referenceNumber =  $_POST['referenceNumber'] ? $_POST['referenceNumber'] : false; 
    $transactionNumber =  $_POST['transactionNumber'] ? $_POST['transactionNumber'] : false; 
    $ORNumber        =  $_POST['ORNumber'] ? $_POST['ORNumber'] : false; 
    $merchantCode    =  $_POST['MERCHANT_CODE'] ? $_POST['MERCHANT_CODE'] : false; 
    $modePayment    =  $_POST['MODE_PAYMENT'] ? $_POST['MODE_PAYMENT'] : false; 

    $additionalQuery = "";
    if($_POST['dateFrom'] AND $_POST['dateTo']){
        $tableQueryData['DATEFROM'] = $dateFrom;
        $tableQueryData['DATETOFORMAT'] = $dateToFormat;
        $additionalQuery .= "(tth.`CREATED_DATE` BETWEEN :DATEFROM AND :DATETOFORMAT) AND ";
    }
    if($_POST['referenceNumber']){
        $tableQueryData['MERCHANT_REF_NUM'] = $referenceNumber;
        $additionalQuery .= "tth.`MERCHANT_REF_NUM` = :MERCHANT_REF_NUM AND ";
    }
    if($_POST['transactionNumber']){
        $tableQueryData['TRN'] = $transactionNumber;
        $additionalQuery .= "tth.`TRN` = :TRN AND ";
    }
    if($_POST['ORNumber']){
        $tableQueryData['EOR'] = $ORNumber;
        $additionalQuery .= "te.`EOR` = :EOR AND ";
    } 
    if($_POST['MERCHANT_CODE']){
        $tableQueryData['MERCHANT_CODE'] = $merchantCode;
        $additionalQuery .= "tijf.`MERCHANT_CODE` = :MERCHANT_CODE AND ";
    } 
    if($_POST['MODE_PAYMENT']){
        $tableQueryData['MODE_PAYMENT'] = $modePayment;
        $additionalQuery .= "trpe.`PE_CODE` = :MODE_PAYMENT AND ";
    } 
    
    $tableData = $dbEnterprise->getResults("SELECT tth.`TRN`,tth.`CREATED_DATE`, tth.`MERCHANT_REF_NUM`, tth.`PM_CODE`, tth.`CONVENIENCE_FEE` - tth.`IPG_FEE` AS MDR, tth.`IPG_FEE`, tth.`COMPANY_CODE_1_FEE`, tth.`COMPANY_CODE_2_FEE`,
        te.`EOR`, trpe.`DESCRIPTION`, tijf.`COMPANY_CODE_1`, tijf.`COMPANY_CODE_2`, tth.`TRANS_STATUS`, tijf.`MERCHANT_CODE`, vmas.`MERCHANT_NAME`,
        (SELECT SUM(tt.`TRANSACTION_AMOUNT`) FROM tbl_transactions AS tt WHERE tth.`TRN` = tt.`TRN`) TOTALAMOUNT 

        FROM tbl_transactions_hdr AS tth 

        LEFT JOIN tbl_eor AS te 
        ON te.`TRN` = tth.`TRN` 

        LEFT JOIN tbl_ref_payment_entity AS trpe 
        ON trpe.`PE_CODE` = tth.`PE_CODE` 
        
        LEFT JOIN vw_merchant_and_submerchant AS vmas
        ON vmas.`MERCHANT_CODE` = tth.`MERCHANT_CODE`

        LEFT JOIN tbl_ipg_jv_fees AS tijf
        ON tijf.`COMPANY_CODE_1` = :COMPANY_CODE_1 OR tijf.`COMPANY_CODE_2` = :COMPANY_CODE_2
                
        WHERE  ".$additionalQuery." tth.`JV_CODE` = tijf.`JV_CODE` 
       
        GROUP BY tth.`TRN`
        ORDER BY tth.CREATED_DATE DESC", $tableQueryData);  

    $merchants = $dbMerchant->getResults("SELECT MERCHANT_CODE, MERCHANT_NAME FROM tbl_merchant_info AS MERCHANT");
    $paymentMode = $dbEnterprise->getResults("SELECT PE_CODE, DESCRIPTION FROM tbl_ref_payment_entity");
?>